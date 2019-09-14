<?php
require_once("../../Tester.php");
require_once("../../../handlers/RoomMessageHandler.php");
require_once("../../../handlers/MessageHandlerFactory.php");

/*
Tests a RoomMessageHandler to ensure that an event with a valid primary type and subtype is handled appropriately,
and that the ChildHandler's callback is invoked.
*/
function TestHandleTrueWithChild() {
    $childrenInitData = array();
    $childrenInitData[] = MessageHandlerFactory::ConstructChildInitData("subtype", "join", function () { return true; });
    $roomMessageHandler = MessageHandlerFactory::ConstructRoomMessageHandler
        ("type",
        "roomMessage", 
        NULL, 
        $childrenInitData);
    $message = array("type"=>"roomMessage", "subtype"=>"join");
    return $roomMessageHandler->handle($message);
}

/*
Tests a RoomMessageHandler to ensure that an event with a valid primary type is not handled even if
the subtype is a valid type for a ChildHandler.
*/
function testHandleFalseWithChild() {
    $childrenInitData = array();
    $childrenInitData[] = MessageHandlerFactory::ConstructChildInitData("subtype", "join", function () { return true; });
    $roomMessageHandler = MessageHandlerFactory::ConstructRoomMessageHandler
        ("type",
        "roomMessage", 
        NULL, 
        $childrenInitData);
    $message = array("type"=>"friendMessage", "subtype"=>"join");
    return $roomMessageHandler->handle($message) === false;
}

/*
Tests a RoomMessageHandler to ensure it returns false when an event with an invalid type is handled.
*/
function TestHandleFalseNoChild() {
    $roomMessageHandler = new RoomMessageHandler("type", "roomMessage", NULL);
    $message = array("type"=>"nonRoomMessage");
    return $roomMessageHandler->handle($message) === false;
}

/*
Tests to ensure that a primary type accepted by a RoomMessageHandle, is not handled by 
any of its ChildrenHandlers if the subtype does not match their types.
*/
function testHandleFalseWithMultipleChildren() {
    $childrenInitData = array();
    $childrenInitData[] = MessageHandlerFactory::ConstructChildInitData("subtype", "join", function () { return true; });
    $childrenInitData[] = MessageHandlerFactory::ConstructChildInitData("subtype", "leave", function () { return true; });
    $childrenInitData[] = MessageHandlerFactory::ConstructChildInitData("subtype", "invite", function () { return true; });
    $roomMessageHandler = MessageHandlerFactory::ConstructRoomMessageHandler
        ("type",
        "roomMessage", 
        NULL, 
        $childrenInitData);
    $message = array("type"=>"roomMessage", "subtype"=>"nonexistantSubtype");
    return $roomMessageHandler->handle($message) === false;
}

$tests = array("TestHandleTrueWithChild", "testHandleFalseWithChild", "TestHandleFalseNoChild",
            "testHandleFalseWithMultipleChildren");
Tester::echoTestResults($tests);
exit();