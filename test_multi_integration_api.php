<?php
/**
 * Test script for multi-integration API endpoint
 * Tests the list_integrations_for_project endpoint
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

// Include necessary files
require_once('config.inc.php');
require_once('common.php');

// Initialize database connection
$db = new database(DB_TYPE);
doDBConnect($db, database::ONERROREXIT);

echo "=== Multi-Integration API Test ===\n";
echo "Testing: list_integrations_for_project endpoint\n\n";

// Test with project ID 242099 (known project from previous logs)
$test_project_id = 242099;

echo "Test 1: Valid project ID ($test_project_id)\n";
echo "URL: custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=$test_project_id\n";

// Simulate GET request
$_GET['action'] = 'list_integrations_for_project';
$_GET['tproject_id'] = $test_project_id;

// Include and test the integrator
ob_start();
include('lib/execute/custom_bugtrack_integrator.php');
$output = ob_get_clean();

echo "Response:\n$output\n\n";

// Test with invalid project ID
echo "Test 2: Invalid project ID (999999)\n";
echo "URL: custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=999999\n";

$_GET['tproject_id'] = 999999;

ob_start();
include('lib/execute/custom_bugtrack_integrator.php');
$output = ob_get_clean();

echo "Response:\n$output\n\n";

// Test with missing project ID
echo "Test 3: Missing project ID\n";
echo "URL: custom_bugtrack_integrator.php?action=list_integrations_for_project\n";

unset($_GET['tproject_id']);

ob_start();
include('lib/execute/custom_bugtrack_integrator.php');
$output = ob_get_clean();

echo "Response:\n$output\n\n";

echo "=== Test Complete ===\n";
echo "Check logs/multi_integration_debug.log for detailed debugging information\n";
?>
