<?php
session_start();
require_once("../database_interface/db.php");

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
        $rooms = getParticipantOrOwnerRooms($conn, $userId);
        $roomsArray = array();
        while ($room = $rooms->fetch_assoc()) {
            array_push($roomsArray, $room);
        }
        echo json_encode($roomsArray);
        $conn->close();
    }
    else {
        error_log("User id is not set.\n", 3, "./error_log.txt");
        return false;
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

function removeRoom() {
    if (isset($_GET["channelId"]) && isset($_GET["clientId"])) {
        $userId = $_GET["clientId"];
        $roomId = $_GET["channelId"];
        $conn = connect();
        $isOwner = isRoomOwner($conn, $userId, $roomId);
        if ($isOwner) {
            if ($success = removeAllMembers($conn, $roomId)) {
                deleteRoomById($conn, $roomId);
            }
        }
        else {
            removeMember($conn, $userId, $roomId);
        }
        $conn->close();
    }
}