<?php
include_once("../db.php");

$conn;
if (!$_SESSION["connection"]) {
    $conn = connect("127.0.0.1", "thielt01", "sharky21", "mio");
}
else {
    $conn = $_SESSION["connection"];
}

$friends = getFriends($conn, $_SESSION["username"]);
foreach ($friends as $friend) {
    echo "<a class='list-group-item' href=''>$friend <i class='fa fa-comment fa-fw'
    style='float:right' aria-hidden='true'></i>&nbsp;</a>";
}
?>