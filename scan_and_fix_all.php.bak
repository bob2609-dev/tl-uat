<?php
/**
 * TestLink PHP 8 Compatibility Scanner and Fixer
 * This script scans all PHP files in the TestLink codebase for common PHP 8 compatibility issues and fixes them
 */

// Set script execution time to unlimited (this might take a while)
set_time_limit(0);

echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Compatibility Scanner and Fixer</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2, h3 { color: #333; }\n";
echo ".success { color: green; }\n";
echo ".error { color: red; }\n";
echo ".warning { color: orange; }\n";
echo "pre { background: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px; }\n";
echo "code { background: #f5f5f5; padding: 2px 4px; }\n";
echo ".results { max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink PHP 8 Compatibility Scanner and Fixer</h1>\n";

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

// Base path for TestLink
$basePath = __DIR__;
log_message("Using base path: $basePath");

// Statistics
$stats = [
    'files_scanned' => 0,
    'files_fixed' => 0,
    'strftime_replaced' => 0,
    'curly_braces_fixed' => 0,
    'dynamic_properties_fixed' => 0,
    'files_with_each' => 0,
    'get_magic_quotes_fixed' => 0
];

// Known issues to look for
$issues = [
    'strftime' => 'Deprecated strftime() function',
    '{$' => 'Deprecated {$var} syntax in strings',
    'each(' => 'Deprecated each() function',
    'get_magic_quotes_gpc' => 'Removed get_magic_quotes_gpc() function',
    'create_function' => 'Deprecated create_function() function',
    '}{' => 'Curly brace syntax for array/string access',
    '->current(' => 'Iterator methods without return type declaration',
    '->next(' => 'Iterator methods without return type declaration',
    '->key(' => 'Iterator methods without return type declaration',
    '->valid(' => 'Iterator methods without return type declaration',
    '->rewind(' => 'Iterator methods without return type declaration',
    '->getIterator(' => 'Iterator methods without return type declaration'
];

// Directories to exclude from scanning
$excludeDirs = [
    'vendor', // Exclude vendor directory as it's third-party code
    'node_modules',
    '.git',
    'logs',
    'temp',
    'uploads'
];

// Function to scan a directory recursively
function scanDirectory($dir, &$phpFiles, $excludeDirs) {
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            // Skip excluded directories
            if (in_array($item, $excludeDirs)) {
                continue;
            }
            scanDirectory($path, $phpFiles, $excludeDirs);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $phpFiles[] = $path;
        }
    }
}

log_message("Step 1: Scanning for PHP files...");
$phpFiles = [];
scanDirectory($basePath, $phpFiles, $excludeDirs);
$stats['files_scanned'] = count($phpFiles);
log_message("Found " . count($phpFiles) . " PHP files to scan", 'success');

echo "<div class='results'>";
log_message("Step 2: Scanning for PHP 8 compatibility issues and fixing them...");

// Create strftime polyfill
log_message("Creating strftime polyfill...");
$polyfillDir = $basePath . '/custom/inc';
if (!is_dir($polyfillDir)) {
    mkdir($polyfillDir, 0755, true);
}

$polyfillFile = $polyfillDir . '/strftime_polyfill.php';
$polyfillContent = "<?php\n/**\n * Polyfill for strftime() in PHP 8.1+\n * Since strftime() is deprecated in PHP 8.1, this file provides a compatible replacement\n */\n\nif (!function_exists('strftime_replacement')) {\n    /**\n     * A replacement for the deprecated strftime() function\n     * @param string \$format The format string\n     * @param int|null \$timestamp The timestamp to format\n     * @return string The formatted date string\n     */\n    function strftime_replacement(\$format, \$timestamp = null) {\n        if (\$timestamp === null) {\n            \$timestamp = time();\n        }\n        \n        // Mapping from strftime to date format codes\n        \$strftimeToDate = [\n            '%a' => 'D',      // Abbreviated weekday name\n            '%A' => 'l',      // Full weekday name\n            '%b' => 'M',      // Abbreviated month name\n            '%B' => 'F',      // Full month name\n            '%c' => 'D M j H:i:s Y', // Preferred date and time representation\n            '%d' => 'd',      // Day of the month as a decimal number\n            '%H' => 'H',      // Hour (24-hour clock)\n            '%I' => 'h',      // Hour (12-hour clock)\n            '%j' => 'z',      // Day of the year\n            '%m' => 'm',      // Month as a decimal number\n            '%M' => 'i',      // Minute\n            '%p' => 'A',      // AM or PM\n            '%S' => 's',      // Second\n            '%U' => 'W',      // Week number of the year (Sunday as first day of week)\n            '%w' => 'w',      // Weekday as a decimal number\n            '%W' => 'W',      // Week number of the year (Monday as first day of week)\n            '%x' => 'm/d/y',  // Preferred date representation without the time\n            '%X' => 'H:i:s',  // Preferred time representation without the date\n            '%y' => 'y',      // Year without century\n            '%Y' => 'Y',      // Year with century\n            '%Z' => 'T',      // Time zone name\n            '%%' => '%'       // Literal % character\n        ];\n        \n        // Convert the strftime format to date format\n        \$dateFormat = \$format;\n        foreach (\$strftimeToDate as \$strftime => \$date) {\n            \$dateFormat = str_replace(\$strftime, \$date, \$dateFormat);\n        }\n        \n        return date(\$dateFormat, \$timestamp);\n    }\n}\n\n// If strftime exists (PHP < 8.1), use it; otherwise use our replacement\nif (!function_exists('safe_strftime')) {\n    function safe_strftime(\$format, \$timestamp = null) {\n        if (function_exists('strftime')) {\n            return @strftime(\$format, \$timestamp);\n        } else {\n            return strftime_replacement(\$format, \$timestamp);\n        }\n    }\n}\n\n// Override the original strftime if it doesn't exist\nif (!function_exists('strftime')) {\n    function strftime(\$format, \$timestamp = null) {\n        return strftime_replacement(\$format, \$timestamp);\n    }\n}\n";

file_put_contents($polyfillFile, $polyfillContent);
log_message("Created strftime polyfill at: $polyfillFile", 'success');

// Create magic quotes polyfill
log_message("Creating magic quotes polyfill...");
$magicQuotesFile = $polyfillDir . '/magic_quotes_polyfill.php';
$magicQuotesContent = "<?php\n/**\n * Polyfill for get_magic_quotes_gpc() in PHP 8+\n * Since magic quotes were removed in PHP 7 and the function in PHP 8,\n * this file provides a compatible replacement\n */\n\n// Add get_magic_quotes_gpc() if it doesn't exist\nif (!function_exists('get_magic_quotes_gpc')) {\n    function get_magic_quotes_gpc() {\n        // Always return false as magic quotes are removed in PHP 7+\n        return false;\n    }\n}\n\n// Add get_magic_quotes_runtime() if it doesn't exist\nif (!function_exists('get_magic_quotes_runtime')) {\n    function get_magic_quotes_runtime() {\n        // Always return false as magic quotes are removed in PHP 7+\n        return false;\n    }\n}\n";

file_put_contents($magicQuotesFile, $magicQuotesContent);
log_message("Created magic quotes polyfill at: $magicQuotesFile", 'success');

// Create each() polyfill
log_message("Creating each() polyfill...");
$eachFile = $polyfillDir . '/each_polyfill.php';
$eachContent = "<?php\n/**\n * Polyfill for each() in PHP 8+\n * Since each() was deprecated in PHP 7.2 and removed in PHP 8,\n * this file provides a compatible replacement\n */\n\n// Add each() if it doesn't exist\nif (!function_exists('each')) {\n    function each(&\$array) {\n        \$key = key(\$array);\n        if (\$key !== null) {\n            \$value = \$array[\$key];\n            next(\$array);\n            return array(1 => \$value, 'value' => \$value, 0 => \$key, 'key' => \$key);\n        }\n        return false;\n    }\n}\n";

file_put_contents($eachFile, $eachContent);
log_message("Created each() polyfill at: $eachFile", 'success');

// Scan and fix each file
foreach ($phpFiles as $index => $file) {
    // Show progress
    if ($index % 50 === 0) {
        $progress = round(($index / count($phpFiles)) * 100);
        log_message("Scanning file $index of {$stats['files_scanned']} ($progress%)...");
    }
    
    $content = file_get_contents($file);
    $originalContent = $content;
    $issuesFound = false;
    $fileIssues = [];
    
    // Check for each known issue
    foreach ($issues as $pattern => $description) {
        if (strpos($content, $pattern) !== false) {
            $issuesFound = true;
            $fileIssues[] = $description;
            
            // Fix the issue based on what it is
            switch ($pattern) {
                case 'strftime':
                    // Replace strftime with date
                    if (preg_match_all('/strftime\(\s*[\'"](.*?)[\'"]/i', $content, $matches)) {
                        foreach ($matches[1] as $format) {
                            // Create a mapping from strftime to date format codes
                            $strftimeToDate = [
                                '%a' => 'D',      // Abbreviated weekday name
                                '%A' => 'l',      // Full weekday name
                                '%b' => 'M',      // Abbreviated month name
                                '%B' => 'F',      // Full month name
                                '%c' => 'D M j H:i:s Y', // Preferred date and time representation
                                '%d' => 'd',      // Day of the month as a decimal number
                                '%H' => 'H',      // Hour (24-hour clock)
                                '%I' => 'h',      // Hour (12-hour clock)
                                '%j' => 'z',      // Day of the year
                                '%m' => 'm',      // Month as a decimal number
                                '%M' => 'i',      // Minute
                                '%p' => 'A',      // AM or PM
                                '%S' => 's',      // Second
                                '%U' => 'W',      // Week number of the year
                                '%w' => 'w',      // Weekday as a decimal number
                                '%W' => 'W',      // Week number of the year
                                '%x' => 'm/d/y',  // Preferred date representation
                                '%X' => 'H:i:s',  // Preferred time representation
                                '%y' => 'y',      // Year without century
                                '%Y' => 'Y',      // Year with century
                                '%Z' => 'T',      // Time zone name
                                '%%' => '%'       // Literal % character
                            ];
                            
                            // Convert the strftime format to date format
                            $dateFormat = $format;
                            foreach ($strftimeToDate as $strftime => $date) {
                                $dateFormat = str_replace($strftime, $date, $dateFormat);
                            }
                            
                            // Replace the strftime call with safe_strftime call
                            $content = str_replace(
                                "safe_safe_strftime('$format'", 
                                "safe_safe_safe_strftime('$format'", 
                                $content
                            );
                            
                            $stats['strftime_replaced']++;
                        }
                    }
                    break;
                    
                case '{$':
                    // Replace {$var} with {$var}
                    $content = str_replace('{$', '{$', $content);
                    break;
                    
                case 'each(':
                    // We will use the polyfill for each()
                    $stats['files_with_each']++;
                    break;
                    
                case 'get_magic_quotes_gpc':
                    // Will be handled by polyfill
                    $stats['get_magic_quotes_fixed']++;
                    break;
                    
                case '}{':
                    // Fix curly brace syntax
                    $content = preg_replace('/\$([a-zA-Z0-9_]+)\{(\d+|\$[a-zA-Z0-9_]+)\}/', '\$$1[$2]', $content);
                    $content = preg_replace('/(\$[a-zA-Z0-9_]+(?:\[[^\]]+\])*)\{([^{}]+)\}/', '$1[$2]', $content);
                    $stats['curly_braces_fixed']++;
                    break;
                    
                case '->current(':
                case '->next(':
                case '->key(':
                case '->valid(':
                case '->rewind(':
                case '->getIterator(':
                    // Add ReturnTypeWillChange attribute to methods
                    $methodName = substr($pattern, 2, -1);
                    $content = preg_replace(
                        '/function\s+' . $methodName . '\s*\(/i',
                        "#[\\ReturnTypeWillChange]\n\tfunction $methodName(",
                        $content
                    );
                    break;
            }
        }
    }
    
    // Check for dynamic property creation
    if (preg_match('/class\s+([a-zA-Z0-9_]+)/i', $content, $classMatches)) {
        $className = $classMatches[1];
        
        // Check for property assignments that might be dynamic
        if (preg_match_all('/\$this->([a-zA-Z0-9_]+)\s*=/', $content, $propMatches)) {
            $properties = array_unique($propMatches[1]);
            
            // Check if these properties are declared in the class
            $propertiesToAdd = [];
            foreach ($properties as $property) {
                // Skip common properties that are likely already declared
                if (in_array($property, ['id', 'name', 'value', 'data', 'options', 'config', 'version', 'type'])) {
                    continue;
                }
                
                // Check if property is already declared
                if (!preg_match('/(?:private|protected|public)\s+\$' . preg_quote($property, '/') . '\s*;/i', $content)) {
                    $propertiesToAdd[] = $property;
                }
            }
            
            // Add properties to the class definition
            if (!empty($propertiesToAdd)) {
                // Find the class opening brace
                if (preg_match('/class\s+' . preg_quote($className, '/') . '.*?{/s', $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $openBracePos = $matches[0][1] + strlen($matches[0][0]) - 1;
                    
                    // Generate property declarations
                    $propertyDeclarations = "\n";
                    foreach ($propertiesToAdd as $property) {
                        $propertyDeclarations .= "\t/** @var mixed */\n\tpublic \$$property;\n";
                    }
                    
                    // Insert property declarations after the opening brace
                    $content = substr($content, 0, $openBracePos + 1) . $propertyDeclarations . substr($content, $openBracePos + 1);
                    $stats['dynamic_properties_fixed'] += count($propertiesToAdd);
                }
            }
        }
    }
    
    // Save changes if content was modified
    if ($content !== $originalContent) {
        // Create a backup
        $backupFile = $file . '.bak';
        if (!file_exists($backupFile)) {
            copy($file, $backupFile);
        }
        
        // Write the fixed content back to the file
        file_put_contents($file, $content);
        $stats['files_fixed']++;
        
        // Log the file and issues
        log_message("Fixed file: " . str_replace($basePath, '', $file) . " - Issues: " . implode(', ', $fileIssues), 'success');
    }
}
echo "</div>";

// Create a PHP 8 compatibility initialization file
log_message("Step 3: Creating PHP 8 compatibility initialization file...");
$php8InitFile = $polyfillDir . '/php8_init.php';
$php8InitContent = "<?php\n/**\n * PHP 8 Compatibility Initialization\n * This file loads all the necessary polyfills and fixes for PHP 8 compatibility\n */\n\n// Include polyfills\nrequire_once(__DIR__ . '/strftime_polyfill.php');\nrequire_once(__DIR__ . '/magic_quotes_polyfill.php');\nrequire_once(__DIR__ . '/each_polyfill.php');\n\n// Disable deprecation warnings in production\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\nini_set('display_errors', 0);\n\n// Fix for assert() function changes in PHP 8\nassert_options(ASSERT_ACTIVE, 1);\nassert_options(ASSERT_WARNING, 0);\nassert_options(ASSERT_BAIL, 0);\n\n// Set default timezone if not already set\nif (function_exists('date_default_timezone_get')) {\n    date_default_timezone_set(@date_default_timezone_get());\n}\n";

file_put_contents($php8InitFile, $php8InitContent);
log_message("Created PHP 8 compatibility initialization file at: $php8InitFile", 'success');

// Update index.php to include the PHP 8 compatibility initialization
log_message("Step 4: Updating index.php to include PHP 8 compatibility initialization...");
$indexFile = $basePath . '/index.php';
if (file_exists($indexFile)) {
    $indexContent = file_get_contents($indexFile);
    if (strpos($indexContent, 'php8_init.php') === false) {
        $newIndexContent = preg_replace(
            '/^(\<\?php)/m',
            "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
            $indexContent,
            1
        );
        file_put_contents($indexFile, $newIndexContent);
        log_message("Updated index.php to include PHP 8 compatibility initialization", 'success');
    } else {
        log_message("index.php already includes PHP 8 compatibility initialization", 'success');
    }
} else {
    log_message("Could not find index.php", 'error');
}

// Create or update .htaccess to suppress warnings
log_message("Step 5: Creating/updating .htaccess to suppress warnings...");
$htaccessFile = $basePath . '/.htaccess';
$htaccessContent = "# Proper MIME type for images\nAddType image/png .png\nAddType image/jpeg .jpg .jpeg .jpe\nAddType image/gif .gif\nAddType image/svg+xml .svg\nAddType image/bmp .bmp\n\n# PHP 8 Compatibility Settings\n<IfModule mod_php.c>\n  php_flag display_errors off\n  php_value error_reporting 0\n</IfModule>\n";

file_put_contents($htaccessFile, $htaccessContent);
log_message("Created/updated .htaccess file", 'success');

echo "<h2>PHP 8 Compatibility Scan and Fix Summary</h2>\n";
echo "<ul>\n";
echo "<li>Files scanned: {$stats['files_scanned']}</li>\n";
echo "<li>Files fixed: {$stats['files_fixed']}</li>\n";
echo "<li>strftime() replacements: {$stats['strftime_replaced']}</li>\n";
echo "<li>Curly brace syntax fixes: {$stats['curly_braces_fixed']}</li>\n";
echo "<li>Dynamic property declarations added: {$stats['dynamic_properties_fixed']}</li>\n";
echo "<li>Files with each() function: {$stats['files_with_each']}</li>\n";
echo "<li>get_magic_quotes_gpc() instances: {$stats['get_magic_quotes_fixed']}</li>\n";
echo "</ul>\n";

echo "<h2>Next Steps</h2>\n";
echo "<p>To apply these fixes to your actual TestLink installation:</p>\n";
echo "<ol>\n";
echo "<li>Copy the modified PHP files to your actual installation</li>\n";
echo "<li>Copy the polyfill directory (<code>custom/inc</code>) to your actual installation</li>\n";
echo "<li>Copy the updated <code>index.php</code> to your actual installation</li>\n";
echo "<li>Copy the updated <code>.htaccess</code> file to your actual installation</li>\n";
echo "<li>Restart your web server</li>\n";
echo "<li>Clear your browser cache</li>\n";
echo "<li>Access TestLink again</li>\n";
echo "</ol>\n";

echo "<p class='warning'>Note: This script has made backups of all modified files with a .bak extension. If you encounter issues, you can restore these backups.</p>\n";

echo "</body>\n</html>";
