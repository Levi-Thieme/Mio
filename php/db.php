<?php
    
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
        return $conn;
    }
    
    /*
    Executes a query.
    */
    function execQuery($sql, $conn) {
        if ($conn->query($sql) === TRUE) {
            error_log($sql . "  Executed successfully", 3, "error_log.txt");
        } else {
            error_log("Error: $sql \n" . $conn->error, 3, "error_log.txt");
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
    
    
    
    /* ------------------------------------------------------------
                Database functions for CRUD operations.
    
    ---------------------------------------------------------------*/
    
    /*
    Retrieves all users
    */
    function getUsers($conn) {
        $sql = "SELECT * FROM user";
        $result = $conn->query($sql);
        $users = array();
        while($user = $result->fetch_array(MYSQLI_NUM)) { // for each row of the resultset
            $users[] = $user; 
        }
        return $users;
    }
    
    /*
    Inserts a new user with the given name.
    */
    function newUser($conn, $name) {
        $sql = "INSERT INTO user (name)
                VALUES ('{$name}')";
        execQuery($sql, $conn);
    }
    
    /*
    Updates a user's email address.
    */
    function updateUserEmail($conn, $email, $name) {
        $sql = "UPDATE user SET email = '$email' where name = '$name'";
        execQuery($sql, $conn);
    }
    
    /*
    Updates a user's password.
    */
    function updateUserPassword($conn, $password, $id) {
        $sql = "UPDATE user SET password = '$password' where id = '$id'";
        execQuery($sql, $conn);
    }
    
    /*
    Deletes a user's account.
    */
    function deleteUserAccount($conn, $id) {
        $sql = "DELETE FROM user where id = '$id'";
        execQuery($sql, $conn);
    }
    
    
    /*
    Updates the user's profile image.
    */
    function updateUserProfileImage($conn, $image, $id) {
        $sql = "UPDATE user SET image = $image WHERE id = '$id'";
        execQuery($sql, $conn);
    }
    
?>