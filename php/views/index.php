<?php
    session_start();
    $root = dirname(__FILE__);
    require_once($root . DIRECTORY_SEPARATOR .  "../database_interface/db.php");
    require_once($root . DIRECTORY_SEPARATOR .  "../router/redirect.php");
    require_once($root . DIRECTORY_SEPARATOR .  "../controllers/AccountController.php");
    // connect to database
    $connection = connect();
    if (!$connection) {
        die("Cannot connect to database.");
    }
    else {
        $_SESSION["connection"] = $connection;
    }
    $_SESSION["wrongCredentials"] = false;
    // if username and password were submitted, check them
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        if (isPassword($connection, $_POST["username"], $_POST["password"])) {
            $_SESSION["wrongCredentials"] = false;
            $_SESSION["authenticated"] = true;
            $_SESSION["username"] = $_POST["username"];
            redirect("main.php");
            die();
        }
        else {
            $_SESSION["wrongCredentials"] = true;
        }
    }
?>

<!DOCTYPE html>
<html lang = "en">
    <head>
        <title>Login Page</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <!-- jQuery library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <!-- Custom styling -->
        <link rel="stylesheet" href="../../styles/common.css">
        <link rel="stylesheet" href="../../styles/login.css">
    </head>
    <body style="background-color: #222;">
        <form id=loginform name="loginForm" class="change-form" action="index.php" method="post">
            <div class="well-lg" style="background-color: #333; color: white; border: none;">
                <h2>Mio Login Page</h2><br>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="form-control" id="username" placeholder="Enter username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password">
                </div>
                <input type="submit" class="btn btn-primary" value="Login">
                <div id="errorMessage" style="color: red"> <?php if($_SESSION["wrongCredentials"]) { echo("Your username or password is incorrect."); } ?> </div>
                <div>
                    <a href="./signup.php">Don't Already Have An Account? Signup Here!</a>
                </div>
            </div>
        </form>
    </body>
</html>