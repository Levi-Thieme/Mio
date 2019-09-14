<?php
require_once("RoomMessageHandler.php");
require_once("ChildHandler.php");
require_once(dirname(__FILE__) . "/../Utils/ArrayUtils.php");
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
    where subtype has a value of "broadcast", "leave", "join", or "delete".
    */
    public static function ConstructDefaultRoomMessageHandler($channelManager) {
        $childInitData = array();
        $broadcastRoomChild = MessageHandlerFactory::ConstructChildInitData("action", "broadcast", function($event) use ($channelManager) {
            $channelManager->broadcast(SocketData::seal(json_encode($event, JSON_PRETTY_PRINT)), $event["roomId"]);
        });
        $childInitData[] = $broadcastRoomChild;
        $leaveRoomChild = MessageHandlerFactory::ConstructChildInitData("action", "leave", function($event) use ($channelManager) {
            $requiredKeys = array("clientId", "roomId", "fromUsername");
            if (!ArrayUtils::hasKeysWithNonEmptyValues($event, $requiredKeys)) {
                return false;
            }
            //removeClientFromChannel returns the client's socket if succesfull, else it returns false.
            $clientSocket = $channelManager->removeClientFromChannel($message["roomId"], $message["clientId"]);
            $channelManager->broadcast(SocketData::seal(json_encode($event, JSON_PRETTY_PRINT, true)), $event["roomId"]);
            return $clientSocket != false;
        });
        $childInitData[] = $leaveRoomChild;
        $joinRoomChild = MessageHandlerFactory::ConstructChildInitData("action", "join", function($event) use ($channelManager) {
            $requiredKeys = array("clientId", "fromUsername", "socket", "roomId", "roomName");
            if (!ArrayUtils::hasKeysWithNonEmptyValues($event, $requiredKeys)) {
                return false;
            }
            $client = new Client($event["clientId"], $event["fromUsername"], $event["socket"]);
            if (!$channelManager->containsChannel($event["roomId"])) {
                $channelManager->addChannel($event["roomId"], $event["roomName"]);   
            }
            $hasJoined = false;
            $hasJoined = $channelManager->addClientToChannel($event["roomId"], $client);
            $message = array("type" => "sendRoomNotification", "action" => "join", "fromUsername" => $event["fromUsername"], "roomName" => $event["roomName"]);
            $channelManager->broadcast(SocketData::seal(json_encode($message, JSON_PRETTY_PRINT, true)), $event["roomId"]);
            return $hasJoined;
        });
        $childInitData[] = $joinRoomChild;
        return MessageHandlerFactory::ConstructRoomMessageHandler("type", "sendRoomNotification", $channelManager, $childInitData);
    }
}