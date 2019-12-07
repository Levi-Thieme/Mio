<?php
$root = dirname(__FILE__);
require_once($root . DIRECTORY_SEPARATOR . "../../../chat_server/Channel.php");
require_once($root . DIRECTORY_SEPARATOR . "../../../chat_server/Client.php");
require_once($root . DIRECTORY_SEPARATOR . "../../Tester.php");
//Tests for the Channel.php class

function testConstruct() {
    $clients = array();
    $channel = new Channel(1, "testName", $clients);
    return $channel->getId() === 1
        && $channel->getName() === "testName"
        && $channel->getClients() === $clients;
}

function testAddClient() {
    $clients = array();
    $channel = new Channel(1, "testName", $clients);
    $client = new Client(1, "Bobbert", "socket");
    $channel->addClient($client);
    return $channel->getClients()[0] === $client;
}

function testAddReplaceClient() {
    $client = new Client(1, "Jimbob", "socket1");
    $sameClient = new Client(1, "JimbobCopy", "socket2");
    $channel = new Channel(2, "a chat room", array());
    $channel->addClient($client);
    $channel->addReplaceClient($sameClient);
    $retrievedClient = $channel->getClientById(1);
    return $retrievedClient->getSocket() === $sameClient->getSocket()
        && $retrievedClient->getUsername() === $sameClient->getUsername();
}

function testRemoveClient() {
    $client = new Client(1, "Bobbert", "socket");
    $clients = array($client);
    $channel = new Channel(1, "testName", $clients);
    $channel->removeClient($client);
    return count($channel->getClients()) === 0;
}

function testRemoveClientById() {
    $client = new Client(1, "Bobbert", "socket");
    $clients = array($client);
    $channel = new Channel(1, "testName", $clients);
    $channel->removeClientById(1);
    return count($channel->getClients()) === 0;
}

function testGetClientById() {
    $client = new Client(1, "Bobbert", "socket");
    $clients = array($client);
    $channel = new Channel(1, "testName", $clients);
    return $channel->getClientById(1) === $client;
}

function testGetClientByUsername() {
    $username = "Bobbert";
    $client = new Client(1, $username, "socket");
    $clients = array($client);
    $channel = new Channel(1, "channel", $clients);
    return $channel->getClientByUsername($username) === $client;
}

$tests = array("testConstruct", "testAddClient", "testAddReplaceClient", "testRemoveClient",
    "testRemoveClientById", "testGetClientById", "testGetClientByUsername");
$results = array();
foreach ($tests as $test) {
$results[$test] = $test();
}
echo json_encode($results, JSON_PRETTY_PRINT);
exit();