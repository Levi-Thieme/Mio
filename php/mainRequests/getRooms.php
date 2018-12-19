<script>
    function leaveRoom(roomName) {
        console.log(roomName);
    }
</script>
<?php
    require_once("../db.php");
    
    session_start();
    
    $conn = connect("localhost", "mio_db", "pfw", "mio_db");
    $rooms = getParticipantRooms($conn, $_SESSION["username"]);
    
    while ($room = $rooms->fetch_assoc()){
        $roomName = $room["name"];
        echo("<div class='panel-body'> " . $roomName . 
        "<i onclick='leaveRoom($roomName)' class='fa fa-trash fa-fw' style='float:right' aria-hidden='true'></i>" .
        "<i onclick='openAddToRoomSlider()' class='fa fa-plus fa-fw' style='float:right' aria-hidden='true'></i></div>");
    }
    $conn->close();
?>