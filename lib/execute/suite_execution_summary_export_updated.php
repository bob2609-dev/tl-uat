<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * @filesource suite_execution_summary_export.php
 * 
 * Export Test Suite Execution Summary data to Excel format
 */

require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, "checkRights");

$templateCfg = templateConfiguration();

// Initialize error-only logging to reduce file size
function logError($message) {
    $logFile = __DIR__ . '/suite_execution_summary.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] EXPORT ERROR: $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Initialize user input
$args = init_args();
$gui = new stdClass();

// Get filter parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedStatus = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
$selectedExecutionPath = isset($_REQUEST['execution_path']) ? trim($_REQUEST['execution_path']) : '';
$startDate = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
$endDate = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '';

// Validate required parameters
if ($selectedProject <= 0) {
    logError('Project ID is required for export');
    // die('Error: Project ID is required for export');
    $selectedProject=1;
}

// Build SQL query to include all test cases (executed and not executed) - matching main summary logic
$sql = "
SELECT 
    -- Test Path column (hierarchical suite path) - use node_hierarchy_paths_v2 for full hierarchy
    nhp.full_path AS test_path,
    
    -- Total test cases in this path (unfiltered count - always shows full count regardless of filters)
    (
        SELECT COUNT(DISTINCT tcv_total.id)
        FROM testplan_tcversions tptcv_total
        JOIN tcversions tcv_total ON tptcv_total.tcversion_id = tcv_total.id
        JOIN nodes_hierarchy nh_tcv_total ON tcv_total.id = nh_tcv_total.id
        JOIN nodes_hierarchy nh_tc_total ON nh_tcv_total.parent_id = nh_tc_total.id
        JOIN nodes_hierarchy parent_nh_total ON nh_tc_total.parent_id = parent_nh_total.id
        LEFT JOIN node_hierarchy_paths_v2 nhp_total ON parent_nh_total.id = nhp_total.node_id
        JOIN testplans tp_total ON tptcv_total.testplan_id = tp_total.id
        JOIN testprojects tproj_total ON tp_total.testproject_id = tproj_total.id
        WHERE nhp_total.full_path = nhp.full_path
        " . ($selectedProject > 0 ? " AND tproj_total.id = $selectedProject" : "") . "
        " . ($selectedPlan > 0 ? " AND tp_total.id = $selectedPlan" : "") . "
    ) AS total_testcases,
    
    -- Test Case Count column (total test cases including non-executed)
    COUNT(DISTINCT tcv.id) AS testcase_count,
    
    -- Total Executions column (passed + failed)
    SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END) AS total_executions,
    
    -- Status count columns
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    -- Count test cases with no execution or status 'n' as not run
    SUM(CASE WHEN e.status IS NULL OR e.status = 'n' THEN 1 
             WHEN e.status NOT IN ('p','f','b','n') THEN 1 
             ELSE 0 END) AS not_run_count,
    
    -- Pass Rate column (passed / executed tests) * 100
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0.00
    END AS pass_rate,
    
    -- Fail Rate column (failed / executed tests) * 100
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0.00
    END AS fail_rate,
    
    -- Block Rate column (blocked / total tests) * 100
    CASE 
        WHEN COUNT(DISTINCT tcv.id) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(DISTINCT tcv.id)) * 100, 2)
        ELSE 0.00
    END AS block_rate,
    
    -- Pending Rate column (not run / non-blocked tests) * 100
    CASE 
        WHEN (COUNT(DISTINCT tcv.id) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status IS NULL OR e.status = 'n' OR e.status NOT IN ('p','f','b','n') THEN 1 ELSE 0 END) / 
                   (COUNT(DISTINCT tcv.id) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END))) * 100, 2)
        ELSE 0.00
    END AS pending_rate

FROM 
    -- Start from all test case versions that are assigned to test plans
    testplan_tcversions tptcv
    -- Join with test case versions to get test case details
    JOIN tcversions tcv ON tptcv.tcversion_id = tcv.id
    -- Join with nodes hierarchy to get test case and suite structure
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    -- Join with parent suite to get the hierarchical path
    JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    -- Join with node_hierarchy_paths_v2 to get the full hierarchical path
    LEFT JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id
    -- Join with test plan and project information for filtering
    JOIN testplans tp ON tptcv.testplan_id = tp.id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    -- LEFT JOIN with executions to get execution data (including latest execution logic)
    LEFT JOIN (
        SELECT e1.tcversion_id, e1.testplan_id, e1.build_id, e1.status, e1.execution_ts
        FROM executions e1
        JOIN (
            SELECT tcversion_id, testplan_id, build_id, MAX(execution_ts) AS latest_exec_ts
            FROM executions
            GROUP BY tcversion_id, testplan_id, build_id
        ) latest_e ON e1.tcversion_id = latest_e.tcversion_id 
                   AND e1.testplan_id = latest_e.testplan_id 
                   AND e1.build_id = latest_e.build_id 
                   AND e1.execution_ts = latest_e.latest_exec_ts
    ) e ON tcv.id = e.tcversion_id AND tptcv.testplan_id = e.testplan_id
    -- LEFT JOIN with builds for filtering (only when we have executions or when build filter is applied)
    LEFT JOIN builds b ON e.build_id = b.id
WHERE 1=1
    AND tp.testproject_id = " . intval($selectedProject) . "
";

// Add filters - matching main summary logic
if ($selectedPlan > 0) {
    $sql .= " AND tptcv.testplan_id = " . intval($selectedPlan);
}

if ($selectedBuild > 0) {
    // For build filtering, we need to ensure we only show test cases that have executions in that build
    // or if no build filter, show all test cases
    $sql .= " AND (e.build_id = " . intval($selectedBuild) . " OR e.build_id IS NULL)";
}

if (!empty($selectedStatus)) {
    if ($selectedStatus == 'n') {
        // For "Not Run" status, include test cases with no executions or explicit 'n' status
        $sql .= " AND (e.status IS NULL OR e.status = 'n')";
    } else {
        // For other statuses, filter by execution status
        $sql .= " AND e.status = '" . $db->prepare_string($selectedStatus) . "'";
    }
}

if (!empty($selectedExecutionPath)) {
    $sql .= " AND nhp.full_path LIKE '%" . $db->prepare_string($selectedExecutionPath) . "%'";
}

if (!empty($startDate)) {
    $sql .= " AND (e.execution_ts IS NULL OR e.execution_ts >= '" . $db->prepare_string($startDate . ' 00:00:00') . "')";
}

if (!empty($endDate)) {
    $sql .= " AND (e.execution_ts IS NULL OR e.execution_ts <= '" . $db->prepare_string($endDate . ' 23:59:59') . "')";
}

// Only include paths that have valid hierarchical path data
$sql .= " AND nhp.full_path IS NOT NULL AND nhp.full_path != ''";

// Group and order by the full hierarchical path
$sql .= " GROUP BY nhp.full_path, tptcv.testplan_id ORDER BY nhp.full_path";

// Execute query
$result = $db->exec_query($sql);

if (!$result) {
    $errorMsg = method_exists($db, 'error_msg') ? $db->error_msg() : 'Unknown database error';
    logError('Export query failed - ' . $errorMsg);
    die('Error executing export query: ' . $errorMsg);
}

// Prepare data for export
$exportData = array();
$totalTestCases = 0;
$totalPassed = 0;
$totalFailed = 0;
$totalBlocked = 0;
$totalNotRun = 0;

while ($row = $db->fetch_array($result)) {
    $exportData[] = array(
        'Test Path' => $row['test_path'],
        'Total Test Cases' => $row['testcase_count'],
        'Total Executions' => $row['total_executions'],
        'Passed' => $row['passed_count'],
        'Failed' => $row['failed_count'],
        'Blocked' => $row['blocked_count'],
        'Not Run' => $row['not_run_count'],
        'Pass Rate (%)' => $row['pass_rate'],
        'Fail Rate (%)' => $row['fail_rate'],
        'Block Rate (%)' => $row['block_rate'],
        'Pending Rate (%)' => $row['pending_rate']
    );
    
    // Calculate totals
    $totalTestCases += $row['testcase_count'];
    $totalPassed += $row['passed_count'];
    $totalFailed += $row['failed_count'];
    $totalBlocked += $row['blocked_count'];
    $totalNotRun += $row['not_run_count'];
}

// Add summary row - matching main summary logic
if (!empty($exportData)) {
    // Calculate overall rates using the same logic as main summary
    $totalExecuted = $totalPassed + $totalFailed;
    $overallPassRate = $totalExecuted > 0 ? round(($totalPassed / $totalExecuted) * 100, 2) : 0;
    $overallFailRate = $totalExecuted > 0 ? round(($totalFailed / $totalExecuted) * 100, 2) : 0;
    $overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked / $totalTestCases) * 100, 2) : 0;
    $totalNonBlocked = $totalTestCases - $totalBlocked;
    $overallPendingRate = $totalNonBlocked > 0 ? round(($totalNotRun / $totalNonBlocked) * 100, 2) : 0;
    
    $exportData[] = array(
        'Test Path' => '--- OVERALL SUMMARY ---',
        'Total Test Cases' => $totalTestCases,
        'Total Executions' => $totalExecuted,
        'Passed' => $totalPassed,
        'Failed' => $totalFailed,
        'Blocked' => $totalBlocked,
        'Not Run' => $totalNotRun,
        'Pass Rate (%)' => $overallPassRate,
        'Fail Rate (%)' => $overallFailRate,
        'Block Rate (%)' => $overallBlockRate,
        'Pending Rate (%)' => $overallPendingRate
    );
}

// Generate filename with timestamp
$timestamp = date('Y-m-d_H-i-s');
$filename = "Test_Suite_Execution_Summary_" . $timestamp . ".csv";

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Open output stream
$output = fopen('php://output', 'w');

// Output CSV content
if (!empty($exportData)) {
    // Header row
    fputcsv($output, array_keys($exportData[0]));
    
    // Data rows
    foreach ($exportData as $row) {
        fputcsv($output, array_values($row));
    }
} else {
    // If no data, output a message
    fputcsv($output, array('No data found for the selected filters'));
}

// Close output stream
fclose($output);

/**
 * Initialize user input
 */
function init_args() {
    $_REQUEST = strings_stripSlashes($_REQUEST);
    return $_REQUEST;
}

/**
 * Check user rights
 */
function checkRights(&$db, &$user) {
    return $user->hasRight($db, "testplan_execute");
}
?>
