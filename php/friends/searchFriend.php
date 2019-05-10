<?php
session_start();
require_once("../database_interface/db.php");
require_once("show_list.php");

$conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");

if (isset($_POST["friendName"])) {
    $sql = sprintf("select name from user where name like '%s%%'", 
        mysqli_real_escape_string($conn, $_POST["friendName"]));
    show_list($sql, $conn);
}
$conn->close();
?>