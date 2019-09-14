<?php
    require_once("BaseHandler.php");
    require_once("Handler.php");
    class BaseMessageHandler extends BaseHandler implements Handler {
        private $handleKey;
        private $handleType;
        
        function __construct($handleKey, $handleType) {
            $this->handleKey = $handleKey;
            $this->handleType = $handleType;
        }

        function canHandle($message) {
            return $this->handleType === $message[$this->handleKey];
        }

        function handle($message) {
            if ($this->canHandle($message)) {
                return true;
            }
            $next = $this->getNext();
            return $next == NULL ? false : $next->handle($message);
        }

        function getHandleKey() {
            return $this->handleKey;
        }

        function getHandleType() {
            return $this->handleType;
        }
    }