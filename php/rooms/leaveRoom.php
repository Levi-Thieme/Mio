<?php
session_start();
require_once("../database_interface/db.php");

if (isset($_SESSION["username"]) && isset($_POST["roomName"])) {
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    $success = false;
    $roomName = $_POST["roomName"];

}
?>