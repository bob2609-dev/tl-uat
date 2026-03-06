<?php
/**
 * Test the stored procedure call directly with the same parameters as UI
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, false);

// Use the exact same parameters as the UI
$projectId = 1;
$planId = null;
$buildId = null;
$testerId = null;
$reportType = 'all';
$startDate = '2026-01-29';
$endDate = '2026-01-29';

echo "=== Direct Stored Procedure Call ===\n";
echo "Parameters: Project=$projectId, Plan=$planId, Build=$buildId, Tester=$testerId, Type=$reportType, Start=$startDate, End=$endDate\n";

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
echo "SQL: $sql\n\n";

$result = $db->exec_query($sql);

if ($result) {
    $count = 0;
    $totalExecutions = 0;
    $totalPassed = 0;
    $totalFailed = 0;
    
    echo "Results:\n";
    echo "Tester Name\t\t\tAssigned\tExecuted\tPassed\tFailed\n";
    echo "------------------------------------------------------------\n";
    
    while ($row = $db->fetch_array($result)) {
        if ($row['total_executions'] > 0) { // Only show testers with executions
            printf("%-20s\t%d\t\t%d\t\t%d\t\t%d\n", 
                $row['tester_name'], 
                $row['total_assigned'], 
                $row['total_executions'], 
                $row['passed'], 
                $row['failed']
            );
        }
        $totalExecutions += $row['total_executions'];
        $totalPassed += $row['passed'];
        $totalFailed += $row['failed'];
        $count++;
    }
    
    echo "\nSummary:\n";
    echo "Total rows returned: $count\n";
    echo "Total executions on 2026-01-29: $totalExecutions\n";
    echo "Total passed: $totalPassed, Total failed: $totalFailed\n";
    
    // Return JSON format like the real AJAX call
    echo "\n=== JSON Response (like AJAX) ===\n";
    
    // Re-execute the query to get fresh result set
    $result2 = $db->exec_query($sql);
    
    $testers = array();
    $summary = ['totalTesters' => 0, 'totalAssigned' => 0, 'totalPassed' => 0, 'totalFailed' => 0, 'totalBlocked' => 0, 'totalNotRun' => 0];
    
    while ($row = $db->fetch_array($result2)) {
        $row['pass_rate'] = ($row['passed'] + $row['failed']) > 0 ? round(($row['passed'] / ($row['passed'] + $row['failed'])) * 100, 1) : 0;
        $testers[] = $row;
        
        $summary['totalTesters']++;
        $summary['totalAssigned'] += intval($row['total_assigned']);
        $summary['totalPassed'] += intval($row['passed']);
        $summary['totalFailed'] += intval($row['failed']);
        $summary['totalBlocked'] += intval($row['blocked']);
        $summary['totalNotRun'] += intval($row['not_run']);
    }
    
    $response = [
        'success' => true,
        'data' => $testers,
        'summary' => $summary
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} else {
    echo "Error executing query: " . $db->error_msg() . "\n";
}
?>
