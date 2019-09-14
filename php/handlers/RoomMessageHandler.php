<?php
require_once("BaseMessageHandler.php");
require_once("Handler.php");
class RoomMessageHandler extends BaseMessageHandler implements Handler {
    private $channelManager;
    private $childHandler;

    function __construct($typeKey, $type, $channelManager) {
        parent::__construct($typeKey, $type);
    }

    function handle($message) {
        echo "Handling " . print_r($message) . "\n";
        if ($this->canHandle($message) && $this->childHandler != NULL) {
            return $this->childHandler->handle($message);
        }
        else if (parent::getNext() != NULL) {
            $next = parent::getNext();
            return $next === NULL ? false : $next->handle($message);
        }
        return false;
    }

    function setChildHandler($childHandler) {
        $this->childHandler = $childHandler;
    }
}