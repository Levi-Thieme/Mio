<?php
    // Enable sessions.
    session_start();
    // If the user clicks the logout button,
    // link them to the logout page,
    // then end their session.
    session_destroy();
    header('Location: ./login.php');
?>  