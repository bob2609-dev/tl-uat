<?php
// Include PHP 8 compatibility layer
require_once('custom/inc/php8_init.php');
/**
 * TestLink PHP 8 - Direct Warning Suppressor
 * This script directly edits files to hide PHP 8 deprecation warnings
 */

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Direct Warning Suppressor</title>\n";
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

echo "<h1>TestLink PHP 8 Direct Warning Suppressor</h1>\n";

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

// Check if we're in the right directory
$basePath = __DIR__;
log_message("Using base path: $basePath");

// Direct fix approach: Create a wrapper file that will be included at the beginning of every request
log_message("Step 1: Creating error suppression wrapper...");

// Create the wrapper file
$wrapperFile = $basePath . '/error_suppress.php';
$wrapperContent = "<?php\n/**\n * TestLink PHP 8 Error Suppression Wrapper\n * This file is included at the beginning of every request to suppress deprecation warnings\n */\n\n// Completely disable error reporting for production\nerror_reporting(0);\ndisplay_errors(0);\nini_set('display_errors', 0);\n\n// If you need to see errors during debugging, comment out the lines above and uncomment this one:\n// error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\n";

file_put_contents($wrapperFile, $wrapperContent);
log_message("Created error_suppress.php wrapper file", 'success');

// Now modify index.php to include this file at the very beginning
log_message("Step 2: Modifying index.php to include wrapper...");
$indexFile = $basePath . '/index.php';

if (file_exists($indexFile)) {
    $content = file_get_contents($indexFile);
    $originalContent = $content;
    
    // Check if our wrapper is already included
    if (strpos($content, 'error_suppress.php') === false) {
        // Add our wrapper as the very first include after the PHP tag
        $content = preg_replace(
            '/^\<\?php/m',
            "<?php\n// Include error suppression wrapper\nrequire_once(__DIR__ . '/error_suppress.php');",
            $content,
            1
        );
        
        if ($content !== $originalContent) {
            file_put_contents($indexFile, $content);
            log_message("Modified index.php to include error suppression wrapper", 'success');
        } else {
            log_message("Failed to modify index.php - pattern not matched", 'error');
        }
    } else {
        log_message("index.php already includes error suppression wrapper", 'success');
    }
} else {
    log_message("Could not find index.php", 'error');
}

// Create a .user.ini file for FastCGI setups
log_message("Step 3: Creating .user.ini file for FastCGI setups...");
$userIniFile = $basePath . '/.user.ini';
$userIniContent = "; PHP 8 Error Suppression Settings\ndisplay_errors = Off\nerror_reporting = 0\n";

file_put_contents($userIniFile, $userIniContent);
log_message("Created .user.ini file with error suppression settings", 'success');

// Create web.config for IIS setups
log_message("Step 4: Creating web.config for IIS setups...");
$webConfigFile = $basePath . '/web.config';
$webConfigContent = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<configuration>\n    <system.webServer>\n        <httpErrors errorMode=\"Detailed\" />\n        <asp scriptErrorSentToBrowser=\"true\"/>\n    </system.webServer>\n    <system.web>\n        <customErrors mode=\"Off\"/>\n        <compilation debug=\"true\"/>\n    </system.web>\n</configuration>\n";

file_put_contents($webConfigFile, $webConfigContent);
log_message("Created web.config file for IIS setups", 'success');

// Direct edit of PHP files mentioned in the warnings
log_message("Step 5: Applying direct fixes to problematic files...");

// Fix common.php
$commonFile = $basePath . '/lib/functions/common.php';
if (file_exists($commonFile)) {
    log_message("Found common.php, creating backup and fixing...");
    copy($commonFile, $commonFile . '.bak');
    
    $lines = file($commonFile);
    // Line 427 has the ${var} issue
    if (isset($lines[426])) { // 0-indexed, so line 427 is at index 426
        $originalLine = $lines[426];
        $lines[426] = str_replace('${', '{$', $lines[426]);
        
        if ($lines[426] !== $originalLine) {
            file_put_contents($commonFile, implode('', $lines));
            log_message("Fixed ${var} issue in common.php line 427", 'success');
        } else {
            log_message("No changes needed for common.php line 427", 'warning');
        }
    } else {
        log_message("Line 427 not found in common.php", 'warning');
    }
} else {
    log_message("Could not find common.php", 'warning');
}

// Fix string_api.php
$stringApiFile = $basePath . '/lib/functions/string_api.php';
if (file_exists($stringApiFile)) {
    log_message("Found string_api.php, creating backup and fixing...");
    copy($stringApiFile, $stringApiFile . '.bak');
    
    $content = file_get_contents($stringApiFile);
    $originalContent = $content;
    
    // Replace all ${var} with {$var}
    $content = str_replace('${', '{$', $content);
    
    if ($content !== $originalContent) {
        file_put_contents($stringApiFile, $content);
        log_message("Fixed all ${var} issues in string_api.php", 'success');
    } else {
        log_message("No changes needed for string_api.php", 'warning');
    }
} else {
    log_message("Could not find string_api.php", 'warning');
}

echo "<h2>All Direct Fixes Applied</h2>\n";
echo "<p>The following changes have been made to suppress PHP 8 deprecation warnings:</p>\n";
echo "<ol>\n";
echo "<li>Created <code>error_suppress.php</code> which completely disables error reporting</li>\n";
echo "<li>Modified <code>index.php</code> to include the error suppression wrapper</li>\n";
echo "<li>Created <code>.user.ini</code> for FastCGI setups</li>\n";
echo "<li>Created <code>web.config</code> for IIS setups</li>\n";
echo "<li>Applied direct fixes to problematic files (common.php, string_api.php)</li>\n";
echo "</ol>\n";

echo "<h3>Next Steps</h3>\n";
echo "<p>To apply these changes to your TestLink installation:</p>\n";
echo "<ol>\n";
echo "<li>Copy <code>error_suppress.php</code> to your TestLink root directory</li>\n";
echo "<li>Copy the modified <code>index.php</code> to your TestLink root directory</li>\n";
echo "<li>Copy <code>.user.ini</code> and <code>web.config</code> to your TestLink root directory</li>\n";
echo "<li>Restart your web server</li>\n";
echo "<li>Clear your browser cache</li>\n";
echo "<li>Access TestLink again</li>\n";
echo "</ol>\n";

echo "<p class='warning'>If you still see warnings, you may need to edit your PHP configuration (php.ini) to disable error display globally.</p>\n";

echo "</body>\n</html>";
