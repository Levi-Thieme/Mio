<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="../styles/main.css" type="text/css">
    <script src="../scripts/main.js"></script>
</head>

<body>
    <?php
        // Begin the session.
        session_start();
    ?>
    <div class="sidebar">
        <div id="myAccount">
            <a href="myAccount.html">
                <img id="myAccountPhoto" src="../imgs/user.png" alt="My Account Photo">
            </a>
            <a href="myAccount.html">
                <img id="myAccountSettings" src="../imgs/gear.png">
            </a>
            <label id="myAccountName">Student 1337</label>
            
            <form action = "./logout.php">
                <input id = "logoutButton" name = "logout" type="submit" value="Log out">
            </form>
        
        </div>
        <div id="sidebar-subaccount">
            <div id="sidebarData">
                <div id="myChats">
                    <h3 class="sidebar-heading">My Chats</h3>
                    <div class="conversation">
                        <a href="myAccount.html">
                            <img src="../imgs/user.png" alt="Noah Parker">
                        </a>
                        <a class="conv-name" href="myAccount.html">Noah Parker</a>
                    </div>
                    <div class="conversation">
                        <a href="myAccount.html">
                            <img src="../imgs/user.png" alt="Jared Johnson">
                        </a>
                        <a class="conv-name" href="myAccount.html">Jared Johnson</a>
                    </div>
                </div>
                <div id="myFriends">
                    <h3 class="sidebar-heading">My Friends</h3>
                    <div class="conversation">
                        <a href="myAccount.html">
                    <img src="../imgs/user.png" alt="Noah Parker">
                </a>
                        <a class="conv-name" href="myAccount.html">Noah Parker</a>
                    </div>
                    <div class="conversation">
                        <a href="myAccount.html">
                    <img src="../imgs/user.png" alt="Jared Johnson">
                </a>
                        <a class="conv-name" href="myAccount.html">Jared Johnson</a>
                    </div>
                    <div class="conversation">
                        <a href="myAccount.html">
                    <img src="../imgs/user.png" alt="Isaac Smith">
                </a>
                        <a class="conv-name" href="myAccount.html">Isaac Smith</a>
                    </div>
                    <div class="conversation">
                        <a href="myAccount.html">
                    <img src="../imgs/user.png" alt="Kazuto Kirigaya">
                </a>
                        <a class="conv-name" href="myAccount.html">Kazuto Kirigaya</a>
                    </div>
                    <div class="conversation">
                        <a href="myAccount.html">
                    <img src="../imgs/user.png" alt="Homer Simpson">
                </a>
                        <a class="conv-name" href="myAccount.html">Homer Simpson</a>
                    </div>
                    <div class="conversation">
                        <a href="myAccount.html">
                    <img src="../imgs/user.png" alt="Dr. Chen">
                </a>
                        <a class="conv-name" href="myAccount.html">Dr. Chen</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main">
        <div id="searchContainer">
            <input type="text" class="form-control" placeholder="Search">
        </div>
        
      <div id="messageContainer">
        <ol class="discussion">
        </ol>
      </div>
      
        <form class="poz" action="" method="get">

            <div class="form-group shadow-textarea">
                <textarea class="form-control z-depth-1" id="message" rows="3" placeholder="Write something here..."></textarea>
            </div>
            <input id="submitButton" name="submit" type="button" value="Send" />

        </form>
    </div>
</body>

</html>
