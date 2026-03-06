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
    // die('Error: Project ID is required for export');
    $selectedProject=1;
}

// Build focused SQL query matching test_suite_execution_summary_focused.sql structure
$sql = "
SELECT 
    -- Test Path column (hierarchical suite path) - use node_hierarchy_paths for full hierarchy
    nhp.full_path AS test_path,
    
    -- Test Case Count column
    COUNT(*) AS testcase_count,
    
    -- Total Executions column (passed + failed)
    SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END) AS total_executions,
    
    -- Status count columns
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS not_run_count,
    
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
        WHEN COUNT(*) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2)
        ELSE 0.00
    END AS block_rate,
    
    -- Pending Rate column (not run / non-blocked tests) * 100
    CASE 
        WHEN (COUNT(*) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) / 
                   (COUNT(*) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END))) * 100, 2)
        ELSE 0.00
    END AS pending_rate

FROM 
    executions e
    -- Get only the latest execution for each test case version per build/testplan combination
    JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
          FROM executions
          GROUP BY tcversion_id, build_id, testplan_id) latest_e 
        ON e.tcversion_id = latest_e.tcversion_id 
        AND e.build_id = latest_e.build_id 
        AND e.testplan_id = latest_e.testplan_id 
        AND e.execution_ts = latest_e.latest_exec_ts
    -- Join with test case version and hierarchy information
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    -- Join with parent suite to get the hierarchical path
    JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    -- Join with node_hierarchy_paths_v2 to get the full hierarchical path
    LEFT JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id
    -- Join with test plan and project information for filtering
    JOIN testplans tp ON e.testplan_id = tp.id
    JOIN builds b ON e.build_id = b.id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
WHERE 1=1
    AND tp.testproject_id = " . intval($selectedProject) . "
";

// Add filters
if ($selectedPlan > 0) {
    $sql .= " AND e.testplan_id = " . intval($selectedPlan);
}

if ($selectedBuild > 0) {
    $sql .= " AND e.build_id = " . intval($selectedBuild);
}

if (!empty($selectedStatus)) {
    $sql .= " AND e.status = '" . $db->prepare_string($selectedStatus) . "'";
}

if (!empty($selectedExecutionPath)) {
    $sql .= " AND nhp.full_path LIKE '%" . $db->prepare_string($selectedExecutionPath) . "%'";
}

if (!empty($startDate)) {
    $sql .= " AND e.execution_ts >= '" . $db->prepare_string($startDate . ' 00:00:00') . "'";
}

if (!empty($endDate)) {
    $sql .= " AND e.execution_ts <= '" . $db->prepare_string($endDate . ' 23:59:59') . "'";
}

$sql .= "
GROUP BY nhp.full_path
ORDER BY nhp.full_path
";

// Execute query
$result = $db->exec_query($sql);

if (!$result) {
    die('Error executing query: ' . $db->error_msg());
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
    $totalTestCases += $row['total_executions'];
    $totalPassed += $row['passed_count'];
    $totalFailed += $row['failed_count'];
    $totalBlocked += $row['blocked_count'];
    $totalNotRun += $row['not_run_count'];
}

// Add summary row
if (!empty($exportData)) {
    $overallPassRate = $totalTestCases > 0 ? round(($totalPassed * 100.0 / $totalTestCases), 2) : 0;
    $overallFailRate = $totalTestCases > 0 ? round(($totalFailed * 100.0 / $totalTestCases), 2) : 0;
    $overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked * 100.0 / $totalTestCases), 2) : 0;
    $overallPendingRate = $totalTestCases > 0 ? round(($totalNotRun * 100.0 / $totalTestCases), 2) : 0;
    
    $exportData[] = array(
        'Test Path' => '--- OVERALL SUMMARY ---',
        'Total Executions' => $totalTestCases,
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
