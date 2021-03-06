<?php

class AccountController {
    private $conn;

    public function __construct($databaseConnection) {
        $this->conn = $databaseConnection;
    }

    public function __destruct() {

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
        $success = deleteFriendByName($this->conn, $userFrom, $userTo);
        return $success;
    }
}