<?php
require_once("../chat_server/ChannelManager.php");
require_once("./Tester.php");
/*
 * Tests the addChannel method.
 */
function testAddChannel()
{
    $manager = new ChannelManager();
    $channelToAdd = "addedChannel";
    $manager->addChannel($channelToAdd);
    return $manager->getChannels()[array_search($channelToAdd, $manager->getChannels())];
}

/*
 * Tests the getChannel method
 */
function testGetChannel() {
    $manager = new ChannelManager();
    $channelName = "testChannel";
    $testChannel = new Channel("testChannel", array());
    $manager->setChannels(array($testChannel));
    return $manager->getChannel($channelName) === $testChannel;
}

/*
 * Tests the addClientToChannel method
 */
function testAddClientToChannel() {
    $manager = new ChannelManager();
    $manager->addChannel("testChannel");
    $client = new Client("testUser", NULL);
    $manager->addClientToChannel("testChannel", $client);
    $channel = $manager->getChannel("testChannel");
    return in_array($client, $channel->clients);
}

/*
 * Tests the removeClientFromChannel method
 */
function testRemoveClientFromChannel() {
    $manager = new ChannelManager();
    $manager->addChannel("testChannel");
    $client = new Client("testUser", NULL);
    $manager->addClientToChannel("testChannel", $client);
    $manager->removeClientFromChannel("testChannel", $client);
    $channel = $manager->getChannel("testChannel");
    return in_array($client, $channel->clients) == false;
}

/*
 * Tests the getUsersChannel method
 */
function testGetClientFromChannels() {
    $manager = new ChannelManager();
    $manager->addChannel("isClientChannel");
    $manager->addChannel("notClientChannel");
    $client = new Client("Gauss", NULL);
    $manager->addClientToChannel("isClientChannel", $client);
    $client = $manager->getClientFromChannels("Gauss");
    return $client != NULL && $client->username === "Gauss";
}

$testsToRun = array("testAddChannel", "testGetChannel", "testAddClientToChannel", "testRemoveClientFromChannel",
    "testGetClientFromChannels");
Tester::runTests($testsToRun);
exit;