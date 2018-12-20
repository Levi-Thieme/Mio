<?php
    session_start();
    include_once("db.php");
    echo "Hello";
    error_log("roomBuilder called.\n", 3, "error_log.txt");
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    if (isset($_SESSION["username"]) && isset($_POST["newRoomName"])) {
        $success = createRoom($conn, $_SESSION['username'], $_POST['newRoomName']);
        if ($success) {
            error_log("Created new room.\n", 3, "error_log.txt");
        }
        else {
            error_log("Failed to create new room.\n", 3, "error_log.txt");
        }
        $roomId = getRoomId($conn, $_POST['newRoomName']);
    }
    $conn->close();
?>