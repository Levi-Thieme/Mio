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

if (isset($_SESSION["username"])) {

$friends = getFriends($conn, $_SESSION["username"]);
    if (empty($friends)) {
        return "";
    }
    foreach ($friends as $friend) {
        echo "<a class='list-group-item' href=''>" . getUsername($conn, implode($friend)) . "<i class='fa fa-comment fa-fw'
        style='float:right' aria-hidden='true'></i>&nbsp;</a>";
    }
}
?>