<?php
require_once("Handler.php");
class ChildHandler extends BaseMessageHandler {
    private $onHandle;

    function __construct($typeKey, $type, $onHandle) {
        parent::__construct($typeKey, $type);
        $this->onHandle = $onHandle;
    }

    function handle($message) {
        if ($this->canHandle($message)) {
            $onHandleCallback = $this->onHandle;
            return $onHandleCallback($message);
        }
        $next = $this->getNext();
        return $next == NULL ? false : $next->handle($message);
    }
}