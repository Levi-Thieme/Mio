<?php
require_once("SocketData.php");
require_once("Client.php");
require_once("Channel.php");

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

    function getChannel($id) {
        foreach(array_values($this->channels) as $channel) {
            if ($channel->getId() === $id)
                return $channel;
        }
        return NULL;
    }

    /**
     * Sends message to a recipientSocket
     * This function requires $message to be encoded by the seal function.
     * @param $message String message
     * @param $recipientSocket resource to receive the message
     * @return int The number of bytes successfully written to the socket or False on failure.
     */
    function send($message, $recipientSocket) {
        if (isset($recipientSocket) && strlen($message) > 0) {
            return @socket_write($recipientSocket, $message, strlen($message));
        }
        return false;
    }

    /**
     * Broadcasts a message to all users of the specified channel.
     * This function requires $message to be encoded by the seal function.
     * @param $message String message
     * @param $channelName String The name of the channel to send message to.
     * @return int The number of bytes successfully written to the socket or False on failure.
     */
	function broadcast($message, $channelId) {
	    $status = true;
	    $channel = $this->getChannel($channelId);
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
	 * Adds a channel to $channels.
	 */
	function addChannel($id, $name) {
        $newChannel = new Channel($id, $name, array());
        array_push($this->channels, $newChannel);
        return $newChannel;
    }

    /*
     * Returns true if there is a channel with id
     */ 
    function containsChannel($id) {
        foreach ($this->channels as $channel) {
            if ($channel->getId() === $id) {
                return true;
            }
        }
        return false;
    }

    /*
     * Adds a Client object to the specified channel.
     * Precondition: A Channel with $channelId must already exist in $this->channels.
     */
	function addClientToChannel($channelId, $client) {
        $channel = $this->getChannel($channelId);
        if (isset($channel)) {
            $channel->addReplaceClient($client);
            return true;
        }
        return false;
    }

    /*
     * Removes the $client from the specified channel.
     * Returns the client's socket resource.
     */
    function removeClientFromChannel($channelId, $clientId) {
        $channel = $this->getChannel($channelId);
        $clientSocket = NULL;
        if ($channel != NULL) {
            $clientSocket = $channel->removeClientById($clientId);
        }
        return $clientSocket;
    }

    /*
    Attempts to retrieve the client where the client's id is $clientId.
    If it is found, this method returns the Client object, else it returns NULL.
    */
    function getClientFromChannels($clientId) {
        $channels = $this->getChannels();
        foreach ($channels as $channel) {
            $client = $channel->getClientById($clientId);
            if ($client != NULL) {
                return $client;
            }
        }
        return NULL;
    }
}