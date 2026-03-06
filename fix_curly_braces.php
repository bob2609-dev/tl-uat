<?php
// Include PHP 8 compatibility layer
require_once('custom/inc/php8_init.php');
/**
 * Fix Curly Brace Syntax for PHP 8 Compatibility
 * This script fixes the curly brace array access syntax in all PHP files
 * which is deprecated in PHP 7.4 and removed in PHP 8
 */

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Curly Brace Syntax Fixer</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2, h3 { color: #333; }\n";
echo ".success { color: green; }\n";
echo ".error { color: red; }\n";
echo ".warning { color: orange; }\n";
echo "pre { background: #f5f5f5; padding: 10px; overflow: auto; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink PHP 8 Curly Brace Syntax Fixer</h1>\n";

// Function to log and display messages
function log_message($message, $type = 'info') {
    $class = '';
    switch ($type) {
        case 'success': $class = 'class="success"'; break;
        case 'error': $class = 'class="error"'; break;
        case 'warning': $class = 'class="warning"'; break;
    }
    echo "<p $class>$message</p>\n";
}

// List of files we know have issues
$known_problematic_files = [
    __DIR__ . '/third_party/kint/inc/kintParser.class.php',
    __DIR__ . '/lib/functions/tlRole.class.php',
    __DIR__ . '/third_party/kint/Kint.class.php',
];

// Fix each known problematic file
log_message("Step 1: Fixing known problematic files...");

$fixed_files = 0;
foreach ($known_problematic_files as $filepath) {
    if (file_exists($filepath)) {
        // Create backup
        $backup_file = $filepath . '.bak';
        if (!file_exists($backup_file)) {
            copy($filepath, $backup_file);
        }
        
        // Fix the file
        $content = file_get_contents($filepath);
        $original_content = $content;
        
        // Replace all instances of curly brace syntax for array/string access
        // Pattern 1: $var{0} -> $var[0]
        $content = preg_replace('/\$([a-zA-Z0-9_]+)\{(\d+|\$[a-zA-Z0-9_]+)\}/', '\$$1[$2]', $content);
        
        // Pattern 2: $var{expression} -> $var[expression]
        $content = preg_replace('/(\$[a-zA-Z0-9_]+(?:\[[^\]]+\])*)\{([^{}]+)\}/', '$1[$2]', $content);
        
        if ($content !== $original_content) {
            file_put_contents($filepath, $content);
            log_message("Fixed curly brace syntax in: " . basename($filepath), 'success');
            $fixed_files++;
        } else {
            log_message("No changes needed in: " . basename($filepath), 'warning');
            
            // Try line-by-line approach for problematic files
            if (basename($filepath) === 'tlRole.class.php') {
                $lines = file($filepath);
                $line_number = 262; // Line 263 (0-indexed)
                if (isset($lines[$line_number])) {
                    $original_line = $lines[$line_number];
                    log_message("Original line 263: " . htmlspecialchars($original_line), 'warning');
                    
                    // Try to fix the specific line
                    $lines[$line_number] = str_replace('{$', '[${', $lines[$line_number]);
                    $lines[$line_number] = str_replace('}', ']', $lines[$line_number]);
                    
                    log_message("Fixed line 263: " . htmlspecialchars($lines[$line_number]), 'success');
                    file_put_contents($filepath, implode('', $lines));
                    $fixed_files++;
                }
            }
        }
    } else {
        log_message("File not found: " . basename($filepath), 'warning');
    }
}

// Now scan important directories for files that might have the same issue
log_message("Step 2: Scanning for other files with curly brace syntax...");

$important_directories = [
    __DIR__ . '/lib/functions',
    __DIR__ . '/lib/api',
    __DIR__ . '/third_party',
    __DIR__ . '/vendor/adodb'
];

function scan_directory($dir, &$fixed_count) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = glob($dir . '/*.php');
    foreach ($files as $file) {
        // Skip backups
        if (strpos($file, '.bak') !== false || strpos($file, '.original') !== false) {
            continue;
        }
        
        // Check if file contains curly brace syntax
        $content = file_get_contents($file);
        if (preg_match('/\$[a-zA-Z0-9_]+\{[^\}]+\}/', $content)) {
            // Create backup
            $backup_file = $file . '.bak';
            if (!file_exists($backup_file)) {
                copy($file, $backup_file);
            }
            
            // Fix the file
            $original_content = $content;
            
            // Replace all instances of curly brace syntax for array/string access
            $content = preg_replace('/\$([a-zA-Z0-9_]+)\{(\d+|\$[a-zA-Z0-9_]+)\}/', '\$$1[$2]', $content);
            $content = preg_replace('/(\$[a-zA-Z0-9_]+(?:\[[^\]]+\])*)\{([^{}]+)\}/', '$1[$2]', $content);
            
            if ($content !== $original_content) {
                file_put_contents($file, $content);
                log_message("Fixed curly brace syntax in: " . basename($file), 'success');
                $fixed_count++;
            }
        }
    }
    
    // Scan subdirectories
    $subdirs = glob($dir . '/*', GLOB_ONLYDIR);
    foreach ($subdirs as $subdir) {
        scan_directory($subdir, $fixed_count);
    }
}

$additional_fixed = 0;
foreach ($important_directories as $dir) {
    if (is_dir($dir)) {
        scan_directory($dir, $additional_fixed);
    } else {
        log_message("Directory not found: " . $dir, 'warning');
    }
}

log_message("Fixed $fixed_files known problematic files and $additional_fixed additional files", 'success');

echo "<h2>Fixing Completed</h2>\n";
echo "<p>The curly brace syntax has been fixed in PHP files. This should resolve the fatal errors in PHP 8.</p>\n";
echo "<p>If you still see deprecation warnings, you can hide them by uncommenting the error_reporting line in custom/inc/php8_compatibility.php.</p>\n";
echo "<p>If you still encounter fatal errors, please check your server error logs for details.</p>\n";

echo "</body>\n</html>";
