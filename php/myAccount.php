<?php
    include './db.php';
    //Connect
    $conn = connect("127.0.0.1", "thielt01", "sharky21", "mio");
    session_start();
    $_SESSION["username"] = "bob";
    
    
    if (isset($_POST["updateEmailSubmit"])) {
        if (!empty($_POST["emailInput"]) && !empty($_POST["updateEmailPasswordInput"])) {
            $newEmail = $_POST["emailInput"];
            updateUserEmail($conn, $newEmail, $_SESSION["username"]);
        }
    }
    else if (isset($_POST["updatePasswordSubmit"])) {
        
    }
    session_destroy();
    $conn->close();
?>

<!DOCTYPE html>
<head>
    <title>My Account</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <!-- client side form validation -->
    <script type="text/javascript" src="../scripts/myAccount.js"></script>
    <!-- Custom styling -->
    <link rel="stylesheet" href="../styles/myAccount.css">
    
</head>
<html lang="en">
    <body>
        <div id="profileContainer" class="fluid-container">
            <div class="well well-sm">
                <div class="row" id="profileRowDiv">
                    <img class="img-fluid" alt="The user's profile image" src="../imgs/user.png">
                    <a href="../html/main.html" id="backBtn" class="btn btn-primary" value="Back to My Chats">Back to My Chats</a>
                    <div>
                        Username<br>Email Address
                    </div>
                </div>
            </div>
        </div>
        <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method="post" id="changeEmailForm" name="changeEmailForm" class="change-form col-md-4 col-md-offset-4">
            <div class="well well-lg">
                <div class="form-group">
                    <label for="updateEmailEmailInput">Update Email address</label>
                    <input type="email" class="form-control" id="updateEmailEmailInput" name="emailInput" aria-describedby="emailHelp" placeholder="Enter new email">
                    <label for="updateEmailPasswordInput">Password</label>
                    <input type="password" class="form-control" id="updateEmailPasswordInput" name="updateEmailPasswordInput" placeholder="Password">
                    <button type="submit" id="updateEmailButtton" name="updateEmailSubmit" class="btn btn-primary">Update Email</button>
                </div>
            </div>
        </form>
        <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method="post" id="changePasswordForm" name="changePasswordForm" class="change-form col-md-4 col-md-offset-4">
            <div class="well well-lg">
                <div class="form-group">
                    <label for="updatePasswordPasswordInput">Update Password</label>
                    <input type="password" class="form-control" id="updatePasswordPasswordInput" name="updatePasswordPasswordInput" aria-describedby="emailHelp" placeholder="Enter old password">
                </div>
                <div class="form-group">
                    <label for="newPassword newPasswordConfirm">Password</label>
                    <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="New password">
                    <input type="password" class="form-control" id="newPasswordConfirm" name="newPasswordConfirm" placeholder="Confirm new password">
                    <button type="submit" id="updatePasswordBtn" name="updatePasswordSubmit" class="btn btn-primary">Update Password</button>
                </div>
            </div>
        </form>
        <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method="post" id="deleteAccountForm" name="deleteAccountForm" class="change-form col-md-4 col-md-offset-4">
            <div class="alert alert-danger">
                <strong>Delete My Account</strong>
                <div class="form-group">
                    <input type="username" class="form-control" id="deleteUsername" name="deleteUsername" placeholder="Username">
                    <input type="password" class="form-control" id="deletePassword" name="deletePassword" placeholder="Password">
                    <input type="password" class="form-control" id="deletePasswordConfirm" name="deletePasswordConfirm" placeholder="Confirm Password">
                    <label>Confirm Deletion <input type="checkbox" id="confirmDeleteCheckbox" name="confirmDeleteCheckbox"></label><br>
                    <a href="./login.html" id="deleteAccountBtn" name="deleteAccountSubmit" type="submit" class="btn btn-primary" role="button">Delete Account</a>
                </div>
            </div>
        </form>
    </body>
</html>

