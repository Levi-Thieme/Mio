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
    static function createFriendDiv($username) {
        $htmlContent = "<div id=$username class='list-group-item' style='background-color: #222'>" . $username .
            "<i class='fa fa-comment fa-fw' aria-hidden='true'></i>" .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createFriendRequestToDiv($username) {
        $htmlContent = "<div id=$username class='list-group-item' style='background-color: #222'>" . $username .
            "<i class='fa fa-comment fa-fw' style='float:right' aria-hidden='true'></i>" .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "<i data-approve-friend-request class='fa fa-plus fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }

    static function createFriendRequestFromDiv($username) {
        $htmlContent = "<div id=$username class='list-group-item' style='background-color: #222'>" . $username .
            "<i data-delete-friend class='fa fa-trash fa-fw' aria-hidden='true'></i>" .
            "</div>";
        return $htmlContent;
    }
}