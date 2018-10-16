
$(document).ready(function () {
    
    /*
    Updates the email address for this account.
    */
    $("#updateEmailButtton").click(function() {
        let emailValid = validEmail($("#updateEmailEmailInput").val());
        let passwordValid = validPassword("username", $("#updateEmailPasswordInput").val());
        if (emailValid && passwordValid) {
            alert("Your email address has been succesfully updated.");
            $("#updateEmailEmailInput").val("");
            $("#updateEmailPasswordInput").val("");
        }
        else if (!emailValid && passwordValid) { //invalid email and valid password
            alert("Your email address is invalid. Please try again.");
            $("#updateEmailPasswordInput").val("");
        }
        else if(emailValid && !passwordValid) { //valid email and invalid password
            alert("Your password is incorrect.");
            $("#updateEmailPasswordInput").val("");
        }
        else if(!emailValid && !passwordValid) { //invalid email and invalid password
            alert("Your email and password are invalid.");
            $("#updateEmailPasswordInput").val("");
        }
    });

    /*
    Updates the password for this account.
    */
    $("#updatePasswordBtn").click(function() {
        alert("You have successfully updated your password.");
    });
    
    /*
    Deletes the user's account
    */
    $("#deleteAccountBtn").click(function() {
        let username = $("#deleteUsername").val();
        let password = $("#deletePassword").val();
        let passwordConfirm = $("#deletePasswordConfirm").val();
        if (!validUsername(username)) {
            alert("Invalid username.");
            $("#deletePassword").val("");
            $("#deletePasswordConfirm").val("");
            return false;
        }
        else if (!validPassword(username, password)) {
            alert("Incorrect password.");
            $("#deletePassword").val("");
            $("#deletePasswordConfirm").val("");
            return false;
        }
        else if (password != passwordConfirm) {
            alert("Passwords do not match.");
            $("#deletePassword").val("");
            $("#deletePasswordConfirm").val("");
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



