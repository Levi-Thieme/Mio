<?php
require_once("../database_interface/db.php");
session_start();

$conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
$success = false;
if (isset($_SESSION["username"]) && isset($_POST["receiver"])) {
    if (createFriendRequest($conn, $_SESSION["username"], $_POST["receiver"])) {
        error_log("Created friend request.\n" . $conn->error, 3, "error_log.txt");
        $success = true;
    }
    else {
        error_log("Failed to create friend request.\n" . $conn->error, 3, "error_log.txt");
        $success = false;
    }
}

$conn->close();
echo $success;
?>