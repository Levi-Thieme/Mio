<?php 
    session_start();
    require_once("../db.php");
    if (isset($_POST["message"]) && isset($_POST["currentRoom"]) && isset($_POST["time"])) {
        //error_log("Message post variables are set\n". $_POST['message'] . " " . $_POST["currentRoom"] . " " . $_POST["time"] . "\n", 3, "error_log.txt");
        $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
        $userId = getUserId($conn, $_SESSION["username"]);
        $roomId = getRoomId($conn, $_POST["currentRoom"]);
        //error_log("user ID: $userId   room ID: $roomId\n", 3, "error_log.txt");
        $success = insertMessageWithRoom($conn, $userId, filter($conn, $_POST["message"]), $_POST["time"], $roomId);
        if ($success) {
            error_log("Successfully inserted: $content \n into Room: $roomId From: $userId\n", 3, "error_log.txt");
        }
        else {
            error_log("Failed to insert: $content \n into Room: $roomId From: $userId\n", 3, "error_log.txt");
        }
        $conn->close();
    }
    else {
        error_log("Message POST variables not set.\n", 3, "error_log.txt");
    }
?>