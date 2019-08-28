<?php
session_start();
require_once("../database_interface/db.php");
require_once("../Renderer.php");

/*
 * $_GET["request"] stores the function to be called.
 */

if (is_callable($_GET["request"])) {
    $_GET["request"]();
}

function getRooms() {
    $conn = connect();
    $rooms = getParticipantOrOwnerRooms($conn, $_SESSION["username"]);
    while ($room = $rooms->fetch_assoc()){
        $roomName = htmlspecialchars($room["name"]);
        $roomDiv = Renderer::createRoomDiv($roomName);
        echo($roomDiv);
    }
    $conn->close();
}

function createRoom() {
    if (isset($_GET["roomName"])) {
        $conn = connect();
        insertRoom($conn, $_SESSION['username'], $_GET['roomName']);
        $conn->close();
    }
}

function addToRoom() {
    if (isset($_GET["userToAdd"]) && isset($_GET["roomName"])) {
        $conn = connect();
        addRoomMember($conn, $_GET["roomName"], $_GET["userToAdd"]);
        $conn->close();
    }
}

function leaveRoom() {
    if (isset($_GET["roomName"])) {
        $conn = connect();
        $userId = getUserId($conn, $_SESSION["username"]);
        $roomId = getRoomId($conn, $_GET["roomName"]);
        $isOwner = isRoomOwner($conn, $userId, $roomId);
        if ($isOwner) {
            if ($success = removeAllMembers($conn, $roomId)) {
                deleteRoomById($conn, $roomId);
            }
        }
        else {
            removeMember($conn, $_SESSION["username"], $_GET["roomName"]);
        }
        $conn->close();
    }
}