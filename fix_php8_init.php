<?php
/**
 * TestLink PHP 8 Init Fix
 * This script fixes the php8_init.php file without trying to redeclare strftime
 */

// Function to log messages
function log_message($message) {
    echo "$message\n";
}

log_message("Starting php8_init.php fix...");

// Target path (actual TestLink installation)
$targetPath = 'D:/xampp/htdocs/tl-uat';
log_message("Target path: $targetPath");

// Find the php8_init.php file
$initFile = $targetPath . '/custom/inc/php8_init.php';

if (!is_dir(dirname($initFile))) {
    mkdir(dirname($initFile), 0755, true);
    log_message("Created directory: " . dirname($initFile));
}

// Create a fixed php8_init.php without redeclaring strftime
$initContent = "<?php\n/**\n * PHP 8 Compatibility Initialization\n * This file contains all necessary functions and settings for PHP 8 compatibility\n */\n\n// Disable deprecation warnings\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\nini_set('display_errors', 0);\n\n// Fix for strftime() deprecation\nif (!function_exists('safe_strftime')) {\n    function safe_strftime(\$format, \$timestamp = null) {\n        if (\$timestamp === null) {\n            \$timestamp = time();\n        }\n        \n        // Simple mapping of common format codes\n        \$map = [\n            '%Y' => 'Y', // Year with century\n            '%y' => 'y', // Year without century\n            '%m' => 'm', // Month as decimal number\n            '%d' => 'd', // Day of the month\n            '%H' => 'H', // Hour (24-hour clock)\n            '%M' => 'i', // Minute\n            '%S' => 's', // Second\n            '%a' => 'D', // Abbreviated weekday name\n            '%A' => 'l', // Full weekday name\n            '%b' => 'M', // Abbreviated month name\n            '%B' => 'F', // Full month name\n        ];\n        \n        \$dateFormat = \$format;\n        foreach (\$map as \$from => \$to) {\n            \$dateFormat = str_replace(\$from, \$to, \$dateFormat);\n        }\n        \n        return date(\$dateFormat, \$timestamp);\n    }\n}\n\n// We can't redefine strftime() because it still exists in PHP 8.1+ even though it's deprecated\n// Instead, just use safe_strftime() where needed\n\n// Fix for curly brace syntax in strings\nif (!function_exists('fix_curly_syntax')) {\n    function fix_curly_syntax(\$string) {\n        return str_replace('\${', '{\$', \$string);\n    }\n}\n\n// Set default timezone if not already set\nif (function_exists('date_default_timezone_get')) {\n    date_default_timezone_set(@date_default_timezone_get());\n}\n\n// Polyfill for get_magic_quotes_gpc() if needed\nif (!function_exists('get_magic_quotes_gpc')) {\n    function get_magic_quotes_gpc() {\n        return false; // Magic quotes were removed in PHP 7.0\n    }\n}\n\n// Polyfill for get_magic_quotes_runtime() if needed\nif (!function_exists('get_magic_quotes_runtime')) {\n    function get_magic_quotes_runtime() {\n        return false; // Magic quotes were removed in PHP 7.0\n    }\n}\n\n// Polyfill for each() if needed\nif (!function_exists('each')) {\n    function each(&\$array) {\n        \$key = key(\$array);\n        if (\$key !== null) {\n            \$value = \$array[\$key];\n            next(\$array);\n            return array(1 => \$value, 'value' => \$value, 0 => \$key, 'key' => \$key);\n        }\n        return false;\n    }\n}\n";

file_put_contents($initFile, $initContent);
log_message("Created fixed php8_init.php without redeclaring strftime");

// Create a test file to verify the functions
$testFile = $targetPath . '/test_strftime.php';
$testContent = "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');\n\necho '<h1>TestLink strftime() Test</h1>';\n\necho '<p>PHP Version: ' . phpversion() . '</p>';\n\n// Test if safe_strftime is defined\necho '<p>safe_strftime() defined: ' . (function_exists('safe_strftime') ? 'Yes' : 'No') . '</p>';\n\n// Test safe_strftime\necho '<p>Current date with safe_strftime(): ' . safe_strftime('%Y-%m-%d') . '</p>';\n\n// Test original strftime (which is still available but deprecated)\necho '<p>Current date with original strftime(): ' . @strftime('%Y-%m-%d') . '</p>';\n\necho '<p>Done testing.</p>';\n";

file_put_contents($testFile, $testContent);
log_message("Created test_strftime.php file at: $testFile");
log_message("Access it at: http://localhost/tl-uat/test_strftime.php");

// Fix any files that use strftime
log_message("\nFixing files that use strftime()...");

// 1. Fix config.inc.php
$configFile = $targetPath . '/config.inc.php';
if (file_exists($configFile)) {
    // Create backup if doesn't exist
    if (!file_exists($configFile . '.bak')) {
        copy($configFile, $configFile . '.bak');
        log_message("Created backup of config.inc.php");
    }
    
    // Read the file
    $lines = file($configFile);
    
    // Look for strftime around line 2026
    $lineNumber = 2025; // Line 2026 (0-indexed)
    $found = false;
    
    // Check 10 lines around the reported line
    for ($i = max(0, $lineNumber - 5); $i <= min(count($lines) - 1, $lineNumber + 5); $i++) {
        if (strpos($lines[$i], 'strftime') !== false) {
            log_message("Found strftime() on line " . ($i + 1) . ": " . trim($lines[$i]));
            
            // Just comment out the line
            $originalLine = $lines[$i];
            $lines[$i] = '// ' . $originalLine . ' // Commented out due to PHP 8 deprecation\n';
            
            log_message("Commented out line " . ($i + 1));
            $found = true;
        }
    }
    
    if ($found) {
        // Write the modified content back to the file
        file_put_contents($configFile, implode('', $lines));
        log_message("Updated config.inc.php");
    } else {
        log_message("Could not find strftime() around line 2026 in config.inc.php");
    }
} else {
    log_message("config.inc.php not found at: $configFile");
}

// Fix index.php if it has the display_errors() issue
$indexFile = $targetPath . '/index.php';
if (file_exists($indexFile)) {
    // Create backup if doesn't exist
    if (!file_exists($indexFile . '.bak')) {
        copy($indexFile, $indexFile . '.bak');
        log_message("Created backup of index.php");
    }
    
    // Read the file
    $content = file_get_contents($indexFile);
    
    // Replace display_errors() with ini_set
    if (strpos($content, 'display_errors()') !== false) {
        $content = str_replace('display_errors()', "ini_set('display_errors', 0)", $content);
        file_put_contents($indexFile, $content);
        log_message("Fixed display_errors() function call in index.php");
    }
}

log_message("\nFix complete! Please restart your web server and clear your browser cache!");
log_message("Then access the test page: http://localhost/tl-uat/test_strftime.php");
