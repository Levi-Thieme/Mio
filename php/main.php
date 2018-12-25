<?php
    session_start();
    require_once("./db.php");
    $conn = connect("localhost", "mio_db", "pfw", "mio_db");
    
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
            error_log("Participant first room: " . $roomName, 3, "error_log.txt");
        }
        else {
            $result = getOwnedRooms($conn, $username);
            if ($result->num_rows > 0) {
                $result = $result->fetch_assoc();
                $roomId = $result["id"];
                $roomName = $result["name"];
                error_log("Owned room: " . $roomName, 3, "error_log.txt");
            }
            else { //user has no rooms
                $roomId = -1;
                $roomName = "";
                error_log("No rooms...: " . $roomName, 3, "error_log.txt");
            }
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../scripts/main.js"></script> 
    <!-- Styles -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- This page's custom styling -->
    <link rel="stylesheet" href="../styles/main.css" type="text/css">
    <!-- Search area styling -->
    <link rel="stylesheet" href="../styles/search.css" type="text/css">
    <!-- Common styling -->
    <link rel="stylesheet" href="../styles/common.css" type="text/css">
</head>

<body>
    <input type='hidden' name="roomName" id="roomName" value=<?php echo "\"$roomName\"";?>/>
    <input type='hidden' name="roomId" id="roomId" value=<?php echo "'" . $roomId . "'";?>/>
    <input type='hidden' name="userId" id="userId" value=<?php echo "'" . $userId . "'";?>/>
    <input type='hidden' name="username" id="username" value=<?php echo "'" . $username . "'";?>/>
  
  <div class="w3-sidebar w3-light-grey w3-card" style="width:200px">
      <a class="list-group-item" href="myAccount.php"><i class="fa fa-user fa-2x fa-fw" aria-hidden="true"></i>&nbsp; My Profile</a>
      <!-- Panel for My Chats accordion -->
      <div class="panel-group">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a id="addRoomBtn" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a>
              <a data-toggle="collapse" class="list-group-item" href="#roomCollapse" onclick="refreshRoomList()">My Chats
              <i class="fa fa-angle-double-down" style="float:right"></i></a>
            </h4>
          </div>
          <div id="roomCollapse" class="panel-collapse collapse">
          </div>
        </div>
        
        <!-- Panel for Friends accordion -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a id="addFriendBtn" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a> 
              <a data-toggle="collapse" class="list-group-item" href="#friendsCollapse" onclick="refreshFriendsList()">Friends
              <i class="fa fa-angle-double-down" style="float:right"></i></a>
            </h4>
          </div>
          <div id="friendsCollapse" class="panel-collapse collapse">
          </div>
        </div>
      </div>
      <a id="signout" class="list-group-item" href="./logout.php"><i class="fa fa-sign-out fa-2x fa-fw fa-rotate-180" aria-hidden="true"></i>&nbsp; Signout</a>
    </div>
    
    
    <div class="main">
      
      <div id="slider" class="slider">
        <a href="javascript:void(0)" id="closeBtn" class="closebtn">&times;</a>
            <div id="sliderFormDiv" class="form-group">
                <label for="search"><h1 id="sliderName"></h1></label>
                <input type="text" class="form-control" id="addName" name="addName">
                <button id="sliderAction" type="submit" class="btn btn-primary"></button>
                <div id = "optionList" name = "optionList"></div>
            </div>
        </form>
      </div>
      
      <div id="searchContainer">
          <input type="text" class="form-control" placeholder="Search">
      </div>
    
      <div id="messageContainer">
        <ol id="messageList" class="discussion">
        </ol>
      </div>
  
      <div class="container" id="imControls">
      <form id="messaging" class="poz" method="post">
      <div class="form-group shadow-textarea">
          <textarea class="form-control z-depth-1" id="message" name ="message" rows="3" placeholder="Write something here..."></textarea>
       <a href="#" id="sendMessageButton" class="w3-bar-item w3-button"><i class="fa fa-comment"></i>  Send</a>
      </div>
         </form>
      </div>
    </div>
</body>

</html>