<?php 
    require_once("../db.php");
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    
    if ($conn && isset($_POST["currentRoom"]) && !empty($_POST["currentRoom"])) {
        $roomName = $_POST["currentRoom"];
        $messages = getMessagesByRoomName($conn, $roomName);
        
        if ($messages->num_rows > 0) {
            while ($message = $messages->fetch_assoc()){
                $userId = str_replace("\\", "", $message["user_id"]);
                $username = getUsername($conn, $userId);
                error_log("User id: " . $userId . "\n", 3, "error_log.txt");
                echo $message["content"]."//".$message["user_id"]."//".$message["time"]."//".$message["id"]."//".$username.">>>";
            }
        }
    }
    $conn->close();
?>