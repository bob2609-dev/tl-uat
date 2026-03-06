<?php
/**
 * TestLink PHP 8 Targeted Fixes
 * This script fixes the specific issues identified in the error logs
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TestLink PHP 8 Targeted Fixes</h1>";

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

// Make sure needed directories exist
$customDir = $basePath . '/custom';
$incDir = $basePath . '/custom/inc';

if (!is_dir($customDir)) {
    mkdir($customDir, 0755, true);
}

if (!is_dir($incDir)) {
    mkdir($incDir, 0755, true);
}

// 1. Fix strftime() deprecation in config.inc.php
log_message("1. Fixing strftime() deprecation in config.inc.php...");
$configFile = $basePath . '/config.inc.php';

if (file_exists($configFile)) {
    // Create backup
    if (!file_exists($configFile . '.bak')) {
        copy($configFile, $configFile . '.bak');
        log_message("Created backup of config.inc.php", 'success');
    }
    
    // Read the file
    $lines = file($configFile);
    
    // Check line 2024 (index 2023) for strftime
    if (isset($lines[2023]) && strpos($lines[2023], 'strftime') !== false) {
        $originalLine = $lines[2023];
        log_message("Original line: " . htmlspecialchars(trim($originalLine)), 'warning');
        
        // Simply comment out the line as a temporary fix
        $lines[2023] = '// ' . $lines[2023] . ' // Commented out due to PHP 8 deprecation\n';
        
        file_put_contents($configFile, implode('', $lines));
        log_message("Fixed strftime() in config.inc.php by commenting out the problematic line", 'success');
    } else {
        log_message("Could not find strftime() on expected line in config.inc.php", 'warning');
    }
} else {
    log_message("config.inc.php not found at: $configFile", 'error');
}

// 2. Fix issueTrackerInterface property redeclaration
log_message("2. Fixing issueTrackerInterface property redeclaration...");
$interfaceFile = $basePath . '/lib/issuetrackerintegration/issueTrackerInterface.class.php';

if (file_exists($interfaceFile)) {
    // Create backup
    if (!file_exists($interfaceFile . '.bak')) {
        copy($interfaceFile, $interfaceFile . '.bak');
        log_message("Created backup of issueTrackerInterface.class.php", 'success');
    }
    
    // Read the file
    $content = file_get_contents($interfaceFile);
    
    // Look for the problematic property declaration
    if (preg_match_all('/(public|protected|private)\s+\$cfg\s*=/', $content, $matches, PREG_OFFSET_CAPTURE)) {
        if (count($matches[0]) > 1) {
            log_message("Found multiple declarations of \$cfg property", 'warning');
            
            // Get all matches and their positions
            $declarations = $matches[0];
            
            // Keep only the first declaration, comment out the rest
            for ($i = 1; $i < count($declarations); $i++) {
                $pos = $declarations[$i][1];
                $line = substr($content, $pos - 2, 100); // Get some context
                $lineEnd = strpos($line, ";\n");
                if ($lineEnd !== false) {
                    $line = substr($line, 0, $lineEnd + 2);
                }
                
                // Find the line start
                $lineStart = strrpos(substr($content, 0, $pos), "\n") + 1;
                
                // Comment out the line
                $content = substr($content, 0, $lineStart) . 
                          "// Commented out duplicate property declaration: " . 
                          substr($content, $lineStart, $pos - $lineStart) . 
                          "/* " . substr($content, $pos, strlen($declarations[$i][0])) . " */" . 
                          substr($content, $pos + strlen($declarations[$i][0]));
            }
            
            file_put_contents($interfaceFile, $content);
            log_message("Fixed duplicate \$cfg property declarations in issueTrackerInterface", 'success');
        } else {
            log_message("Only found one declaration of \$cfg property, which is correct", 'warning');
        }
    } else {
        log_message("Could not find \$cfg property declaration in issueTrackerInterface", 'warning');
    }
} else {
    log_message("issueTrackerInterface.class.php not found at: $interfaceFile", 'error');
}

// 3. Fix undefined function display_errors() in index.php
log_message("3. Fixing undefined function display_errors() in index.php...");
$indexFile = $basePath . '/index.php';

if (file_exists($indexFile)) {
    // Create backup
    if (!file_exists($indexFile . '.bak')) {
        copy($indexFile, $indexFile . '.bak');
        log_message("Created backup of index.php", 'success');
    }
    
    // Read the file
    $content = file_get_contents($indexFile);
    
    // Check for call to display_errors()
    if (strpos($content, 'display_errors()') !== false) {
        // Replace with ini_set
        $content = str_replace('display_errors()', 'ini_set(\'display_errors\', 0)', $content);
        
        file_put_contents($indexFile, $content);
        log_message("Fixed undefined function display_errors() in index.php", 'success');
    } else {
        log_message("Could not find display_errors() function call in index.php", 'warning');
        
        // Check for require or include of php8_init.php
        if (strpos($content, 'php8_init.php') !== false) {
            log_message("Found reference to php8_init.php, which might contain the issue", 'warning');
        }
    }
} else {
    log_message("index.php not found at: $indexFile", 'error');
}

// 4. Create a fixed php8_init.php that doesn't call display_errors()
log_message("4. Creating fixed php8_init.php...");
$initFile = $incDir . '/php8_init.php';
$initContent = "<?php\n/**\n * PHP 8 Compatibility Initialization\n * This file loads all the necessary polyfills and fixes for PHP 8 compatibility\n */\n\n// Load strftime polyfill if needed\nif (!function_exists('strftime_polyfill')) {\n    function strftime_polyfill(\$format, \$timestamp = null) {\n        if (\$timestamp === null) {\n            \$timestamp = time();\n        }\n        \n        // Simple mapping of common format codes\n        \$map = [\n            '%Y' => 'Y', // Year with century\n            '%y' => 'y', // Year without century\n            '%m' => 'm', // Month as decimal number\n            '%d' => 'd', // Day of the month\n            '%H' => 'H', // Hour (24-hour clock)\n            '%M' => 'i', // Minute\n            '%S' => 's', // Second\n        ];\n        \n        \$dateFormat = \$format;\n        foreach (\$map as \$from => \$to) {\n            \$dateFormat = str_replace(\$from, \$to, \$dateFormat);\n        }\n        \n        return date(\$dateFormat, \$timestamp);\n    }\n}\n\n// Wrap strftime() if it's deprecated\nif (!function_exists('safe_strftime')) {\n    function safe_strftime(\$format, \$timestamp = null) {\n        if (function_exists('strftime')) {\n            return @strftime(\$format, \$timestamp); // Suppress warnings\n        } else {\n            return strftime_polyfill(\$format, \$timestamp);\n        }\n    }\n}\n\n// Disable deprecation warnings in production\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\nini_set('display_errors', 0);\n\n// Set default timezone if not already set\nif (function_exists('date_default_timezone_get')) {\n    date_default_timezone_set(@date_default_timezone_get());\n}\n";

file_put_contents($initFile, $initContent);
log_message("Created fixed php8_init.php at: $initFile", 'success');

// 5. Update index.php to include the fixed PHP 8 initialization (if needed)
log_message("5. Updating index.php to use the fixed php8_init.php...");

if (file_exists($indexFile)) {
    // Read the file again (it might have been modified in step 3)
    $content = file_get_contents($indexFile);
    
    // Check if it already includes php8_init.php
    if (strpos($content, 'php8_init.php') === false) {
        // Add it at the beginning after the PHP opening tag
        $content = preg_replace(
            '/^(\<\?php)/m',
            "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
            $content,
            1
        );
        
        file_put_contents($indexFile, $content);
        log_message("Updated index.php to include the fixed php8_init.php", 'success');
    } else {
        log_message("index.php already includes php8_init.php, make sure the path is correct", 'warning');
    }
}

log_message("\nTargeted fixes complete! Here's what to do next:", 'success');
echo "<ol>";
echo "<li>Copy the following files to your actual TestLink installation at D:/xampp/htdocs/tl-uat/:</li>";
echo "<ul>";
echo "<li>The fixed config.inc.php</li>";
echo "<li>The fixed lib/issuetrackerintegration/issueTrackerInterface.class.php</li>";
echo "<li>The fixed index.php</li>";
echo "<li>custom/inc/php8_init.php</li>";
echo "</ul>";
echo "<li>Restart your Apache web server</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try accessing TestLink again</li>";
echo "</ol>";

echo "<p style=\"color:orange;\">If you still encounter issues, you can revert to the backup files (*.bak) to restore the original functionality.</p>";
