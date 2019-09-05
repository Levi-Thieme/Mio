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
            return ("<div style='color: green'>$function passed!</div><br>");
        }
        else {
            return ("<div style='color: red'>$function failed! :(</div><br>");
        }
    }

    /*
     * Print text in a div with a break element afterwards
     */
    public static function p($text) {
        return ("<div> $text </div>");
    }

    /*
     * Runs each test in $functionsToTest.
     * Each test must return true or false to indicate whether or not it passed.
     */
    public static function runTests($class, $testsToRun) {
        $results = array();
        foreach ($testsToRun as $test) {
            $results[] = Tester::p("<div style='color: blue'>Running $test test...</div>");
            $results[] = Tester::printTestResult($test, $class->$test());
        }
        return $results;
    }


}
