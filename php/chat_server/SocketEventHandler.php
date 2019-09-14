<?php
require_once(dirname(__FILE__) . "\../handlers/MessageHandlerFactory.php");
define ("LOG_URL", "../../../../logs/error_log.txt");

class SocketEventHandler {
    private $channelManager;
    private $messageHandler;

    function __construct($channelManager) {
        $this->channelManager = $channelManager;
        $this->messageHandler = MessageHandlerFactory::ConstructDefaultRoomMessageHandler($channelManager);
    }

    function getChannelManager() {
        return $this->channelManager;
    }

    /*
    Returns true if the given array contains $key as a key with a value
    */
    function hasKeyWithNonEmptyValue($array, $key) {
        return array_key_exists($key, $array) && !empty($array[$key]);
    }

    /*
    Returns true if the given array contains all the keys contained in $keys,
    and every key has a value that is not empty.
    */
    function hasKeysWithNonEmptyValues($array, $keys) {
        foreach ($keys as $key) {
            if ($this->hasKeyWithNonEmptyValue($array, $key) === false) {
                return false;
            }
        }
        return true;
    }

    /*
    Returns true if the $request contains the required parameters for completing its action.
    */
    function requestActionParamsAreValid($request) {
        if ($this->hasKeysWithNonEmptyValues($request, array("type", "action", "content")) === false) {
            return false;
        }
        $type = $request["type"];
        $action = $request["action"];
        $content = $request["content"];
        if ($type === "message") {
            if ($action === "broadcast" && 
                $this->hasKeysWithNonEmptyValues($content, array("fromId", "roomId", "fromUsername", "message"))) {
                return true;
            }
        }
        else if ($type === "sendFriendNotification") {
            if (($action === "accepted" || $action === "denied" || $action === "newRequest")
                && $this->hasKeysWithNonEmptyValues($content, array("toId", "fromUsername"))) {
                return true;
            }
        }
        else if ($type === "sendRoomNotification") {
            if (($action === "left" || $action === "joined" || $action === "deleted") &&
                $this->hasKeysWithNonEmptyValues($content, array("roomId", "fromUsername"))) {
                return true;
            }
            else if ($action === "inviteToRoom"
                && $this->hasKeysWithNonEmptyValues($content, array("roomId", "fromUsername", "toId", "roomName"))) {
                return true;
            }
        }
        return false;
    }

    /*
    Handles a socket event
    */
    function handleSocketMessage($sendingSocket, $message) {
        $message = json_decode($message, true);
        $this->messageHandler->handle($message);
    }

    function handleMessage($message) {
        echo "Handling message...\n";
        if ($message["action"] === "broadcast") {
            $this->sendMessageToChannel(json_encode($message), $message["content"]["roomId"]);
        }
    }

    function handleFriendNotification($request) {
        $this->sendNotificationToUser($request["content"]["toId"], json_encode($request));
    }

    function handleRoomNotification($message) {
        $this->sendMessageToChannel($message, $message["content"]["roomId"]);
    }

    /*
    Broadcasts the message to the channel with id = channelId
    */
    function sendMessageToChannel($message, $channelId) {
        echo "Sending message to Channel " . $channelId . "\n";
        $this->channelManager->broadcast(SocketData::seal($message), $channelId);
    }
    /*
        Posts message in the user with id = userId
     */
    function sendMessageToUser($clientId, $message) {
        $client = $this->channelManager->getClientFromChannels($clientId);
        //if Client is online
        if ($client != NULL) {
            $clientSocket = $client->getSocket();
            $this->channelManager->send(SocketData::seal($message), $clientSocket);
        }
    }
    /*
    - message appears as toast to the user with id = userId
    */
    function sendNotificationToUser($clientId, $message) {
        $client = $this->channelManager->getClientFromChannels($clientId);
        //if Client is online
        if ($client != NULL) {
            $clientSocket = $client->getSocket();
            $this->channelManager->send(SocketData::seal($message), $clientSocket);
        }
    }
}