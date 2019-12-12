<?php
session_start();
$root = dirname(__FILE__);
require_once($root . DIRECTORY_SEPARATOR .  "../database_interface/db.php");
require_once($root . DIRECTORY_SEPARATOR .  "../views/Renderer.php");

/*
 *  Handle the request...
 * $_GET["request"] stores the function to be called.
 */
if (is_callable($_GET["request"])) {
    $_GET["request"]();
}

function getFriendDivs() {
    $userId = $_GET["userId"];
    $conn = connect();
    $username = getUsername($conn, $userId);
    $friendData = array();
    $friends = getFriends($conn, $username);
    foreach ($friends as $friend) {
        $friendName = getUsername($conn, implode($friend));
        $friendAssoc = (object) array("id" => $friend["to_id"], "name" => $friendName, "type" => "accepted");
        array_push($friendData, $friendAssoc);
    }
    $requestsTo = getRequestsToUser($conn, $username);
    foreach ($requestsTo as $request) {
        $friendName = getUsername($conn, implode($request));
        $friendAssoc = (object) array("id" => $request["from_id"], "name" => $friendName, "type" => "toMe");
        array_push($friendData, $friendAssoc);
    }
    $requestsFrom = getRequestsFromUser($conn, $username);
    foreach ($requestsFrom as $request) {
        $friendName = getUsername($conn, implode($request));
        $friendAssoc = (object) array("id" => $request["to_id"], "name" => $friendName, "type" => "fromMe");
        array_push($friendData, $friendAssoc);
    }
    echo json_encode($friendData);
    $conn->close();
}

function searchFriend() {
    if(isset($_GET["friendName"])) {
        $conn = connect();
        $namesLike = searchFriends($conn, $_GET["friendName"])->fetch_all();
        foreach($namesLike as $name) {
            echo Renderer::listGroupItem($name[0]);
        }
        $conn->close();
    }
}

function sendFriendRequest() {
    if (isset($_GET["receiver"])) {
        $conn = connect();
        createFriendRequest($conn, $_SESSION["username"], $_GET["receiver"]);
        $conn->close();
    }
}

function acceptFriendRequest() {
    if (isset($_GET["requester"])) {
        $conn = connect();
        updateFriendRequest($conn, $_SESSION["username"], $_GET["requester"]);
        $conn->close();
    }
}

function deleteFriend() {
    if (isset($_GET["friendName"])) {
        $conn = connect();
        deleteFriendByName($conn, $_SESSION["username"], $_GET["friendName"]);
        $conn->close();
    }
}
