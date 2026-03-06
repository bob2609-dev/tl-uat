<?php
/**
 * TestLink PHP 8 Config Direct Fix
 * This script directly comments out the strftime() call in config.inc.php
 */

// Function to log messages
function log_message($message) {
    echo "$message\n";
}

log_message("Starting config.inc.php direct fix...");

// Target path (actual TestLink installation)
$targetPath = 'D:/xampp/htdocs/tl-uat';
log_message("Target path: $targetPath");

// Find config.inc.php
$configFile = $targetPath . '/config.inc.php';

if (!file_exists($configFile)) {
    log_message("ERROR: config.inc.php not found at: $configFile");
    exit(1);
}

// Create backup if doesn't exist
if (!file_exists($configFile . '.bak')) {
    copy($configFile, $configFile . '.bak');
    log_message("Created backup of config.inc.php");
}

// Check if we need to restore from backup
if (file_exists($configFile . '.bak')) {
    // Restore from backup first to ensure we have a clean file
    copy($configFile . '.bak', $configFile);
    log_message("Restored config.inc.php from backup");
}

// Read the file
$lines = file($configFile);

// Find the line with strftime
$lineNumber = 2025; // Line 2026 (0-indexed)
$found = false;

// Check 10 lines around the reported line to be safe
for ($i = max(0, $lineNumber - 5); $i <= min(count($lines) - 1, $lineNumber + 5); $i++) {
    if (strpos($lines[$i], 'strftime') !== false) {
        log_message("Found strftime() on line " . ($i + 1) . ": " . trim($lines[$i]));
        
        // Simply comment out the line as a safer approach
        $originalLine = $lines[$i];
        $lines[$i] = '// ' . $originalLine . ' // Commented out due to PHP 8 deprecation\n';
        
        log_message("Commented out line " . ($i + 1));
        $found = true;
        break;
    }
}

if ($found) {
    // Write the modified content back to the file
    file_put_contents($configFile, implode('', $lines));
    log_message("SUCCESS: Commented out strftime() in config.inc.php");
    log_message("\nPlease restart your web server and clear your browser cache!");
} else {
    log_message("WARNING: Could not find strftime() around line 2026 in config.inc.php");
    
    // Check the entire file
    log_message("Searching entire file for strftime()...");
    $found = false;
    
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], 'strftime') !== false) {
            log_message("Found strftime() on line " . ($i + 1) . ": " . trim($lines[$i]));
            
            // Simply comment out the line
            $originalLine = $lines[$i];
            $lines[$i] = '// ' . $originalLine . ' // Commented out due to PHP 8 deprecation\n';
            
            log_message("Commented out line " . ($i + 1));
            $found = true;
        }
    }
    
    if ($found) {
        // Write the modified content back to the file
        file_put_contents($configFile, implode('', $lines));
        log_message("SUCCESS: Commented out all strftime() calls in config.inc.php");
        log_message("\nPlease restart your web server and clear your browser cache!");
    } else {
        log_message("ERROR: Could not find any strftime() calls in config.inc.php");
    }
}
