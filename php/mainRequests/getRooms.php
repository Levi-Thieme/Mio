<?php
    require_once("../db.php");
    
    session_start();
    
    $conn = connect("localhost", "mio_db", "pfw", "mio_db");
    $rooms = getParticipantRooms($conn, $_SESSION["username"]);
    
    while (($room = $rooms->fetch_assoc())){
        echo "<a onclick = \"document.getElementById('chat" . $room['id'] . "').submit(); return false;\"><div style='cursor:pointer;' class='panel-body'> " . $room['id'] . ": " . $room['name'] . "</div>";
        echo "<form id='chat" . $room['id'] . "' action=''> <input name='room_id' type='hidden' value='". $room['id'] . "'/></form>";
        echo "</a>\n";
    }
?>