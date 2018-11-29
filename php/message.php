<?php 
require_once("../php/db.php");
     $conn = connect("127.0.0.1", "odonap01", "Zarchex1", "Mio");
        $time = $_POST['currentDate'];
      $sql = "select* from message where time>'$time'";
   
      $result = $conn->query($sql) or die(mysqli_error($conn)); 
     while( $message = $result->fetch_assoc()){
        echo $message["content"]."/".$message["user_id"].",";
        
     }
      
   
  ?>