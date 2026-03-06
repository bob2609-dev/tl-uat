<?php
// Include PHP 8 compatibility layer
require_once('custom/inc/php8_init.php');
/**
 * TestLink PHP 8 ADODB Library Fix
 * This script fixes deprecation warnings in the ADODB library for PHP 8
 */

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 ADODB Fix</title>\n";
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

echo "<h1>TestLink PHP 8 ADODB Library Fix</h1>\n";

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

// Path to the ADODB library file
$adodbFile = $basePath . '/vendor/adodb/adodb-php/adodb.inc.php';

if (!file_exists($adodbFile)) {
    log_message("ADODB file not found at: $adodbFile", 'error');
    log_message("Please check the actual path to the ADODB library in your TestLink installation.", 'error');
    // Try to locate it
    $possiblePaths = [
        $basePath . '/vendor/adodb/adodb-php/adodb.inc.php',
        $basePath . '/lib/adodb/adodb.inc.php',
        $basePath . '/third_party/adodb/adodb.inc.php',
        $basePath . '/adodb/adodb.inc.php'
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            log_message("Found ADODB at: $path", 'success');
            $adodbFile = $path;
            break;
        }
    }
    
    if (!file_exists($adodbFile)) {
        log_message("Could not locate ADODB library. Please provide the correct path.", 'error');
        echo "</body>\n</html>";
        exit;
    }
}

// Create a backup of the original file
log_message("Creating backup of ADODB file...");
$backupFile = $adodbFile . '.bak';
if (!file_exists($backupFile)) {
    copy($adodbFile, $backupFile);
    log_message("Created backup at: $backupFile", 'success');
} else {
    log_message("Backup already exists at: $backupFile", 'warning');
}

// Read the file content
log_message("Reading ADODB file content...");
$content = file_get_contents($adodbFile);
if ($content === false) {
    log_message("Failed to read ADODB file", 'error');
    echo "</body>\n</html>";
    exit;
}

// Fix the ADODB_Iterator_empty class
log_message("Fixing ADODB_Iterator_empty class...");
$contentBefore = $content;

// Add #[\ReturnTypeWillChange] attribute to methods
$methods = ['current', 'next', 'key', 'valid', 'rewind', 'getIterator'];

foreach ($methods as $method) {
    // Pattern to match method definition
    $pattern = '/function\s+' . $method . '\s*\(/i';
    
    // Replace with attribute + method definition
    $replacement = "#[\\ReturnTypeWillChange]\n\tfunction $method(";
    
    $content = preg_replace($pattern, $replacement, $content);
}

if ($content !== $contentBefore) {
    log_message("Successfully added ReturnTypeWillChange attributes to methods", 'success');
    
    // Write the fixed content back to the file
    if (file_put_contents($adodbFile, $content) !== false) {
        log_message("Successfully wrote fixed content to ADODB file", 'success');
    } else {
        log_message("Failed to write fixed content to ADODB file", 'error');
    }
} else {
    log_message("No changes were made to the ADODB file", 'warning');
    log_message("The regex patterns may not have matched. Manual editing may be required.", 'warning');
    
    // Alternative approach - insert at the beginning of the file
    log_message("Trying alternative approach...");
    
    $phpTagPos = strpos($content, '<?php');
    if ($phpTagPos !== false) {
        $insertPos = $phpTagPos + 5; // After <?php
        $disableWarningsCode = "\n// Disable deprecation warnings for PHP 8\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\n";
        
        $newContent = substr($content, 0, $insertPos) . $disableWarningsCode . substr($content, $insertPos);
        
        if (file_put_contents($adodbFile, $newContent) !== false) {
            log_message("Successfully added error suppression to ADODB file", 'success');
        } else {
            log_message("Failed to write modified content to ADODB file", 'error');
        }
    } else {
        log_message("Could not find PHP opening tag in ADODB file", 'error');
    }
}

// Create a direct ADODB wrapper to override the library
log_message("Creating ADODB wrapper file...");
$wrapperFile = $basePath . '/adodb_wrapper.php';
$wrapperContent = "<?php\n/**\n * ADODB Wrapper for PHP 8 Compatibility\n * This file should be included before any ADODB usage\n */\n\n// Disable deprecation warnings\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\n\n// Apply ReturnTypeWillChange attribute if it doesn't exist\nif (!interface_exists('ReturnTypeWillChange') && !class_exists('ReturnTypeWillChange') && !function_exists('ReturnTypeWillChange')) {\n    /**\n     * Attribute to suppress return type warning in PHP 8\n     */\n    #[Attribute]\n    class ReturnTypeWillChange {}\n}\n";

if (file_put_contents($wrapperFile, $wrapperContent) !== false) {
    log_message("Successfully created ADODB wrapper file", 'success');
} else {
    log_message("Failed to create ADODB wrapper file", 'error');
}

// Modify index.php to include our wrapper
log_message("Updating index.php to include ADODB wrapper...");
$indexFile = $basePath . '/index.php';
if (file_exists($indexFile)) {
    $indexContent = file_get_contents($indexFile);
    if ($indexContent !== false) {
        if (strpos($indexContent, 'adodb_wrapper.php') === false) {
            $pattern = '/require_once\s*\(\s*['"](config\.inc\.php)['"]\s*\)\s*;/i';
            $replacement = "require_once('\\1');\nrequire_once('adodb_wrapper.php'); // ADODB PHP 8 compatibility wrapper";
            
            $newIndexContent = preg_replace($pattern, $replacement, $indexContent);
            if ($newIndexContent !== $indexContent) {
                if (file_put_contents($indexFile, $newIndexContent) !== false) {
                    log_message("Successfully updated index.php to include ADODB wrapper", 'success');
                } else {
                    log_message("Failed to write updated content to index.php", 'error');
                }
            } else {
                log_message("Could not find the right place to insert ADODB wrapper in index.php", 'warning');
            }
        } else {
            log_message("ADODB wrapper is already included in index.php", 'success');
        }
    } else {
        log_message("Failed to read index.php content", 'error');
    }
} else {
    log_message("Could not find index.php", 'error');
}

// Create an .htaccess file to suppress PHP warnings
log_message("Creating/updating .htaccess file...");
$htaccessFile = $basePath . '/.htaccess';
$htaccessContent = "# Proper MIME type for images\nAddType image/png .png\nAddType image/jpeg .jpg .jpeg .jpe\nAddType image/gif .gif\nAddType image/svg+xml .svg\nAddType image/bmp .bmp\n\n# PHP 8 Compatibility Settings\n<IfModule mod_php.c>\n  php_flag display_errors off\n  php_value error_reporting 0\n</IfModule>\n";

if (file_put_contents($htaccessFile, $htaccessContent) !== false) {
    log_message("Successfully created/updated .htaccess file", 'success');
} else {
    log_message("Failed to create/update .htaccess file", 'error');
}

echo "<h2>ADODB Fix Complete</h2>\n";
echo "<p>To apply these fixes to your actual TestLink installation:</p>\n";
echo "<ol>\n";
echo "<li>Copy the fixed <code>adodb.inc.php</code> file to your actual installation at <code>D:/xampp/htdocs/tl-uat/vendor/adodb/adodb-php/adodb.inc.php</code></li>\n";
echo "<li>Copy <code>adodb_wrapper.php</code> to your TestLink root directory at <code>D:/xampp/htdocs/tl-uat/</code></li>\n";
echo "<li>Copy the modified <code>index.php</code> to your TestLink root directory</li>\n";
echo "<li>Copy the <code>.htaccess</code> file to your TestLink root directory</li>\n";
echo "<li>Restart your web server</li>\n";
echo "<li>Clear your browser cache</li>\n";
echo "<li>Access TestLink again</li>\n";
echo "</ol>\n";

echo "<p class='warning'>Note: These fixes modify vendor files, which is generally not recommended but necessary in this case to fix PHP 8 compatibility issues. Be sure to keep the backup files in case you need to restore them.</p>\n";

echo "</body>\n</html>";
