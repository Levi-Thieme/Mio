<?php 
require_once("../db.php");
      $conn = connect("localhost", "mio_db", "pfw", "mio_db");
      $roomId = $_POST["roomId"];
      $sql = "select* from message where id IN(SELECT message_id from room_message where room_id = $roomId)";
      $result = $conn->query($sql) or die(mysqli_error($conn)); 
     while( $message = $result->fetch_assoc()){
        echo $message["content"]."//".$message["user_id"]."//".$message["time"]."//".$message["id"]."//".getUsername($conn,$message["user_id"]).">>>";
        
     }
      
   
  ?>