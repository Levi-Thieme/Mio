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
    function filter($connection, $sql) {
        return mysqli_real_escape_string($connection, $sql);
    }
    
    /*
    Executes a query. Returns false if error, else returns results of query.
    The $sql parameter should be filtered before passing to this function.
    */
    function execQuery($sql, $conn) {
        $result = $conn->query($sql);
        if ($result === false) {
            error_log("Error: $sql \n" . $conn->error . "\n", 3, "error_log.txt");
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
        $name = filter($conn, $name);
        $email = filter($conn, $email);
        $password = filter($conn, $password);
        $sql = "INSERT INTO user (name, email, password)
                VALUES ('$name', '$email', '$password')";
        return execQuery($sql, $conn);
    }
    
    /*
    Returns true if password is associated with the username for an account
    */
    function isPassword($conn, $username, $pass) {
        $username = filter($conn, $username);
        $pass = filter($conn, $pass);
        $sql = "SELECT * from user WHERE name = '$username' AND password = '$pass'";
        $result = execQuery($sql, $conn);
        return $result->num_rows === 1;
    }
    
    /*
    Updates a user's email address.
    */
    function updateUserEmail($conn, $email, $name) {
        $email = filter($conn, $email);
        $name = filter($conn, $name);
        $sql = "UPDATE user SET email = '$email' WHERE name = '$name'";
        return execQuery($sql, $conn);
    }
    
    /*
    Updates a user's password.
    */
    function updateUserPassword($conn, $name, $password, $newPassword) {
        $name = filter($conn, $name);
        $password = filter($conn, $password);
        $newPassword = filter($conn, $newPassword);
        $sql = "UPDATE user SET password = '$newPassword' 
            WHERE name = '$name' AND password = '$password'";
        return execQuery($sql, $conn);
    }
    
    /*
    Deletes a user's account.
    */
    function deleteUser($conn, $name, $password) {
        $name = filter($conn, $name);
        $password = filter($conn, $password);
        $sql = "DELETE FROM user WHERE name = '$name' AND password = '$password'";
        return execQuery($sql, $conn);
    }
    
    /*
    Updates the user's profile image.
    */
    function updateUserProfileImage($conn, $image, $id) {
        $id = filter($conn, strval($id));
        $sql = "UPDATE user SET image = $image WHERE id = '$id'";
        return execQuery($sql, $conn);
    }
    
    /*
    Gets the user's email address
    */
    function getUserEmail($conn, $username) {
        $username = filter($conn, $username);
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
        $sql = "SELECT name FROM user WHERE id = $id";
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
        $sql = "SELECT to_id FROM friends WHERE from_id = $userId AND pending = false UNION DISTINCT " .
            "SELECT from_id FROM friends WHERE to_id = $userId AND pending = false";
        return execQuery($sql, $conn);
    }
    
    
    /*
    Gets all pending requests sent from the user
    
    $username -  the username of user who is a sender of requests
    */
    function getRequestsFromUser($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT to_id FROM friends WHERE from_id = $userId AND pending";
        return execQuery($sql, $conn);
    }
    
    /*
    Gets all pending request sent to this user
    
    $username - the username of user who is a recipient of requests
    */
    function getRequestsToUser($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT from_id FROM friends WHERE to_id = $userId AND pending";
        return execQuery($sql, $conn);
    }
    
    /*
    Creates a friend request from user to recipient
    
    $requester - username of the user requesting the friend request
    $recipient - username of the recipient
    */
    function createFriendRequest($conn, $requester, $recipient) {
        $requesterId = getUserId($conn, $requester);
        $recipientId = getUserId($conn, $recipient);
        $sql = "INSERT INTO friends(from_id, to_id) VALUES($requesterId, $recipientId)";
        return execQuery($sql, $conn);
    }
    
    /*
    Creates a friend request from user to recipient, but makes it accepted by default
    
    $requester - username of the user requesting the friend request
    $recipient - username of the recipient
    */
    function createFriendRequestNoConfirm($conn, $requester, $recipient) {
        $requesterId = getUserId($conn, $requester);
        $recipientId = getUserId($conn, $recipient);
        $sql = "INSERT INTO friends(from_id, to_id, pending) VALUES($requesterId, $recipientId, false)";
        return execQuery($sql, $conn);
    }
    
    /*
    Accepts a friend request
    
    $acceptor - username of the person accepting the request
    $requester - username of the person who sent the request
    */
    function acceptFriendRequest($conn, $acceptor, $requester) {
        $acceptorId = getUserId($conn, $acceptor);
        $requestorId = getUserId($conn, $requester);
        $sql = "UPDATE friends SET pending = false WHERE from_id = $requestorId AND to_id = $acceptorId";
        return execQuery($sql, $conn);
    }
    
    
    /*
    Deletes a friend from the from_user
    */
    function deleteFriend($conn, $from_user, $to_user) {
        $from_id = getUserId($conn, $from_user);
        $to_id = getUserId($conn, $to_user);
        $sql = "DELETE FROM friends WHERE (from_id = $from_id AND to_id = $to_id) OR".
            " (from_id = $to_id AND to_id = $from_id)";
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
    
    function getRoomName($conn, $id) {
        $sql = "SELECT name FROM room WHERE id = $id";
        return execQuery($sql, $conn)[0];
    }
    
    /*
    Deletes a room
    
    $ownerName - the username of the room's owner
    $roomName - the name of the room
    */
    function deleteRoom($conn, $ownerId, $roomName) {
        $sql = "DELETE FROM room WHERE user_id = $ownerId AND name = '$roomName'";
        return execQuery($sql, $conn);
    }
    
    /*
    Deletes a room with the given room id
    
    $roomId - id of the room to delete
    */
    function deleteRoomById($conn, $roomId) {
        $sql = "DELETE FROM room WHERE id = $roomId";
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
        $roomId = getRoomId($conn, $roomName);
        $memberId = getUserId($conn, $memberName);
        $sql = "INSERT INTO room_member(room, usr) VALUES($roomId, $memberId)";
        error_log($sql . "\n", 3, "error_log.txt");
        return execQuery($sql, $conn);
    }
        
    /*
    Checks if the username is the owner of room
    
    $username - id of the user
    $roomName - name of the room
    */
    function isRoomOwner($conn, $userId, $roomId) {
        $sql = "SELECT COUNT(*) FROM room WHERE id = $roomId AND user_id = $userId";
        $result = execQuery($sql, $conn)->fetch_array();
        return ($result[0] > 0);
    }
    
    /*
    Removes a member from a room
    
    $username - the username of the user to remove
    $roomName - the name of the room
    */
    function removeMember($conn, $username, $roomName) {
        $userId = getUserId($conn, $username);
        $roomId = getRoomId($conn, $roomName);
        $sql = "DELETE FROM room_member WHERE room = $roomId AND usr = $userId";
        $success = execQuery($sql, $conn);
        return $success;
    }
    
    /*
    Removes all members from a given room.
    
    roomId - the id from which to remove all members.
    */
    function removeAllMembers($conn, $roomId) {
        $sql = "DELETE FROM room_member WHERE room = $roomId";
        return execQuery($sql, $conn);
    }
    
    /*
    Gets all rooms where user is the owner.
    
    $usernam - the username of the owner for which to retrieve rooms.
    */
    function getOwnedRooms($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM room WHERE user_id = $userId";
        return execQuery($sql, $conn);
    }
    
    
    /*
    Gets all rooms where user is a participant, but not the owner
    
    $username - the username of the participant
    */
    function getParticipantRooms($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM room r WHERE r.id IN (SELECT rm.room FROM room_member rm where usr = $userId)";
        return execQuery($sql, $conn);
    }
    
    /*
    Gets the first room where the user is a participant
    
    $username - the username of the participant
    */
    function getParticipantFirstRoom($conn, $userId) {
        $sql = "SELECT room FROM room_member WHERE usr=" . $userId . " LIMIT 1;";
        return execQuery($sql, $conn);
    }
    
    /*
    Gets all rooms where user is a participant or owner.
    
    $username - the username of the owner or participant
    */
    function getParticipantOrOwnerRooms($conn, $username) {
        $userId = getUserId($conn, $username);
        $sql = "SELECT * FROM room r where r.id IN" .
            "(SELECT rm.room FROM room_member rm where usr = $userId) OR r.user_id = $userId";
        return execQuery($sql, $conn);
    }
    
    
    /*---------------------------------------
    Functions handling messages
    ----------------------------------------*/
    
    /*
    Inserts a message
    
    $userId - id of sender
    $content - the content of the message
    $time - time the message was sent
    */
    function insertMessage($conn, $userId, $content, $time) {
        $sql = "INSERT INTO message (user_id, content, time) VALUES ($userId , '$content', '$time')";
        return execQuery($conn, $sql);
    }
    
    /*
    Inserts a message into a room.
    
    $roomId - id of the room
    $usrId - id of the sender
    */
    function insertRoomMessage($conn, $roomId, $userId) {
        $sql = "INSERT INTO room_message (room_id, message_id, user_id) VALUES ($roomId, LAST_INSERT_ID(), $userId)";
        return execQuery($conn, $sql);
    }
    
    /*
    Gets all messages for a given room
    
    $roomId - the id of the room to retrieve messages from
    */
    function getRoomMessages($conn, $roomId) {
        $sql = "SELECT * FROM message WHERE id IN (SELECT message_id FROM room_message WHERE room_id = $roomId)";
        return execQuery($sql, $conn);
    }
?>