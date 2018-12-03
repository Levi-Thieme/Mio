<?php
require_once("../db.php");
session_start();

$conn;
if (!isset($_SESSION["connection"])) {
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
}
else {
    $conn = $_SESSION["connection"];
}

if (isset($_SESSION["username"]) && isset($_GET["receiver"])) {
    if (createFriendRequestNoConfirm($conn, $_SESSION["username"], $_GET["receiver"])) {
        error_log("Created friend request.\n", 3, "error_log.txt");
    }
}
else {
    error_log("Failed to create friend request.\n" . $conn->error, 3, "error_log.txt");
    return false;
}
return true;
?>