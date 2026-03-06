<?php
/**
 * Optimized Suite Execution Summary Export (Stored Procedure Version)
 * Exports the suite execution summary data to CSV format with memory management
 */
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Set memory limit and execution time for large exports
ini_set('memory_limit', '512M');
set_time_limit(300); // 5 minutes

testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];

// Check permissions
if (!is_object($currentUser) || !$currentUser->hasRight($db, 'testplan_metrics')) {
    die('Access denied');
}

// Get filter parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedStatus = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
$selectedExecutionPath = isset($_REQUEST['execution_path']) ? trim($_REQUEST['execution_path']) : '';
$startDate = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
$endDate = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '';

// Validate that we have minimum required filters
$hasAdditionalFilters = ($selectedPlan > 0) || ($selectedBuild > 0) || ($selectedStatus !== '') || ($selectedExecutionPath !== '') || ($startDate !== '') || ($endDate !== '');
if ($selectedProject <= 0 || !$hasAdditionalFilters) {
    die('Please select a project and at least one additional filter');
}

// Build stored procedure call with proper escaping
$statusParam = ($selectedStatus !== '') ? "'" . addslashes($selectedStatus) . "'" : "NULL";
$pathParam = ($selectedExecutionPath !== '') ? "'" . addslashes($selectedExecutionPath) . "'" : "NULL";
$startParam = ($startDate !== '') ? "'" . addslashes($startDate . ' 00:00:00') . "'" : "NULL";
$endParam = ($endDate !== '') ? "'" . addslashes($endDate . ' 23:59:59') . "'" : "NULL";

$call = 'CALL suite_execution_summary(' . intval($selectedProject) . ', ' . intval($selectedPlan) . ', ' . intval($selectedBuild) . ', ' . $statusParam . ', ' . $pathParam . ', ' . $startParam . ', ' . $endParam . ')';

$result = $db->exec_query($call);

if (!$result) {
    die('Error executing stored procedure');
}

// Generate filename with timestamp
$filename = 'suite_execution_summary_' . date('Ymd_His') . '.csv';

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Content-Transfer-Encoding: binary');

// Stream output directly to browser for memory efficiency
$output = fopen('php://output', 'w');

// Add BOM for Excel UTF-8 compatibility
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write header row
fputcsv($output, array(
    'Test Path',
    'Overall Total',
    'Test Case Count',
    'Passed',
    'Failed',
    'Blocked',
    'Not Run',
    'Pass Rate (%)',
    'Fail Rate (%)',
    'Block Rate (%)',
    'Pending Rate (%)'
));

// Process data row by row to minimize memory usage
$totalTestCases = 0;
$totalPassed = 0;
$totalFailed = 0;
$totalBlocked = 0;
$totalNotRun = 0;

while ($row = $db->fetch_array($result)) {
    // Write row immediately to output
    fputcsv($output, array(
        $row['test_path'],
        isset($row['total_testcases']) ? $row['total_testcases'] : 0,
        isset($row['testcase_count']) ? $row['testcase_count'] : 0,
        isset($row['passed_count']) ? $row['passed_count'] : 0,
        isset($row['failed_count']) ? $row['failed_count'] : 0,
        isset($row['blocked_count']) ? $row['blocked_count'] : 0,
        isset($row['not_run_count']) ? $row['not_run_count'] : 0,
        isset($row['pass_rate']) ? $row['pass_rate'] : 0,
        isset($row['fail_rate']) ? $row['fail_rate'] : 0,
        isset($row['block_rate']) ? $row['block_rate'] : 0,
        isset($row['pending_rate']) ? $row['pending_rate'] : 0
    ));
    
    // Update totals
    $totalTestCases += (int)$row['testcase_count'];
    $totalPassed += (int)$row['passed_count'];
    $totalFailed += (int)$row['failed_count'];
    $totalBlocked += (int)$row['blocked_count'];
    $totalNotRun += (int)$row['not_run_count'];
    
    // Flush output buffer every 100 rows
    if (($totalTestCases % 100) === 0) {
        ob_flush();
        flush();
    }
}

// Add summary row
$totalExecuted = $totalPassed + $totalFailed;
$overallPassRate = $totalExecuted > 0 ? round(($totalPassed / $totalExecuted) * 100, 2) : 0;
$overallFailRate = $totalExecuted > 0 ? round(($totalFailed / $totalExecuted) * 100, 2) : 0;
$overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked / $totalTestCases) * 100, 2) : 0;
$totalNonBlocked = $totalTestCases - $totalBlocked;
$overallPendingRate = $totalNonBlocked > 0 ? round(($totalNotRun / $totalNonBlocked) * 100, 2) : 0;

// Write empty row and summary
fputcsv($output, array()); // Empty row
fputcsv($output, array(
    'TOTAL',
    $totalTestCases,
    $totalTestCases,
    $totalPassed,
    $totalFailed,
    $totalBlocked,
    $totalNotRun,
    $overallPassRate,
    $overallFailRate,
    $overallBlockRate,
    $overallPendingRate
));

// Close output stream
fclose($output);
exit();
