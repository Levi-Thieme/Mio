
$(document).ready(function validate() {
    
    /*
    Updates the email address for this account.
    */
    $("#signupBtn").click(function validate() {
        let usernameValid = validUsername($("#username").val());
        let emailValid = validEmail($("#email").val());
        let passwordValid = validPassword("username", $("#password").val());
        if ((usernameValid && emailValid) && passwordValid) {
            alert("Your credentials have been recorded. Loading main page...");
            $("#username").val(""); 
            $("#email").val(""); 
            $("#password").val("");
        }
        else if((!usernameValid) && (!emailValid) && (!passwordValid)) { //invalid email and invalid password
            alert("Please enter your information.");
            $("#username").val("");
            $("#email").val("");
            $("#password").val("");
        }
        else if(!usernameValid) {
            alert("Your username is invalid.");
            $("#username").val("");
        }
        else if (!emailValid) { //invalid email and valid password
            alert("Your email address is invalid. Please try again.");
            $("#email").val("");
        }
        else if(!passwordValid) { //valid email and invalid password
            alert("Your password is incorrect.");
            $("#password").val("");
        }
        
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