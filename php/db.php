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
    
    
    /*-----------------------------
    Functions for the user table.
    ------------------------------*/
    
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
        return execQuery($sql, $conn);
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
        return execQuery($sql, $conn);
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
        return execQuery($sql, $conn);
    }
    
    /*
    Gets the user's email address
    */
    function getUserEmail($conn, $username) {
        $username = filter($username);
        $sql = "SELECT email FROM user WHERE name = '$username'";
        return execQuery($sql, $conn);
    }
    
    /*
    Gets the id associated with the username
    */
    function getUserId($conn, $username) {
        $sql = "SELECT id FROM user WHERE name = '$username'";
        return execQuery($sql, $conn)->fetch_assoc()["id"];
    }
    
    /*
    Gets the username associated with the id
    */
    function getUsername($conn, $id) {
        $sql = "SELECT name FROM user WHERE id = '$id'";
        return execQuery($sql, $conn)->fetch_assoc()["name"];
    }
    
    /*-----------------------------
    Functions for the friends table.
    ------------------------------*/
    
    /*
    Gets the names of all friends for a user
    
    $username - username of the user for which to query all current friends    
    */
    function getFriends($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM friends WHERE from_id = $userId";
        $friendRecords = execQuery($sql, $conn)->fetch_all(MYSQLI_ASSOC);
        //error_log($friendRecords, 3, "./error_log.txt");
        $friends = array();
        foreach($friendRecords as $friend) {
            $name = getUsername($conn, $friend["to_id"]);
            $friends[] = $name;
        }
        sort($friends);
        return $friends;
    }
    
    /*
    Gets all friend requests for the user
    
    $username - username of the user for which to query friend requests where user is the recipient.
    */
    function getFriendRequests($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM friends WHERE to_id = $userId AND pending";
        return execQuery($sql, $conn)->fetch_assoc();
    }
    
    /*
    Gets all pending requests sent from the user
    
    $username - username of the user for which to query friend requests where user is the sender.
    */
    function getPendingFriendRequests($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM friends WHERE from_id = $userId AND pending";
        return execQuery($sql, $conn)->fetch_assoc();
    }
    
    /*
    Creates a friend request from user to recipient
    
    $requester - username of the user requesting the friend request
    $recipient - username of the recipient
    */
    function createFriendRequest($conn, $requester, $recipient) {
        $requesterId = getUserId($conn, $requester);
        $recipientId = getUserId($conn, $recipient);
        $sql = "INSERT INTO friends('from_id', 'to_id') VALUES($requesterId, $recipientId)";
        return execQuery($sql, $conn);
    }
    
    /*
    Accepts a friend request
    
    $acceptor - username of the person accepting the request
    $requester - username of the person who sent the request
    */
    function acceptFriendRequest($conn, $acceptor, $requester) {
        $acceptorId = getUserId($conn, $acceptor);
        $requestorId = getUserId($conn, $requestor);
        $sql = "UPDATE TABLE friends SET pending = false WHERE from_id = $requestorId AND to_id = $acceptorId";
        return execQuery($sql, $conn);
    }
    
    
    
    
    
    
    /*-------------------------------
    Functions for the room table.
    -------------------------------*/
    
    /*
    Retrieves the id associated with the room name
    
    $roomName - the name of the room
    */
    function getRoomId($conn, $roomName) {
        $sql = "SELECT id FROM room WHERE name = '$roomName'";
        return execQuery($sql, $conn)->fetch_assoc()["id"];
    }
    
    /*
    Deletes a room
    
    $ownerName - the username of the room's owner
    $roomName - the name of the room
    */
    function deleteRoom($conn, $ownerName, $roomName) {
        $ownerId = getUserId($conn, $ownerName);
        $sql = "DROP FROM room WHERE user_id = $ownerId AND name = '$roomName'";
        return execQuery($sql, $conn);
    }
    
    
    /*
    Creates a room
    
    $username - the owner of the room
    $roomName - the name of the room
    */
    function createRoom($conn, $username, $roomName) {
        $userId = getUserId($conn, $username);
        $sql = "INSERT INTO room(user_id, name) VALUES($userId, '$roomName')";
        return execQuery($sql, $conn);
    }
    
    /*
    Adds a member to a room
    
    $roomName - the name of the room to add a member to
    $memberName - the username of the member to add
    */
    function addRoomMember($conn, $roomName, $memberName) {
        $memberId = getUserId($conn, $memberName);
        $sql = "INSERT INTO room_member(room, usr) VALUES('$roomName', '$memberId')";
        return execQuery($sql, $conn);
    }
    
    /*
    Removes a member from a room
    
    $username - the username of the user to remove
    $roomName - the name of the room
    */
    function removeMember($conn, $username, $roomName) {
        $userId = getUserId($conn, $username);
        
        $sql = "DROP FROM room_member WHERE room = '$roomName' AND usr = $userId";
        return execQuery($sql, $conn);
    }
    
    /*
    Gets all rooms where user is the owner.
    
    $usernam - the username of the owner for which to retrieve rooms.
    */
    function getOwnedRooms($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM room WHERE user_id = $userId";
        return execQuery($sql, $conn)->fetch_assoc();
    }
    
    /*
    Gets all rooms where user is a participant, but not the owner
    
    $username - the username of the participant
    */
    function getParticipantRooms($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM room r WHERE r.id IN (SELECT rm.room FROM room_member rm where usr = $userId)";
        return execQuery($sql, $conn)->fetch_assoc();
    }
?>