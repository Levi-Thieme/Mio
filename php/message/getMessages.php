<?php 
    require_once("../db.php");
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    
    if ($conn && isset($_POST["currentRoom"]) && !empty($_POST["currentRoom"]) && isset($_POST["messageCount"])) {
        $roomName = $_POST["currentRoom"];
        $limit = getRoomMessageCount($conn, $roomName) - $_POST["messageCount"];
        if ($limit > 0) {
            $messages = getNewMessages($conn, $roomName, $limit);
            
            if ($messages->num_rows > 0) {
                $messageDataArray = array();
                while ($message = $messages->fetch_assoc()){
                    $userId = str_replace("\\", "", $message["user_id"]);
                    $username = getUsername($conn, $userId);
                    $messageData = array("userId"=>$userId, "username"=>$username, "messageId"=>$message["id"], "content"=>$message["content"], "time"=>$message["time"]);
                    $messageDataArray[] = $messageData;
                }
                echo json_encode($messageDataArray, JSON_PRETTY_PRINT);
            }
        }
    }
    $conn->close();
?>