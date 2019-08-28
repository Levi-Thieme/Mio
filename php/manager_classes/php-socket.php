<?php
define('HOST_NAME', "localhost");
define('PORT', "8080");
$null = NULL;

require_once("ChannelManager.php");

//Helper functions

/*
 * Removes the header added by seal from $socketData,
 * and returns a decoded version of $socketData.
 */
function unseal($socketData) {
    $length = ord($socketData[1]) & 127;
    if($length == 126) {
        $masks = substr($socketData, 4, 4);
        $data = substr($socketData, 8);
    }
    elseif($length == 127) {
        $masks = substr($socketData, 10, 4);
        $data = substr($socketData, 14);
    }
    else {
        $masks = substr($socketData, 2, 4);
        $data = substr($socketData, 6);
    }
    $socketData = "";
    for ($i = 0; $i < strlen($data); ++$i) {
        $socketData .= $data[$i] ^ $masks[$i%4];
    }
    return $socketData;
}

/*
 * Prepends a header onto $socketData to describe the contents.
 */
function seal($socketData) {
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($socketData);
    $header = "";
    if($length <= 125)
        $header = pack('CC', $b1, $length);
    elseif($length > 125 && $length < 65536)
        $header = pack('CCn', $b1, 126, $length);
    elseif($length >= 65536)
        $header = pack('CCNN', $b1, 127, $length);
    return $header.$socketData;
}

/*
 * Performs a handshake this a client socket resource.
 */
function doHandshake($received_header,$client_socket_resource, $host_name, $port) {
    $headers = array();
    $lines = preg_split("/\r\n/", $received_header);
    foreach($lines as $line)
    {
        $line = chop($line);
        if(preg_match('/\A(\S+): (.*)\z/', $line, $matches))
        {
            $headers[$matches[1]] = $matches[2];
        }
    }
    $secKey = $headers['Sec-WebSocket-Key'];
    $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
    $buffer  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
        "Upgrade: websocket\r\n" .
        "Connection: Upgrade\r\n" .
        "WebSocket-Origin: $host_name\r\n" .
        "WebSocket-Location: ws://$host_name:$port/demo/shout.php\r\n".
        "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
    socket_write($client_socket_resource,$buffer,strlen($buffer));
}

/*
 * Returns an error message describing the last socket error's code.
 */
function getErrorMessage() {
    $errorCode = socket_last_error();
    $errorMessage = socket_strerror($errorCode);
    return $errorMessage;
}

//end helper functions


$channelManager = new ChannelManager();

//serverSocket listens for and accepts any new client socket connection requests
$serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($serverSocket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($serverSocket, HOST_NAME, PORT);
socket_listen($serverSocket);
$clients = array();

//Enter infinite loop of receiving data from client sockets and handling new client socket connections.
while (true) {
	$socketsToRead = $clients;
	array_push($socketsToRead, $serverSocket);
	/*
	 * Get the number of sockets that have data available to read and handle new connections.
     * If $toRead == 0, then no new connections need to be handled nor do any sockets need to be read from.
	 * If $serverSocket is in $readSockets, then a new socket connection needs to be accepted.
	 * If $serverSocket is not in $readSockets AND $toRead > 0, then the sockets in $readSockets need to be read from.
	 */
	$needReadCount = socket_select($socketsToRead, $null, $null, $null, $null);
	//TODO see if socket_select is actually blocking, and remove this if it is blocking.
    if ($needReadCount === 0) {
        continue;
    }
    //If $serverSocket is in the $readSockets array, then a new connection has been requested by a remote client socket.
	if (in_array($serverSocket, $socketsToRead)) {
        $newClient = socket_accept($serverSocket);
        if ($newClient === false) {
            $errorMessage = getErrorMessage();
            error_log($errorMessage, 3, "./socket_error_log.txt");
        }
        else {
            $clients[] = $newClient;
            $header = socket_read($newClient, 1024);
            doHandshake($header, $newClient, HOST_NAME, PORT);
            if (socket_recv($newClient, $receiveBuffer, 1024, 0) > 0) {
                $clientInfo = json_decode(unseal($receiveBuffer), true);
                $channelManager->addNewClient($newClient, $clientInfo);
            }
        }
		//remove the serverSocket from the readSockets array
		$newSocketIndex = array_search($serverSocket, $socketsToRead);
		unset($socketsToRead[$newSocketIndex]);
	}
	//Receive incoming data from client sockets
	foreach ($socketsToRead as $socketToRead) {
		$socketData = socket_read($socketToRead, 1024);
		//close and unset any sockets closed by the client
		if ($socketData == false || $socketData == "") {
		    @socket_close($socketToRead);
		    unset($clients[array_search($socketToRead, $clients)]);
        }
		else {
            $socketMessage = unseal($socketData);
            $json = json_decode($socketMessage, true);
            //Pass off the JSON message to $channelManager to be handled.
            $channelManager->handleSocketMessage($socketToRead, $json);
        }
	}
}
socket_close($serverSocket);

