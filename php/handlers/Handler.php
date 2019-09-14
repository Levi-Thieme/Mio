<?php
interface Handler {
    function canHandle($event);
    function handle($event);
}