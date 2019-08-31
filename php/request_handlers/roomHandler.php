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