<?php
session_start();
require_once("../database_interface/db.php");
require_once("../Renderer.php");

/*
 *  Handle the request...
 * $_GET["request"] stores the function to be called.
 */

if (is_callable($_GET["request"])) {
    $_GET["request"]();
}

function getFriendDivs() {
    $username = $_SESSION["username"];
    $conn = connect();
    $friends = getFriends($conn, $username);
    $requestsFrom = getRequestsFromUser($conn, $username);
    $requestsTo = getRequestsToUser($conn, $username);

    foreach ($friends as $friend) {
        $friendName = getUsername($conn, implode($friend));
        echo Renderer::createFriendDiv($friendName);
    }

    foreach ($requestsTo as $request) {
        $friendName = getUsername($conn, implode($request));
        echo Renderer::createFriendRequestToDiv($friendName);
    }

    foreach ($requestsFrom as $request) {
        $friendName = getUsername($conn, implode($request));
        echo Renderer::createFriendRequestFromDiv($friendName);
    }
}

function searchFriend() {
    if(isset($_GET["friendName"])) {
        $conn = connect();
        $namesLike = searchFriends($conn, $_GET["friendName"])->fetch_all();
        foreach($namesLike as $name) {
            echo Renderer::listItem($name[0]);
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
