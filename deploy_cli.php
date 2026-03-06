<?php
/**
 * TestLink PHP 8 Fix Command-Line Deployment Script
 * This script copies the fixed files from the backup directory to the actual installation
 */

// Source path (backup directory)
$sourcePath = __DIR__;
echo "Source path: $sourcePath\n";

// Target path (actual TestLink installation)
$targetPath = 'D:/xampp/htdocs/tl-uat';
echo "Target path: $targetPath\n";

if (!is_dir($targetPath)) {
    echo "ERROR: Target directory does not exist: $targetPath\n";
    exit(1);
}

echo "Starting deployment...\n";

// 1. Make sure custom/inc directory exists in target
$targetCustomIncDir = $targetPath . '/custom/inc';
if (!is_dir($targetCustomIncDir)) {
    mkdir($targetCustomIncDir, 0755, true);
    echo "Created directory: $targetCustomIncDir\n";
}

// 2. Create php8_init.php in target
$targetInitFile = $targetCustomIncDir . '/php8_init.php';
$initContent = "<?php\n/**\n * PHP 8 Compatibility Initialization\n * This file loads all the necessary polyfills and fixes for PHP 8 compatibility\n */\n\n// Load strftime polyfill if needed\nif (!function_exists('strftime_polyfill')) {\n    function strftime_polyfill(\$format, \$timestamp = null) {\n        if (\$timestamp === null) {\n            \$timestamp = time();\n        }\n        \n        // Simple mapping of common format codes\n        \$map = [\n            '%Y' => 'Y', // Year with century\n            '%y' => 'y', // Year without century\n            '%m' => 'm', // Month as decimal number\n            '%d' => 'd', // Day of the month\n            '%H' => 'H', // Hour (24-hour clock)\n            '%M' => 'i', // Minute\n            '%S' => 's', // Second\n        ];\n        \n        \$dateFormat = \$format;\n        foreach (\$map as \$from => \$to) {\n            \$dateFormat = str_replace(\$from, \$to, \$dateFormat);\n        }\n        \n        return date(\$dateFormat, \$timestamp);\n    }\n}\n\n// Wrap strftime() if it's deprecated\nif (!function_exists('safe_strftime')) {\n    function safe_strftime(\$format, \$timestamp = null) {\n        if (function_exists('strftime')) {\n            return @strftime(\$format, \$timestamp); // Suppress warnings\n        } else {\n            return strftime_polyfill(\$format, \$timestamp);\n        }\n    }\n}\n\n// Disable deprecation warnings in production\nerror_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);\nini_set('display_errors', 0);\n\n// Set default timezone if not already set\nif (function_exists('date_default_timezone_get')) {\n    date_default_timezone_set(@date_default_timezone_get());\n}\n";

if (file_put_contents($targetInitFile, $initContent)) {
    echo "Created php8_init.php in target\n";
} else {
    echo "ERROR: Failed to create php8_init.php\n";
    exit(1);
}

// 3. Check if we need to fix login.php
$loginFile = $targetPath . '/login.php';
if (file_exists($loginFile)) {
    // Backup the file
    if (!file_exists($loginFile . '.bak')) {
        copy($loginFile, $loginFile . '.bak');
        echo "Created backup of login.php\n";
    }
    
    $loginContent = file_get_contents($loginFile);
    if (strpos($loginContent, 'php8_init.php') !== false) {
        // Already includes it, but we should check the path
        echo "login.php already includes php8_init.php\n";
    } else {
        // Add the include to login.php
        $loginContent = preg_replace(
            '/^(\<\?php)/m',
            "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
            $loginContent,
            1
        );
        
        if (file_put_contents($loginFile, $loginContent)) {
            echo "Updated login.php to include php8_init.php\n";
        } else {
            echo "ERROR: Failed to update login.php\n";
        }
    }
} else {
    echo "WARNING: login.php not found at: $loginFile\n";
}

// 4. Fix the index.php file
$indexFile = $targetPath . '/index.php';
if (file_exists($indexFile)) {
    // Backup the file
    if (!file_exists($indexFile . '.bak')) {
        copy($indexFile, $indexFile . '.bak');
        echo "Created backup of index.php\n";
    }
    
    $indexContent = file_get_contents($indexFile);
    if (strpos($indexContent, 'php8_init.php') !== false) {
        echo "index.php already includes php8_init.php\n";
    } else {
        // Add the include to index.php
        $indexContent = preg_replace(
            '/^(\<\?php)/m',
            "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
            $indexContent,
            1
        );
        
        if (file_put_contents($indexFile, $indexContent)) {
            echo "Updated index.php to include php8_init.php\n";
        } else {
            echo "ERROR: Failed to update index.php\n";
        }
    }
} else {
    echo "WARNING: index.php not found at: $indexFile\n";
}

// 5. Fix all PHP files in the root directory to include the compatibility layer
echo "Checking all PHP files in the root directory...\n";
$rootFiles = glob($targetPath . '/*.php');
foreach ($rootFiles as $file) {
    $filename = basename($file);
    
    // Skip index.php and login.php which we've already handled
    if ($filename === 'index.php' || $filename === 'login.php') {
        continue;
    }
    
    // Backup the file
    if (!file_exists($file . '.bak')) {
        copy($file, $file . '.bak');
        echo "Created backup of $filename\n";
    }
    
    $content = file_get_contents($file);
    if (strpos($content, 'php8_init.php') !== false) {
        echo "$filename already includes php8_init.php\n";
    } else {
        // Add the include to the PHP file
        $content = preg_replace(
            '/^(\<\?php)/m',
            "<?php\n// Include PHP 8 compatibility layer\nrequire_once('custom/inc/php8_init.php');",
            $content,
            1
        );
        
        if (file_put_contents($file, $content)) {
            echo "Updated $filename to include php8_init.php\n";
        } else {
            echo "ERROR: Failed to update $filename\n";
        }
    }
}

echo "\nDeployment complete! Please restart your web server and clear your browser cache.\n";
