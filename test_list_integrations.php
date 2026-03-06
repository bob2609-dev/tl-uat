<?php
// Test script for list_integrations API endpoint
define('NOCRYPT', true);
require_once('config.inc.php');
require_once('lib/functions/common.php');

// Initialize database
$db = new database(DB_TYPE);
doDBConnect($db, database::ONERROREXIT);

echo "Testing list_integrations API endpoint...\n\n";

// Simulate request
$_REQUEST['action'] = 'list_integrations';

// Include the API
include('lib/execute/custom_bugtrack_integrator.php');
?>
