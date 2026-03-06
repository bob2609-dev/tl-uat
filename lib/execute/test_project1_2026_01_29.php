<?php
/**
 * Test with project_id = 1 and 2026-01-29 (should show 4 executions by Muumini Mohamed)
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, false);

// Test with project_id = 1 and 2026-01-29
$projectId = 1;
$planId = null;
$buildId = null;
$testerId = null;
$reportType = 'all';
$startDate = '2026-01-29';
$endDate = '2026-01-29';

echo "=== Testing with project_id = 1 and 2026-01-29 ===\n";
echo "Expected: 4 executions by Muumini Mohamed\n\n";

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
    $totalPassed = 0;
    $totalFailed = 0;
    
    while ($row = $db->fetch_array($result)) {
        if ($row['total_executions'] > 0) { // Only show testers with executions
            echo "Tester: {$row['tester_name']}, Assigned: {$row['total_assigned']}, Executions: {$row['total_executions']}, Passed: {$row['passed']}, Failed: {$row['failed']}, Blocked: {$row['blocked']}\n";
        }
        $totalExecutions += $row['total_executions'];
        $totalPassed += $row['passed'];
        $totalFailed += $row['failed'];
        $count++;
    }
    echo "\nTotal rows returned: $count\n";
    echo "Total executions on 2026-01-29: $totalExecutions\n";
    echo "Total passed: $totalPassed, Total failed: $totalFailed\n";
    
    if ($totalExecutions == 4) {
        echo "✅ SUCCESS: Found expected 4 executions!\n";
    } else {
        echo "❌ ISSUE: Expected 4 executions but found $totalExecutions\n";
    }
} else {
    echo "Error executing query\n";
}
?>
