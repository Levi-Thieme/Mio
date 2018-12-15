<head>
    <script type="text/javascript" src="../scripts/main.js"></script> 
</head>

<?php
include_once("../db.php");

session_start();
$conn;
if (!$_SESSION["connection"]) {
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    $_SESSION["connection"] = $conn;
}
else {
    $conn = $_SESSION["connection"];
}
$conn = connect("localhost", "mio_db", "pfw", "mio_db");
if (isset($_SESSION["username"])) {

$friends = getFriends($conn, $_SESSION["username"]);
    if (empty($friends)) {
        return "";
    }
    foreach ($friends as $friend) {
        $username = getUsername($conn, implode($friend));
        echo "<div id=$username class='list-group-item'>" . $username . 
                "<i class='fa fa-comment fa-fw' style='float:right' aria-hidden='true'></i>" .
                "<i onclick='deleteFriend($username)' class='fa fa-trash fa-fw' style='float:right' aria-hidden='true'></i>
            &nbsp</div>";
    }
}
?>