<?php
/**
 * TestLink PHP 8 Fix Revert Script
 * This script reverts all changes made by restoring the .bak files
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Fix Revert</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2 { color: #333; }\n";
echo ".success { color: green; }\n";
echo ".error { color: red; }\n";
echo ".warning { color: orange; }\n";
echo "code { background: #f5f5f5; padding: 2px 4px; }\n";
echo ".results { max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink PHP 8 Fix Revert</h1>\n";
echo "<p>This script will revert all changes by restoring the .bak files to their original locations.</p>\n";

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

// Base path
$basePath = __DIR__;
log_message("Using base path: $basePath");

// Function to scan a directory recursively for .bak files
function findBakFiles($dir, &$bakFiles) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            findBakFiles($path, $bakFiles);
        } elseif (substr($item, -4) === '.bak') {
            $bakFiles[] = $path;
        }
    }
}

// Find all .bak files
log_message("Scanning for .bak files...");
$bakFiles = [];
findBakFiles($basePath, $bakFiles);

if (empty($bakFiles)) {
    log_message("No .bak files found. Nothing to revert.", 'warning');
} else {
    log_message("Found " . count($bakFiles) . " .bak files.", 'success');
    
    echo "<div class='results'>";
    // Restore each .bak file
    $restored = 0;
    $failed = 0;
    
    foreach ($bakFiles as $bakFile) {
        $originalFile = substr($bakFile, 0, -4); // Remove .bak extension
        $relativePath = str_replace($basePath, '', $originalFile);
        
        log_message("Restoring: $relativePath");
        
        if (copy($bakFile, $originalFile)) {
            log_message("u2713 Successfully restored $relativePath", 'success');
            $restored++;
        } else {
            log_message("u2717 Failed to restore $relativePath", 'error');
            $failed++;
        }
    }
    echo "</div>";
    
    log_message("Restoration complete. Restored: $restored, Failed: $failed", ($failed > 0 ? 'warning' : 'success'));
}

// Remove custom/inc directory if it was created by our scripts
$customIncDir = $basePath . '/custom/inc';
if (is_dir($customIncDir)) {
    $phpFiles = [
        'php8_init.php',
        'strftime_polyfill.php',
        'error_suppress.php',
        'magic_quotes_polyfill.php',
        'each_polyfill.php'
    ];
    
    log_message("Cleaning up custom/inc directory...");
    $removed = 0;
    
    foreach ($phpFiles as $file) {
        $fullPath = $customIncDir . '/' . $file;
        if (file_exists($fullPath)) {
            if (unlink($fullPath)) {
                log_message("Removed: custom/inc/$file", 'success');
                $removed++;
            } else {
                log_message("Failed to remove: custom/inc/$file", 'error');
            }
        }
    }
    
    // Try to remove the directory if empty
    if ($removed > 0 && count(scandir($customIncDir)) <= 2) { // Only . and .. remain
        if (rmdir($customIncDir)) {
            log_message("Removed empty directory: custom/inc", 'success');
            
            // Try to remove custom directory if empty
            $customDir = $basePath . '/custom';
            if (count(scandir($customDir)) <= 2) { // Only . and .. remain
                if (rmdir($customDir)) {
                    log_message("Removed empty directory: custom", 'success');
                }
            }
        }
    }
}

// Remove .htaccess if it was created by our scripts
$htaccessFile = $basePath . '/.htaccess';
if (file_exists($htaccessFile) && !file_exists($htaccessFile . '.bak')) {
    if (unlink($htaccessFile)) {
        log_message("Removed .htaccess file", 'success');
    } else {
        log_message("Failed to remove .htaccess file", 'error');
    }
}

echo "<h2>Next Steps</h2>";
echo "<p>To apply these reversions to your actual TestLink installation:</p>";
echo "<ol>";
echo "<li>Copy the restored files from this backup directory to your actual installation</li>";
echo "<li>Delete any custom/inc directory and files created in your actual installation</li>";
echo "<li>Remove any .htaccess file created by our scripts in your actual installation</li>";
echo "<li>Restart your web server</li>";
echo "<li>Clear your browser cache</li>";
echo "</ol>";

echo "</body>\n</html>";
