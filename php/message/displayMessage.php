<?php

require_once("../db.php");
      $conn = connect("localhost", "mio_db", "pfw", "mio_db");
      $userid = $_POST["id"];
      echo getUsername($conn,$userid);
?>