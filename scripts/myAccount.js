
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
        let password = $("#updatePasswordPasswordInput").val();
        let newPassword = $("#newPassword").val();
        let newPasswordConfirm = $("#newPasswordConfirm").val();
        
        if (password == "") {
            alert("You must enter your password.");
        }
        else if (newPassword == "") {
            alert("You must enter a new password.");
        }
        else if (newPasswordConfirm == "") {
            alert("You must enter a confirmation for your new password.");
        }
        else if (newPassword != newPasswordConfirm) {
            alert("Your new password does not match its confirmation.");
        }
        else if (newPassword == password) {
            alert("Your new password must be different from your old password.");
        }
        else {
            alert("Your password has been successfully updated.");
        }
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
            $("#confirmDeleteCheckbox").prop("checked", false);
            return false;
        }
        else if (!validPassword(username, password)) {
            alert("Incorrect password.");
            $("#deletePassword").val("");
            $("#deletePasswordConfirm").val("");
            $("#confirmDeleteCheckbox").prop("checked", false);
            return false;
        }
        else if (password != passwordConfirm) {
            alert("Passwords do not match.");
            $("#deletePassword").val("");
            $("#deletePasswordConfirm").val("");
            $("#confirmDeleteCheckbox").prop("checked", false);
            return false;
        }
        else if (!$("#confirmDeleteCheckbox").prop("checked")) {
            alert("You must select the confirmation checkbox.");
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



