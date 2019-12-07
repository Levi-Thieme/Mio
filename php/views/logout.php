<?php
    $root = dirname(__FILE__);
    require_once ($root . DIRECTORY_SEPARATOR .  "../router/redirect.php");
    $_SESSION = array();
    session_destroy();
    redirect("index.php");
    die();