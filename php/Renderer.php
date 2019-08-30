<?php
/**
 * Created by PhpStorm.
 * User: signo
 * Date: 5/8/2019
 * Time: 12:13 AM
 */

class Renderer {

    static function divWrap($content) {
        return "<div>" . $content . "</div>";
    }

    static function listGroupItem($content) {
        return "<li class='list-group-item'>" . $content . "</li>";
    }

    static function createFriendDiv($username) {
        $htmlContent = "<div id=$username class='friendDiv'>". $username .
            "<i class='fa fa-comment fa-fw' aria-hidden='true'></i>" .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createFriendRequestToDiv($username) {
        $htmlContent = "<div id=$username class='friendDiv'>" . $username .
            "<i class='fa fa-comment fa-fw' style='float:right' aria-hidden='true'></i>" .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "<i data-approve-friend-request class='fa fa-plus fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createFriendRequestFromDiv($username) {
        $htmlContent = "<div id=$username class='friendDiv'>" . $username .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createRoomDiv($roomId, $roomName) {
        return "<div id=$roomId class='list-group-item roomItem' data-to-room style='background-color: #222; color:white'>" .
            "<span>$roomName</span>" .
            "<i data-leave-room class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "<i data-add-to-room class='fa fa-plus fa-fw' aria-hidden='true'></i></div>";
    }
}