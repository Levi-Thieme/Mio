<?php
session_start();
include_once("../db.php");

if (isset($_POST["requester"]) && isset($_SESSION["username"])) {
    $requester = $_POST["requester"];
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    acceptFriendRequest($conn, $_SESSION["username"], $requester);
    $conn->close();
}
?>