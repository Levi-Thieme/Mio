<?php

class ArrayUtils {
    /*
    Returns true if the given array contains $key as a key with a value
    */
    public static function hasKeyWithNonEmptyValue($array, $key) {
        return array_key_exists($key, $array) && !empty($array[$key]);
    }

    /*
    Returns true if the given array contains all the keys contained in $keys,
    and every key has a value that is not empty.
    */
    public static function hasKeysWithNonEmptyValues($array, $keys) {
        foreach ($keys as $key) {
            if (ArrayUtils::hasKeyWithNonEmptyValue($array, $key) === false) {
                return false;
            }
        }
        return true;
    }
}