<?php
    $conn = null;
    
    /*
    Attempts to connect to the server specified by parameters.
    */
    function connect($servername, $username, $password, $dbName) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbName = "mio";
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbName);
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error . "\n");
        } 
        echo "Connected successfully\n";
        return $conn;
    }
    
    /*
    Executes a query.
    */
    function execQuery($sql, $conn) {
        if ($conn->query($sql) === TRUE) {
            log($sql . "  Executed successfully");
        } else {
            log("Error: " . $sql . "<br>" . $conn->error);
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
    $conn = connect();
    //Show databases
    showDatabases($conn);
    //Insert user
    newUser($conn, "Joe the Great");
    //Close the connection
    $conn->close();
    
    /* ------------------------------------------------------------
                Database functions for CRUD operations.
    
    ---------------------------------------------------------------*/
    
    /*
    Inserts a new user with the given name.
    */
    function newUser($conn, $name) {
        $sql = "INSERT INTO user (name)
                VALUES ({$name})";
        execQuery($sql, $conn);
    }
    
    /*
    Updates a user's email address.
    */
    function updateUserEmail($email, $id) {
        $sql = "UPDATE user SET email = {$email} where id = {$id}";
        if ($conn == null) {
            $conn = connect();
        }
        execQuery($sql, $conn);
    }
    
    /*
    Updates a user's password.
    */
    function updateUserPassword($password, $id) {
        $sql = "UPDATE user SET password = {$password} where id = {$id}";
        if ($conn == null) {
            $conn = connect();
        }
        execQuery($sql, $conn);
    }
    
    /*
    Deletes a user's account.
    */
    function deleteUserAccount($id) {
        $sql = "DELETE FROM user where id = {$id}";
        if ($conn == null) {
            $conn = connect();
        }
        execQuery($sql, $conn);
    }
    
    
    /*
    Updates the user's profile image.
    */
    function updateUserProfileImage($image, $id) {
        $sql = "UPDATE user SET image = {$image} WHERE id = {$id}";
    }
    
?>