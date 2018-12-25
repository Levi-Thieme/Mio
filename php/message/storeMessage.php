<?php 
    session_start();
    require_once("../db.php");
    if (isset($_POST["message"]) && isset($_POST["currentRoom"])) {
        $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
        $userId = getUserId($conn, $_SESSION["username"]);
        $roomId = getRoomId($conn, $_POST["currentRoom"]);
        $content = htmlspecialchars($_POST["message"]);
        $content = filter($conn, $content);
        $success = insertMessageWithRoom($conn, $userId, $content, $roomId);
        $conn->close();
    }
    else {
        error_log("Message POST variables not set.\n", 3, "error_log.txt");
    }
?>