<?php
/**
 * Direct test of PHP backend to bypass JavaScript issues
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, false);

// Simulate different date scenarios
echo "=== Testing PHP Backend Directly ===\n\n";

// Test 1: No dates
echo "Test 1: No date filtering\n";
$_REQUEST['ajax'] = 1;
$_REQUEST['action'] = 'run_report';
$_REQUEST['project_id'] = 1;
$_REQUEST['testplan_id'] = '';
$_REQUEST['build_id'] = '';
$_REQUEST['tester_id'] = '';
$_REQUEST['report_type'] = 'all';
$_REQUEST['start_date'] = '';
$_REQUEST['end_date'] = '';

// Call the main function
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedTester = isset($_REQUEST['tester_id']) ? intval($_REQUEST['tester_id']) : 0;
$reportType = isset($_REQUEST['report_type']) ? $_REQUEST['report_type'] : 'assigned';
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';

echo "Parameters: Project=$selectedProject, Start='$startDate', End='$endDate'\n";

// Build parameter string exactly like the PHP does
$spParams = array();
$spParams[] = $selectedProject > 0 ? $selectedProject : 'NULL';
$spParams[] = $selectedPlan > 0 ? $selectedPlan : 'NULL';
$spParams[] = $selectedBuild > 0 ? $selectedBuild : 'NULL';
$spParams[] = $selectedTester > 0 ? $selectedTester : 'NULL';
$spParams[] = "'" . addslashes($reportType) . "'";
$spParams[] = !empty($startDate) ? "'" . addslashes($startDate) . "'" : 'NULL';
$spParams[] = !empty($endDate) ? "'" . addslashes($endDate) . "'" : 'NULL';

$paramString = implode(', ', $spParams);
$sql = "CALL sp_tester_execution_report_historical($paramString)";
echo "SQL: $sql\n";

$result = $db->exec_query($sql);
$totalRows = 0;
$totalExecutions = 0;
if ($result) {
    while ($row = $db->fetch_array($result)) {
        $totalRows++;
        $totalExecutions += $row['total_executions'];
    }
}
echo "Result: $totalRows rows, $totalExecutions executions\n\n";

// Test 2: Date range 2026-01-29
echo "Test 2: Date range 2026-01-29\n";
$_REQUEST['start_date'] = '2026-01-29';
$_REQUEST['end_date'] = '2026-01-29';
$startDate = '2026-01-29';
$endDate = '2026-01-29';

$spParams[5] = !empty($startDate) ? "'" . addslashes($startDate) . "'" : 'NULL';
$spParams[6] = !empty($endDate) ? "'" . addslashes($endDate) . "'" : 'NULL';
$paramString = implode(', ', $spParams);
$sql = "CALL sp_tester_execution_report_historical($paramString)";
echo "SQL: $sql\n";

$result = $db->exec_query($sql);
$totalRows = 0;
$totalExecutions = 0;
if ($result) {
    while ($row = $db->fetch_array($result)) {
        $totalRows++;
        $totalExecutions += $row['total_executions'];
        if ($row['total_executions'] > 0) {
            echo "  {$row['tester_name']}: {$row['total_executions']} executions\n";
        }
    }
}
echo "Result: $totalRows rows, $totalExecutions executions\n\n";

// Test 3: Date range 2025-09-09
echo "Test 3: Date range 2025-09-09\n";
$_REQUEST['start_date'] = '2025-09-09';
$_REQUEST['end_date'] = '2025-09-09';
$startDate = '2025-09-09';
$endDate = '2025-09-09';

$spParams[5] = !empty($startDate) ? "'" . addslashes($startDate) . "'" : 'NULL';
$spParams[6] = !empty($endDate) ? "'" . addslashes($endDate) . "'" : 'NULL';
$paramString = implode(', ', $spParams);
$sql = "CALL sp_tester_execution_report_historical($paramString)";
echo "SQL: $sql\n";

$result = $db->exec_query($sql);
$totalRows = 0;
$totalExecutions = 0;
if ($result) {
    while ($row = $db->fetch_array($result)) {
        $totalRows++;
        $totalExecutions += $row['total_executions'];
    }
}
echo "Result: $totalRows rows, $totalExecutions executions\n\n";

echo "=== Summary ===\n";
echo "If these results show different numbers, the PHP backend is working.\n";
echo "If the UI still shows the same numbers, the issue is in JavaScript.\n";
?>
