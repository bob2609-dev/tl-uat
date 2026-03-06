<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * PHP 8 Compatibility Layer
 * This file provides compatibility functions for supporting PHP 8
 *
 * @filesource  php8_compatibility.php
 * @package     TestLink
 */

// Disable deprecation warnings if not in development mode
// Uncomment this line in production to hide deprecation warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

// Only define these functions if they don't exist

// PHP 8 removed get_magic_quotes_gpc
if (!function_exists('get_magic_quotes_gpc')) {
    function get_magic_quotes_gpc() {
        // Always return false as magic quotes are removed in PHP 7+
        return false;
    }
}

// PHP 8 removed get_magic_quotes_runtime
if (!function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime() {
        // Always return false as magic quotes are removed in PHP 7+
        return false;
    }
}

// PHP 8 removed each() function
if (!function_exists('each_compat')) {
    function each_compat(&$array) {
        $key = key($array);
        if ($key !== null) {
            $value = $array[$key];
            next($array);
            return array(1 => $value, 'value' => $value, 0 => $key, 'key' => $key);
        }
        return false;
    }
}

// Helper function to safely handle strpos null parameter changes in PHP 8
if (!function_exists('safe_strpos')) {
    function safe_strpos($haystack, $needle, $offset = 0) {
        if ($haystack === null || $needle === null) {
            return false;
        }
        return strpos($haystack, $needle, $offset);
    }
}

// Helper function for safer substr in PHP 8
if (!function_exists('safe_substr')) {
    function safe_substr($string, $start, $length = null) {
        if ($string === null) {
            return false;
        }
        if ($length === null) {
            return substr($string, $start);
        }
        return substr($string, $start, $length);
    }
}

// PHP 8 has stricter handling for implode parameters
if (!function_exists('safe_implode')) {
    function safe_implode($separator, $array) {
        // Handle reversed parameter order that worked in PHP 7 but fails in PHP 8
        if (is_array($separator) && !is_array($array)) {
            // Parameters are likely reversed
            return implode($array, $separator);
        }
        return implode($separator, $array);
    }
}

// PHP 8 has stricter handling of null/false/empty string in string functions
if (!function_exists('safe_trim')) {
    function safe_trim($string, $characters = " \n\r\t\v\0") {
        if ($string === null || $string === false) {
            return '';
        }
        return trim($string, $characters);
    }
}

// PHP 8 deprecated strftime() function
if (!function_exists('safe_strftime')) {
    function safe_strftime($format, $timestamp = null) {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        // Map strftime() format codes to date() format codes
        $formats = [
            '%a' => 'D', // Abbreviated weekday name (Sun through Sat)
            '%A' => 'l', // Full weekday name (Sunday through Saturday)
            '%b' => 'M', // Abbreviated month name (Jan through Dec)
            '%B' => 'F', // Full month name (January through December)
            '%c' => 'D M j H:i:s Y', // Preferred date and time representation
            '%d' => 'd', // Day of the month as a decimal number (01 through 31)
            '%H' => 'H', // Hour as a decimal number using a 24-hour clock (00 through 23)
            '%I' => 'h', // Hour as a decimal number using a 12-hour clock (01 through 12)
            '%j' => 'z', // Day of the year as a decimal number (001 through 366)
            '%m' => 'm', // Month as a decimal number (01 through 12)
            '%M' => 'i', // Minute as a decimal number (00 through 59)
            '%p' => 'A', // Either AM or PM
            '%S' => 's', // Second as a decimal number (00 through 59)
            '%U' => 'W', // Week number of the year (Sunday as the first day of the week)
            '%w' => 'w', // Weekday as a decimal number (0 through 6)
            '%W' => 'W', // Week number of the year (Monday as the first day of the week)
            '%x' => 'm/d/y', // Preferred date representation without the time
            '%X' => 'H:i:s', // Preferred time representation without the date
            '%y' => 'y', // Year as a decimal number without a century (00 through 99)
            '%Y' => 'Y', // Year as a decimal number including the century
            '%Z' => 'T', // Time zone name or abbreviation
            '%%' => '%', // A literal % character
        ];
        
        // Convert strftime format to date format
        $date_format = str_replace(
            array_keys($formats),
            array_values($formats),
            $format
        );
        
        // Try to use strftime if it exists (for backward compatibility)
        if (function_exists('strftime')) {
            return @strftime($format, $timestamp);
        }
        
        // Fall back to date() if strftime doesn't exist or fails
        return date($date_format, $timestamp);
    }
}

// Fix for ${var} in strings being deprecated in PHP 8
if (!function_exists('fix_curly_syntax')) {
    function fix_curly_syntax($string) {
        // Replace ${var} with {$var} in strings
        return preg_replace('/\$\{([a-zA-Z0-9_]+)\}/', '{\$$1}', $string);
    }
}
