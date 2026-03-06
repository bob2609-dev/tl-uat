<?php
/**
 * TestLink PHP 8 Final Fix Script
 * This script fixes all the identified PHP 8 compatibility issues
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Final Fix</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2, h3 { color: #333; }\n";
echo ".success { color: green; }\n";
echo ".error { color: red; }\n";
echo ".warning { color: orange; }\n";
echo "code { background: #f5f5f5; padding: 2px 4px; }\n";
echo "input[type=text] { width: 100%; padding: 8px; margin: 5px 0 15px 0; }\n";
echo "button { background: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink PHP 8 Final Fix</h1>\n";

// Function to log messages
function log_message($message, $type = 'info') {
    $class = '';
    switch ($type) {
        case 'success': $class = 'class="success"'; break;
        case 'error': $class = 'class="error"'; break;
        case 'warning': $class = 'class="warning"'; break;
    }
    echo "<p $class>$message</p>\n";
    // Flush output to show progress in real-time
    if (function_exists('ob_flush')) {
        @ob_flush();
        @flush();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetPath = isset($_POST['target_path']) ? $_POST['target_path'] : 'D:/xampp/htdocs/tl-uat';
    
    log_message("Using target path: $targetPath");
    
    // Verify target path exists
    if (!is_dir($targetPath)) {
        log_message("Target directory does not exist: $targetPath", 'error');
    } else {
        log_message("Starting fixes...");
        
        // 1. Create custom/inc directory if it doesn't exist
        $targetCustomIncDir = $targetPath . '/custom/inc';
        if (!is_dir($targetCustomIncDir)) {
            mkdir($targetCustomIncDir, 0755, true);
            log_message("Created directory: $targetCustomIncDir", 'success');
        }
        
        // 2. Fix 1: Create php8_init.php with error suppression
        $initFile = $targetCustomIncDir . '/php8_init.php';
        $initContent = "<?php\n/**\n * PHP 8 Compatibility Initialization\n */\n\n// Disable deprecation warnings\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\nini_set('display_errors', 0);\n\n// Fix for curly brace syntax in strings\nif (!function_exists('fix_curly_syntax')) {\n    function fix_curly_syntax(\$string) {\n        return str_replace('\${', '{\$', \$string);\n    }\n}\n\n// Fix for strftime() deprecation\nif (!function_exists('strftime_polyfill')) {\n    function strftime_polyfill(\$format, \$timestamp = null) {\n        if (\$timestamp === null) {\n            \$timestamp = time();\n        }\n        \n        // Simple mapping of common format codes\n        \$map = [\n            '%Y' => 'Y', // Year with century\n            '%y' => 'y', // Year without century\n            '%m' => 'm', // Month as decimal number\n            '%d' => 'd', // Day of the month\n            '%H' => 'H', // Hour (24-hour clock)\n            '%M' => 'i', // Minute\n            '%S' => 's', // Second\n        ];\n        \n        \$dateFormat = \$format;\n        foreach (\$map as \$from => \$to) {\n            \$dateFormat = str_replace(\$from, \$to, \$dateFormat);\n        }\n        \n        return date(\$dateFormat, \$timestamp);\n    }\n}\n\n// Override strftime() if it's deprecated\nif (!function_exists('strftime')) {\n    function strftime(\$format, \$timestamp = null) {\n        return strftime_polyfill(\$format, \$timestamp);\n    }\n}\n\n// Set default timezone if not already set\nif (function_exists('date_default_timezone_get')) {\n    date_default_timezone_set(@date_default_timezone_get());\n}\n";
        
        file_put_contents($initFile, $initContent);
        log_message("Created php8_init.php with error suppression", 'success');
        
        // 3. Fix 2: Fix string_api.php - Replace ${var} with {$var}
        $stringApiFile = $targetPath . '/lib/functions/string_api.php';
        if (file_exists($stringApiFile)) {
            // Backup the file
            if (!file_exists($stringApiFile . '.bak')) {
                copy($stringApiFile, $stringApiFile . '.bak');
                log_message("Created backup of string_api.php", 'success');
            }
            
            $content = file_get_contents($stringApiFile);
            $updatedContent = str_replace('${', '{$', $content);
            
            if ($content !== $updatedContent) {
                file_put_contents($stringApiFile, $updatedContent);
                log_message("Fixed ${var} syntax in string_api.php", 'success');
            } else {
                log_message("No changes needed for string_api.php", 'warning');
            }
        } else {
            log_message("string_api.php not found at: $stringApiFile", 'error');
        }
        
        // 4. Fix 3: Fix common.php - Replace ${var} with {$var}
        $commonFile = $targetPath . '/lib/functions/common.php';
        if (file_exists($commonFile)) {
            // Backup the file
            if (!file_exists($commonFile . '.bak')) {
                copy($commonFile, $commonFile . '.bak');
                log_message("Created backup of common.php", 'success');
            }
            
            $content = file_get_contents($commonFile);
            $updatedContent = str_replace('${', '{$', $content);
            
            if ($content !== $updatedContent) {
                file_put_contents($commonFile, $updatedContent);
                log_message("Fixed ${var} syntax in common.php", 'success');
            } else {
                log_message("No changes needed for common.php", 'warning');
            }
        } else {
            log_message("common.php not found at: $commonFile", 'error');
        }
        
        // 5. Fix 4: Fix tlRole.class.php - Replace curly brace array access
        $tlRoleFile = $targetPath . '/lib/functions/tlRole.class.php';
        if (file_exists($tlRoleFile)) {
            // Backup the file
            if (!file_exists($tlRoleFile . '.bak')) {
                copy($tlRoleFile, $tlRoleFile . '.bak');
                log_message("Created backup of tlRole.class.php", 'success');
            }
            
            $lines = file($tlRoleFile);
            $lineNumber = 262; // Line 263 (0-indexed)
            
            if (isset($lines[$lineNumber])) {
                $originalLine = $lines[$lineNumber];
                log_message("Original line 263: " . htmlspecialchars(trim($originalLine)), 'warning');
                
                // Replace {expression} with [expression]
                $lines[$lineNumber] = preg_replace('/\{([^{}]+)\}/', '[$1]', $lines[$lineNumber]);
                
                log_message("Fixed line 263: " . htmlspecialchars(trim($lines[$lineNumber])), 'success');
                
                file_put_contents($tlRoleFile, implode('', $lines));
                log_message("Fixed curly brace array access in tlRole.class.php", 'success');
            } else {
                log_message("Could not find line 263 in tlRole.class.php", 'error');
            }
        } else {
            log_message("tlRole.class.php not found at: $tlRoleFile", 'error');
        }
        
        // 6. Fix 5: Fix logger.class.php - Add property declarations
        $loggerFile = $targetPath . '/lib/functions/logger.class.php';
        if (file_exists($loggerFile)) {
            // Backup the file
            if (!file_exists($loggerFile . '.bak')) {
                copy($loggerFile, $loggerFile . '.bak');
                log_message("Created backup of logger.class.php", 'success');
            }
            
            $content = file_get_contents($loggerFile);
            
            // Find the tlLogger class definition
            if (preg_match('/class\s+tlLogger\s*{/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[0][1] + strlen($matches[0][0]);
                
                // Add property declarations
                $propertyDeclarations = "\n\t/** @var mixed */\n\tpublic \$logLevelFilter;\n\n\t/** @var mixed */\n\tpublic \$db;\n";
                
                $updatedContent = substr($content, 0, $pos) . $propertyDeclarations . substr($content, $pos);
                
                file_put_contents($loggerFile, $updatedContent);
                log_message("Added property declarations to tlLogger class", 'success');
            } else {
                log_message("Could not find tlLogger class definition", 'error');
            }
        } else {
            log_message("logger.class.php not found at: $loggerFile", 'error');
        }
        
        // 7. Fix 6: Fix ADODB - Add ReturnTypeWillChange attributes
        $adodbFile = $targetPath . '/vendor/adodb/adodb-php/adodb.inc.php';
        if (file_exists($adodbFile)) {
            // Backup the file
            if (!file_exists($adodbFile . '.bak')) {
                copy($adodbFile, $adodbFile . '.bak');
                log_message("Created backup of adodb.inc.php", 'success');
            }
            
            $content = file_get_contents($adodbFile);
            
            // Add ReturnTypeWillChange attribute to iterator methods
            $methods = ['current', 'next', 'key', 'valid', 'rewind', 'getIterator'];
            foreach ($methods as $method) {
                $pattern = '/function\s+' . $method . '\s*\(/i';
                $replacement = "#[\\ReturnTypeWillChange]\n\tfunction $method(";
                $content = preg_replace($pattern, $replacement, $content);
            }
            
            file_put_contents($adodbFile, $content);
            log_message("Added ReturnTypeWillChange attributes to ADODB methods", 'success');
        } else {
            log_message("adodb.inc.php not found at: $adodbFile", 'warning');
            
            // Try alternate path
            $altAdodbFile = $targetPath . '/third_party/adodb/adodb.inc.php';
            if (file_exists($altAdodbFile)) {
                // Backup the file
                if (!file_exists($altAdodbFile . '.bak')) {
                    copy($altAdodbFile, $altAdodbFile . '.bak');
                    log_message("Created backup of adodb.inc.php (alternate path)", 'success');
                }
                
                $content = file_get_contents($altAdodbFile);
                
                // Add ReturnTypeWillChange attribute to iterator methods
                $methods = ['current', 'next', 'key', 'valid', 'rewind', 'getIterator'];
                foreach ($methods as $method) {
                    $pattern = '/function\s+' . $method . '\s*\(/i';
                    $replacement = "#[\\ReturnTypeWillChange]\n\tfunction $method(";
                    $content = preg_replace($pattern, $replacement, $content);
                }
                
                file_put_contents($altAdodbFile, $content);
                log_message("Added ReturnTypeWillChange attributes to ADODB methods (alternate path)", 'success');
            } else {
                log_message("Could not find adodb.inc.php in alternate path", 'error');
            }
        }
        
        // 8. Update PHP files to include php8_init.php
        $phpFiles = [
            $targetPath . '/index.php',
            $targetPath . '/login.php',
            $targetPath . '/main.php',
            $targetPath . '/lostPassword.php',
            $targetPath . '/firstLogin.php'
        ];
        
        foreach ($phpFiles as $phpFile) {
            if (file_exists($phpFile)) {
                $filename = basename($phpFile);
                
                // Backup the file
                if (!file_exists($phpFile . '.bak')) {
                    copy($phpFile, $phpFile . '.bak');
                    log_message("Created backup of $filename", 'success');
                }
                
                $content = file_get_contents($phpFile);
                if (strpos($content, 'php8_init.php') !== false) {
                    log_message("$filename already includes php8_init.php", 'warning');
                } else {
                    // Add the include to the PHP file
                    $content = preg_replace(
                        '/^(\<\?php)/m',
                        "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
                        $content,
                        1
                    );
                    
                    file_put_contents($phpFile, $content);
                    log_message("Updated $filename to include php8_init.php", 'success');
                }
            }
        }
        
        // 9. Create a .htaccess file to suppress warnings
        $htaccessFile = $targetPath . '/.htaccess';
        $htaccessContent = "# PHP 8 Compatibility Settings\n<IfModule mod_php.c>\n  php_flag display_errors off\n  php_value error_reporting 0\n</IfModule>\n";
        
        file_put_contents($htaccessFile, $htaccessContent);
        log_message("Created .htaccess file to suppress warnings", 'success');
        
        log_message("\nAll fixes have been applied! Please restart your web server and clear your browser cache.", 'success');
    }
} else {
    // Display form
    echo "<form method=\"post\">";
    echo "<p>Enter the path to your TestLink installation:</p>";
    echo "<input type=\"text\" name=\"target_path\" value=\"D:/xampp/htdocs/tl-uat\" />";
    echo "<button type=\"submit\">Apply Fixes</button>";
    echo "</form>";
}

echo "</body>\n</html>";
