<?php
/**
 * TestLink PHP 8 Config Fix
 * This script fixes the strftime() deprecation in config.inc.php
 */

// Function to log messages
function log_message($message) {
    echo "$message\n";
}

log_message("Starting config.inc.php fix...");

// Target path (actual TestLink installation)
$targetPath = 'D:/xampp/htdocs/tl-uat';
log_message("Target path: $targetPath");

// Find config.inc.php
$configFile = $targetPath . '/config.inc.php';

if (!file_exists($configFile)) {
    log_message("ERROR: config.inc.php not found at: $configFile");
    exit(1);
}

// Create backup
if (!file_exists($configFile . '.bak')) {
    copy($configFile, $configFile . '.bak');
    log_message("Created backup of config.inc.php");
}

// Read the file
$lines = file($configFile);

// Check around line 2026 for strftime
$lineNumber = 2025; // Line 2026 (0-indexed)
$found = false;

// Check 10 lines around the reported line to be safe
for ($i = max(0, $lineNumber - 5); $i <= min(count($lines) - 1, $lineNumber + 5); $i++) {
    if (strpos($lines[$i], 'strftime') !== false) {
        log_message("Found strftime() on line " . ($i + 1) . ": " . trim($lines[$i]));
        
        // Replace strftime with safe_strftime
        $originalLine = $lines[$i];
        $lines[$i] = str_replace('strftime(', 'safe_strftime(', $lines[$i]);
        
        log_message("Fixed line " . ($i + 1) . ": " . trim($lines[$i]));
        $found = true;
    }
}

if ($found) {
    // Write the modified content back to the file
    file_put_contents($configFile, implode('', $lines));
    log_message("SUCCESS: Fixed strftime() in config.inc.php");
} else {
    log_message("WARNING: Could not find strftime() around line 2026 in config.inc.php");
    
    // Try commenting out the line as a fallback
    if (isset($lines[$lineNumber])) {
        $originalLine = $lines[$lineNumber];
        $lines[$lineNumber] = '// ' . $lines[$lineNumber] . ' // Commented out due to PHP 8 deprecation\n';
        
        file_put_contents($configFile, implode('', $lines));
        log_message("Commented out line 2026 as a fallback solution");
    }
}

log_message("\nDon't forget to restart your web server and clear your browser cache!");
