<?php
//Tests for the Client class.
require_once("../../chat_server/Client.php");
require_once("../Tester.php");

function testClientConstruct() {
    $id = 1;
    $username = "Bob";
    $socket = "socketResource";
    $client = new Client($id, $username, $socket);
    return $client->getId() === $id &&
            $client->getUsername() === $username &&
            $client->getSocket() === $socket;
}

function testClientGetId() {
    $client = new Client(1, "", "");
    return $client->getId() === 1;
}

function testClientSetId() {
    $client = new Client(1, "", "");
    $client->setId(2);
    return $client->getId() === 2;
}


$testsToRun = array("testClientConstruct", "testClientGetId" , "testClientSetId");
Tester::runTests($testsToRun);
exit();