<?php 
    require_once("../db.php");
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    if ($conn && isset($_POST["roomId"]) && !empty($_POST["roomId"])) {
        $roomId = $_POST["roomId"];
        $messages = getRoomMessages($conn, $roomId);
        if ($messages) {
            while ($message = $messages->fetch_assoc()){
                // id = \'17\' 
                $userId = str_replace("\\", "", $message["user_id"]);
                $username = getUsername($conn, $userId);
                error_log("User id: " . $userId . "\n", 3, "error_log.txt");
                echo $message["content"]."//".$message["user_id"]."//".$message["time"]."//".$message["id"]."//".$username.">>>";
            }
        }
    }
    $conn->close();
?>