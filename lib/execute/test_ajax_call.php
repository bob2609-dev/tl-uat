<?php
/**
 * Test the exact AJAX call that the UI makes
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, false);

// Simulate the exact AJAX request from UI
$_REQUEST['ajax'] = 1;
$_REQUEST['action'] = 'run_report';
$_REQUEST['project_id'] = 1;
$_REQUEST['testplan_id'] = '';
$_REQUEST['build_id'] = '';
$_REQUEST['tester_id'] = '';
$_REQUEST['report_type'] = 'all';
$_REQUEST['start_date'] = '2026-01-29';
$_REQUEST['end_date'] = '2026-01-29';
$_REQUEST['date_mode'] = 'date_range';

echo "=== Simulating AJAX Call from UI ===\n";
echo "REQUEST data:\n";
print_r($_REQUEST);

// Call the same function that the AJAX calls
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedTester = isset($_REQUEST['tester_id']) ? intval($_REQUEST['tester_id']) : 0;
$reportType = isset($_REQUEST['report_type']) ? $_REQUEST['report_type'] : 'assigned';
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';

echo "\n=== Parsed Parameters ===\n";
echo "Project: $selectedProject\n";
echo "Plan: $selectedPlan\n";
echo "Build: $selectedBuild\n";
echo "Tester: $selectedTester\n";
echo "Report Type: $reportType\n";
echo "Start Date: $startDate\n";
echo "End Date: $endDate\n";

// Call the stored procedure directly (include the function definition)
require_once('tester_execution_report_professional.php');
runTesterReport($db, $selectedProject, $selectedPlan, $selectedBuild, $selectedTester, $reportType, $startDate, $endDate);
?>
