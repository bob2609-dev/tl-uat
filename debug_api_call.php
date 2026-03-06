<?php
/**
 * Debug script to test API call context
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

echo "=== API Context Debug ===\n";
echo "Current working directory: " . getcwd() . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";

// Test if config file exists
$configPath = '../../config.inc.php';
echo "Looking for config at: $configPath\n";
echo "Config file exists: " . (file_exists($configPath) ? 'YES' : 'NO') . "\n";

// Test absolute path
$absConfigPath = dirname(__FILE__) . '/../../config.inc.php';
echo "Absolute config path: $absConfigPath\n";
echo "Absolute config file exists: " . (file_exists($absConfigPath) ? 'YES' : 'NO') . "\n";

// Try to include config
if (file_exists($absConfigPath)) {
    echo "Attempting to include config...\n";
    try {
        define('NOCRYPT', true);
        require_once($absConfigPath);
        echo "Config included successfully\n";
    } catch (Exception $e) {
        echo "Config include failed: " . $e->getMessage() . "\n";
    }
}

// Now test the actual API call
echo "\n=== Testing API Call ===\n";
$_GET['action'] = 'list_integrations_for_project';
$_GET['tproject_id'] = 242099;

echo "GET data: " . json_encode($_GET) . "\n";

// Try to include the integrator
$integratorPath = 'lib/execute/custom_bugtrack_integrator.php';
echo "Looking for integrator at: $integratorPath\n";
echo "Integrator file exists: " . (file_exists($integratorPath) ? 'YES' : 'NO') . "\n";

if (file_exists($integratorPath)) {
    echo "Attempting to include integrator...\n";
    try {
        include($integratorPath);
        echo "Integrator included successfully\n";
    } catch (Exception $e) {
        echo "Integrator include failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "Integrator file not found\n";
}
?>
