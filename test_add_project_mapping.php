<?php
// Test script for add_project_mapping API endpoint
define('NOCRYPT', true);
require_once('config.inc.php');
require_once('lib/functions/common.php');

// Initialize database
$db = new database(DB_TYPE);
doDBConnect($db, database::ONERROREXIT);

echo "Testing add_project_mapping API endpoint...\n\n";

// Test data
$post_data = [
    'tproject_id' => 242099, // Use the known project ID
    'integration_id' => 7     // Use Redmine1 integration
];

echo "POST data: " . json_encode($post_data) . "\n\n";

// Simulate POST request
$_POST = $post_data;
$_REQUEST['action'] = 'add_project_mapping';

// Include the API
include('lib/execute/custom_bugtrack_integrator.php');
?>
