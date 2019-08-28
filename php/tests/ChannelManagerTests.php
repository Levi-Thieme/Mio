<?php

require_once("../manager_classes/ChannelManager.php");

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
 * Prints the results of the test.
 */
function printTestResult($function, $success) {
    if ($success) {
        echo("<div style='color: green'>$function passed!</div><br>");
    }
    else {
        echo("<div style='color: red'>$function failed! :(</div><br>");
    }
}

/*
 * Print text in a div with a break element afterwards
 */
function p($text) {
    echo("<div> $text </div>");
}

$functionsToTest = array("testAddChannel", "testGetChannel", "testAddClientToChannel", "testRemoveClientFromChannel");
foreach ($functionsToTest as $test) {
    p("<div style='color: blue'>Running $test test...</div>");
    printTestResult($test, $test());
}
exit;