<?php
/**
 * Test with 2025-09-09 which should have many executions
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, false);

// Test with 2025-09-09 (should have many executions)
$projectId = 1;
$planId = null;
$buildId = null;
$testerId = null;
$reportType = 'all';
$startDate = '2025-09-09';
$endDate = '2025-09-09';

echo "=== Testing with 2025-09-09 (should have many executions) ===\n";

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
echo "Parameter string: $paramString\n";

// Execute the SQL call
$sql = "CALL sp_tester_execution_report_historical($paramString)";
$result = $db->exec_query($sql);

if ($result) {
    $count = 0;
    $totalExecutions = 0;
    while ($row = $db->fetch_array($result)) {
        if ($count < 10) { // Show first 10 results
            echo "Tester: {$row['tester_name']}, Assigned: {$row['total_assigned']}, Executions: {$row['total_executions']}, Passed: {$row['passed']}, Failed: {$row['failed']}\n";
        }
        $totalExecutions += $row['total_executions'];
        $count++;
    }
    echo "Total rows returned: $count\n";
    echo "Total executions on 2025-09-09: $totalExecutions\n";
} else {
    echo "Error executing query\n";
}
?>
