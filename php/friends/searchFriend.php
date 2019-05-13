<?php
session_start();
require_once("../database_interface/db.php");

function show_list($sql, $connection) {
    // execute query
    $result = $connection->query($sql) or die(mysqli_error($connection));

    // check whether we found a row
    while ($user = $result->fetch_assoc())
    {
        echo "<div>" . implode($user) . "</div>";
    }
}

$conn = connect();

if (isset($_POST["friendName"])) {
    $sql = sprintf("select name from user where name like '%s%%'", 
        mysqli_real_escape_string($conn, $_POST["friendName"]));
    show_list($sql, $conn);
}
$conn->close();
?>