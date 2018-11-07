<?php
    include '../db.php';
    //Connect
    $conn = connect("127.0.0.1", "thielt01", "sharky21", "mio");
    $name = 'Rob the Great';
    updateUserEmail($conn, "hello", $name);
    exit;

?>