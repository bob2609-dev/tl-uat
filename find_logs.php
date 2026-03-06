<?php
// Script to help find and display error logs

echo "<h1>PHP Error Log Locations</h1>";

// Get loaded php.ini file
echo "<p>Loaded php.ini file: " . php_ini_loaded_file() . "</p>";

// Get error_log setting
echo "<p>error_log setting: " . ini_get('error_log') . "</p>";

// Check common log locations
$possibleLogLocations = array(
    ini_get('error_log'),
    'C:/xampp/apache/logs/error.log',
    'C:/wamp/logs/apache_error.log',
    'C:/wamp64/logs/apache_error.log',
    '../logs/error.log',
    './logs/error.log',
    './error.log',
    'C:/Windows/Temp/php_errors.log',
    'C:/Windows/Temp/php-errors.log'
);

echo "<h2>Checking possible log locations:</h2>";
echo "<ul>";
foreach ($possibleLogLocations as $location) {
    if (empty($location)) continue;
    
    echo "<li>" . $location . ": ";
    if (file_exists($location)) {
        echo "<span style='color:green'>EXISTS</span> - ";
        echo "Size: " . round(filesize($location) / 1024, 2) . " KB, ";
        echo "Last modified: " . date("Y-m-d H:i:s", filemtime($location));
        
        // Show last few log entries
        echo "<br><strong>Last 10 log entries:</strong><br>";
        echo "<pre style='background-color:#f5f5f5;padding:10px;max-height:200px;overflow:auto'>";
        $logContent = file_get_contents($location);
        $lines = explode("\n", $logContent);
        $lastLines = array_slice($lines, max(0, count($lines) - 10));
        echo htmlspecialchars(implode("\n", $lastLines));
        echo "</pre>";
    } else {
        echo "<span style='color:red'>NOT FOUND</span>";
    }
    echo "</li>";
}
echo "</ul>";

// Try to write to error log
echo "<h2>Writing test message to error log</h2>";
error_log("Test message from find_logs.php at " . date("Y-m-d H:i:s"));
echo "<p>Test message written to error log. Refresh this page to see if it appears in any of the logs above.</p>";

// Display system information
echo "<h2>System Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Check if we can create a log file in the current directory
echo "<h2>Creating a custom log file</h2>";
$customLogFile = "./redmine_integration_log.txt";
try {
    file_put_contents($customLogFile, "Log created at " . date("Y-m-d H:i:s") . "\n", FILE_APPEND);
    echo "<p>Created custom log file at: " . realpath($customLogFile) . "</p>";
    echo "<p>You can use this file for custom logging.</p>";
} catch (Exception $e) {
    echo "<p>Could not create custom log file: " . $e->getMessage() . "</p>";
}
?>
