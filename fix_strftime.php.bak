<?php
/**
 * Fix for strftime() deprecation in PHP 8.1+
 * This script replaces the deprecated strftime() function in config.inc.php
 */

// Function to log messages
function log_message($message) {
    echo $message . "\n";
}

log_message("Starting strftime() deprecation fix...");

// Find the config file
$basePath = __DIR__;
$configFile = $basePath . '/config.inc.php';

if (!file_exists($configFile)) {
    log_message("ERROR: Could not find config.inc.php file. Please specify the correct path manually.");
    exit(1);
}

// Create a backup
$backupFile = $configFile . '.bak';
if (!file_exists($backupFile)) {
    copy($configFile, $backupFile);
    log_message("Created backup at: $backupFile");
}

// Read the file content
$content = file_get_contents($configFile);
if ($content === false) {
    log_message("ERROR: Failed to read config.inc.php file");
    exit(1);
}

// Find the line with strftime
$lines = file($configFile);
$found = false;
$lineNumber = 0;

foreach ($lines as $i => $line) {
    if (strpos($line, 'strftime') !== false) {
        log_message("Found strftime() on line " . ($i + 1) . ": " . trim($line));
        $found = true;
        $lineNumber = $i;
    }
}

if (!$found) {
    log_message("strftime() not found in config.inc.php. The warning might be coming from a different file.");
} else {
    // Replace strftime with date
    $originalLine = $lines[$lineNumber];
    
    // Check if this is the line we're looking for (around line 2024)
    if ($lineNumber >= 2023 && $lineNumber <= 2025) {
        // Extract the format string from strftime() call
        if (preg_match('/strftime\(\s*[\'"](.*?)[\'"]/i', $originalLine, $matches)) {
            $formatStr = $matches[1];
            log_message("Found format string: $formatStr");
            
            // Create a mapping from strftime to date format codes
            $strftimeToDate = [
                '%a' => 'D',      // Abbreviated weekday name
                '%A' => 'l',      // Full weekday name
                '%b' => 'M',      // Abbreviated month name
                '%B' => 'F',      // Full month name
                '%c' => 'D M j H:i:s Y', // Preferred date and time representation
                '%d' => 'd',      // Day of the month as a decimal number
                '%H' => 'H',      // Hour (24-hour clock)
                '%I' => 'h',      // Hour (12-hour clock)
                '%j' => 'z',      // Day of the year
                '%m' => 'm',      // Month as a decimal number
                '%M' => 'i',      // Minute
                '%p' => 'A',      // AM or PM
                '%S' => 's',      // Second
                '%U' => 'W',      // Week number of the year (Sunday as first day of week)
                '%w' => 'w',      // Weekday as a decimal number
                '%W' => 'W',      // Week number of the year (Monday as first day of week)
                '%x' => 'm/d/y',  // Preferred date representation without the time
                '%X' => 'H:i:s',  // Preferred time representation without the date
                '%y' => 'y',      // Year without century
                '%Y' => 'Y',      // Year with century
                '%Z' => 'T',      // Time zone name
                '%%' => '%'       // Literal % character
            ];
            
            // Convert the strftime format to date format
            $dateFormat = $formatStr;
            foreach ($strftimeToDate as $strftime => $date) {
                $dateFormat = str_replace($strftime, $date, $dateFormat);
            }
            
            // Replace the strftime call with date call
            $newLine = str_replace(
                "strftime('$formatStr'", 
                "date('$dateFormat'", 
                $originalLine
            );
            
            $lines[$lineNumber] = $newLine;
            
            // Write the modified content back to the file
            file_put_contents($configFile, implode('', $lines));
            log_message("Successfully replaced strftime() with date() on line " . ($lineNumber + 1));
        } else {
            log_message("WARNING: Could not extract format string from strftime() call");
        }
    } else {
        log_message("WARNING: Found strftime() but not on expected line number (around 2024). Manual inspection may be required.");
    }
}

// Create or update the polyfill for strftime
$polyfillFile = $basePath . '/custom/inc/strftime_polyfill.php';
$polyfillContent = "<?php\n/**\n * Polyfill for strftime() in PHP 8.1+\n * Since strftime() is deprecated in PHP 8.1, this file provides a compatible replacement\n */\n\nif (!function_exists('strftime_replacement')) {\n    /**\n     * A replacement for the deprecated strftime() function\n     * @param string \$format The format string\n     * @param int|null \$timestamp The timestamp to format\n     * @return string The formatted date string\n     */\n    function strftime_replacement(\$format, \$timestamp = null) {\n        if (\$timestamp === null) {\n            \$timestamp = time();\n        }\n        \n        // Mapping from strftime to date format codes\n        \$strftimeToDate = [\n            '%a' => 'D',      // Abbreviated weekday name\n            '%A' => 'l',      // Full weekday name\n            '%b' => 'M',      // Abbreviated month name\n            '%B' => 'F',      // Full month name\n            '%c' => 'D M j H:i:s Y', // Preferred date and time representation\n            '%d' => 'd',      // Day of the month as a decimal number\n            '%H' => 'H',      // Hour (24-hour clock)\n            '%I' => 'h',      // Hour (12-hour clock)\n            '%j' => 'z',      // Day of the year\n            '%m' => 'm',      // Month as a decimal number\n            '%M' => 'i',      // Minute\n            '%p' => 'A',      // AM or PM\n            '%S' => 's',      // Second\n            '%U' => 'W',      // Week number of the year (Sunday as first day of week)\n            '%w' => 'w',      // Weekday as a decimal number\n            '%W' => 'W',      // Week number of the year (Monday as first day of week)\n            '%x' => 'm/d/y',  // Preferred date representation without the time\n            '%X' => 'H:i:s',  // Preferred time representation without the date\n            '%y' => 'y',      // Year without century\n            '%Y' => 'Y',      // Year with century\n            '%Z' => 'T',      // Time zone name\n            '%%' => '%'       // Literal % character\n        ];\n        \n        // Convert the strftime format to date format\n        \$dateFormat = \$format;\n        foreach (\$strftimeToDate as \$strftime => \$date) {\n            \$dateFormat = str_replace(\$strftime, \$date, \$dateFormat);\n        }\n        \n        return date(\$dateFormat, \$timestamp);\n    }\n}\n\n// If strftime exists (PHP < 8.1), use it; otherwise use our replacement\nif (!function_exists('safe_strftime')) {\n    function safe_strftime(\$format, \$timestamp = null) {\n        if (function_exists('strftime')) {\n            return @strftime(\$format, \$timestamp);\n        } else {\n            return strftime_replacement(\$format, \$timestamp);\n        }\n    }\n}\n\n// Override the original strftime if it doesn't exist\nif (!function_exists('strftime')) {\n    function strftime(\$format, \$timestamp = null) {\n        return strftime_replacement(\$format, \$timestamp);\n    }\n}\n";

file_put_contents($polyfillFile, $polyfillContent);
log_message("Created strftime polyfill at: $polyfillFile");

// Update the PHP 8 initialization file to include the strftime polyfill
$initFile = $basePath . '/custom/inc/php8_init.php';
if (file_exists($initFile)) {
    $initContent = file_get_contents($initFile);
    if (strpos($initContent, 'strftime_polyfill.php') === false) {
        $newInitContent = str_replace(
            "require_once(__DIR__ . '/php8_compatibility.php');",
            "require_once(__DIR__ . '/php8_compatibility.php');\nrequire_once(__DIR__ . '/strftime_polyfill.php');",
            $initContent
        );
        file_put_contents($initFile, $newInitContent);
        log_message("Updated php8_init.php to include strftime polyfill");
    } else {
        log_message("php8_init.php already includes strftime polyfill");
    }
} else {
    log_message("WARNING: Could not find php8_init.php");
}

log_message("\nFix complete! Please copy these files to your actual TestLink installation:");
log_message("1. The fixed config.inc.php file");
log_message("2. custom/inc/strftime_polyfill.php");
log_message("3. The updated custom/inc/php8_init.php file");
log_message("\nThen restart your web server and clear your browser cache.");
