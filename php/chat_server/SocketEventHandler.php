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
    Handles a socket event by passing it off to the first messageHandler in the handling chain.
    */
    function handleSocketMessage($sendingSocket, $message) {
        $message = json_decode($message, true, JSON_PRETTY_PRINT);
        $message["socket"] = $sendingSocket;
        $wasHandled = $this->messageHandler->handle($message);
        if ($wasHandled) {
            echo $message["action"] . " was handled.";
        }
        else {
            echo "Failed to handle " . $message["action"];
        }
    }
}