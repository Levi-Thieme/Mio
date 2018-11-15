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
    Filters sql input strings
    */
    function filter($sql) {
        return mysql_real_escape_string($sql);
    }
    
    /*
    Executes a query. Returns false if error, else returns results of query.
    The $sql parameter should be filtered before passing to this function.
    */
    function execQuery($sql, $conn) {
        $result = $conn->query($sql);
        if ($result === false) {
            error_log("Error: $sql \n" . $conn->error, 3, "error_log.txt");
        }
        return $result;
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
        $result = execQuery($sql, $conn);
        $users = array();
        while($user = $result->fetch_array(MYSQLI_NUM)) { // for each row of the resultset
            $users[] = $user; 
        }
        return $users;
    }
    
    /*
    Inserts a new user with name, email, and password
    */
    function newUser($conn, $name, $email, $password) {
        $name = filter($name);
        $email = filter($email);
        $password = filter($password);
        $sql = "INSERT INTO user (name, email, password)
                VALUES ('$name', '$email', '$password')";
        return execQuery($sql, $conn);
    }
    
    /*
    Returns true if password is associated with the username for an account
    */
    function isPassword($conn, $username, $pass) {
        $username = filter($username);
        $pass = filter($pass);
        $sql = "SELECT * from user WHERE name = '$username' AND password = '$pass'";
        $result = execQuery($sql, $conn);
        return $result->num_rows === 1;
    }
    
    /*
    Updates a user's email address.
    */
    function updateUserEmail($conn, $email, $name) {
        $email = filter($email);
        $name = filter($name);
        $sql = "UPDATE user SET email = '$email' WHERE name = '$name'";
        execQuery($sql, $conn);
    }
    
    /*
    Updates a user's password.
    */
    function updateUserPassword($conn, $name, $password, $newPassword) {
        $name = filter($name);
        $password = filter($password);
        $newPassword = filter($newPassword);
        $sql = "UPDATE user SET password = '$newPassword' 
            WHERE name = '$name' AND password = '$password'";
        execQuery($sql, $conn);
    }
    
    /*
    Deletes a user's account.
    */
    function deleteUser($conn, $name, $password) {
        $name = filter($name);
        $password = filter($password);
        $sql = "DELETE FROM user WHERE name = '$name' AND password = '$password'";
        return execQuery($sql, $conn);
    }
    
    /*
    Updates the user's profile image.
    */
    function updateUserProfileImage($conn, $image, $id) {
        $id = filter(strval($id));
        $sql = "UPDATE user SET image = $image WHERE id = '$id'";
        execQuery($sql, $conn);
    }
    
    /*
    Gets the user's email address
    */
    function getUserEmail($conn, $username) {
        $username = filter($username);
        $sql = "SELECT email FROM user WHERE name = '$username'";
        return execQuery($sql, $conn);
    }
    
?>