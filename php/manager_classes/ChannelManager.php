<?php
class ChannelManager {
    private $channels;

    function __construct() {
        $this->channels = array();
    }

    /**
     * Formats data into a JSON object and seals it.
     * @param $message String message
     * @param $username String username of sender
     * @param $channel String channel name
     * @return string a sealed JSON object
     */
    function formatMessage($message, $username, $channel) {
        $messageAssoc = array("message" => $message,
            "username" => $username,
            "channel" => $channel,
            "time" => date("F d, Y h:i:s A", time()),
            "messageId" => 0);
        return $this->seal(json_encode($messageAssoc));
    }

    /**
     * Sends message to a recipientSocket
     * This function requires $message to be encoded by the seal function.
     * @param $message String message
     * @param $recipientSocket resource to receive the message
     * @return int The number of bytes successfully written to the socket or False on failure.
     */
    function send($message, $recipientSocket) {
        return @socket_write($recipientSocket, $message, strlen($message));
    }

    /**
     * Broadcasts a message to all users of the specified channel.
     * This function requires $message to be encoded by the seal function.
     * @param $message String message
     * @param $channel String The name of the channel to send message to.
     * @return int The number of bytes successfully written to the socket or False on failure.
     */
	function broadcast($message, $channel) {
	    $status = true;
        if ($this->hasChannel($channel)) {
            foreach ($this->channels[$channel] as $username => $socket) {
                @socket_write($socket, $message, strlen($message));
            }
        }
        else {
            error_log($channel . " does not exist\n", 3, "../logs/error_log.txt");
            $status = false;
        }
		return $status;
	}

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

	function seal($socketData) {
		$b1 = 0x80 | (0x1 & 0x0f);
		$length = strlen($socketData);
		
		if($length <= 125)
			$header = pack('CC', $b1, $length);
		elseif($length > 125 && $length < 65536)
			$header = pack('CCn', $b1, 126, $length);
		elseif($length >= 65536)
			$header = pack('CCNN', $b1, 127, $length);
		return $header.$socketData;
	}

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
	
	function newConnectionACK($username, $channel) {
		$message = 'New client ' . $username.' has joined.';
		$ACK = $this->formatMessage($message, $username, $channel);
		return $ACK;
	}
	
	function connectionDisconnectACK($client_ip_address) {
		$message = 'Client ' . $client_ip_address.' disconnected';

		$ACK = $this->formatMessage($message, $client_ip_address, "Joe's Room");
		return $ACK;
	}

	function addChannel($channelName, $channelUsersAssoc) {
	    $this->channels[$channelName] = $channelUsersAssoc;
	    print_r($this->channels);
    }

	function addUserToChannel($channelName, $username, $socketResource) {
        array_push($this->channels[$channelName], array($username => $socketResource));
        print_r($this->channels[$channelName]);
    }

    function removeUserFromChannel($channelName, $username) {
	    foreach($this->channels[$channelName] as $user => $socket) {
	        unset($user, $this->channels[$channelName]);
        }
    }

    function hasChannel($channelName) {
        return array_key_exists($channelName, $this->channels);
    }
}
?>