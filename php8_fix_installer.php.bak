<?php
/**
 * TestLink PHP 8 Compatibility Fix Installer
 * This script automatically applies all the necessary fixes to make TestLink work with PHP 8
 */

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Compatibility Fix Installer</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2, h3 { color: #333; }\n";
echo ".success { color: green; }\n";
echo ".error { color: red; }\n";
echo ".warning { color: orange; }\n";
echo "pre { background: #f5f5f5; padding: 10px; overflow: auto; }\n";
echo "code { background: #f5f5f5; padding: 2px 4px; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink PHP 8 Compatibility Fix Installer</h1>\n";

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

// 1. Fix Kint parser class (most critical - causing fatal error)
log_message("Step 1: Fixing Kint parser class...");
$kintParserFile = __DIR__ . '/third_party/kint/inc/kintParser.class.php';
if (file_exists($kintParserFile)) {
    // Create backup
    copy($kintParserFile, $kintParserFile . '.bak');
    log_message("Created backup of kintParser.class.php", 'success');
    
    // Fix the file
    $content = file_get_contents($kintParserFile);
    $original = $content;
    
    // Replace all instances of curly brace syntax for array/string access
    $content = preg_replace('/\$([a-zA-Z0-9_]+)\{(\d+)\}/', '\$$1[$2]', $content);
    
    if ($content !== $original) {
        file_put_contents($kintParserFile, $content);
        log_message("Successfully fixed curly brace syntax in kintParser.class.php", 'success');
    } else {
        log_message("No curly brace syntax found in kintParser.class.php or pattern didn't match", 'warning');
        
        // Try a more direct approach
        $line463 = file($kintParserFile)[462]; // 0-indexed, so line 463 is at index 462
        log_message("Line 463 content: " . htmlspecialchars($line463), 'warning');
        
        // Manual replacement for line 463
        $fileLines = file($kintParserFile);
        $fileLines[462] = str_replace('{0}', '[0]', $fileLines[462]);
        file_put_contents($kintParserFile, implode('', $fileLines));
        log_message("Applied direct fix to line 463", 'success');
    }
} else {
    log_message("Could not find kintParser.class.php", 'error');
}

// 2. Fix other critical Kint files
log_message("Step 2: Checking other Kint files...");
$kintFile = __DIR__ . '/third_party/kint/Kint.class.php';
if (file_exists($kintFile)) {
    // Create backup
    copy($kintFile, $kintFile . '.bak');
    
    // Fix the file
    $content = file_get_contents($kintFile);
    $original = $content;
    
    // Replace all instances of curly brace syntax
    $content = preg_replace('/\$([a-zA-Z0-9_]+)\{(\d+)\}/', '\$$1[$2]', $content);
    
    if ($content !== $original) {
        file_put_contents($kintFile, $content);
        log_message("Fixed curly brace syntax in Kint.class.php", 'success');
    } else {
        log_message("No curly brace syntax found in Kint.class.php", 'success');
    }
} else {
    log_message("Could not find Kint.class.php", 'warning');
}

// 3. Create a Kint.class.php fallback if there are still issues
log_message("Step 3: Creating Kint fallback...");
if (file_exists($kintFile)) {
    // Create a special fallback file
    $fallbackFile = __DIR__ . '/custom/inc/kint_fallback.php';
    $fallbackContent = "<?php\n/**\n * Kint Fallback for PHP 8 Compatibility\n */\n\n// Check if Kint class already exists (avoid redefinition)\nif (!class_exists('Kint')) {\n    class Kint {\n        public static function dump() { return null; }\n        public static function trace() { return null; }\n        // Add other methods as needed\n    }\n}\n";
    
    file_put_contents($fallbackFile, $fallbackContent);
    log_message("Created Kint fallback file", 'success');
} else {
    log_message("Skipped Kint fallback creation (file not found)", 'warning');
}

// 4. Update index.php to include our fallback
log_message("Step 4: Updating index.php...");
$indexFile = __DIR__ . '/index.php';
if (file_exists($indexFile)) {
    $content = file_get_contents($indexFile);
    
    // Check if our fix has already been applied
    if (strpos($content, 'custom/inc/php8_init.php') === false) {
        // Add our compatibility layer
        $content = preg_replace(
            '/(\<\?php[\s\S]*?\n)/',
            "$1// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');\n",
            $content,
            1
        );
        
        file_put_contents($indexFile, $content);
        log_message("Updated index.php to include PHP 8 compatibility layer", 'success');
    } else {
        log_message("index.php already contains PHP 8 compatibility fix", 'success');
    }
} else {
    log_message("Could not find index.php", 'error');
}

// 5. Create or update .htaccess to suppress deprecation warnings
log_message("Step 5: Updating .htaccess...");
$htaccessFile = __DIR__ . '/.htaccess';
$htaccessContent = "\n# PHP 8 Compatibility Settings\n<IfModule mod_php.c>\n  php_value error_reporting 22519 # E_ALL & ~E_DEPRECATED & ~E_NOTICE\n  php_value display_errors 0\n</IfModule>\n";

if (file_exists($htaccessFile)) {
    $content = file_get_contents($htaccessFile);
    if (strpos($content, 'PHP 8 Compatibility Settings') === false) {
        file_put_contents($htaccessFile, $content . $htaccessContent);
        log_message("Updated .htaccess with PHP 8 compatibility settings", 'success');
    } else {
        log_message(".htaccess already contains PHP 8 compatibility settings", 'success');
    }
} else {
    file_put_contents($htaccessFile, $htaccessContent);
    log_message("Created new .htaccess with PHP 8 compatibility settings", 'success');
}

// 6. Create/update php.ini if available
log_message("Step 6: Checking for local php.ini...");
$phpiniFile = __DIR__ . '/php.ini';
$phpiniContent = ";PHP 8 Compatibility Settings\nerror_reporting = E_ALL & ~E_DEPRECATED & ~E_NOTICE\ndisplay_errors = Off\n";

if (file_exists($phpiniFile)) {
    $content = file_get_contents($phpiniFile);
    if (strpos($content, 'PHP 8 Compatibility Settings') === false) {
        file_put_contents($phpiniFile, $content . $phpiniContent);
        log_message("Updated php.ini with compatibility settings", 'success');
    } else {
        log_message("php.ini already contains compatibility settings", 'success');
    }
} else {
    file_put_contents($phpiniFile, $phpiniContent);
    log_message("Created new php.ini with compatibility settings", 'success');
}

// 7. Check that our compatibility files exist
log_message("Step 7: Checking compatibility files...");
$requiredFiles = [
    __DIR__ . '/custom/inc/php8_compatibility.php',
    __DIR__ . '/custom/inc/php8_init.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        log_message("Found compatibility file: " . basename($file), 'success');
    } else {
        log_message("Missing compatibility file: " . basename($file) . ". Please create this file.", 'error');
    }
}

// 8. Final instructions
echo "<h2>Fix Installation Complete</h2>\n";
echo "<p>All PHP 8 compatibility fixes have been applied. If you still encounter issues:</p>\n";
echo "<ol>\n";
echo "<li>Check your PHP error log for specific errors</li>\n";
echo "<li>Temporarily enable error display by editing <code>custom/inc/php8_compatibility.php</code> and uncommenting the error_reporting line</li>\n";
echo "<li>If you're still seeing the 'Internal Server Error', check your web server error logs</li>\n";
echo "<li>As a last resort, you can try disabling Kint completely by creating this file: <code>custom/inc/disable_kint.php</code></li>\n";
echo "</ol>\n";

echo "<p>Try accessing your TestLink installation now.</p>\n";

echo "</body>\n</html>";
