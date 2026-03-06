<?php
// Test script for add_integration API endpoint
define('NOCRYPT', true);
require_once('config.inc.php');
require_once('lib/functions/common.php');

// Initialize database
$db = new database(DB_TYPE);
doDBConnect($db, database::ONERROREXIT);

// Test data
$post_data = [
    'name' => 'Test Integration ' . date('Y-m-d H:i:s'),
    'type' => 'REDMINE',
    'url' => 'https://test.example.com',
    'api_key' => 'test_key_123',
    'username' => 'test_user',
    'password' => 'test_pass',
    'project_key' => 'TEST',
    'default_priority' => 'Normal'
];

echo "Testing add_integration API endpoint...\n";
echo "POST data: " . json_encode($post_data) . "\n\n";

// Simulate POST request
$_POST = $post_data;
$_REQUEST['action'] = 'add_integration';

// Include the API
include('lib/execute/custom_bugtrack_integrator.php');
?>
