<head>
    <script type="text/javascript" src="../scripts/main.js"></script> 
</head>

<?php
require_once("../db.php");

session_start();

function createFriendDiv($username) {
    $htmlContent = "<div id=$username class='list-group-item'>" . $username . 
                "<i class='fa fa-comment fa-fw' style='float:right' aria-hidden='true'></i>" .
                "<i data-delete-friend class='fa fa-trash fa-fw' style='float:right' aria-hidden='true'></i>" .
              "</div>";
    return $htmlContent;
}

function createFriendRequestToDiv($username) {
    $htmlContent = "<div id=$username class='list-group-item'>" . $username . 
                "<i class='fa fa-comment fa-fw' style='float:right' aria-hidden='true'></i>" .
                "<i data-delete-friend class='fa fa-trash fa-fw' style='float:right' aria-hidden='true'></i>" .
                "<i data-approve-friend-request class='fa fa-plus fa-fw' style='float:right' aria-hidden='true'></i>" .
              "</div>";
    return $htmlContent;
}

function createFriendRequestFromDiv($username) {
    $htmlContent = "<div id=$username class='list-group-item'>" . $username . 
                "<i data-delete-friend class='fa fa-trash fa-fw' style='float:right' aria-hidden='true'></i>" .
              "</div>";
    return $htmlContent;
}

if (isset($_SESSION["username"])) {
    $conn = connect("127.0.0.1", "mio_db", "pfw", "mio_db");
    $friends = getFriends($conn, $_SESSION["username"]);
    $requestsFrom = getRequestsFromUser($conn, $_SESSION["username"]);
    $requestsTo = getRequestsToUser($conn, $_SESSION["username"]);
    
    foreach ($friends as $friend) {
        $username = getUsername($conn, implode($friend));
        echo createFriendDiv($username);
    }
    
    foreach ($requestsTo as $request) {
        $username = getUsername($conn, implode($request));  
        echo createFriendRequestToDiv($username);
    }
    
    foreach ($requestsFrom as $request) {
        $username = getUsername($conn, implode($request));
        echo createFriendRequestFromDiv($username);
    }
    $conn->close();
}
?>