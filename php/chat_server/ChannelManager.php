<?php

/*
 * The Client class is used to wrap a username and socket resource in an object, so that
 * the $channels array doesn't have nested arrays.
 */
class Client {
    public $username;
    public $socket;

    function __construct($username, $socketResource)
    {
        $this->username = $username;
        $this->socket = $socketResource;
    }
}

class Channel {
    public $name;
    public $clients;

    function __construct($name, $clients) {
        $this->name = $name;
        $this->clients = $clients;
    }

    public function addClient($client) {
        array_push($this->clients, $client);
    }

    public function removeClient($client) {
        unset($this->clients[array_search($client, $this->clients)]);
    }

    public function getClientByUsername($username) {
        foreach ($this->clients as $client) {
            if ($client->username === $username) {
                return $client;
            }
        }
        return NULL;
    }
}

class ChannelManager {
    private $channels;

    function __construct() {
        $this->channels = array();
    }

    function getChannels() {
        return $this->channels;
    }

    function setChannels($channels) {
        $this->channels = $channels;
    }

    function getChannel($channelName) {
        foreach(array_values($this->channels) as $channel) {
            if ($channel->name === $channelName)
                return $channel;
        }
        return NULL;
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
     * @param $channelName String The name of the channel to send message to.
     * @return int The number of bytes successfully written to the socket or False on failure.
     */
	function broadcast($message, $channelName) {
	    $status = true;
	    $channel = $this->getChannel($channelName);
        if ($channel != NULL) {
            foreach ($channel->clients as $client) {
                $this->send($message, $client->socket);
            }
        }
        else {
            error_log($channelName . " does not exist\n", 3, "../logs/error_log.txt");
            $status = false;
        }
		return $status;
	}

	/*
	 * Broadcasts a message to the specified channel that a new user has joined.
	 */
	function broadcastClientJoined($username, $channelName) {
		$message = "{$username} has joined the chat.";
		$formattedMessage = $this->formatMessage($message, $username, $channelName);
		$this->broadcast($formattedMessage, $channelName);
	}

    /*
     * Broadcast a message to the specified channel that a user has left.
     */
	function broadcastClientLeft($username, $channel) {
		$message = "{$username} has left the chat.";
		$formattedMessage = $this->formatMessage($message, $username, $channel);
		$this->broadcast($formattedMessage, $channel);
	}

	/*
	 * Adds a channel to $channels.
	 */
	function addChannel($channelToAdd) {
	    array_push($this->channels, new Channel($channelToAdd, array()));
    }

    /*
     * Adds a Client object to the specified channel.
     */
	function addClientToChannel($channelName, $client) {
	    $channel = $this->getChannel($channelName);
	    $channel->addClient($client);
	    //error_log("Add {$client->username} to {$channelName}\n", 3, "socket_error_log.txt");
    }

    /*
     * Removes the $client from the specified channel.
     * Returns the client's socket resource.
     */
    function removeClientFromChannel($channelName, $client) {
        if ($client == NULL) {
            return NULL;
        }
        $channel = $this->getChannel($channelName);
        $clientSocket = $client->socket;
        $channel->removeClient($client);
        //error_log("Remove {$client->username} from {$channelName}\n", 3, "socket_error_log.txt");
        return $clientSocket;
    }

    /*
     * Handles a new client connection by placing them in their channel if available,
     * and sending a greeting.
     */
    function addNewClient($clientSocket, $clientInfo) {
        $clientUsername = $clientInfo["username"];
        $channelName = $clientInfo["channel"];
        $newClient = new Client($clientUsername, $clientSocket);
        //ensure the channel is contained in $channels, if not add it
        if ($this->getChannel($channelName) === NULL) {
            $this->addChannel($channelName);
            //error_log("Creating channel: {$channelName}\n", 3, "socket_error_log.txt");
        }
        $this->addClientToChannel($channelName, $newClient);
    }

    /*
     * Handles a socket message according to the message association's content.
     *
     * There are currently two message types: Broadcast, MoveToChannel
     */
    function handleSocketMessage($clientSocket, $messageAssoc) {
        $username = $messageAssoc["username"];
        //error_log(print_r($messageAssoc), 3, "socket_error_log.txt");
        if (isset($messageAssoc["action"])) {
            if ($messageAssoc["action"] === "MoveToChannel") {
                $currentChannelName = $messageAssoc["currentChannelName"];
                $channelToId = $messageAssoc["channelToId"];
                $channelToName = $messageAssoc["channelToName"];
                $currentChannelObj = $this->getChannel($currentChannelName);
                if ($currentChannelObj == NULL) {
                    $this->send($this->formatMessage($currentChannelName . " cannot be found.", $username, $currentChannelName), $clientSocket);
                } else {
                    $client = $currentChannelObj->getClientByUsername($username);
                    $this->removeClientFromChannel($currentChannelName, $client);
                    $this->broadcastClientLeft($username, $currentChannelName);
                    if ($this->getChannel($channelToName) === NULL) {
                        $this->addChannel($channelToName);
                    }
                    $client = new Client($username, $clientSocket);
                    $this->addClientToChannel($channelToName, $client);
                    $this->broadcastClientJoined($username, $channelToName);
                }
            }
            else if ($messageAssoc["action"] === "notifyFriendRequest") {
                $this->notifyFriendRequest($username, $messageAssoc["toUsername"]);
            }
        }
        else {
            $currentChannelName = $messageAssoc["currentChannelName"];
            $message = $messageAssoc["message"];
            $this->broadcast($this->formatMessage($message, $username, $currentChannelName), $currentChannelName);
        }
    }

    //Attempts to retrieve the channel that a client with $username belongs to.
    function getClientFromChannels($username) {
        $channels = $this->getChannels();
        foreach ($channels as $channel) {
            $client = $channel->getClientByUsername($username);
            if ($client != NULL) {
                return $client;
            }
        }
        return NULL;
    }

    //Attempts to send a friend request message to a user with $toUsername if they are online.
    function notifyFriendRequest($fromUsername, $toUsername) {
        $toClient = $this->getClientFromChannels($toUsername);
        if ($toClient != NULL) {
            $message = $this->formatMessage("You have a friend request from {$fromUsername}.", $fromUsername, "testChannel");
            $this->send($message, $toClient->socket);
            //error_log("sent: you have a friend request from {$fromUsername}\n", 3, "socket_error_log.txt");
            return true;
        }
        return false;
    }
}