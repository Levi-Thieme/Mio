<head>
    <script type="text/javascript" src="../scripts/main.js"></script> 
</head>

<?php
require_once("../db.php");

session_start();

$conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
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
$conn->close();
?>