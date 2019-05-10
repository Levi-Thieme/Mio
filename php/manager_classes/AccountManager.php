<?php
/**
 * Created by PhpStorm.
 * User: signo
 * Date: 5/7/2019
 * Time: 11:24 PM
 */
require_once("Manager.php");

class AccountManager extends Manager {
    private $username;

    public function __construct($databaseConnection, $username) {
        parent::construct($databaseConnection);
        $this->username = $username;
    }

    public function __destruct() {
        parent::destruct();
    }

    public function searchFriends($username) {
        $result = searchFriends($this->conn, $username);
        while ($user = $result->fetch_assoc()) {
            echo Renderer::divWrap(implode($user));
        }
    }

    public function createFriendRequest($userFrom, $userTo) {
        $success = true;
        if (!createFriendRequest($this->conn, $userFrom, $userTo)) {
            error_log("Failed to create friend request.\n" . $this->conn->error, 3, "error_log.txt");
            $success = false;
        }
        return $success;
    }

    public function acceptFriendRequest($acceptor, $requester) {
        acceptFriendRequest($this->conn, $acceptor, $requester);
    }

    public function deleteFriend($userFrom, $userTo) {
        $success = deleteFriend($this->conn, $userFrom, $userTo);
        return $success;
    }

    public function createFriendDivs($username) {
        $friends = getFriends($this->conn, $username);
        $requestsFrom = getRequestsFromUser($this->conn, $username);
        $requestsTo = getRequestsToUser($this->conn, $username);

        foreach ($friends as $friend) {
            $friendName = getUsername($this->conn, implode($friend));
            echo Renderer::createFriendDiv($friendName);
        }

        foreach ($requestsTo as $request) {
            $friendName = getUsername($this->conn, implode($request));
            echo Renderer::createFriendRequestToDiv($friendName);
        }

        foreach ($requestsFrom as $request) {
            $friendName = getUsername($this->conn, implode($request));
            echo Renderer::createFriendRequestFromDiv($friendName);
        }
    }
}