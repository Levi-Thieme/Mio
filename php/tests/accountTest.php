<?php
    include '../database_interface/db.php';
    
    /*
    Tests the isPassword function
    */
    function testIsPassword($conn) {
        $name = "test";
        $email = "test@gmail.com";
        $pass = "myPassword";
        if (!newUser($conn, $name, $email, $pass)) {
            p("Failed to insert new user");
            return false;
        }
        else {
            p("Inserted user $name");
        }
        if (isPassword($conn, $name, $pass)) {
            deleteUser($conn, $name, $pass);
            return true;
        }
        else {
            return false;
        }
    }
    
    
    /*
    Tests the delete user function
    */
    function testDeleteUser($conn) {
        $name = "Joe Delete";
        $email = "nomas@gmail.com";
        $pass = "joePass";
        if (!newUser($conn, $name, $email, $pass)) {
            p("Failed to insert new user");
            return false;
        }
        p("Inserted user $name.");
        if (!deleteUser($conn, $name, $pass)) {
            p("Failed to delete user: $name");
            return false;
        }
        p("Deleted user: $name");
        return true;
    }
    
    function testNoConfirm($conn) {
        $success = false;
        //Create and insert two users
        $sender = "Sender User";
        $senderEmail = "$sender@gmail.com";
        $senderPass = "$senderpassword";
        if (!newUser($conn, $sender, $email, $pass)) {
            p("Failed to insert new user");
            return false;
        }
        p("Inserted user $name.");
        
        $receiver = "Receiver User";
        $receiverEmail = "receiver@gmail.com";
        $receiverPass = "receiverpassword";
        if (!newUser($conn, $receiver, $receiverEmail, $receiverPass)) {
            p("Failed to insert new user");
            return false;
        }
        p("Inserted user $receiver.");
        
        if (createFriendRequestNoConfirm($conn, $sender, $receiver)) {
            p("Successfully added a friend.");
            $success = true;
        }
        else {
            p("Failed to add a friend.");
            p($conn->error);
            $success = false;
        }
        
        //Delete the users
        if (!deleteUser($conn, $sender, $senderPass)) {
            p("Failed to delete user: $sender");
            return false;
        }
        p("Deleted user: $sender");
        
        if (!deleteUser($conn, $receiver, $receiverPass)) {
            p("Failed to delete user: $receiver");
            return false;
        }
        p("Deleted user: $receiver");
        
        return $success;
    }
    
    
    function testGetFriends($conn) {
        $results = getFriends($conn, "Joe");
        foreach($results as $r) {
            echo getUsername($conn, implode($r)) . "<br>";
        }
        return true;
    }
    
    /*
    Not fully implemented yet...
    */
    function testGetRooms($conn) {
        $username = "";
        $rooms = getParticipantRooms($conn, $username);
        while(($room = $rooms->fetch_assoc())) {
            p($room['id'] . " " . $room['name']);
        }
        return false;
    }
    
    /*
    Print text in a div with a break element afterwards
    */
    function p($text) {
        echo("<div> $text </div>");
    }
    
    function printTestResult($function, $success) {
        if ($success) {
            echo("<div style='color: green'>$function passed!</div><br>");
        }
        else {
            echo("<div style='color: red'>$function failed! :(</div><br>");
        }
    }
    
    //Connect
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    if (!$conn) {
        p("Failed to connect.");
        exit;
    }
    
    //Create list of all test functions to run and call them
    $functionsToTest = array(testIsPassword, testDeleteUser, testNoConfirm, testGetFriends, testGetRooms);
    foreach ($functionsToTest as $function) {
        p("<div style='color: blue'>Running $function test...</div>");
        printTestResult($function, $function($conn));
    }
    exit;
?>