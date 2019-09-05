<?php
require_once("SocketData.php");
require_once("Client.php");
require_once("Channel.php");
//define("LOG_URL", "../logs/socket_error_log.txt");

/*
The ChannelManager class' responsibility is create and destroy channels, and place clients in the proper channel.
*/
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
            if ($channel->getName() === $channelName)
                return $channel;
        }
        return NULL;
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
        return SocketData::seal(json_encode($messageAssoc));
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
            foreach ($channel->getClients() as $client) {
                $this->send($message, $client->getSocket());
            }
        }
        else {
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
	    $channel->addReplaceClient($client);
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
        $clientSocket = $client->getSocket();
        $channel->removeClient($client);
        return $clientSocket;
    }

    /*
     * Handles a new client connection by placing them in their channel if available,
     * and sending a greeting.
     */
    function addNewClient($clientSocket, $clientInfo) {
        $clientId = $clientInfo["id"];
        $clientUsername = $clientInfo["username"];
        $channelName = $clientInfo["channel"];
        $newClient = new Client($clientId, $clientUsername, $clientSocket);
        //ensure the channel is contained in $channels, if not add it
        if ($this->getChannel($channelName) === NULL) {
            $this->addChannel($channelName);
        }
        $this->addClientToChannel($channelName, $newClient);
    }

    /*
     * Handles a socket message according to the message association's content.
     *
     * There are currently two message types: Broadcast, MoveToChannel
     */
    function handleSocketMessage($clientSocket, $messageAssoc) {
        $id = $messageAssoc["id"];
        $username = $messageAssoc["username"];
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
                    $client = new Client($id, $username, $clientSocket);
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
            $this->send($message, $toClient->getSocket());
            return true;
        }
        return false;
    }
}