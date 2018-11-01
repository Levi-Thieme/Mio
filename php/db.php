<?php
    /* ------------------------------------------------------------
                Database functions for CRUD operations.
    
    ---------------------------------------------------------------*/
    
    
    /*
    Inserts a new user with the given name.
    */
    function newUser($conn, $name) {
        $sql = "INSERT INTO user (name)
                VALUES ('{$name}')";
        execQuery($sql, $conn);
    }
    
    /*
    Updates a user's email address.
    */
    function updateUserEmail($email, $id) {
        $sql = "UPDATE user SET email = {$email} where id = {$id}";
        if ($conn == null) {
            $conn = connect();
        }
        execQuery($sql, $conn);
    }
    
    /*
    Updates a user's password.
    */
    function updateUserPassword($password, $id) {
        $sql = "UPDATE user SET password = {$password} where id = {$id}";
        if ($conn == null) {
            $conn = connect();
        }
        execQuery($sql, $conn);
    }
    
    /*
    Deletes a user's account.
    */
    function deleteUserAccount($id) {
        $sql = "DELETE FROM user where id = {$id}";
        if ($conn == null) {
            $conn = connect();
        }
        execQuery($sql, $conn);
    }
    
    
    /*
    Updates the user's profile image.
    */
    function updateUserProfileImage($image, $id) {
        $sql = "UPDATE user SET image = {$image} WHERE id = {$id}";
    }
    

?>