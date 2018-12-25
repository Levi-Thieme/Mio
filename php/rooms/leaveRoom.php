<?php
session_start();
require_once("../db.php");

if (isset($_SESSION["username"]) && isset($_POST["roomName"])) {
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    $success = false;
    $roomName = $_POST["roomName"];
    $userId = getUserId($conn, $_SESSION["username"]);
    $roomId = getRoomId($conn, $roomName);
    $isOwner = isRoomOwner($conn, $userId, $roomId);
    if ($isOwner) {
        if ($success = removeAllMembers($conn, $roomId)) {
            $success = deleteRoomById($conn, $roomId);
        }
    }
    else {
        $success = removeMember($conn, $_SESSION["username"], $roomName);
    }
    $conn->close();
}
?>