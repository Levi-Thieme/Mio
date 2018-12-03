<?php
    
    // enable sessions
    session_start();

    define("USER", "mio_db");
    define("PASS", "pfw");
    define("DB", "mio_db");

    // connect to database
    if (($connection = mysql_connect('localhost', USER, PASS)) === false)
    {    die("Could not connect to database");
        
    }
    // select database
    if (mysql_select_db(DB, $connection) === false)
        die("Could not select database");

    // if username and password were submitted, check them
    if (isset($_POST["username"]) && isset($_POST["password"]))
    {
        // prepare SQL
        $sql = sprintf("SELECT * FROM user WHERE name='%s' AND password=PASSWORD('%s')",
                       mysql_real_escape_string($_POST["username"]),
                       mysql_real_escape_string($_POST["password"]));

        
        // execute query
        $result = mysql_query($sql);
        echo $result;
        if ($result === false)
            die("Could not query database");

        // check whether we found a row
        if (mysql_num_rows($result) == 1)
        {
            // fetch row
            $row = mysql_fetch_assoc($result);
 
            
            
            // remember that user's logged in
            $_SESSION["authenticated"] = true;
            $_SESSION["username"] = $_POST["username"];

            // redirect user to home page, using absolute path, per
            // http://us2.php.net/manual/en/function.header.php
            $host = $_SERVER["HTTP_HOST"];
            $path = rtrim(dirname($_SERVER["PHP_SELF"]), "/\\");
            header("Location: ./main.php");
            exit;
            
        }
        else{
            echo "Incorrect Username and/or Password.";
            
        }  
    }
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
    <link rel="stylesheet" href="../styles/login.css">
</head>
<html lang="en">
    <body>
        <form id=loginform name="loginForm" class="change-form col-md-4 col-md-offset-4" action="login.php" method="post">
            <div class="well well-lg">
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
                
            </div>
        </form>
    </body>
</html>