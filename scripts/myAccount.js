



$(document).ready(function () {
    /*
    Handles client side verification of update email for the myAccount page.
    */
    function changeEmail(e) {
        //Get username and password
        let emailValid = validEmail($("#updateEmailEmailInput").val());
        let passwordValid = validPassword($("#updateEmailPasswordInput").val());
        //Validate email and password
        if (emailValid && passwordValid) {
            return true;
        }
        alert("HI");
        return false;
    }
        
    //Bind changeEmail validation function to the change email form.
    ///$("changeEmailForm").on("submit", changeEmail);

    /*
    Handles client side verification of update password for the myAccount page.
    */
    function updatePassword(e) {
        let password = $("#updatePasswordPasswordInput").val();
        let newPassword = $("#newPassword").val();
        let newPasswordConfirm = $("#newPasswordConfirm").val();
        
        if (password == "") {
            alert("You must enter your password.");
            return false;
        }
        else if (newPassword == "") {
            alert("You must enter a new password.");
            return false;
        }
        else if (newPasswordConfirm == "") {
            alert("You must enter a confirmation for your new password.");
            return false;
        }
        else if (newPassword != newPasswordConfirm) {
            alert("Your new password does not match its confirmation.");
            return false;
        }
        else if (newPassword == password) {
            alert("Your new password must be different from your old password.");
            return false;
        }
        else {
            alert("Your password has been successfully updated.");
            return true;
        }
    }
    
    //$("changePasswordForm").on("submit", updatePassword);
    
    /*
    Handles client side verification of update password for the myAccount page.
    */
    function deleteAccount() {
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
    }
    
    //$("deleteAccountForm").on("submit", deleteAccount);
    
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
    function validPassword(password) {
        return password != "";
    }
    
    /*
    Verifies username is associated with the currently logged in account
    */
    function validUsername(username) {
        return username != "";
    }
});



