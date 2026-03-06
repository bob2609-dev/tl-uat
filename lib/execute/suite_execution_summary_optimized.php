<?php
/**
 * Optimized Test Suite Execution Summary - Standalone Backend
 * 
 * AJAX endpoints for the standalone HTML interface
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Initialize session and check permissions
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];
$gui = new stdClass();
$gui->pageTitle = 'Optimized Test Suite Execution Summary';
$gui->warning_msg = '';

// Get filter parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';

// Initialize data for AJAX requests (needed before AJAX check)
$testProjectMgr = new testproject($db);
$gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);

// Get test plans for selected project
$gui->testplans = array();
if ($selectedProject > 0) {
    $gui->testplans = $testProjectMgr->get_all_testplans($selectedProject, array('plan_status' => 1));
}

// Get builds for selected test plan
$gui->builds = array();
if ($selectedPlan > 0) {
    $testPlanMgr = new testplan($db);
    $gui->builds = $testPlanMgr->get_builds($selectedPlan);
}

// Check if this is an AJAX request
if (isset($_REQUEST['ajax'])) {
    header('Content-Type: application/json');
    
    try {
        // Handle different AJAX actions
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
        
        switch ($action) {
            case 'get_initial_data':
                getInitialData($db, $gui);
                break;
                
            case 'get_testplans':
                getTestPlans($db, $selectedProject);
                break;
                
            case 'get_builds':
                getBuilds($db, $selectedPlan);
                break;
                
            case 'run_report':
                runSuiteReport($db, $selectedProject, $selectedPlan, $selectedBuild, $startDate, $endDate);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Check permissions
if (!$currentUser->hasRight($db, 'testplan_metrics')) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

/**
 * Get initial data for dropdowns (projects, test plans, builds)
 */
function getInitialData($db, $gui) {
    echo json_encode([
        'success' => true,
        'data' => array(
            'projects' => $gui->testprojects,
            'testplans' => $gui->testplans,
            'builds' => $gui->builds
        )
    ]);
}

/**
 * Get test plans for a specific project
 */
function getTestPlans($db, $projectId) {
    $testProjectMgr = new testproject($db);
    $testplans = $testProjectMgr->get_all_testplans($projectId, array('plan_status' => 1));
    
    echo json_encode([
        'success' => true,
        'testplans' => $testplans
    ]);
}

/**
 * Get builds for a specific test plan
 */
function getBuilds($db, $planId) {
    $testPlanMgr = new testplan($db);
    $builds = $testPlanMgr->get_builds($planId);
    
    echo json_encode([
        'success' => true,
        'builds' => $builds
    ]);
}

/**
 * Run the suite execution report with all filters applied
 */
function runSuiteReport($db, $projectId, $planId, $buildId, $startDate, $endDate) {
    // Clean output buffer
    if (ob_get_length()) ob_clean();
    
    // Optimized query for suite execution summary
    $sql = "SELECT 
                parent_nh.id AS suite_id,
                parent_nh.name AS suite_name,
                COUNT(DISTINCT e.tcversion_id) AS total_cases,
                SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed,
                SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed,
                SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked,
                SUM(CASE WHEN e.status = 'n' THEN 1 ELSE 0 END) AS not_run,
                MAX(e.execution_ts) AS last_execution
            FROM 
                executions e
                JOIN (
                    -- Get unique executions by tcversion_id and execution_ts
                    -- If multiple entries have same tcversion_id and execution_ts, count as one
                    SELECT 
                        tcversion_id, 
                        build_id, 
                        testplan_id, 
                        execution_ts,
                        MIN(id) as min_id
                    FROM executions
                    GROUP BY tcversion_id, build_id, testplan_id, execution_ts
                ) unique_exec 
                    ON e.tcversion_id = unique_exec.tcversion_id 
                    AND e.build_id = unique_exec.build_id 
                    AND e.testplan_id = unique_exec.testplan_id 
                    AND e.execution_ts = unique_exec.execution_ts
                    AND e.id = unique_exec.min_id
                JOIN tcversions tcv ON e.tcversion_id = tcv.id
                JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
                JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
                LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
                JOIN testplans tp ON e.testplan_id = tp.id
                JOIN testprojects tproj ON tp.testproject_id = tproj.id
            WHERE 1=1";
    
    // Apply filters
    if ($projectId > 0) {
        $sql .= " AND tp.testproject_id = " . intval($projectId);
    }
    
    if ($planId > 0) {
        $sql .= " AND e.testplan_id = " . intval($planId);
    }
    
    if ($buildId > 0) {
        $sql .= " AND e.build_id = " . intval($buildId);
    }
    
    // Apply date filters
    if (!empty($startDate)) {
        $sql .= " AND e.execution_ts >= '" . addslashes($startDate . ' 00:00:00') . "'";
    }
    
    if (!empty($endDate)) {
        $sql .= " AND e.execution_ts <= '" . addslashes($endDate . ' 23:59:59') . "'";
    }
    
    $sql .= " GROUP BY parent_nh.id, parent_nh.name
              ORDER BY parent_nh.name";
    
    $result = $db->exec_query($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Database query failed']);
        return;
    }
    
    $suites = array();
    $totalSuites = 0;
    $totalPassed = 0;
    $totalFailed = 0;
    $totalBlocked = 0;
    $totalNotRun = 0;
    
    while ($row = $db->fetch_array($result)) {
        $suites[] = array(
            'suite_id' => $row['suite_id'],
            'suite_name' => $row['suite_name'],
            'total_cases' => intval($row['total_cases']),
            'passed' => intval($row['passed']),
            'failed' => intval($row['failed']),
            'blocked' => intval($row['blocked']),
            'not_run' => intval($row['not_run']),
            'last_execution' => $row['last_execution']
        );
        
        $totalSuites++;
        $totalPassed += intval($row['passed']);
        $totalFailed += intval($row['failed']);
        $totalBlocked += intval($row['blocked']);
        $totalNotRun += intval($row['not_run']);
    }
    
    echo json_encode([
        'success' => true,
        'data' => $suites,
        'summary' => array(
            'totalSuites' => $totalSuites,
            'totalPassed' => $totalPassed,
            'totalFailed' => $totalFailed,
            'totalBlocked' => $totalBlocked,
            'totalNotRun' => $totalNotRun
        )
    ]);
}
?>
