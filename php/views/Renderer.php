<?php
class Renderer {

    static function divWrap($content) {
        return "<div>" . $content . "</div>";
    }

    static function listGroupItem($content) {
        return "<li class='list-group-item'>" . $content . "</li>";
    }

    static function printError($msg, $color) {
        echo "<p style=color:$color text-align:center> $msg </p>";
    }

    /*
    Error handling function for the myAccount page.
    Prints the error associated with $errorToDisplay.
    */
    static function myAccountErrorHandler($errorToDisplay) {
        $errorMessages = array(
            "emailError" => "Your email address is invalid.",
            "passError" => "Your password is incorrect.",
            "passConfirmError" => "Your passwords do not match.",
            "userError" => "Your username is incorrect.",
            "confirmNotChecked" => "You must check the confirm checkbox.",
            "tryAgain" => "Please try again."
        );
        if ($errorToDisplay === "") {
            return;
        }
        $errorMessage = $errorMessages[$errorToDisplay];
        if ($errorMessage) {
            Renderer::printError($errorMessage, "red");
            Renderer::printError($errorMessages["tryAgain"], "red");
        }
    }
}