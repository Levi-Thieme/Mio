<?php
    session_start();
    $root = dirname(__FILE__);
    require_once($root . DIRECTORY_SEPARATOR .  "../database_interface/db.php");
    $conn = connect();
    if (!$_SESSION["authenticated"]) {
        error_log("User is not authenticated.", 3, "error_log.txt");
        header("Location: ./index.php");
        die();
    }
    
    if (!isset($_SESSION["username"])) {
        error_log("Username is not set in session variable.", 3, "error_log.txt");
        header("Location: ./index.php");
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
    <script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous">
    </script>
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
    <!-- Sidebar styling -->
    <link rel="stylesheet" href="../../styles/sidebar.css">
    <link rel="stylesheet" href="../../styles/toast.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.css">
</head>

<body class="dark">
    <input type='hidden' name="roomName" id="roomName" value=<?php echo "\"$roomName\"";?>/>
    <input type='hidden' name="roomId" id="roomId" value=<?php echo "'" . $roomId . "'";?>/>
    <input type='hidden' name="userId" id="userId" value=<?php echo "'" . $userId . "'";?>/>
    <input type='hidden' name="username" id="username" value=<?php echo "'" . $username . "'";?>/>

    <div id="pageGridContainer" class="grid-container">
        <div id="sidebar" class="grid-item dark sidebar animated slideInLeft"
            style="width: 300px;">

            <div id="sidebarHeader" class="sidebarPanel">
                <a id="myAccountLink" class="sidebarMiddle" href="myAccount.php">
                    <h4><i class="fa fa-user" aria-hidden="true"></i> My Account</h4>
                </a>
                <div id="hideShowDiv" class="sidebarRight">
                    <i id="hideShowSidebarBtn" class="fa fa-bars fa-3x"></i>
                </div>
            </div>

            <!-- Panel for My Chats accordion -->
            <div id="chatPanel" class="sidebarPanel">
                <a id="addRoomBtn" class="firstRow sidebarLeft" onclick="openCreateRoomModal()"><i class="fa fa-plus-circle fa-3x"></i></a>
                <h4 class="firstRow sidebarMiddle">Chats</h4>
                <a id="toggleRoomsCollapse" class="firstRow sidebarRight" data-toggle="collapse" href="#roomCollapse">
                    <i id="toggleChatsIcon" class="fa fa-angle-double-down fa-3x"></i>
                </a>
                <div id="roomCollapse" class="collapse animated zoomIn secondRow spanAllCols">
                    <div id="roomList"></div>
                </div>
            </div>

            <!-- Panel for Friends accordion -->
            <div id="friendPanel" class="sidebarPanel">
                <a id="addFriendBtn" class="firstRow sidebarLeft" onclick="openFriendRequestModal()"><i class="fa fa-plus-circle fa-3x sidebarLeft"></i></a>
                <h4 class="firstRow sidebarMiddle">Friends</h4>
                <a id="toggleFriendsCollapse" class="firstRow sidebarRight" data-toggle="collapse" href="#friendsCollapse">
                    <i class="fa fa-angle-double-down fa-3x"></i></a>
                <div id="friendsCollapse" class="collapse secondRow spanAllCols"></div>
            </div>

            <div id="sidebarFooter" class="sidebarPanel">
                <a id="signout" class="dark sidebarMiddle" href="logout.php"><button id="signoutBtn" class="btn btn-primary">Signout</button></a>
            </div>
        </div>

        <div id="mainPanel" class="main">
            <!-- Top Right notification Toast wrapping div -->
            <div id="toastWrapper">
                <div class="toast">
                    <div class="toast-header">
                        <strong id="notificationToastHeader" class="mr-auto"><i class="fa fa-grav"></i></strong>
                        <div id="notificationToastTime"></div>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="notificationToastBody" class="toast-body">
                    </div>
                </div>
            </div>
            <div id="messageContainer">
                <ol id="messageList" class="discussion"></ol>
            </div>
            <div id="imControls" class="input-group mb-3">
                <input id="message" name ="message"  type="text" class="form-control animated slideInUp" placeholder="Write something here...">
                <div id="submitBtnDiv" class="input-group-append dark animated slideInRight">
                    <button id="sendMessageButton" class="w3-button">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 id="modalTitle" class="modal-title">Hello!</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body dark">
                    <div class="input-group">
                        <input id="modalInput" name="sliderInput" type="text" class="form-control input-dark">
                        <button id="modalSubmitBtn" type="button" class="btn btn-primary" data-dismiss="modal">Submit</button>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer modal-dialog-scrollable dark">
                    <ul id="optionList" class="list-group"></ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>