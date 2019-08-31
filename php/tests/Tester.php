<?php

/*
 * The tester class provides functions for executing test functions, and
 * outputting their pass/fail statuses.
 */
class Tester {
    /*
    * Prints the results of the test.
    */
    public static function printTestResult($function, $success) {
        if ($success) {
            echo("<div style='color: green'>$function passed!</div><br>");
        }
        else {
            echo("<div style='color: red'>$function failed! :(</div><br>");
        }
    }

    /*
     * Print text in a div with a break element afterwards
     */
    public static function p($text) {
        echo("<div> $text </div>");
    }

    /*
     * Runs each test in $functionsToTest.
     * Each test must return true or false to indicate whether or not it passed.
     */
    public static function runTests($testsToRun) {
        foreach ($testsToRun as $test) {
            Tester::p("<div style='color: blue'>Running $test test...</div>");
            Tester::printTestResult($test, $test());
        }
    }


}
