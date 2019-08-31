<?php
    require_once ("../router/redirect.php");
    $_SESSION = array();
    session_destroy();
    redirect("login.php");
    die();