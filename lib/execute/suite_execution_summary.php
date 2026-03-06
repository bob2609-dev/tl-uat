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

// Initialize logging
function logDebug($message) {
    $logFile = __DIR__ . '/suite_execution_summary.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

logDebug('=== Suite Execution Summary Debug Session Started ===');
logDebug('PHP Version: ' . phpversion());
logDebug('Current User ID: ' . (isset($currentUser->dbID) ? $currentUser->dbID : 'NOT SET'));
logDebug('Database object type: ' . gettype($db));
logDebug('Database class: ' . (is_object($db) ? get_class($db) : 'NOT AN OBJECT'));

// Check user permissions
if (!$currentUser->hasRight($db, "testplan_execute")) {
    logDebug('ERROR: User does not have testplan_execute permission');
    $gui->warning_msg = lang_get('no_permissions');
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}

logDebug('User permissions OK - proceeding with page logic');

// Get test project list for selection
logDebug('Getting test project list...');
try {
    $testProjectMgr = new testproject($db);
    $gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);
    logDebug('Successfully got ' . count($gui->testprojects) . ' accessible projects');
} catch (Exception $e) {
    logDebug('ERROR getting test projects: ' . $e->getMessage());
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

logDebug('Filter parameters: Project=' . $selectedProject . ', Plan=' . $selectedPlan . ', Build=' . $selectedBuild . ', Status=' . $selectedStatus . ', Path=' . $selectedExecutionPath);

// Get testplans for selected project
$gui->testplans = array();
if ($selectedProject > 0) {
    logDebug('Getting testplans for project ID: ' . $selectedProject);
    $tplanSql = "SELECT id, notes FROM testplans WHERE testproject_id = " . intval($selectedProject) . " AND active = 1 ORDER BY notes";
    logDebug('Testplan SQL: ' . $tplanSql);
    
    try {
        logDebug('About to execute testplan query...');
        $tplanResult = $db->exec_query($tplanSql);
        logDebug('Query executed, result type: ' . gettype($tplanResult));
        
        if (!$tplanResult) {
            $errorMsg = method_exists($db, 'error_msg') ? $db->error_msg() : 'Unknown database error';
            logDebug('ERROR: Testplan query failed - ' . $errorMsg);
            $gui->warning_msg = 'Database error getting testplans: ' . $errorMsg;
        } else {
            logDebug('Testplan query successful, fetching rows...');
            $count = 0;
            while ($row = $db->fetch_array($tplanResult)) {
                $gui->testplans[] = array('id' => $row['id'], 'name' => $row['notes']);
                $count++;
            }
            logDebug('Successfully fetched ' . $count . ' testplans');
        }
    } catch (Exception $e) {
        logDebug('EXCEPTION in testplan query: ' . $e->getMessage());
        logDebug('Exception trace: ' . $e->getTraceAsString());
        $gui->warning_msg = 'Exception getting testplans: ' . $e->getMessage();
    }
} else {
    logDebug('No project selected, skipping testplan query');
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

// Build the main query using node_hierarchy_paths for full hierarchical paths
$sql = "SELECT 
    -- Use the pre-calculated full hierarchical path from node_hierarchy_paths
    nhp.full_path AS test_path,
    COUNT(*) AS testcase_count,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS not_run_count,
    
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
        WHEN COUNT(*) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2)
        ELSE 0.00
    END AS block_rate,
    
    -- Calculate pending rate (not run / non-blocked tests) * 100
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
WHERE 1=1";

// Apply filters
if ($selectedProject > 0) {
    $sql .= " AND tp.testproject_id = " . intval($selectedProject);
}

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

// Only include paths that have execution data
$sql .= " AND nhp.full_path IS NOT NULL AND nhp.full_path != ''";

// Group and order by the full hierarchical path
$sql .= " GROUP BY nhp.full_path ORDER BY nhp.full_path";

// Execute query
$result = $db->exec_query($sql);

if (!$result) {
    $gui->warning_msg = "Error executing query: " . $db->error_msg();
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

while ($row = $db->fetch_array($result)) {
    $suiteData[] = array(
        'test_path' => $row['test_path'],
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
$smarty->display($templateCfg->template_dir . 'suite_execution_summary.tpl');

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
