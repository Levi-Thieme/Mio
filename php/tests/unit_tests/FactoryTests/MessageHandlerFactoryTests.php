<?php
require_once("../../../handlers/MessageHandlerFactory.php");
require_once("../../Tester.php");

function testConstructChildInitData() {
    $initData = MessageHandlerFactory::ConstructChildInitData("subtype", "join", function() { return true; });
    return $initData["typeKey"] == "subtype" && $initData["type"] === "join" && $initData["onHandle"]();
}
$tests = array("testConstructChildInitData");
Tester::echoTestResults($tests);