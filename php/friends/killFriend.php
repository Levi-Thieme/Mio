<?php
    include_once("../db.php");
    
    session_start();
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    $user_from = $_SESSION["username"];
    $user_to = $_POST["friend"];
    $success = false;
    if (deleteFriend($conn, $user_from, $user_to)) {
        $success = true;
    }
    else {
        error_log($conn->error, 3, "error_log.txt");
        $success = false;
    }
    $conn->close();
    return $success;
?>