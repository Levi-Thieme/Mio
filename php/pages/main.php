<?php
    session_start();
    require_once("../database_interface/db.php");

    $conn = connect();

    if (!$_SESSION["authenticated"]) {
        error_log("User is not authenticated.", 3, "error_log.txt");
        header("Location: ./login.php");
        die();
    }
    
    if (!isset($_SESSION["username"])) {
        error_log("Username is not set in session variable.", 3, "error_log.txt");
        header("Location: ./login.php");
        die();
    }
    
    $username = $_SESSION["username"];
    $userId = getUserId($conn, $username);
    
    if (isset($_POST["currentRoom"])) {
        $roomName = $_POST["currentRoom"];
        $roomId = getRoomId($conn, $roomName);
    } else {
        $result = getParticipantFirstRoom($conn, $userId);
        if ($result->num_rows > 0) {
            $roomId = $result->fetch_array()[0];
            $roomName = getRoomName($conn, $roomId);
        }
        else {
            $result = getOwnedRooms($conn, $username);
            if ($result->num_rows > 0) {
                $result = $result->fetch_assoc();
                $roomId = $result["id"];
                $roomName = $result["name"];
            }
            else { //user has no rooms
                $roomId = -1;
                $roomName = "";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Scripts -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- Popper JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="../../scripts/socket.js" type="text/javascript"></script>
    <script src="../../scripts/main.js" type="text/javascript"></script>
    <script src="../../scripts/modal.js" type="text/javascript"></script>
    <script src="../../scripts/sidebar.js" type="text/javascript"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- This page's custom styling -->
    <link rel="stylesheet" href="../../styles/main.css">
    <!-- Search area styling -->
    <link rel="stylesheet" href="../../styles/search.css">
    <!-- Slider styling -->
    <link rel="stylesheet" href="../../styles/modal.css">
    <!-- Common styling -->
    <link rel="stylesheet" href="../../styles/common.css">
</head>

<body>
    <input type='hidden' name="roomName" id="roomName" value=<?php echo "\"$roomName\"";?>/>
    <input type='hidden' name="roomId" id="roomId" value=<?php echo "'" . $roomId . "'";?>/>
    <input type='hidden' name="userId" id="userId" value=<?php echo "'" . $userId . "'";?>/>
    <input type='hidden' name="username" id="username" value=<?php echo "'" . $username . "'";?>/>
  
  <div id="sidebar" class="w3-sidebar w3-card">
      <a class="list-group-item" href="myAccount.php" style="background-color:#222"><i class="fa fa-user fa-2x fa-fw" aria-hidden="true"></i>&nbsp; My Profile</a>

      <!-- Panel for My Chats accordion -->
      <div class="panel-group">
        <div class="panel panel-default">
          <div class="panel-heading" style="background-color: #222">
            <h4 class="panel-title dark">
              <a id="addRoomBtn" onclick="openCreateRoomModal()" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a>
              <a data-toggle="collapse" class="list-group-item" href="#roomCollapse" style="background-color:#222;" onclick=<?php echo "refreshRoomList($userId)";?> >My Chats
              <i class="fa fa-angle-double-down" style="float:right"></i></a>
            </h4>
          </div>
          <div id="roomCollapse" class="panel-collapse collapse">
          </div>
        </div>
        
        <!-- Panel for Friends accordion -->
        <div class="panel panel-default">
          <div class="panel-heading" style="background-color: #222;">
            <h4 class="panel-title">
              <a id="addFriendBtn" onclick="openFriendRequestModal()" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a>
              <a data-toggle="collapse" class="list-group-item" href="#friendsCollapse" style="background-color: #222" onclick="refreshFriendsList()">Friends
              <i class="fa fa-angle-double-down" style="float:right"></i></a>
            </h4>
          </div>
          <div id="friendsCollapse" class="panel-collapse collapse">
          </div>
        </div>
      </div>
      <a id="signout" class="list-group-item" href="logout.php" style="background-color:#222;"><i class="fa fa-sign-out fa-2x fa-fw fa-rotate-180" aria-hidden="true"></i>&nbsp; Signout</a>
    </div>
    
    
    <div id="mainPanel" class="main">
        <div id="messageContainer">
            <ol id="messageList" class="discussion"></ol>
        </div>
        <div id="imControls" class="input-group">
            <input type="text" class="form-control z-depth-1 input-dark" id="message" name ="message" placeholder="Write something here..."></textarea>
            <button id="sendMessageButton" class="w3-bar-item w3-button">Send</button>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 id="modalTitle" class="modal-title">Hello!</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <input id="modalInput" name="sliderInput" type="text" class="form-control input-dark">
                    <ul id="optionList" class="list-group">
                    </ul>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button id="modalSubmitBtn" type="button" class="btn btn-primary" data-dismiss="modal">Submit</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>