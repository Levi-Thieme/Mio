    /*
    Validates the user's username, email, and password.
    Returns true if all are valid.
    */
    function validateSignup() {
        let usernameValid = validUsername($("#username").val());
        let emailValid = validEmail($("#email").val());
        let passwordValid = validPassword($("#password").val());

        if(!usernameValid) { //invalid username
            $("#username").val("");
            $("#password").val("");
            $("#confirmPassword").val("");
            return false;
        }
        else if(!emailValid) { //invalid email
            $("#password").val("");
            $("#confirmPassword").val("");
            return false;
        }
        else if (!passwordValid) { //invalid password
            $("#password").val("");
            $("#confirmPassword").val("");
            return false;
        }
        else if ($("#password").val() !== $("#confirmPassword").val()) {
            $("#password").val("");
            $("#confirmPassword").val("");
            return false;
        }
        return true;
    }
    
    /*
    Returns true if email is a valid email address.
    */
    function validEmail(email) {
        var emailRegex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        return email.match(emailRegex);
    }
    
    /*
    Returns true if the password meets the password requirements for length
    */
    function validPassword(password) {
        return password.trim().length > 6;
    }
    
    /*
    Returns true if the username meets the username requirements for length
    */
    function validUsername(username) {
        return username.trim().length > 2;
    }