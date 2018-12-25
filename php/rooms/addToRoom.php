<?php
    session_start();
    require_once("../db.php");
    
    if (isset($_SESSION["username"]) && isset($_POST["userToAdd"]) && isset($_POST["roomName"])) {
        $conn = connect('127.0.0.1', 'mio_db', 'pfw', 'mio_db');
        $success = addRoomMember($conn, $_POST["roomName"], $_POST["userToAdd"]);
        $conn->close();
    }
?>