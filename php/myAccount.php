<?php

    include './db.php';

    $conn = null;
    
    /*
    Attempts to connect to the server specified by parameters.
    */
    function connect($servername, $username, $password, $dbName) {
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbName);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error . "\n");
        } 
        echo "Connected successfully \n";
        return $conn;
    }
    
    /*
    Executes a query.
    */
    function execQuery($sql, $conn) {
        if ($conn->query($sql) === TRUE) {
            echo($sql . "  Executed successfully");
        } else {
            echo("Error: " . $sql . "<br>" . $conn->error);
        }
    }
    
    /*
    Lists all available databases for the connection parameter.
    */
    function showDatabases($conn) {
        $sql = "SHOW DATABASES";
        $result = $conn->query($sql);
        if ($result === false) {
            throw new Exception("Could not execute query: " . $conn->error);
        }
        
        $db_names = array();
        while($row = $result->fetch_array(MYSQLI_NUM)) { // for each row of the resultset
            $db_names[] = $row[0]; // Add db name to $db_names array
        }
        
        echo "Database names: " . PHP_EOL . print_r($db_names, TRUE); // display array
    }
    
    //Connect
    $conn = connect("127.0.0.1", "thielt01", "", "mio");
    //Show databases
    showDatabases($conn);
    
    updateUserProfileImage("./img.jpg", 3);
    //Close the connection
    $conn->close();
?>