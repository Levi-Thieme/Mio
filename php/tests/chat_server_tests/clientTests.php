<?php
    //Tests for the Client class.
    require_once("../../chat_server/Client.php");
    $tests = array("testClientConstruct", "testClientGetId" , "testClientSetId");

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

    $results = array();
    foreach ($tests as $test) {
        $results[$test] = $test();
    }
    echo json_encode($results, JSON_PRETTY_PRINT);
    exit();