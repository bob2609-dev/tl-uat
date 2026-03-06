<?php
/**
 * PHP 8 Compatibility Initialization
 * This file contains all necessary functions and settings for PHP 8 compatibility
 */

// Disable deprecation warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

// Fix for strftime() deprecation
if (!function_exists('safe_strftime')) {
    function safe_strftime($format, $timestamp = null) {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        // Simple mapping of common format codes
        $map = [
            '%Y' => 'Y', // Year with century
            '%y' => 'y', // Year without century
            '%m' => 'm', // Month as decimal number
            '%d' => 'd', // Day of the month
            '%H' => 'H', // Hour (24-hour clock)
            '%M' => 'i', // Minute
            '%S' => 's', // Second
            '%a' => 'D', // Abbreviated weekday name
            '%A' => 'l', // Full weekday name
            '%b' => 'M', // Abbreviated month name
            '%B' => 'F', // Full month name
        ];
        
        $dateFormat = $format;
        foreach ($map as $from => $to) {
            $dateFormat = str_replace($from, $to, $dateFormat);
        }
        
        return date($dateFormat, $timestamp);
    }
}

// We can't redefine strftime() because it still exists in PHP 8.1+ even though it's deprecated
// Instead, just use safe_strftime() where needed

// Fix for curly brace syntax in strings
if (!function_exists('fix_curly_syntax')) {
    function fix_curly_syntax($string) {
        return str_replace('${', '{$', $string);
    }
}

// Fix for database fetchArrayRowsIntoMap method in PHP 8
if (!function_exists('php8_fetchArrayRowsIntoMap')) {
    function php8_fetchArrayRowsIntoMap($db, $sql, $column_name_key, $cumulative = false) {
        $result = $db->exec_query($sql);
        $recordset = null;
        if ($result) {
            $recordset = array();
            while ($row = $db->fetch_array($result)) {
                // Make sure we have the key before trying to use it as an array index
                if (isset($row[$column_name_key])) {
                    $keyValue = $row[$column_name_key];
                    if ($cumulative) {
                        $recordset[$keyValue][] = $row;
                    } else {
                        $recordset[$keyValue] = $row;
                    }
                }
            }
        }
        return !empty($recordset) ? $recordset : null;
    }
}

// Override database fetchArrayRowsIntoMap method
class_exists('database'); // Make sure database class is loaded
if (!function_exists('override_database_methods')) {
    function override_database_methods() {
        if (method_exists('database', 'fetchArrayRowsIntoMap')) {
            database::$php8_patched = true; // Flag to avoid multiple patching
        }
    }
    // Call the function to register the override
    override_database_methods();
}

// Set default timezone if not already set
if (function_exists('date_default_timezone_get')) {
    date_default_timezone_set(@date_default_timezone_get());
}

// Polyfill for get_magic_quotes_gpc() if needed
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() {
        return false; // Magic quotes were removed in PHP 7.0
    }
}

// Polyfill for get_magic_quotes_runtime() if needed
if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime() {
        return false; // Magic quotes were removed in PHP 7.0
    }
}

// Polyfill for each() if needed
if (!function_exists('each')) {
    function each(&$array) {
        $key = key($array);
        if ($key !== null) {
            $value = $array[$key];
            next($array);
            return array(1 => $value, 'value' => $value, 0 => $key, 'key' => $key);
        }
        return false;
    }
}
