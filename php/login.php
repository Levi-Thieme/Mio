
<?php

?>

<!DOCTYPE html>
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
    <script type="text/javascript" src="../scripts/login.js"></script>
    <link rel="stylesheet" href="../styles/login.css">
</head>
<html lang="en">
    <body>
        <form id=login-form name="loginForm" class="change-form col-md-4 col-md-offset-4" onsubmit="return validate();">
            <div class="well well-lg">
                <h2>Mio Login Page</h2><br>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="form-control" id="exampleInputUsername1" placeholder="Enter username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                </div>
                <input type="submit" class="btn btn-primary" value="Login">
                <br><br><button type="button">I Forgot My Password</button>
            </div>
        </form>
    </body>
</html>