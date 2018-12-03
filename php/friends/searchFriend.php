<?php
require_once("../db.php");
require_once("show_list.php");

session_start();

$conn;
if (!isset($_SESSION["connection"])) {
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
}
else {
    $conn = $_SESSION["connection"];
}


if (isset($_GET["friendName"])) {
    $sql = sprintf("select name from user where name like '%s%%'", 
        $conn->real_escape_string($_GET["friendName"]));
    show_list($sql, $conn);
}
?>