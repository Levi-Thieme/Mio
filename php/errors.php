
<?php
/*
This file contains functions for presenting error messages on webpages.
*/
function printError($msg, $color) {
    echo "<p style=color:$color text-align:center> $msg </p>"; 
}

/*
Error handling function for the myAccount page. 
Prints the error associated with $errorToDisplay.
*/
function myAccountErrorHandler($errorToDisplay) {
    $errorMessages = array(
        "emailError" => "Your email address is invalid.",
        "passError" => "Your password is incorrect.",
        "passConfirmError" => "Your passwords do not match.",
        "userError" => "Your username is incorrect.",
        "confirmNotChecked" => "You must check the confirm checkbox.",
        "tryAgain" => "Please try again."
        );
    
    $errorMessage = $errorMessages[$errorToDisplay];
    if ($errorMessage) {
        printError($errorMessage, "red");
        printError($errorMessages["tryAgain"], "red");
    }
}


?>
