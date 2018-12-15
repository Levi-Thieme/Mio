<!DOCTYPE html>
<html>
    <head>
        
    </head>
    <body>
        <?php
            session_start();
            require_once('./db.php');
            $conn;
            if (!$_SESSION["connection"]) {
                $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
            }
            else {
                $conn = $_SESSION["connection"];
            }
            $roomName = $_POST['newRoomName'];
            addNewRoom($conn, $roomName, $_SESSION['username']);
            $roomId = getRoomId($conn, $roomName);
            echo "<form id='loginForm' action='./main.php' method='get'>";
            echo "<input type='hidden' name='room_id' value='" . $roomId . "'>";
            echo "</form>";
            echo "<script type='text/javascript'>";
            echo "document.getElementById('loginForm').submit()";
            echo "</script>";
            
        ?>
    </body>
</html>