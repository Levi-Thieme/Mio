<?php
    require("db.php");
    define("USER", "mio_db");
    define("PASS", "pfw");
    define("DB", "mio_db");
    
    function reroute(){
        echo "<form id='loginForm' action='./login.php' method='post'>";
        echo '<input type="hidden" name="username" value="' . $_POST['name'] . '">';
        echo '<input type="hidden" name="password" value="' . $_POST['password'] . '">';
        echo "</form>";
        echo "<script type='text/javascript'>";
        echo "document.getElementById('loginForm').submit()";
        echo "</script>";
    }
    
    function passwordsMatch() {
        return $_POST['password'] === $_POST['confirmPassword'];
    }
    
    function submittedEmpty($name) {
        return (isset($_POST[$name]) && $_POST[$name] == "");
    }
    
    function badEmailFormat() {
        if (isset($_POST["submit"])){
            return !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
        } else {
            return false;
        }
    }
    
    function badName() {
        if(isset($_POST['name'])) {
            define("USER", "mio_db");
            define("PASS", "pfw");
            define("DB", "mio_db");
            $nameTooLong = strlen($_POST['name']) > 64;
            if ($nameTooLong) {
                return true;
            }
            $conn = connect("localhost", USER, PASS, DB);
            $sql = "SELECT * FROM user WHERE name='" . filter($conn, $_POST['name']) . "';";
            $result = execQuery($sql, $conn);
            if($result !== false) {
                return $result->num_rows != 0;
            }else {
                return true;
            }
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
    
</head>
<html lang="en">
    <body style="background-color: #222; color: white;">
        <?php
           $conn = connect("localhost", USER, PASS, DB);
            if(isset($_POST["submit"])) {
                $filled = true;
                if($_POST["name"] == "" || badName()) {
                    $filled = false;
                } elseif(strlen($_POST["name"]) > 64) {
                    $filled = false;
                }
                if($_POST["email"] ==  "" || badEmailFormat()) {
                    $filled = false;
                } elseif(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                    $filled = false;
                }
                if($_POST["password"] == ""){
                    $filled = false;
                }elseif(strlen($_POST["password"]) > 40){
                    $filled = false;
                }
                if($_POST["confirmPassword"] != $_POST["password"]) {
                    $filled = false;
                }
                if($_POST["agree"] != "on") {
                    $filled = false;
                }
                if (isset($_POST['profile'])) {
                    echo $_POST['profile'];
                }
                if($filled) {
                    $name = $_POST['name'];
                    $sql = sprintf("INSERT INTO `user`( `name`, `email`, `password`, `image`) 
                    VALUES ('%s', '%s', PASSWORD('%s'), 'an Image')",
                    $name,
                    $conn->real_escape_string($_POST['email']),
                    $conn->real_escape_string($_POST['password']));
                    if ($conn->query($sql) === true) {
                        reroute();
                    } else {
                        echo "<b style='color:red;'>FAILURE IN QUERY</b>";
                    }
                }
            }
        ?>
        <br>
        <br>
        <form action="" method="post" id="signup" class="change-form col-md-4 col-md-offset-4">
            <div class="well well-lg" align = "center" style="background-color: #333; color: white; border: none;">
                <div class="form-group" align = "center">
                    <h2> Sign Up </h2>
                    <br>
                    <label for="name">Name</label>
                    <input <?php echo (submittedEmpty('name') || badName()) ? "style='border:solid red;'" : ''
                            ?> name="name" type="text" class="form-control" id="username" aria-describedby="enter name" placeholder="Enter your name" value ="<?php echo isset($_POST['name']) ? $_POST['name'] : '' ?>">
                    
                    <label for="newEmail:">Email</label>
                    <input <?php 
                        if(submittedEmpty('email')) {
                            echo "style='border:solid red;'";
                        } elseif(badEmailFormat()){ 
                            echo "style='border:solid red;'";
                        }?> name="email" type="text" class="form-control" id="email" placeholder="Enter an email address" value ="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>">
                    
                    <label>Password</label>
                    <input <?php if(!passwordsMatch() || submittedEmpty('password')) echo "style='border:solid red;'" ?> name = "password" type="password" class="form-control" placeholder="Enter a password" value ="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>">
                    
                    <label>Confirm Password</label>
                    <input <?php if(!passwordsMatch() || submittedEmpty('confirmPassword')) echo "style='border:solid red;'" ?> name = "confirmPassword" type="password" class="form-control" placeholder="Confirm password" value ="<?php echo isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '' ?>">
                    
                    <label>Profile picture upload</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000">
                    <input name = "profile" type="file" class="form-control" accept="image/*">
                    
                    <br>
                    <label for = "terms">I agree to the terms and conditions</label>
                    <input name = "agree" type = "checkbox" onclick = "document.forms.signup.signupBtn.disabled = !document.forms.signup.signupBtn.disabled">
                    <br>
                    
                    <input name="submit" disabled="disabled" type="submit" id="signupBtn" class="btn btn-primary" value="submit">
                </div>
            </div>
        </form>
    </body>
</html>