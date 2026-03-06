<?php
/**
 * PHP 8 Compatibility Functions for TestLink
 * These functions provide compatibility with PHP 8 for TestLink code
 * that may be using deprecated or removed features.
 */

/**
 * Override the database fetchArrayRowsIntoMap method to make it PHP 8 compatible
 * This gets monkey patched into the database class.
 */
function php8_fetchArrayRowsIntoMap($db, $sql, $column_name_key)
{
    $result = $db->exec_query($sql);
    $recordset = null;
    if ($result) {
        $recordset = array();
        while ($row = $db->fetch_array($result)) {
            // Make sure we have the key before trying to use it as an array index
            if (isset($row[$column_name_key])) {
                $recordset[$row[$column_name_key]] = $row;
            }
        }
    }
    return !empty($recordset) ? $recordset : null;
}

/**
 * Safe version of array_key_exists that works with both arrays and objects
 * In PHP 8, array_key_exists() no longer works with objects
 * 
 * @param string|int $key Key to check for
 * @param array|object $array_or_object Array or object to check in
 * @return bool True if the key exists, false otherwise
 */
function tl_array_key_exists($key, $array_or_object) {
    if (is_array($array_or_object)) {
        return array_key_exists($key, $array_or_object);
    } elseif (is_object($array_or_object)) {
        return property_exists($array_or_object, $key);
    }
    return false;
}

/**
 * Safe version of isset that handles potential null values in arrays
 * without warnings in PHP 8
 * 
 * @param mixed $var The variable to check
 * @param string|int $key Optional key for arrays/objects
 * @return bool True if set and not null, false otherwise
 */
function tl_isset(&$var, $key = null) {
    if (!isset($var)) {
        return false;
    }
    
    if ($key !== null) {
        if (is_array($var)) {
            return isset($var[$key]);
        } elseif (is_object($var)) {
            return isset($var->$key);
        }
        return false;
    }
    
    return true;
}

/**
 * Polyfill for PHP 8's str_contains function
 * for PHP 7.x compatibility
 */
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

/**
 * Safe alternative to using count() on potentially non-countable items
 * 
 * @param mixed $item The variable to count
 * @return int The count or 0 if not countable
 */
function tl_count($item) {
    if (is_array($item) || (is_object($item) && $item instanceof Countable)) {
        return count($item);
    }
    return 0;
}

/**
 * Handles the deprecated get_magic_quotes_gpc() function
 * Always returns false in PHP 8+
 * 
 * @return bool Always false in PHP 8+
 */
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() {
        return false;
    }
}
