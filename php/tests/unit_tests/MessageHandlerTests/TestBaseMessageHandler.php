<?php
require_once("../../../handlers/BaseMessageHandler.php");
require_once("../../Tester.php");

function testCanHandleTrue() {
    $handler = new BaseMessageHandler("type", "testType");
    $message = array("type" => "testType");
    return $handler->canHandle($message) === true;
}

function testCanHandleFalse() {
    $handler = new BaseMessageHandler("type", "testType");
    $message = array("type" => "badType");
    return $handler->canHandle($message) === false;
}

function testNextHandleTrue() {
    $handler = new BaseMessageHandler("type", "firstType");
    $secondHandler = new BaseMessageHandler("type", "secondType");
    $handler->setNext($secondHandler);
    $message = array("type" => "secondType");
    return $handler->handle($message);
}

function testNextHandleFalse() {
    $handler = new BaseMessageHandler("type", "firstType");
    $secondHandler = new BaseMessageHandler("type", "secondType");
    $handler->setNext($secondHandler);
    $message = array("type" => "nonexistantType");
    return $handler->handle($message) === false;
}
$tests = array("testCanHandleTrue", "testCanHandleFalse", "testNextHandleTrue", "testNextHandleFalse");
Tester::echoTestResults($tests);
exit();