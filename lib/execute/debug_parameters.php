<?php
/**
 * Debug script to test parameter passing
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, false);

// Simulate the parameters that should be passed
$projectId = 1;
$planId = null;
$buildId = null;
$testerId = null;
$reportType = 'all';
$startDate = '2026-01-28';
$endDate = null;

echo "=== Testing Parameter Passing ===\n";
echo "Project ID: $projectId\n";
echo "Plan ID: $planId\n";
echo "Build ID: $buildId\n";
echo "Tester ID: $testerId\n";
echo "Report Type: $reportType\n";
echo "Start Date: $startDate\n";
echo "End Date: $endDate\n";

// Build parameter string exactly like the PHP does
$spParams = array();
$spParams[] = $projectId > 0 ? $projectId : 'NULL';
$spParams[] = $planId > 0 ? $planId : 'NULL';
$spParams[] = $buildId > 0 ? $buildId : 'NULL';
$spParams[] = $testerId > 0 ? $testerId : 'NULL';
$spParams[] = "'" . addslashes($reportType) . "'";
$spParams[] = !empty($startDate) ? "'" . addslashes($startDate) . "'" : 'NULL';
$spParams[] = !empty($endDate) ? "'" . addslashes($endDate) . "'" : 'NULL';

$paramString = implode(', ', $spParams);
echo "\nGenerated parameter string: $paramString\n";

// Build and execute the SQL call
$sql = "CALL sp_tester_execution_report_historical($paramString)";
echo "\nSQL to execute: $sql\n";

// Execute and show results
echo "\n=== Results ===\n";
$result = $db->exec_query($sql);
if ($result) {
    $count = 0;
    while ($row = $db->fetch_array($result)) {
        if ($count < 5) { // Show first 5 results
            echo "Tester: {$row['tester_name']}, Assigned: {$row['total_assigned']}, Executions: {$row['total_executions']}\n";
        }
        $count++;
    }
    echo "Total rows returned: $count\n";
} else {
    echo "Error executing query: " . $db->db->error . "\n";
}
?>
