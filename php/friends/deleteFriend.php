<?php
    require_once("../database_interface/db.php");
    session_start();
    $success = false;
    if (isset($_SESSION["username"]) && isset($_POST["friend"])) {
        $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
        $user_from = $_SESSION["username"];
        $user_to = $_POST["friend"];
        $success = deleteFriend($conn, $user_from, $user_to);
        if ($success) {
            error_log("Succesfully delete $user_to\n", 3, "error_log.txt");
        }
        else {
            error_log($conn->error . "\nFailed to delete user: $user_from\n", 3, "error_log.txt");
        }
        $conn->close();
    }
    else {
        error_log($conn->error . "\n", 3, "error_log.txt");
        $success = false;
    }
    return $success;
?>