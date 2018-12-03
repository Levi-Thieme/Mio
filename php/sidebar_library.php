<?php
    require_once("./db.php");
    
    function getRoomsAssociativeArrayFromUserId($userId){
        $ids = getRoomsFromUserId($userId);
        $roomNames = array();
        
        //generate the array by id=>roomName
        for($i = 0; $i < sizeof($ids); $i++) {
            $roomNames[$ids[$i]] = getRoomByRoomId($ids[$i]);
        }
        
        return $roomNames;
    }
    
    // Get every room associated with id from room member table.
    function getRoomsFromUserId($id) {
        if($id === NULL) {
            die("The parameter user id is NULL");
        }
        $conn = connectToDB();
        $sql = "SELECT room FROM room_member WHERE usr = '" . $id . " ' ";
        
        $rooms = array();// Create an associative array of rooms.
        
        /* If the query executes successfully, return
         * the result. Otherwise, exit the program.
        */
        $result = getResult($sql, $conn);
        while($userId = $result->fetch_row()) {
            $rooms[] = $userId[0];
        }
        return $rooms;
    }
    
    // select name from room where id = room_id
    // Get the name of a room by its id.
    function getRoomName($room_id) {
        $roomsAssoc = array();
        $roomsAssoc = array(1 => "userId", 2 => "roomId");
        for($i = 0; i < end($room_id);) {
            
        }
        return $roomsAssoc;
    }
    
    function getRoomByRoomId($room_id) {
        // get name from room where id = x
        $conn = connectToDB();
        $sql = "SELECT name FROM room WHERE id = '" . $room_id . " ' ";
        $result = execQuery($sql, $conn);
        return $result->fetch_assoc()["name"];
    }
    
    // Connect to a db using pre-existing credentials.
    function connectToDB() {
        $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
        return $conn;
    }
    
    // Execute the query and return the result
    // if successful.
    function getResult($sql, $conn) {
        if($sql === NULL) {
            die("SQL statement is NULL.");
        }
        
        // If not already connected, connect now.
        if($conn === NULL) {
            $conn = connectToDb();
        }
        $result = execQuery($sql, $conn);
        if($result === false) {
            die("Could not execute the query: '" . $result . " ' ");
        }
        
        return $result;
    }
?>