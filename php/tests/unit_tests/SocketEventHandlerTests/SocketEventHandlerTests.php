<?php
require_once("../../../chat_server/SocketEventHandler.php");
require_once("../../../chat_server/ChannelManager.php");
require_once("../../Tester.php");
define(LOG_URL, "../log.txt");
/*
Test Suite for the SocketEventHandler class.
*/


/*
Tests the constructor of SocketEventHandler
*/
function testSocketEventHandlerConstruct() {
    $channelManager = new ChannelManager();
    $eventHandler = new SocketEventHandler($channelManager);
    return $eventHandler->getChannelManager() === $channelManager;
}


/*
Tests the hasKeyWithNonEmptyValue function
*/
function testHasKeyWithNonEmptyValueForArrayWithEmptyArray() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array();
    return $eventHandler->hasKeyWithNonEmptyValue($array, "key") === false;
}

/*
Tests the hasKeyWithNonEmptyValue function
*/
function testHasKeyWithNonEmptyValueForArrayWithKeyAndValue() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array("key" => "value");
    return $eventHandler->hasKeyWithNonEmptyValue($array, "key");
}

/*
Tests the hasKeyWithNonEmptyValue function
*/
function testHasKeyWithNonEmptyValueForArrayWithEmptyKeyAndValue() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array("" => "value");
    return $eventHandler->hasKeyWithNonEmptyValue($array, "key") === false;
}


/*
Tests the hasKeyWithNonEmptyValue function
*/
function testHasKeyWithNonEmptyValueForArrayWithKeyAndNoValue() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array("key");
    return $eventHandler->hasKeyWithNonEmptyValue($array, "key") === false;
}

/*
Tests the hasKeyWithNonEmptyValue function
*/
function testHasKeyWithNonEmptyValueForArrayWithKeyAndNullValue() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array("key" => NULL);
    return $eventHandler->hasKeyWithNonEmptyValue($array, "key") === false;
}

/*
Tests the hasKeyWithNonEmptyValue function
*/
function testHasKeyWithNonEmptyValueForArrayWithKeyAndEmptyStringValue() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array("key" => "");
    return $eventHandler->hasKeyWithNonEmptyValue($array, "key") === false;
}

/*
Tests the hasKeysWithNonEmptyValues function
*/
function testHasKeysWithNonEmptyValuesForTrue() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array("key1" => "value1", "key2" => "value2");
    $keys = array("key1", "key2");
    return $eventHandler->hasKeysWithNonEmptyValues($array, $keys);
}

/*
Tests the hasKeysWithNonEmptyValues function for a false return value.
*/
function testHasKeysWithNonEmptyValuesForFalse() {
    $eventHandler = new SocketEventHandler(NULL);
    $array = array("key1" => "", "" => "value2");
    $keys = array("key1", "key2");
    return $eventHandler->hasKeysWithNonEmptyValues($array, $keys) === false;
}

/*
Tests the requestContentIsValid function for returning false if 
the request does not have the key "type" with a non-empty values.
*/
function testRequestActionParamsAreValidForMissingType() {
    $channelManager = new ChannelManager();
    $eventHandler = new SocketEventHandler($channelManager);
    $missingTypeRequest = array("" => "sendFriendNotification", "action" => "accepted",
        "content" => array("toId" => 1, "fromUsername" => "bobbert"));
    $missingTypeKey = $eventHandler->requestActionParamsAreValid($missingTypeRequest);
    $missingTypeValueRequest = array("" => "sendFriendNotification", "action" => "accepted",
        "content" => array("toId" => 1, "fromUsername" => "bobbert"));
    $missingTypeValue = $eventHandler->requestActionParamsAreValid($missingTypeValueRequest);
    return ($missingTypeKey || $missingTypeValue) === false;
}

/*
Tests the requestContentIsValid function for returning false if 
the request does not have the key "action" with a non-empty values.
*/
function testRequestActionParamsAreValidForMissingAction() {
    $channelManager = new ChannelManager();
    $eventHandler = new SocketEventHandler($channelManager);
    $missingActionRequest = array("type" => "sendFriendNotification", "" => "accepted",
        "content" => array("toId" => 1, "fromUsername" => "bobbert"));
    $missingActionKey = $eventHandler->requestActionParamsAreValid($missingActionRequest);
    $missingActionValueRequest = array("type" => "sendFriendNotification", "action" => "",
        "content" => array("toId" => 1, "fromUsername" => "bobbert"));
    $missingActionValue = $eventHandler->requestActionParamsAreValid($missingActionValueRequest);
    return ($missingActionKey || $missingActionValue) === false;
}

/*
Tests the requestContentIsValid function for returning false if 
the request does not have the key "content" with a non-empty values.
*/
function testRequestActionParamsAreValidForMissingContent() {
    $channelManager = new ChannelManager();
    $eventHandler = new SocketEventHandler($channelManager);
    $missingContentRequest = array("type" => "sendFriendNotification", "action" => "accepted",
        "" => array("toId" => 1, "fromUsername" => "bobbert"));
    $missingContentKey = $eventHandler->requestActionParamsAreValid($missingContentRequest);
    $missingContentValueRequest = array("type" => "sendFriendNotification", "action" => "accepted",
        "content" => array());
    $missingContentValue =  $eventHandler->requestActionParamsAreValid($missingContentValueRequest);
    return ($missingContentKey || $missingContentValue) === false;
}

/*
Tests the requestContentIsValid function for a request of type sendFriendNotification
*/
function testRequestActionParamsAreValidForSendFriendNotificationType() {
    $channelManager = new ChannelManager();
    $eventHandler = new SocketEventHandler($channelManager);
    $goodRequest = array("type" => "sendFriendNotification", "action" => "accepted",
        "content" => array("toId" => 1, "fromUsername" => "bobbert"));
    $goodRequestResult = $eventHandler->requestActionParamsAreValid($goodRequest);
    $missingFromUsernameKey = array("type" => "sendFriendNotification", "action" => "accepted",
        "content" => array("toId" => 1, "" => "bobbert"));
    $missingFromUsernameKeyResult = $eventHandler->requestActionParamsAreValid($missingFromUsernameKey);
    $missingFromUsernameValue = array("type" => "sendFriendNotification", "action" => "accepted",
        "content" => array("toId" => 1, "fromUsername" => ""));
    $missingFromUsernameValueResult = $eventHandler->requestActionParamsAreValid($missingFromUsernameValue);
    return $goodRequestResult && (($missingFromUsernameKeyResult || $missingFromUsernameValueResult) === false);
}

/*
Tests the requestContentIsValid function for a request of type sendRoomNotification
*/
function testRequestActionParamsAreValidForRoomType() {
    $channelManager = new ChannelManager();
    $eventHandler = new SocketEventHandler($channelManager);
    $goodRequest = array("type" => "sendRoomNotification", "action" => "left",
        "content" => array("roomId" => 1, "fromUsername" => "bobbert"));
    $goodRequestResult = $eventHandler->requestActionParamsAreValid($goodRequest);
    $missingRoomIdKey = array("type" => "sendRoomNotification", "action" => "deleted",
        "content" => array("" => 1, "fromUsername" => "bobbert"));
    $missingRoomIdKeyResult = $eventHandler->requestActionParamsAreValid($missingRoomIdKey);
    $missingRoomIdValue = array("type" => "sendRoomNotification", "action" => "deleted",
        "content" => array("roomId" => NULL, "fromUsername" => "bobbert"));
    $missingRoomIdValueResult = $eventHandler->requestActionParamsAreValid($missingRoomIdValue);
    $goodRoomInviteRequest = array("type" => "sendRoomNotification", "action" => "left",
        "content" => array("roomId" => 1, "fromUsername" => "bobbert", "toId" => 2, "roomName" => "testRoom"));
    $goodRoomInviteResult = $eventHandler->requestActionParamsAreValid($goodRoomInviteRequest);
    $badRoomInvite = array("type" => "sendRoomNotification", "action" => "inviteToRoom",
        "content" => array("roomId" => 1, "fromUsername" => "bobbert", "toId" => 2, "roomName" => ""));
    $badRoomInviteResult = $eventHandler->requestActionParamsAreValid($badRoomInvite);
    return ($goodRequestResult && $goodRoomInviteResult) && 
        (($missingRoomIdKeyResult || $missingRoomIdValueResult || $badRoomInviteResult) === false);
}

$tests = array("testSocketEventHandlerConstruct", 
    "testHasKeyWithNonEmptyValueForArrayWithEmptyKeyAndValue", "testHasKeyWithNonEmptyValueForArrayWithEmptyArray",
    "testHasKeyWithNonEmptyValueForArrayWithKeyAndValue", "testHasKeyWithNonEmptyValueForArrayWithKeyAndNoValue", 
    "testHasKeyWithNonEmptyValueForArrayWithKeyAndNullValue", "testHasKeyWithNonEmptyValueForArrayWithKeyAndEmptyStringValue",
    "testHasKeysWithNonEmptyValuesForTrue", "testHasKeysWithNonEmptyValuesForFalse", "testRequestActionParamsAreValidForMissingType",
    "testRequestActionParamsAreValidForMissingAction", "testRequestActionParamsAreValidForMissingContent",
    "testRequestActionParamsAreValidForSendFriendNotificationType", "testRequestActionParamsAreValidForRoomType");
Tester::echoTestResults($tests);
exit();