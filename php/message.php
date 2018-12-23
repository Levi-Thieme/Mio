<?php 
    require_once("../php/db.php");
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    
    $roomId = $_POST["roomId"];
    $results = getRoomMessages($conn, $roomId);
    
    foreach($results as $message) {
        echo $message["content"]."/".$message["user_id"]."/".$message["time"]."/".$message["id"].",";
    }
    $conn->close();
?>