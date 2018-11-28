<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../styles/main.css" type="text/css">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="../scripts/main.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="w3-sidebar w3-light-grey w3-card" style="width:200px">
      
      
      <a class="list-group-item" href="myAccount.php"><i class="fa fa-user fa-2x fa-fw" aria-hidden="true"></i>&nbsp; My Profile</a>
    
      <!-- Panel for My Chats accordion -->
      <div class="panel-group">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a href="#" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a> 
              <a href="#" class="w3-bar-item w3-button"><i class="fa fa-search"></i></a> 
              <a data-toggle="collapse" class="list-group-item" href="#collapse1">My Chats
              <i class="fa fa-angle-double-down" style="float:right"></i></a>
            </h4>
          </div>
          <div id="collapse1" class="panel-collapse collapse">
            <div class="panel-body">Chat 1</div>
            <div class="panel-footer">Chat 2</div>
          </div>
        </div>
        
        <!-- Panel for Friends accordion -->
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
              <a href="#" class="w3-bar-item w3-button"><i class="fa fa-plus-circle"></i></a> 
              <a href="#" class="w3-bar-item w3-button"><i class="fa fa-search"></i></a> 
              <a data-toggle="collapse" class="list-group-item" href="#collapse2">Friends
              <i class="fa fa-angle-double-down" style="float:right"></i></a></a>
            </h4>
          </div>
          <div id="collapse2" class="panel-collapse collapse">
            <?php echo "<div class='panel-body'>Bob</div>"; ?>
          </div>
        </div>
      </div>
      
      <a id="signout" class="list-group-item" href="./logout.php"><i class="fa fa-sign-out fa-2x fa-fw fa-rotate-180" aria-hidden="true"></i>&nbsp; Signout</a>
    </div>

    <div class="main">
        <div id="searchContainer">
            <input type="text" class="form-control" placeholder="Search">
        </div>
        
      <div id="messageContainer">
        <ol class="discussion">
        </ol>
      </div>
      
        <div class="container" id="imControls">
            <div class="form-group shadow-textarea">
                <textarea class="form-control z-depth-1" id="message" rows="3" placeholder="Write something here..."></textarea>
                <a href="#" id="submitButton" class="w3-bar-item w3-button"><i class="fa fa-comment"></i>  Send</a>
            </div>
        </div>
    </div>
</body>

</html>
