<?php
    session_start();
    require_once("./db.php");
    require_once("./sidebar_library.php");
   $conn = connect("localhost", "mio_db", "pfw", "mio_db");
    
    //TODO ADD AUTHENTICATION
    if($_SESSION["authenticated"]){
      $sql = "SELECT id FROM user WHERE name='" . $_SESSION['username'] . "';";
      $result = execQuery($sql, $conn);
      if ($result !== false) {
        $userId = $result->fetch_assoc()['id'];
      } else {
        die("Could not get username");
      }
    }else {
      die("Not Logged in");
    }
    if(isset($_GET['room_id'])){
      $roomId = $_GET['room_id'];
    } else {
      $sql = "SELECT room FROM room_member WHERE usr=" . $userId . " LIMIT 1;";
      error_log("Error: $sql \n" . $conn->error, 3, "error_log.txt");
      $result = execQuery($sql, $conn);
      $roomId = $result->fetch_assoc()['room'];
    }
  
        if(!empty($_POST["message"])){
                 sendMessage($conn,$userId,$roomId);
        }
                
    
   function sendMessage($conn,$userId,$roomId) {
        
       
        
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error . "\n");
        } 
        
            $content = filter($conn,htmlspecialchars($_POST["message"]));
            $time = $_POST["time"];
            $nowRoomId= $_POST["nowRoomId"];
            $sql = "INSERT INTO message (user_id, content,time) VALUES ($userId ,'$content','$time')";
            $test = "INSERT INTO room_message (room_id,message_id,user_id) values ($nowRoomId,LAST_INSERT_ID(),$userId)";
            execQuery($sql,$conn);
            execQuery($test,$conn);
            $_POST["message"] = "";
       
            
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
</head>

<body>
  <input type='hidden' name="room_id" id="roomId" value=<?php echo "'" . $roomId . "'";?>/>
  <input type='hidden' name="userId" id="userId" value=<?php echo "'" . $userId . "'";?>/>
  <div class="w3-sidebar w3-light-grey w3-card" style="width:200px">
      <a class="list-group-item" href="myAccount.php"><i class="fa fa-user fa-2x fa-fw" aria-hidden="true"></i>&nbsp; My Profile</a>
      <!-- Panel for My Chats accordion -->
      <div class="panel-group">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a id = "createChatBtn" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a> 
              <a data-toggle="collapse" class="list-group-item" href="#collapse1">My Chats
              <i class="fa fa-angle-double-down" style="float:right"></i></a>
            </h4>
          </div>
          <div id="collapse1" class="panel-collapse collapse">
            <?php
              $myChatRooms = getRoomsAssociativeArrayFromUserId(getUserId($conn, $_SESSION['username']));
              foreach($myChatRooms as $room_id => $room_name) {
                echo "<a onclick = \"document.getElementById('chat" . $room_id . "').submit(); return false;\"><div style='cursor:pointer;' class='panel-body'> " . $room_id . ": " . $room_name . "</div>";
                echo "<form id='chat" . $room_id . "' action=''> <input name='room_id' type='hidden' value='". $room_id . "'/></form>";
                echo "</a>\n";
              }
            ?>
            <div class="panel-footer">Add new Room
              <form action='./roomBuilder.php' method='post'>
                <input type="text" name="newRoomName" placeholder="Enter a room name"/>
                <input type="submit" value="Add"/>
              </form>
            </div>
          </div>
        </div>
        
        <!-- Panel for Friends accordion -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a id = "addFriendBtn" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a> 
              <a data-toggle="collapse" class="list-group-item" href="#friendsCollapse" onclick="refreshFriendsList()">Friends
              <i class="fa fa-angle-double-down" style="float:right"></i></a></a>
            </h4>
          </div>
          <div id="friendsCollapse" class="panel-collapse collapse">
          </div>
        </div>
      </div>
      <a id="signout" class="list-group-item" href="./logout.php"><i class="fa fa-sign-out fa-2x fa-fw fa-rotate-180" aria-hidden="true"></i>&nbsp; Signout</a>
      ><div class = 'panel-body'> </div>
        <form action='./addToRoom.php' method='get'>
          <?php error_log("Error: ".implode(" ", $_GET) . "////" . implode(" ", array_keys($_GET)) . " \n" . $conn->error, 3, "error_log.txt");?>
          <input type="hidden" name='thisRoom' value=<?php echo "'" . $_GET['room_id'] . "'"; ?>/>
          <input class = 'form-control' type="submit" value="Add Friends to this Chat"/>
        </form>
    </div>
    
    
    
    <div class="main">
      
      <div id="slider" class="slider">
        <a href="javascript:void(0)" id="closeBtn" class="closebtn">&times;</a>
        
                <div id="sliderFormDiv" class="form-group">
                    <label for="search"><h1 id="sliderName">Name</h1></label>
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
        <ol class="discussion">
        </ol>
      </div>
      
      
    

            <div class="container" id="imControls">
                        
            <form id="messaging" class="poz" method="post">
            <div class="form-group shadow-textarea">
                <textarea class="form-control z-depth-1" id="message" name ="message" rows="3" placeholder="Write something here..."></textarea>
             <a href="#" id="submitButton" class="w3-bar-item w3-button" onclick = "insertData()"><i class="fa fa-comment"></i>  Send</a>
            </div>
               </form>
            </div>
     
    </div>
</body>

</html>