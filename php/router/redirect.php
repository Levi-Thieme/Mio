<?php
/*
Redirects to the given page in the current directory
*/
function redirect($path) {
    $relativePath = "../views/";
    $actualPath = $relativePath . $path;
    $host  = $_SERVER["HTTP_HOST"];
    $uri   = rtrim(dirname($_SERVER["PHP_SELF"]), '/\\');
    header("Location: http://$host$uri/$actualPath");
    die();
}
