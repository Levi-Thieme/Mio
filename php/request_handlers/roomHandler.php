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
    if (isset($_GET["userId"])) {
        $conn = connect();
        $userId = $_GET["userId"];
        $username = getUsername($conn, $userId);
        $rooms = getParticipantOrOwnerRooms($conn, $username);
        while ($room = $rooms->fetch_assoc()){
            $roomName = htmlspecialchars($room["name"]);
            $roomDiv = Renderer::createRoomDiv($room["id"], $roomName);
            echo($roomDiv);
        }
        $conn->close();
    }
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
    if (isset($_GET["roomId"]) && isset($_GET["userId"])) {
        $conn = connect();
        $isOwner = isRoomOwner($conn, $_GET["userId"], $_GET["roomId"]);
        if ($isOwner) {
            if ($success = removeAllMembers($conn, $_GET["roomId"])) {
                deleteRoomById($conn, $_GET["roomId"]);
            }
        }
        else {
            removeMember($conn, getUsername($conn, $_GET["userId"]), $_GET["roomName"]);
        }
        $conn->close();
    }
}