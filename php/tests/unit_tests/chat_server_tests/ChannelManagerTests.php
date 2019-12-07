<?php
//Tests for the ChannelManager class.
$root = dirname(__FILE__);
require_once($root . DIRECTORY_SEPARATOR .  "../../../chat_server/ChannelManager.php");
require_once($root . DIRECTORY_SEPARATOR .  "../../Tester.php");

/*
* Tests the getChannel method
*/
function testGetChannel() {
    $manager = new ChannelManager();
    $id = 1;
    $channelName = "testChannel";
    $testChannel = new Channel($id, "testChannel", array());
    $manager->setChannels(array($testChannel));
    return $manager->getChannel($id) === $testChannel;
}

/*
* Tests the addChannel method.
*/
function testAddChannel() {
    $manager = new ChannelManager();
    $id = 1;
    $name = "addedChannel";
    $manager->addChannel($id, $name);
    $channels = $manager->getChannels();
    return $channels[0]->getId() === $id;
}

/*
 * Tests the containsChannel method
 */
function testContainsChannel() {
    $manager = new ChannelManager();
    $id = 1;
    $name = "addedChannel";
    $manager->addChannel($id, $name);
    return $manager->containsChannel(1);
}

/*
* Tests the addClientToChannel method
*/
function testAddClientToChannel() {
    $manager = new ChannelManager();
    $manager->addChannel(1, "testChannel");
    $client = new Client(1, "testUser", NULL);
    $manager->addClientToChannel(1, $client);
    $channel = $manager->getChannel(1);
    return in_array($client, $channel->getClients());
}



/*
* Tests the removeClientFromChannel method
*/
function testRemoveClientFromChannel() {
    $manager = new ChannelManager();
    $channelId = 1;
    $clientId = 2;
    $manager->addChannel($channelId, "testChannel");
    $client = new Client($clientId, "testUser", NULL);
    $manager->addClientToChannel($channelId, $client);
    $manager->removeClientFromChannel($channelId, $clientId);
    $channel = $manager->getChannel($channelId);
    return in_array($client, $channel->getClients()) == false;
}

/*
 * Tests the addNewClient method.
 */ 
function testAddNewClient() {
    $manager = new ChannelManager();
    $clientSocket = "socketResource";
    $clientInfo = array("clientId"=>1, "username"=>"Jimbob", "channelId"=>2, "channelName"=>"channel name");
    $manager->addNewClient($clientSocket, $clientInfo);
    $addedChannel = $manager->getChannels()[0];
    $addedClient = $addedChannel->getClients()[0];
    return $addedChannel->getId() === 2
        && $addedChannel->getName() === "channel name"
        && $addedClient->getId() === 1
        && $addedClient->getUsername() === "Jimbob"
        && $addedClient->getSocket() === $clientSocket;
}

/*
* Tests the getUsersChannel method
*/
function testGetClientFromChannels() {
    $manager = new ChannelManager();
    $manager->addChannel(1, "isClientChannel");
    $manager->addChannel(2, "notClientChannel");
    $client = new Client(1, "Gauss", NULL);
    $manager->addClientToChannel(1, $client);
    $client = $manager->getClientFromChannels(1);
    return $client != NULL && $client->getId() === 1 && $client->getUsername() === "Gauss";
}

$tests = array("testAddChannel", "testContainsChannel", "testGetChannel", "testAddClientToChannel", "testRemoveClientFromChannel",
    "testAddNewClient", "testGetClientFromChannels");
$results = array();
foreach ($tests as $test) {
    $results[$test] = $test();
}
echo json_encode($results, JSON_PRETTY_PRINT);
exit();