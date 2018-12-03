<?php
    session_start();
    require_once('./db.php');
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    $roomName = $_POST['newRoomName'];
    addNewRoom($conn, $roomName, $_SESSION['username']);
    
    function reroute(){
        echo "<form id='loginForm' action='./login.php' method='post'>";
        echo '<input type="hidden" name="username" value="' . $_POST['name'] . '">';
        echo '<input type="hidden" name="password" value="' . $_POST['password'] . '">';
        echo "</form>";
        echo "<script type='text/javascript'>";
        echo "document.getElementById('loginForm').submit()";
        echo "</script>";
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        
    </head>
    <body>
        <?php reroute();?>
    </body>
</html>