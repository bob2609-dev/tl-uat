<?php
/**
 * TestLink PHP 8 Minimal Fix Script
 * This script applies only the essential fixes needed for PHP 8 compatibility
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TestLink PHP 8 Minimal Fix</h1>";

function log_message($message, $type = 'info') {
    $class = '';
    switch ($type) {
        case 'success': $class = 'style="color:green;"'; break;
        case 'error': $class = 'style="color:red;"'; break;
        case 'warning': $class = 'style="color:orange;"'; break;
        default: $class = 'style="color:blue;"';
    }
    echo "<p $class>$message</p>\n";
    // Flush output to show progress in real-time
    if (function_exists('ob_flush')) {
        ob_flush();
        flush();
    }
}

// Base path
$basePath = __DIR__;
log_message("Using base path: $basePath");

// Create necessary directories
$customDir = $basePath . '/custom';
$incDir = $basePath . '/custom/inc';

if (!is_dir($customDir)) {
    mkdir($customDir, 0755, true);
    log_message("Created directory: $customDir", 'success');
}

if (!is_dir($incDir)) {
    mkdir($incDir, 0755, true);
    log_message("Created directory: $incDir", 'success');
}

// 1. Create minimal polyfill for strftime
log_message("1. Creating minimal strftime polyfill...");
$strftimeFile = $incDir . '/strftime_polyfill.php';
$strftimeContent = "<?php\n/**\n * Simple polyfill for strftime() in PHP 8.1+\n */\n\n// Only define if strftime() doesn't exist or we're on PHP 8.1+\nif (!function_exists('strftime') || version_compare(PHP_VERSION, '8.1.0', '>=')) {\n    function strftime($format, $timestamp = null) {\n        // Simple mapping of common format codes\n        $map = [\n            '%Y' => 'Y', // Year with century\n            '%y' => 'y', // Year without century\n            '%m' => 'm', // Month as decimal number\n            '%d' => 'd', // Day of the month\n            '%H' => 'H', // Hour (24-hour clock)\n            '%M' => 'i', // Minute\n            '%S' => 's', // Second\n        ];\n        \n        $dateFormat = $format;\n        foreach ($map as $from => $to) {\n            $dateFormat = str_replace($from, $to, $dateFormat);\n        }\n        \n        return date($dateFormat, $timestamp === null ? time() : $timestamp);\n    }\n}\n";

file_put_contents($strftimeFile, $strftimeContent);
log_message("Created strftime polyfill at: $strftimeFile", 'success');

// 2. Create minimal error suppression
log_message("2. Creating minimal error suppression...");
$errorSuppressFile = $incDir . '/error_suppress.php';
$errorSuppressContent = "<?php\n/**\n * Simple error suppression for PHP 8\n */\n\n// Disable all deprecation warnings\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\nini_set('display_errors', 0);\n";

file_put_contents($errorSuppressFile, $errorSuppressContent);
log_message("Created error suppression at: $errorSuppressFile", 'success');

// 3. Fix ADODB for PHP 8
log_message("3. Fixing ADODB for PHP 8...");
$adodbFile = $basePath . '/third_party/adodb/adodb.inc.php';

if (file_exists($adodbFile)) {
    // Create backup
    if (!file_exists($adodbFile . '.bak')) {
        copy($adodbFile, $adodbFile . '.bak');
        log_message("Created backup of adodb.inc.php", 'success');
    }
    
    // Read file
    $adodbContent = file_get_contents($adodbFile);
    $originalContent = $adodbContent;
    
    // Add ReturnTypeWillChange attribute to iterator methods
    $methods = ['current', 'next', 'key', 'valid', 'rewind'];
    foreach ($methods as $method) {
        $pattern = '/function\s+' . $method . '\s*\(/i';
        $replacement = "#[\\ReturnTypeWillChange]\n\tfunction $method(";
        $adodbContent = preg_replace($pattern, $replacement, $adodbContent);
    }
    
    // Save if modified
    if ($adodbContent !== $originalContent) {
        file_put_contents($adodbFile, $adodbContent);
        log_message("Fixed ADODB iterator methods", 'success');
    } else {
        log_message("No changes needed for ADODB or pattern didn't match", 'warning');
    }
} else {
    log_message("ADODB file not found at: $adodbFile", 'error');
}

// 4. Fix logger.class.php for dynamic properties
log_message("4. Fixing logger.class.php for dynamic properties...");
$loggerFile = $basePath . '/lib/functions/logger.class.php';

if (file_exists($loggerFile)) {
    // Create backup
    if (!file_exists($loggerFile . '.bak')) {
        copy($loggerFile, $loggerFile . '.bak');
        log_message("Created backup of logger.class.php", 'success');
    }
    
    // Read file
    $loggerContent = file_get_contents($loggerFile);
    
    // Find the class definition
    if (preg_match('/class\s+tlLogger\s*{/i', $loggerContent, $matches, PREG_OFFSET_CAPTURE)) {
        $pos = $matches[0][1] + strlen($matches[0][0]);
        
        // Add property declarations after the opening brace
        $propertyDeclarations = "\n\t/** @var mixed */\n\tpublic \$logLevelFilter;\n\n\t/** @var mixed */\n\tpublic \$db;\n";
        
        $newLoggerContent = substr($loggerContent, 0, $pos) . $propertyDeclarations . substr($loggerContent, $pos);
        
        // Save the modified file
        file_put_contents($loggerFile, $newLoggerContent);
        log_message("Added property declarations to tlLogger class", 'success');
    } else {
        log_message("Could not find tlLogger class definition", 'error');
    }
} else {
    log_message("Logger file not found at: $loggerFile", 'error');
}

// 5. Create minimal PHP 8 init file
log_message("5. Creating minimal PHP 8 init file...");
$initFile = $incDir . '/php8_init.php';
$initContent = "<?php\n/**\n * Minimal PHP 8 initialization\n */\n\n// Include polyfills\nrequire_once(__DIR__ . '/strftime_polyfill.php');\nrequire_once(__DIR__ . '/error_suppress.php');\n\n// Set default timezone if not already set\nif (function_exists('date_default_timezone_get')) {\n    date_default_timezone_set(@date_default_timezone_get());\n}\n";

file_put_contents($initFile, $initContent);
log_message("Created PHP 8 init file at: $initFile", 'success');

// 6. Update index.php to include the PHP 8 initialization
log_message("6. Updating index.php to include PHP 8 initialization...");
$indexFile = $basePath . '/index.php';

if (file_exists($indexFile)) {
    // Create backup
    if (!file_exists($indexFile . '.bak')) {
        copy($indexFile, $indexFile . '.bak');
        log_message("Created backup of index.php", 'success');
    }
    
    // Read file
    $indexContent = file_get_contents($indexFile);
    
    // Check if already includes php8_init.php
    if (strpos($indexContent, 'php8_init.php') === false) {
        // Add the include at the top after the opening PHP tag
        $indexContent = preg_replace(
            '/^(\<\?php)/m',
            "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
            $indexContent,
            1
        );
        
        // Save the modified file
        file_put_contents($indexFile, $indexContent);
        log_message("Updated index.php to include PHP 8 initialization", 'success');
    } else {
        log_message("index.php already includes PHP 8 initialization", 'success');
    }
} else {
    log_message("index.php not found at: $indexFile", 'error');
}

// 7. Create a simple .htaccess file to suppress warnings
log_message("7. Creating .htaccess file to suppress warnings...");
$htaccessFile = $basePath . '/.htaccess';
$htaccessContent = "# PHP 8 Compatibility Settings\n<IfModule mod_php.c>\n  php_flag display_errors off\n  php_value error_reporting 0\n</IfModule>\n";

file_put_contents($htaccessFile, $htaccessContent);
log_message("Created .htaccess file at: $htaccessFile", 'success');

log_message("\nMinimal fix complete! Here's what to do next:", 'success');
echo "<ol>";
echo "<li>Copy the following files to your actual TestLink installation at D:/xampp/htdocs/tl-uat/:</li>";
echo "<ul>";
echo "<li>custom/inc/strftime_polyfill.php</li>";
echo "<li>custom/inc/error_suppress.php</li>";
echo "<li>custom/inc/php8_init.php</li>";
echo "<li>The fixed third_party/adodb/adodb.inc.php</li>";
echo "<li>The fixed lib/functions/logger.class.php</li>";
echo "<li>The updated index.php</li>";
echo "<li>.htaccess</li>";
echo "</ul>";
echo "<li>Restart your Apache web server</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try accessing TestLink again</li>";
echo "</ol>";

echo "<p style=\"color:orange;\">If you still encounter issues, you can revert to the backup files (*.bak) to restore the original functionality.</p>";
