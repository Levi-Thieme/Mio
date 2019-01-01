<?php
    require_once("../db.php");
    
    session_start();
    
    $conn = connect("localhost", "mio_db", "pfw", "mio_db");
    $rooms = getParticipantOrOwnerRooms($conn, $_SESSION["username"]);
    
    while ($room = $rooms->fetch_assoc()){
        $roomName = htmlspecialchars($room["name"]);
        echo("<div class='panel-body'><a data-to-room style='color: #e68a00;'>$roomName</a>" .
        "<i data-leave-room class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
        "<i data-add-to-room class='fa fa-plus fa-fw' aria-hidden='true'></i></div>");
    }
    $conn->close();
?>