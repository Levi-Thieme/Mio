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