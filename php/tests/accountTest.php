<?php
    include '../db.php';
    
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
    $conn = connect("127.0.0.1", "thielt01", "sharky21", "mio");
    if (!$conn) {
        p("Failed to connect.");
        exit;
    }
    
    //Create list of all test functions to run and call them
    $functionsToTest = array(testIsPassword, testDeleteUser);
    foreach ($functionsToTest as $function) {
        p("<div style='color: blue'>Running $function test...</div>");
        printTestResult($function, $function($conn));
    }
    exit;
?>