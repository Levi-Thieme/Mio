<?php
require_once("../db.php");

$conn;
if (!isset($_SESSION["connection"])) {
    $conn = connect("127.0.0.1", "thielt01", "sharky21", "mio");
}
else {
    $conn = $_SESSION["connection"];
}

if (isset($_SESSION["username"]) && isset($_POST["receiver"])) {
    //createFriendRequest($conn, $_POST["username"], $_POST["receiver"]);
    createFriendRequestNoConfirm($conn, $_SESSION["username"], $_POST["receiver"]);
    error_log("Created friend request.\n", 3, "error_log.txt");
}
else {
    error_log("Failed to create friend request.\n" . $conn->error, 3, "error_log.txt");
    return false;
}
return true;
?>