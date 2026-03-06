<?php
/**
 * Direct test of backend functions for multi-integration
 * Tests the core functions without the full API wrapper
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

// Include necessary files
require_once('config.inc.php');
require_once('common.php');

// Initialize database connection
$db = new database(DB_TYPE);
doDBConnect($db, database::ONERROREXIT);

echo "=== Backend Functions Test ===\n\n";

// Test 1: Test getIntegrationsForProject function
echo "Test 1: getIntegrationsForProject() function\n";
echo "Project ID: 242099\n";

// Include our custom integration functions
require_once('lib/execute/custom_issue_integration_safe.php');

// Call the function
$integrations = getIntegrationsForProject($db, 242099);

echo "Result: Found " . count($integrations) . " integrations\n";
if (!empty($integrations)) {
    echo "Integrations found:\n";
    foreach ($integrations as $integration) {
        echo "- ID: {$integration['id']}, Name: {$integration['name']}, Type: {$integration['type']}, URL: {$integration['url']}\n";
    }
} else {
    echo "No integrations found for this project\n";
}
echo "\n";

// Test 2: Test getCustomIntegrationForProject without integration_id (existing behavior)
echo "Test 2: getCustomIntegrationForProject() without integration_id\n";
$integration = getCustomIntegrationForProject($db, 242099);
if ($integration) {
    echo "Result: Found integration - ID: {$integration['id']}, Name: {$integration['name']}, Type: {$integration['type']}\n";
} else {
    echo "Result: No integration found\n";
}
echo "\n";

// Test 3: Test getCustomIntegrationForProject with specific integration_id
echo "Test 3: getCustomIntegrationForProject() with specific integration_id\n";
if (!empty($integrations)) {
    $test_integration_id = $integrations[0]['id'];
    echo "Testing with integration_id: $test_integration_id\n";
    
    $integration = getCustomIntegrationForProject($db, 242099, $test_integration_id);
    if ($integration) {
        echo "Result: Found specific integration - ID: {$integration['id']}, Name: {$integration['name']}, Type: {$integration['type']}\n";
        echo "API Key (first 8 chars): " . substr($integration['api_key'], 0, 8) . "...\n";
    } else {
        echo "Result: No integration found with that ID\n";
    }
} else {
    echo "Skipping test - no integrations available\n";
}
echo "\n";

// Test 4: Test with invalid integration_id
echo "Test 4: getCustomIntegrationForProject() with invalid integration_id\n";
$integration = getCustomIntegrationForProject($db, 242099, 999999);
if ($integration) {
    echo "Result: Found integration - ID: {$integration['id']}, Name: {$integration['name']}\n";
} else {
    echo "Result: No integration found (expected for invalid ID)\n";
}
echo "\n";

echo "=== Backend Functions Test Complete ===\n";
echo "Check logs/multi_integration_debug.log for detailed debugging information\n";
?>
