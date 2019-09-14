<?php
require_once("RoomMessageHandler.php");
require_once("ChildHandler.php");
class MessageHandlerFactory {
    /*
    Constructs and returns a RoomMessageHandler with the specified type as handleType.
    childrenInitData is an array containing keyTyp, type, and handleCallback for a ChildHandler. 
    If childrenInitData is empty, no children are constructed. $roomHandler is a reference to a ChannelManager object.
    */
    public static function ConstructRoomMessageHandler($typeKey, $type, $roomHandler, $childrenInitData) {
        $parentHandler = new RoomMessageHandler($typeKey, $type, $roomHandler);
        $firstChild = NULL;
        $lastChild = NULL;
        foreach ($childrenInitData as $initData) {
            $childType = $initData["typeKey"];
            $childKeyType = $initData["type"];
            $handleCallback = $initData["onHandle"];
            $child = new ChildHandler($childType, $childKeyType, $handleCallback);
            if ($firstChild === NULL) {
                $firstChild = $child;
            }
            else {
                $lastChild->setNext($child);
            }
            $lastChild = $child;
        }
        $parentHandler->setChildHandler($firstChild);
        return $parentHandler;
    }

    /*
    Returns an associative array that can be used to initialize a ChildHandler object.
    $typeKey - the key for indexing the value for "type". 
    $type - the value used in canHandle to determine if the ChildHandler can handle the event.
    $onHandle - a callback function that is invoked when the ChildHandler handles an event.
    */
    public static function ConstructChildInitData($typeKey, $type, $onHandle) {
        return array("typeKey" => $typeKey, "type" => $type, "onHandle" => $onHandle);
    }

    /*
    Constructs a RoomMessageHandler with a typekey of type and a type of roomEvent.
    The RoomMessageHandler has children for events with typeKeys of "subtype",
    where subtype has a value of "left", "joined", or "deleted".
    */
    public static function ConstructDefaultRoomMessageHandler($channelManager) {
        $childInitData = array();
        $leaveRoomChild = MessageHandlerFactory::ConstructChildInitData("action", "left", function($event) use ($channelManager) {
            $channelManager->broadcastClientLeft($event["fromUsername"], $event["roomId"]);
        });
        $childInitData[] = $leaveRoomChild;
        $joinRoomChild = MessageHandlerFactory::ConstructChildInitData("action", "joined", function($event) use ($channelManager) {
            $channelManager->broadcastClientJoined($event["fromUsername"], $event["roomId"]);
        });
        return MessageHandlerFactory::ConstructRoomMessageHandler("type", "sendRoomNotification", $channelManager, $childInitData);
    }
}