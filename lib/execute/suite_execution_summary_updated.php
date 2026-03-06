<?php
/**
 * Test Suite Execution Summary - Standalone Page
 * 
 * This page displays a focused view of test execution data grouped by
 * hierarchical test suite paths with accurate metrics and filtering capabilities.
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once("../../config.inc.php");
require_once('common.php');
require_once('users.inc.php');

// Initialize session and check permissions
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];
$args = init_args();
$gui = new stdClass();
$gui->pageTitle = 'Test Suite Execution Summary';
$gui->warning_msg = '';

// Initialize error-only logging to reduce file size
function logError($message) {
    $logFile = __DIR__ . '/suite_execution_summary.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] ERROR: $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Log critical information (session start, major operations)
function logInfo($message) {
    $logFile = __DIR__ . '/suite_execution_summary.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] INFO: $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

logInfo('Suite Execution Summary session started for user: ' . (isset($currentUser->dbID) ? $currentUser->dbID : 'NOT SET'));

// Check user permissions
if (!$currentUser->hasRight($db, "testplan_execute")) {
    logError('User does not have testplan_execute permission');
    $gui->warning_msg = lang_get('no_permissions');
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}

// Get test project list for selection
try {
    $testProjectMgr = new testproject($db);
    $gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);
} catch (Exception $e) {
    logError('Failed to get test projects: ' . $e->getMessage());
    $gui->testprojects = array();
}

// Get filter parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedStatus = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$selectedExecutionPath = isset($_REQUEST['execution_path']) ? $_REQUEST['execution_path'] : '';
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';

// Filter parameters captured

// Get testplans for selected project
$gui->testplans = array();
if ($selectedProject > 0) {
    $tplanSql = "SELECT id, notes FROM testplans WHERE testproject_id = " . intval($selectedProject) . " AND active = 1 ORDER BY notes";
    
    try {
        $tplanResult = $db->exec_query($tplanSql);
        
        if (!$tplanResult) {
            $errorMsg = method_exists($db, 'error_msg') ? $db->error_msg() : 'Unknown database error';
            logError('Testplan query failed - ' . $errorMsg);
            $gui->warning_msg = 'Database error getting testplans: ' . $errorMsg;
        } else {
            while ($row = $db->fetch_array($tplanResult)) {
                $gui->testplans[] = array('id' => $row['id'], 'name' => $row['notes']);
            }
        }
    } catch (Exception $e) {
        logError('Exception in testplan query: ' . $e->getMessage());
        $gui->warning_msg = 'Exception getting testplans: ' . $e->getMessage();
    }
}

// Get builds for selected testplan
$gui->builds = array();
if ($selectedPlan > 0) {
    $buildSql = "SELECT id, name FROM builds WHERE testplan_id = " . intval($selectedPlan) . " AND active = 1 ORDER BY name";
    $buildResult = $db->exec_query($buildSql);
    if (!$buildResult) {
        $gui->warning_msg = 'Database error getting builds: ' . $db->error_msg();
    } else {
        while ($row = $db->fetch_array($buildResult)) {
            $gui->builds[] = array('id' => $row['id'], 'name' => $row['name']);
        }
    }
}

// Get execution statuses
$gui->statuses = array(
    'p' => 'Passed',
    'f' => 'Failed', 
    'b' => 'Blocked',
    'n' => 'Not Run'
);

// Build the main query starting from all test cases (including non-executed ones)
$sql = "SELECT 
    -- Use the pre-calculated full hierarchical path from node_hierarchy_paths_v2
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
    COUNT(DISTINCT tcv.id) AS testcase_count,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    -- Count test cases with no execution or status 'n' as not run
    SUM(CASE WHEN e.status IS NULL OR e.status = 'n' THEN 1 
             WHEN e.status NOT IN ('p','f','b','n') THEN 1 
             ELSE 0 END) AS not_run_count,
    
    -- Calculate pass rate (passed / executed tests) * 100
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0.00
    END AS pass_rate,
    
    -- Calculate fail rate (failed / executed tests) * 100
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0.00
    END AS fail_rate,
    
    -- Calculate block rate (blocked / total tests) * 100
    CASE 
        WHEN COUNT(DISTINCT tcv.id) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(DISTINCT tcv.id)) * 100, 2)
        ELSE 0.00
    END AS block_rate,
    
    -- Calculate pending rate (not run / non-blocked tests) * 100
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
WHERE 1=1";

// Apply filters
if ($selectedProject > 0) {
    $sql .= " AND tp.testproject_id = " . intval($selectedProject);
}

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
    logError('Main query failed - ' . $errorMsg);
    $gui->warning_msg = "Error executing query: " . $errorMsg;
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}

// Process results
$suiteData = array();
$totalTestCases = 0;
$totalPassed = 0;
$totalFailed = 0;
$totalBlocked = 0;
$totalNotRun = 0;
$rowCount = 0;

while ($row = $db->fetch_array($result)) {
    $rowCount++;
    
    $suiteData[] = array(
        'test_path' => $row['test_path'],
        'total_testcases' => $row['total_testcases'],
        'testcase_count' => $row['testcase_count'],
        'passed_count' => $row['passed_count'],
        'failed_count' => $row['failed_count'],
        'blocked_count' => $row['blocked_count'],
        'not_run_count' => $row['not_run_count'],
        'pass_rate' => $row['pass_rate'],
        'fail_rate' => $row['fail_rate'],
        'block_rate' => $row['block_rate'],
        'pending_rate' => $row['pending_rate']
    );
    
    // Calculate totals
    $totalTestCases += $row['testcase_count'];
    $totalPassed += $row['passed_count'];
    $totalFailed += $row['failed_count'];
    $totalBlocked += $row['blocked_count'];
    $totalNotRun += $row['not_run_count'];
}

// Calculate overall rates
$totalExecuted = $totalPassed + $totalFailed;
$overallPassRate = $totalExecuted > 0 ? round(($totalPassed / $totalExecuted) * 100, 2) : 0;
$overallFailRate = $totalExecuted > 0 ? round(($totalFailed / $totalExecuted) * 100, 2) : 0;
$overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked / $totalTestCases) * 100, 2) : 0;
$totalNonBlocked = $totalTestCases - $totalBlocked;
$overallPendingRate = $totalNonBlocked > 0 ? round(($totalNotRun / $totalNonBlocked) * 100, 2) : 0;

// Assign data to GUI
$gui->suiteData = $suiteData;
$gui->totalTestCases = $totalTestCases;
$gui->totalPassed = $totalPassed;
$gui->totalFailed = $totalFailed;
$gui->totalBlocked = $totalBlocked;
$gui->totalNotRun = $totalNotRun;
$gui->overallPassRate = $overallPassRate;
$gui->overallFailRate = $overallFailRate;
$gui->overallBlockRate = $overallBlockRate;
$gui->overallPendingRate = $overallPendingRate;

// Assign filter values for form persistence
$gui->selectedProject = $selectedProject;
$gui->selectedPlan = $selectedPlan;
$gui->selectedBuild = $selectedBuild;
$gui->selectedStatus = $selectedStatus;
$gui->selectedExecutionPath = $selectedExecutionPath;
$gui->startDate = $startDate;
$gui->endDate = $endDate;

// Initialize Smarty template engine
$templateCfg = templateConfiguration();
$smarty = new TLSmarty();

$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . 'suite_execution_summary_updated.tpl');

/**
 * Initialize user input
 * 
 * @return object input parameters
 */
function init_args() {
    $args = new stdClass();
    $args->tproject_id = isset($_REQUEST['tproject_id']) ? intval($_REQUEST['tproject_id']) : 0;
    
    return $args;
}

?>
