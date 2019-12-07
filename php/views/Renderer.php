<?php
class Renderer {

    static function divWrap($content) {
        return "<div>" . $content . "</div>";
    }

    static function listGroupItem($content) {
        return "<li class='list-group-item'>" . $content . "</li>";
    }

    static function createFriendDiv($username) {
        $htmlContent = "<div id=$username class='friendDiv animated zoomIn'>". $username .
            "<i class='fa fa-comment fa-fw' aria-hidden='true'></i>" .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createFriendRequestToDiv($username) {
        $htmlContent = "<div id=$username class='friendDiv animated zoomIn'>" . $username .
            "<i class='fa fa-comment fa-fw' style='float:right' aria-hidden='true'></i>" .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "<i data-approve-friend-request class='fa fa-plus fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createFriendRequestFromDiv($username) {
        $htmlContent = "<div id=$username class='friendDiv animated zoomIn'>" . $username .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createRoomDiv($roomId, $roomName) {
        return "<div id=$roomId class='list-group-item roomItem animated zoomIn' data-to-room style='background-color: #222; color:white'>" .
            "<span>$roomName</span>" .
            "<i data-leave-room class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "<i data-add-to-room class='fa fa-plus fa-fw' aria-hidden='true'></i></div>";
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
            printError($errorMessage, "red");
            printError($errorMessages["tryAgain"], "red");
        }
    }
}