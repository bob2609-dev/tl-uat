<?php
/**
 * TestLink PHP 8 Debugging Script
 * This script helps identify issues causing 500 errors
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TestLink PHP 8 Debug</h1>";

// Function to log messages
function log_message($message, $type = 'info') {
    echo "<p style=\"" . ($type == 'error' ? 'color:red;' : 'color:blue;') . "\">$message</p>\n";
}

// Check PHP version
log_message("PHP Version: " . phpversion());

// Try to include the main TestLink files one by one to find which one causes the error
log_message("Testing includes...");

// List of files to check
$filesToCheck = [
    'custom/inc/php8_init.php',
    'custom/inc/strftime_polyfill.php',
    'custom/inc/magic_quotes_polyfill.php',
    'custom/inc/each_polyfill.php',
    'lib/functions/database.class.php',
    'lib/functions/logger.class.php',
    'lib/functions/common.php',
    'lib/functions/string_api.php',
    'third_party/adodb/adodb.inc.php'
];

// Base path
$basePath = __DIR__;

foreach ($filesToCheck as $file) {
    $fullPath = $basePath . '/' . $file;
    if (file_exists($fullPath)) {
        try {
            log_message("Including: $file");
            include_once($fullPath);
            log_message("✓ Successfully included $file");
        } catch (Throwable $e) {
            log_message("✗ Error including $file: " . $e->getMessage(), 'error');
            log_message("  Error in file: " . $e->getFile() . " on line " . $e->getLine(), 'error');
            // Show the problematic code
            if (file_exists($e->getFile())) {
                $lines = file($e->getFile());
                $lineNumber = $e->getLine() - 1;
                $start = max(0, $lineNumber - 5);
                $end = min(count($lines) - 1, $lineNumber + 5);
                
                echo "<pre style=\"background-color:#f5f5f5; padding:10px;\">";
                for ($i = $start; $i <= $end; $i++) {
                    $highlight = ($i == $lineNumber) ? 'background-color:#ffcccc;' : '';
                    echo "<div style=\"$highlight\">" . ($i + 1) . ": " . htmlspecialchars($lines[$i]) . "</div>";
                }
                echo "</pre>";
            }
        }
    } else {
        log_message("File not found: $file", 'error');
    }
}

// Check for specific error logs
log_message("Checking error logs...");
$errorLogFile = ini_get('error_log');
if ($errorLogFile && file_exists($errorLogFile)) {
    log_message("Error log file: $errorLogFile");
    // Get the last 20 lines of the error log
    $errorLog = file($errorLogFile);
    $errorLog = array_slice($errorLog, -20);
    
    echo "<pre style=\"background-color:#f5f5f5; padding:10px;\">";
    foreach ($errorLog as $line) {
        echo htmlspecialchars($line);
    }
    echo "</pre>";
} else {
    log_message("Error log file not found");
}

// Check for specific PHP configuration issues
log_message("Checking PHP configuration...");
echo "<table border=\"1\" cellpadding=\"5\" style=\"border-collapse:collapse;\">";
echo "<tr><th>Setting</th><th>Value</th></tr>";
$phpSettings = [
    'display_errors',
    'error_reporting',
    'memory_limit',
    'post_max_size',
    'upload_max_filesize',
    'max_execution_time',
    'max_input_time',
    'date.timezone'
];

foreach ($phpSettings as $setting) {
    echo "<tr><td>$setting</td><td>" . ini_get($setting) . "</td></tr>";
}
echo "</table>";

// Recommendations to fix issues
echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Check server error logs for more detailed information</li>";
echo "<li>Verify that the correct php8_init.php file is being included</li>";
echo "<li>Make sure all polyfill files are in the correct location</li>";
echo "<li>Restore .bak files if you need to revert changes</li>";
echo "<li>Try commenting out sections of code in the initialization files to isolate the issue</li>";
echo "<li>Check for syntax errors in recently modified files</li>";
echo "</ol>";

echo "<p>You can also try accessing the application with error display enabled to see the actual error message:</p>";
echo "<code>?display_errors=1</code>";
