<?php
    require_once("db.php");
    require_once("errors.php");
    
    //signup form has been submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
            $conn = connect(LOCALHOST, USER, PASS, DB);
            if ($conn) {
                insertUser($conn, $_POST["name"], $_POST["email"], $_POST["password"], $_POST["confirmPassword"]);
                $conn->close();
                redirect("login.php");
            }
        }
        else {
            $errorMessage = "Your signup credentials are invalid.";
        }
    }
?>
<!DOCTYPE html>
<head>
    <title>Signup Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- Custom styling -->
    <link rel="stylesheet" href="../styles/myAccount.css">
    <link rel="stylesheet" href="../styles/common.css">
    <!-- Javascript -->
    <script src="../scripts/signup.js" type="text/javascript"></script>
    
</head>
<html lang="en">
    <body style="background-color: #222; color: white;">
        <form action=' <?php echo($_SERVER['SELF']); ?> ' onsubmit="return validateSignup()" method="POST" id="signup" name="signup" class="change-form col-md-4 col-md-offset-4">
            <div class="well well-lg" align = "center" style="background-color: #333; color: white; border: none;">
                <div class="form-group" align = "center">
                    <h2> Sign Up </h2>
                    <br>
                    <div id="errorMessage" name="errorMessage" style="color: red;"></div>
                    <label for="name">Name</label>
                    <input id="username" name="name" type="text" class="form-control" aria-describedby="enter name" placeholder="Enter your name">
                    
                    <label for="newEmail:">Email</label>
                    <input id="email" name="email" type="text" class="form-control" placeholder="Enter an email address">
                    
                    <label>Password</label>
                    <input id="password" name="password" type="password" class="form-control" placeholder="Enter a password">
                    
                    <label>Confirm Password</label>
                    <input id="confirmPassword" name="confirmPassword" type="password" class="form-control" placeholder="Confirm password">
                    
                    <label>Profile picture upload</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000">
                    <input name="profile" type="file" class="form-control" accept="image/*">
                    
                    <br>
                    <label for="terms">I agree to the terms and conditions</label>
                    <input id="agree" name="agree" type="checkbox" onclick="document.forms.signup.signupBtn.disabled = !document.forms.signup.signupBtn.disabled">
                    <br>
                    
                    <input name="submit" disabled="disabled" type="submit" id="signupBtn" class="btn btn-primary" value="submit">
                </div>
            </div>
        </form>
    </body>
</html>