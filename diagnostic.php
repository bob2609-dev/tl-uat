<?php
// Include PHP 8 compatibility layer
// require_once('custom/inc/php8_init.php');
/**
 * TestLink Diagnostic Tool for Redmine Integration
 * 
 * This script checks for common issues with the Redmine integration
 */

// Do NOT force verbose error display in production
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Restrict access: allow only localhost
$remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
$isLocal = in_array($remote, ['127.0.0.1','::1']);
if (!$isLocal) {
    header('HTTP/1.1 403 Forbidden');
    echo '403 Forbidden';
    exit;
}

// Output as plain text for command line, HTML for browser
$isCli = (php_sapi_name() == 'cli');
if (!$isCli) {
    header('Content-Type: text/html');
    echo "<!DOCTYPE html>\n<html>\n<head>\n";
    echo "<title>TestLink Redmine Diagnostic</title>\n";
    echo "<style>\nbody { font-family: Arial, sans-serif; margin: 20px; }\n";
    echo "h1, h2, h3 { color: #1a73e8; }\n";
    echo "pre { background: #f5f5f5; padding: 10px; overflow: auto; }\n";
    echo "code { background: #f5f5f5; padding: 2px 4px; }\n";
    echo "table { border-collapse: collapse; width: 100%; }\n";
    echo "table, th, td { border: 1px solid #ddd; }\n";
    echo "th, td { padding: 8px; text-align: left; }\n";
    echo "th { background-color: #f2f2f2; }\n";
    echo "</style>\n";
    echo "</head>\n<body>\n";
    echo "<h1>TestLink Redmine Integration Diagnostic</h1>\n";
} else {
    echo "TestLink Redmine Integration Diagnostic\n";
    echo "======================================\n\n";
}

// Report helper function
function reportSection($title, $result, $details = null) {
    global $isCli;
    
    if ($isCli) {
        echo "\n=== $title ===\n";
        echo $result ? "✓ PASS" : "✗ FAIL";
        if ($details) {
            echo "\n";
            echo $details;
        }
        echo "\n";
    } else {
        echo "<h2>$title</h2>\n";
        echo "<p style='font-weight: bold; color: " . ($result ? "green" : "red") . "'>";
        echo $result ? "✓ PASS" : "✗ FAIL";
        echo "</p>\n";
        if ($details) {
            if (is_array($details)) {
                echo "<table>\n";
                echo "<tr><th>Item</th><th>Status</th><th>Details</th></tr>\n";
                foreach ($details as $item) {
                    echo "<tr>";
                    echo "<td>{$item['name']}</td>";
                    echo "<td style='color: " . ($item['status'] ? "green" : "red") . "'>" . 
                         ($item['status'] ? "✓ PASS" : "✗ FAIL") . "</td>";
                    echo "<td>{$item['details']}</td>";
                    echo "</tr>\n";
                }
                echo "</table>\n";
            } else {
                echo "<pre>$details</pre>\n";
            }
        }
    }
}

// Check if file exists with correct path
function checkFile($path, $description) {
    $exists = file_exists($path);
    $details = $exists ? "File found at $path" : "File not found at $path";
    return [
        'name' => $description,
        'status' => $exists,
        'details' => $details
    ];
}

// ----------------------------------------------------------------
// Step 1: Check file existence

$files = [
    // Check both folder locations to diagnose path issues
    ['path' => 'lib/issuetrackerintegration/redminerestInterface.class.php', 
     'desc' => 'Redmine Interface (issuetrackerintegration path)'],
    ['path' => 'lib/issuetrackers/redminerestInterface.class.php', 
     'desc' => 'Redmine Interface (issuetrackers path)'],
    ['path' => 'lib/issuetrackerintegration/issueTrackerInterface.class.php', 
     'desc' => 'Base Interface'],
    ['path' => 'lib/issuetrackerintegration/redminecreator.class.php', 
     'desc' => 'Custom Redmine Creator'],
    ['path' => 'third_party/redmine-php-api/lib/redmine-rest-api.php', 
     'desc' => 'Redmine PHP API'], 
    ['path' => 'custom_config.inc.php', 
     'desc' => 'Custom Config']
];

$fileResults = [];
foreach ($files as $file) {
    $fileResults[] = checkFile($file['path'], $file['desc']);
}

$allFilesExist = !in_array(false, array_column($fileResults, 'status'));
reportSection("File Existence Check", $allFilesExist, $fileResults);

// ----------------------------------------------------------------
// Step 2: Check method existence for key classes if files exist

$methodResults = [];

// Check redminerestInterface methods if the file exists
if (file_exists('lib/issuetrackerintegration/redminerestInterface.class.php')) {
    require_once('lib/issuetrackerintegration/redminerestInterface.class.php');
    
    if (class_exists('redminerestInterface')) {
        // Get all methods in the class
        $methods = get_class_methods('redminerestInterface');
        
        // Critical methods to check
        $criticalMethods = ['setCfg', 'checkEnv', 'connect', 'setResolvedStatusCfg'];
        
        foreach ($criticalMethods as $method) {
            $exists = in_array($method, $methods);
            $methodResults[] = [
                'name' => "redminerestInterface::$method()",
                'status' => $exists,
                'details' => $exists ? "Method exists" : "Method missing"
            ];
        }
    } else {
        $methodResults[] = [
            'name' => "redminerestInterface class",
            'status' => false,
            'details' => "Class doesn't exist despite file being present"
        ];
    }
} else if (file_exists('lib/issuetrackers/redminerestInterface.class.php')) {
    // Try the alternative path if the first one doesn't exist
    require_once('lib/issuetrackers/redminerestInterface.class.php');
    
    if (class_exists('redminerestInterface')) {
        $methods = get_class_methods('redminerestInterface');
        $criticalMethods = ['setCfg', 'checkEnv', 'connect', 'setResolvedStatusCfg'];
        
        foreach ($criticalMethods as $method) {
            $exists = in_array($method, $methods);
            $methodResults[] = [
                'name' => "redminerestInterface::$method() (alt path)",
                'status' => $exists,
                'details' => $exists ? "Method exists" : "Method missing"
            ];
        }
    } else {
        $methodResults[] = [
            'name' => "redminerestInterface class (alt path)",
            'status' => false,
            'details' => "Class doesn't exist despite file being present"
        ];
    }
}

$allMethodsExist = !in_array(false, array_column($methodResults, 'status'));
reportSection("Critical Method Check", $allMethodsExist, $methodResults);

// ----------------------------------------------------------------
// Step 3: Check TestLink configuration for Redmine integration

$configResults = [];

// We need to carefully load only what we need to check configuration
// without causing errors for missing files
if (file_exists('config.inc.php')) {
    // Try to extract just the relevant parts of configuration
    $configContent = file_get_contents('config.inc.php');
    
    // Check if issue tracker is enabled
    $issueTrackerEnabled = strpos($configContent, '$tlCfg->issue_tracker_enabled') !== false && 
                          strpos($configContent, '$tlCfg->issue_tracker_enabled = TRUE') !== false;
    
    $configResults[] = [
        'name' => "Issue Tracker Enabled",
        'status' => $issueTrackerEnabled,
        'details' => $issueTrackerEnabled ? 
            "\$tlCfg->issue_tracker_enabled is set to TRUE" : 
            "\$tlCfg->issue_tracker_enabled is not set or not TRUE"
    ];
    
    // Check for interface bugs configuration
    $interfaceBugsEnabled = strpos($configContent, '$tlCfg->interface_bugs') !== false &&
                           strpos($configContent, '$tlCfg->interface_bugs = TRUE') !== false;
    
    $configResults[] = [
        'name' => "Interface Bugs Enabled",
        'status' => $interfaceBugsEnabled,
        'details' => $interfaceBugsEnabled ? 
            "\$tlCfg->interface_bugs is set to TRUE" : 
            "\$tlCfg->interface_bugs is not set or not TRUE"
    ];
}

// Check custom config if it exists
if (file_exists('custom_config.inc.php')) {
    $customConfigContent = file_get_contents('custom_config.inc.php');
    
    // Check for custom issue tracker configuration
    $redmineConfigured = strpos($customConfigContent, 'redminerest') !== false ||
                        strpos($customConfigContent, 'redminecreator') !== false;
    
    $configResults[] = [
        'name' => "Redmine Configuration in custom_config.inc.php",
        'status' => $redmineConfigured,
        'details' => $redmineConfigured ? 
            "Found Redmine configuration in custom_config.inc.php" : 
            "No Redmine configuration found in custom_config.inc.php"
    ];
}

$configOk = !in_array(false, array_column($configResults, 'status'));
reportSection("Configuration Check", $configOk, $configResults);

// ----------------------------------------------------------------
// Step 4: Check PHP extensions

$extensionResults = [];

// Check for required PHP extensions
$extensions = ['curl', 'simplexml', 'json'];

foreach ($extensions as $ext) {
    $loaded = extension_loaded($ext);
    $extensionResults[] = [
        'name' => "PHP $ext extension",
        'status' => $loaded,
        'details' => $loaded ? "Extension loaded" : "Extension not loaded"
    ];
}

$allExtensionsLoaded = !in_array(false, array_column($extensionResults, 'status'));
reportSection("PHP Extensions Check", $allExtensionsLoaded, $extensionResults);

// ----------------------------------------------------------------
// Step 5: Create summary and recommendations

$issues = [];

if (!$allFilesExist) {
    $issues[] = "Missing critical files";
}

if (!$allMethodsExist) {
    $issues[] = "Missing critical methods in integration classes";
}

if (!$configOk) {
    $issues[] = "Configuration issues detected";
}

if (!$allExtensionsLoaded) {
    $issues[] = "Missing required PHP extensions";
}

if (!$isCli) {
    echo "<h2>Summary</h2>\n";
    
    if (count($issues) > 0) {
        echo "<p>Found " . count($issues) . " issues:</p>\n";
        echo "<ul>\n";
        foreach ($issues as $issue) {
            echo "<li>$issue</li>\n";
        }
        echo "</ul>\n";
        
        echo "<h2>Recommendations</h2>\n";
        echo "<ol>\n";
        
        if (!$allFilesExist) {
            echo "<li>Check that all required files exist in the correct paths</li>\n";
            echo "<li>Verify whether you're using 'issuetrackerintegration' or 'issuetrackers' as folder name, and be consistent</li>\n";
        }
        
        if (!$allMethodsExist) {
            echo "<li>Ensure the redminerestInterface.class.php file contains all required methods (setCfg, checkEnv, etc.)</li>\n";
            echo "<li>Consider reverting to the version with our added fixes, which included these methods</li>\n";
        }
        
        if (!$configOk) {
            echo "<li>Update custom_config.inc.php to enable issue tracker integration</li>\n";
            echo "<li>Make sure you've registered the Redmine integration class properly</li>\n";
        }
        
        if (!$allExtensionsLoaded) {
            echo "<li>Install missing PHP extensions (" . implode(', ', array_filter($extensions, function($ext) use ($extensionResults) {
                foreach ($extensionResults as $result) {
                    if ($result['name'] == "PHP $ext extension" && !$result['status']) {
                        return true;
                    }
                }
                return false;
            })) . ")</li>\n";
        }
        
        echo "<li>Consider using the simplified test script to validate the Redmine API connection directly</li>\n";
        echo "</ol>\n";
    } else {
        echo "<p style='color:green'>No issues found. If you're still experiencing problems, they might be related to runtime behavior or credentials.</p>\n";
        echo "<p>Try running the standalone test script to check API connectivity.</p>\n";
    }
    
    echo "</body>\n</html>";
} else {
    echo "\nSummary:\n";
    
    if (count($issues) > 0) {
        echo "Found " . count($issues) . " issues:\n";
        foreach ($issues as $issue) {
            echo "- $issue\n";
        }
        
        echo "\nRecommendations:\n";
        
        if (!$allFilesExist) {
            echo "1. Check that all required files exist in the correct paths\n";
            echo "2. Verify whether you're using 'issuetrackerintegration' or 'issuetrackers' as folder name\n";
        }
        
        if (!$allMethodsExist) {
            echo "3. Ensure redminerestInterface.class.php contains all required methods\n";
            echo "4. Consider reverting to the version with our added fixes\n";
        }
        
        // Similar recommendations for command line output
    } else {
        echo "No issues found. If you're still experiencing problems, run the standalone test script.\n";
    }
}
 
// Local-only PHP configuration dump (for verifying .user.ini)
if ($isLocal) {
    echo "<pre>";
    echo 'display_errors=' . ini_get('display_errors') . PHP_EOL;
    echo 'error_reporting=' . ini_get('error_reporting') . PHP_EOL;
    echo 'log_errors=' . ini_get('log_errors') . PHP_EOL;
    echo 'error_log=' . ini_get('error_log') . PHP_EOL;
    echo "</pre>";
}
?>
