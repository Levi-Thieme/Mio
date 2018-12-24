<?php
    session_start();
    include_once("db.php");
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    if (isset($_SESSION["username"]) && isset($_POST["newRoomName"])) {
        $success = createRoom($conn, $_SESSION['username'], $_POST['newRoomName']);
        $roomId = getRoomId($conn, $_POST['newRoomName']);
    }
    $conn->close();
?>