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
