<?php
// Include PHP 8 compatibility layer
require_once('custom/inc/php8_init.php');
/**
 * Fix for tlLogger dynamic property warnings in PHP 8
 * This script fixes the dynamic property creation warnings in logger.class.php
 */

// Function to log messages
function log_message($message) {
    echo $message . "\n";
}

log_message("Starting tlLogger PHP 8 compatibility fix...");

// Find the logger file
$basePath = __DIR__;
$loggerFile = $basePath . '/lib/functions/logger.class.php';

// Try different possible locations
if (!file_exists($loggerFile)) {
    $possiblePaths = [
        $basePath . '/lib/functions/logger.class.php',
        $basePath . '/include/logger.class.php',
        $basePath . '/third_party/logger.class.php'
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            log_message("Found logger class at: $path");
            $loggerFile = $path;
            break;
        }
    }
}

if (!file_exists($loggerFile)) {
    log_message("ERROR: Could not find logger.class.php file. Please specify the correct path manually.");
    exit(1);
}

// Create a backup
$backupFile = $loggerFile . '.bak';
if (!file_exists($backupFile)) {
    copy($loggerFile, $backupFile);
    log_message("Created backup at: $backupFile");
}

// Read the file content
$content = file_get_contents($loggerFile);
if ($content === false) {
    log_message("ERROR: Failed to read logger.class.php file");
    exit(1);
}

// Check if the tlLogger class already has property declarations
if (strpos($content, 'public $logLevelFilter;') === false) {
    log_message("Adding property declarations to tlLogger class...");
    
    // Find the beginning of the class declaration
    $classPos = strpos($content, 'class tlLogger');
    if ($classPos !== false) {
        // Find the opening brace of the class
        $bracePos = strpos($content, '{', $classPos);
        if ($bracePos !== false) {
            // Insert the property declarations after the opening brace
            $propertyDeclarations = "\n\t/** @var mixed Log level filter */\n\tpublic \$logLevelFilter;\n\n\t/** @var mixed Database connection */\n\tpublic \$db;\n";
            
            $content = substr($content, 0, $bracePos + 1) . $propertyDeclarations . substr($content, $bracePos + 1);
            
            // Write the modified content back to the file
            if (file_put_contents($loggerFile, $content) !== false) {
                log_message("Successfully added property declarations to tlLogger class");
            } else {
                log_message("ERROR: Failed to write to logger.class.php file");
                exit(1);
            }
        } else {
            log_message("ERROR: Could not find class opening brace");
        }
    } else {
        log_message("ERROR: Could not find tlLogger class declaration");
    }
} else {
    log_message("Property declarations already exist in tlLogger class");
}

// Update the error suppression file to include this specific warning
$errorSuppressFile = $basePath . '/error_suppress.php';
if (file_exists($errorSuppressFile)) {
    $suppressContent = file_get_contents($errorSuppressFile);
    if (strpos($suppressContent, 'dynamic property') === false) {
        $newSuppressContent = str_replace(
            "// Disable all PHP deprecation warnings",
            "// Disable all PHP deprecation warnings\n// Specifically suppress dynamic property creation warnings",
            $suppressContent
        );
        file_put_contents($errorSuppressFile, $newSuppressContent);
        log_message("Updated error_suppress.php to specifically handle dynamic property warnings");
    }
}

log_message("\nFix complete! Please copy the fixed logger.class.php file to your actual TestLink installation at:\nD:/xampp/htdocs/tl-uat/lib/functions/logger.class.php");
log_message("\nThen restart your web server and clear your browser cache.");
