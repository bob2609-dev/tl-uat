<?php
/**
 * TestLink PHP 8 Fix Deployment Script
 * This script copies the fixed files from the backup directory to the actual installation
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Fix Deployment</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2 { color: #333; }\n";
echo ".success { color: green; }\n";
echo ".error { color: red; }\n";
echo ".warning { color: orange; }\n";
echo "code { background: #f5f5f5; padding: 2px 4px; }\n";
echo "input[type=text] { width: 100%; padding: 8px; margin: 5px 0 15px 0; }\n";
echo "button { background: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink PHP 8 Fix Deployment</h1>\n";

// Function to log messages
function log_message($message, $type = 'info') {
    $class = '';
    switch ($type) {
        case 'success': $class = 'class="success"'; break;
        case 'error': $class = 'class="error"'; break;
        case 'warning': $class = 'class="warning"'; break;
    }
    echo "<p $class>$message</p>\n";
}

// Source path (backup directory)
$sourcePath = __DIR__;
log_message("Source path: $sourcePath");

// Target path (actual TestLink installation)
$targetPath = 'D:/xampp/htdocs/tl-uat';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['target_path'])) {
        $targetPath = $_POST['target_path'];
    }
    
    log_message("Target path: $targetPath");
    
    // Verify target path exists
    if (!is_dir($targetPath)) {
        log_message("Target directory does not exist: $targetPath", 'error');
    } else {
        log_message("Starting deployment...");
        
        // 1. Make sure custom/inc directory exists in target
        $targetCustomIncDir = $targetPath . '/custom/inc';
        if (!is_dir($targetCustomIncDir)) {
            mkdir($targetCustomIncDir, 0755, true);
            log_message("Created directory: $targetCustomIncDir", 'success');
        }
        
        // 2. Copy php8_init.php
        $sourceInitFile = $sourcePath . '/custom/inc/php8_init.php';
        $targetInitFile = $targetCustomIncDir . '/php8_init.php';
        if (file_exists($sourceInitFile)) {
            if (copy($sourceInitFile, $targetInitFile)) {
                log_message("Copied php8_init.php to target", 'success');
            } else {
                log_message("Failed to copy php8_init.php", 'error');
            }
        } else {
            log_message("Source php8_init.php not found at: $sourceInitFile", 'error');
        }
        
        // 3. Check if we need to fix login.php
        $loginFile = $targetPath . '/login.php';
        if (file_exists($loginFile)) {
            $loginContent = file_get_contents($loginFile);
            if (strpos($loginContent, 'php8_init.php') !== false) {
                // Already includes it, but we should check the path
                log_message("login.php already includes php8_init.php", 'warning');
            } else {
                // Add the include to login.php
                $loginContent = preg_replace(
                    '/^(\<\?php)/m',
                    "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
                    $loginContent,
                    1
                );
                
                if (file_put_contents($loginFile, $loginContent)) {
                    log_message("Updated login.php to include php8_init.php", 'success');
                } else {
                    log_message("Failed to update login.php", 'error');
                }
            }
        } else {
            log_message("login.php not found at: $loginFile", 'warning');
        }
        
        // 4. Fix the index.php file
        $indexFile = $targetPath . '/index.php';
        if (file_exists($indexFile)) {
            $indexContent = file_get_contents($indexFile);
            if (strpos($indexContent, 'php8_init.php') !== false) {
                log_message("index.php already includes php8_init.php", 'warning');
            } else {
                // Add the include to index.php
                $indexContent = preg_replace(
                    '/^(\<\?php)/m',
                    "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
                    $indexContent,
                    1
                );
                
                if (file_put_contents($indexFile, $indexContent)) {
                    log_message("Updated index.php to include php8_init.php", 'success');
                } else {
                    log_message("Failed to update index.php", 'error');
                }
            }
        } else {
            log_message("index.php not found at: $indexFile", 'warning');
        }
        
        // 5. Add files for any other potential issues
        $targetLoggerFile = $targetPath . '/lib/functions/logger.class.php';
        $sourceLoggerFile = $sourcePath . '/lib/functions/logger.class.php';
        if (file_exists($sourceLoggerFile) && file_exists($targetLoggerFile)) {
            if (copy($sourceLoggerFile, $targetLoggerFile)) {
                log_message("Copied fixed logger.class.php to target", 'success');
            } else {
                log_message("Failed to copy logger.class.php", 'error');
            }
        }
        
        // 6. Fix issueTrackerInterface if needed
        $targetInterfaceFile = $targetPath . '/lib/issuetrackerintegration/issueTrackerInterface.class.php';
        $sourceInterfaceFile = $sourcePath . '/lib/issuetrackerintegration/issueTrackerInterface.class.php';
        if (file_exists($sourceInterfaceFile) && file_exists($targetInterfaceFile)) {
            if (copy($sourceInterfaceFile, $targetInterfaceFile)) {
                log_message("Copied fixed issueTrackerInterface.class.php to target", 'success');
            } else {
                log_message("Failed to copy issueTrackerInterface.class.php", 'error');
            }
        }
        
        // 7. Create standalone polyfill for strftime
        $strftimeContent = "<?php\n/**\n * Simple polyfill for strftime() in PHP 8.1+\n */\n\n// Only define if strftime() doesn't exist or we're on PHP 8.1+\nif (!function_exists('strftime') || version_compare(PHP_VERSION, '8.1.0', '>=')) {\n    function strftime($format, $timestamp = null) {\n        // Simple mapping of common format codes\n        $map = [\n            '%Y' => 'Y', // Year with century\n            '%y' => 'y', // Year without century\n            '%m' => 'm', // Month as decimal number\n            '%d' => 'd', // Day of the month\n            '%H' => 'H', // Hour (24-hour clock)\n            '%M' => 'i', // Minute\n            '%S' => 's', // Second\n        ];\n        \n        $dateFormat = $format;\n        foreach ($map as $from => $to) {\n            $dateFormat = str_replace($from, $to, $dateFormat);\n        }\n        \n        return date($dateFormat, $timestamp === null ? time() : $timestamp);\n    }\n}\n";
        
        $targetStrftimeFile = $targetCustomIncDir . '/strftime_polyfill.php';
        if (file_put_contents($targetStrftimeFile, $strftimeContent)) {
            log_message("Created strftime_polyfill.php in target", 'success');
        } else {
            log_message("Failed to create strftime_polyfill.php", 'error');
        }
        
        log_message("\nDeployment complete! Please restart your web server and clear your browser cache.", 'success');
    }
} else {
    // Display form
    echo "<form method=\"post\">";
    echo "<p>Enter the path to your actual TestLink installation:</p>";
    echo "<input type=\"text\" name=\"target_path\" value=\"$targetPath\" />";
    echo "<button type=\"submit\">Deploy Fixes</button>";
    echo "</form>";
}

echo "</body>\n</html>";
