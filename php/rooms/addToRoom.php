<?php
    
    // enable sessions
    session_start();
    require_once("../db.php");
    $conn = connect('127.0.0.1', 'mio_db', 'pfw', 'mio_db');
    
    if (isset($_GET['submit'])) {
        $sql = "INSERT INTO `room_member` (`room`, `usr`) VALUES ('" . $_GET['thisRoom'] . "', '" . getUserId($conn, $_GET['username']) . "')";
        execQuery($sql, $conn);
        echo "<form id='thisForm' action='./main.php' method='get'>";
        echo '<input type="hidden" name="room_id" value="' . $_GET['thisRoom'] . '">';
        echo "</form>";
        echo "<script type='text/javascript'>";
        echo "document.getElementById('thisForm').submit()";
        echo "</script>";
    }
?>

<!DOCTYPE html>
<head>
    <title>Add To Room</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- Custom styling -->
    <link rel="stylesheet" href="../styles/login.css">
</head>
<html lang="en">
    <body>
        <form class="change-form col-md-4 col-md-offset-4" action="" method="get">
            <div class="well well-lg">
                <?php $room_id = $_GET['thisRoom']?>
                <h2>Add User to room <?php echo getRoomName($room_id); ?></h2><br>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type='hidden' name="thisRoom" value= <?php echo "'" . $room_id . "'"; ?>>
                    <input type="text" name="username" class="form-control" id="username" placeholder="Enter username">
                </div>
                <input type="submit" name = 'submit' class="btn btn-primary" value="Add this user to the room">
                
            </div>
        </form>
    </body>
</html>