<?php
/**
 * TestLink PHP 8 Index Fix
 * This script fixes the undefined function display_errors() in index.php
 */

// Function to log messages
function log_message($message) {
    echo "$message\n";
}

log_message("Starting index.php fix...");

// Target path (actual TestLink installation)
$targetPath = 'D:/xampp/htdocs/tl-uat';
log_message("Target path: $targetPath");

// Find index.php
$indexFile = $targetPath . '/index.php';

if (!file_exists($indexFile)) {
    log_message("ERROR: index.php not found at: $indexFile");
    exit(1);
}

// Create backup if doesn't exist
if (!file_exists($indexFile . '.bak')) {
    copy($indexFile, $indexFile . '.bak');
    log_message("Created backup of index.php");
}

// Read the file
$lines = file($indexFile);

// Look for display_errors function call
$found = false;
for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'display_errors()') !== false) {
        log_message("Found display_errors() on line " . ($i + 1) . ": " . trim($lines[$i]));
        
        // Replace with ini_set
        $originalLine = $lines[$i];
        $lines[$i] = str_replace('display_errors()', "ini_set('display_errors', 0)", $lines[$i]);
        
        log_message("Fixed line " . ($i + 1) . ": " . trim($lines[$i]));
        $found = true;
    }
}

if ($found) {
    // Write the modified content back to the file
    file_put_contents($indexFile, implode('', $lines));
    log_message("SUCCESS: Fixed display_errors() function call in index.php");
    log_message("\nPlease restart your web server and clear your browser cache!");
} else {
    log_message("WARNING: Could not find display_errors() function call in index.php");
    
    // Show the first 10 lines of the file
    log_message("\nFirst 10 lines of index.php:");
    for ($i = 0; $i < min(10, count($lines)); $i++) {
        log_message("Line " . ($i + 1) . ": " . trim($lines[$i]));
    }
    
    // Look for php8_init.php include
    $found = false;
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], 'php8_init.php') !== false) {
            log_message("Found php8_init.php include on line " . ($i + 1) . ": " . trim($lines[$i]));
            $found = true;
        }
    }
    
    if (!$found) {
        log_message("php8_init.php is not included in index.php. Adding it now...");
        
        // Add the include at the beginning after the PHP opening tag
        $indexContent = file_get_contents($indexFile);
        $indexContent = preg_replace(
            '/^(\<\?php)/m',
            "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
            $indexContent,
            1
        );
        
        file_put_contents($indexFile, $indexContent);
        log_message("Added php8_init.php include to index.php");
    }
}

// Create a simple PHP file to verify the php8_init.php is working
log_message("\nCreating test.php to verify the PHP 8 compatibility layer...");
$testFile = $targetPath . '/test.php';
$testContent = "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');\n\necho '<h1>TestLink PHP 8 Compatibility Test</h1>';\n\necho '<p>PHP Version: ' . phpversion() . '</p>';\n\n// Test strftime polyfill\necho '<p>Current date (using safe_strftime): ' . (function_exists('safe_strftime') ? safe_strftime('%Y-%m-%d') : 'safe_strftime not defined') . '</p>';\n\n// Test error suppression\necho '<p>Error reporting: ' . error_reporting() . '</p>';\necho '<p>Display errors: ' . ini_get('display_errors') . '</p>';\n";

file_put_contents($testFile, $testContent);
log_message("Created test.php file at: $testFile");
log_message("Access it at: http://localhost/tl-uat/test.php");
