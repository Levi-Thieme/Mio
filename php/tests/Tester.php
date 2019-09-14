<?php

/*
 * The tester class provides function(s) for executing tests, and outputting the result.
 */
class Tester {

    /*
     * Runs each test in $functionsToTest.
     * Each test must return true or false to indicate whether or not it passed.
     */
    public static function echoTestResults($tests) {
        $testResults = array();
        foreach ($tests as $test) {
            $testResults[$test] = $test();
        }
        echo json_encode($testResults, JSON_PRETTY_PRINT);
    }
}
