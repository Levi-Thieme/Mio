<?php
    class BaseHandler {
        private $next;
        function setNext($next) {
            $this->next = $next;
        }
        function getNext() {
            return $this->next;
        }
    }