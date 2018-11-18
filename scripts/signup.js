function validate() {
    
    $("#signupBtn").click(function validate() {
        let usernameValid = validUsername($("#username").val());
        let emailValid = validEmail($("#email").val());
        let passwordValid = validPassword("username", $("#password").val());

        if(!usernameValid) { //invalid username
            alert("Please enter a valid username.");
            $("#username").val("");
            $("#password").val("");
            return false;
        }
        else if(!emailValid) { //invalid email
            alert("Please enter a valid email address.");
            $("#password").val("");
            return false;
        }
        else if (!passwordValid) { //invalid password
            alert("Please enter a valid password.");
            $("#password").val("");
            return false;
        }
        return true;
    });
    /*
    Returns true if email is a valid email address.
    */
    function validEmail(email) {
        var emailRegex = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
        return email.match(emailRegex);
    }
    
    /*
    Returns true if username and password match.
    This will be properly implemented in the future when accounts are
    properly stored in a database with their passwords' salts and hashes.
    */
    function validPassword(username, password) {
        return password != "";
    }
    
    /*
    Verifies username is associated with the currently logged in account
    */
    function validUsername(username) {
        return username != "";
    }
});