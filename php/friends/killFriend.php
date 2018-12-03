<?php
    include_once("../db.php");
    
    session_start();
    $conn;
    if (!$_SESSION["connection"]) {
        $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    }
    else {
        $conn = $_SESSION["connection"];
    }
    
    $user_from = $_SESSION["username"];
    $user_to = $_POST["friend"];
    if (!deleteFriend($conn, $user_from, $user_to)) {
        error_log($conn->error, 3, "error_log.txt");
        return false;
    }
    return true;
?>