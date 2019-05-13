<?php
define('HOST_NAME', "localhost");
define('PORT', "8080");
$null = NULL;

require_once("ChannelManager.php");
$chatHandler = new ChannelManager();

/*
 * server sockets accepts client sockets and listens to them
 *
 */
$serverSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($serverSocket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($serverSocket, HOST_NAME, PORT);
socket_listen($serverSocket);

$clientSocketArray = array($serverSocket);

while (true) {
	$readSockets = $clientSocketArray;
    //get all client sockets that have incoming data
	socket_select($readSockets, $null, $null, 0, 10);

	//if the serverSocket has a request for a connection, then accept it
	if (in_array($serverSocket, $readSockets)) {
	    //accept and handle the client socket
		$clientSocket = socket_accept($serverSocket);
		$clientSocketArray[] = $clientSocket;

		//read the header and process it
		$header = socket_read($clientSocket, 1024);

		//handshake with the client
		$chatHandler->doHandshake($header, $clientSocket, HOST_NAME, PORT);

		/*
		 * Receive the client's username and their current channel
		 * after a connection has been established.
		 */
		if (socket_recv($clientSocket, $clientInfo, 1024, 0) >= 1) {
		    $clientInfoObj = json_decode($chatHandler->unseal($clientInfo), true);
            $clientUsername = $clientInfoObj["username"];
            $channelName = $clientInfoObj["channel"];
            if ($channelName !== "") {
                //add new channels
                if (!$chatHandler->hasChannel($channelName)) {
                    $clientNameSocketAssoc = array($clientUsername => $clientSocket);
                    $chatHandler->addChannel($channelName, $clientNameSocketAssoc);
                    //error_log("Adding " . $channelName . " with " . $clientUsername . "\n", 3, "../logs/error_log.txt");
                } else {
                    $chatHandler->addUserToChannel($channelName, $clientUsername, $clientSocket);
                    //error_log("Adding " . $clientUsername . " to " . $channelName . "\n", 3, "../logs/error_log.txt");
                }
                $message = $chatHandler->formatMessage($clientUsername . " has joined " . $channelName, $clientUsername, $channelName);
                $chatHandler->broadcast($message, $channelName);
            }
            else {
                $chatHandler->send("Welcome " . $clientUsername, $clientSocket);
            }
        }


		//remove the serverSocket from the readSockets array
		$newSocketIndex = array_search($serverSocket, $readSockets);
		unset($readSockets[$newSocketIndex]);
	}
	
	foreach ($readSockets as $newSocketArrayResource) {
	    //for each clientSocket that has received data(a message), send it to server to broadcast to a channel
		while(socket_recv($newSocketArrayResource, $socketData, 1024, 0) >= 1){
			$socketMessage = $chatHandler->unseal($socketData);
			$json = json_decode($socketMessage, true);

            $username = $json["username"];
            $channel = $json["channel"];
            $message = $json["message"];


			$chatHandler->broadcast($chatHandler->formatMessage($message, $username, $channel), $channel);
			break 2;
		}

		/*
		//remove clients that left from a channel
		$socketData = @socket_read($newSocketArrayResource, 1024, PHP_NORMAL_READ);
		if ($socketData === false) {
			socket_getpeername($newSocketArrayResource, $client_ip_address);
			$connectionACK = $chatHandler->connectionDisconnectACK($client_ip_address);
			$chatHandler->send($connectionACK, $newSocketArrayResource);
			$newSocketIndex = array_search($newSocketArrayResource, $clientSocketArray);
			unset($clientSocketArray[$newSocketIndex]);
		}
		*/
	}
}
socket_close($serverSocket);