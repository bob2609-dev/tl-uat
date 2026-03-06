<?php
/**
 * Optimized Test Execution Summary with Tester Filtering
 * 
 * Enhanced version of the original execution summary with:
 * - Modern AJAX-based UI
 * - Tester filtering capability
 * - Improved performance
 * - Real-time data updates
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once("../../config.inc.php");
require_once('common.php');
require_once('users.inc.php');

// Initialize session and check permissions
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];
$gui = new stdClass();
$gui->pageTitle = 'Optimized Test Execution Summary';
$gui->warning_msg = '';

// Get filter parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedStatus = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$selectedTester = isset($_REQUEST['tester_id']) ? intval($_REQUEST['tester_id']) : 0;
$selectedExecutionPath = isset($_REQUEST['execution_path']) ? trim($_REQUEST['execution_path']) : '';
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
                
            case 'get_testers':
                getTesters($db, $selectedProject, $selectedPlan, $selectedBuild);
                break;
                
            case 'run_report':
                runExecutionReport($db, $selectedProject, $selectedPlan, $selectedBuild, 
                                  $selectedStatus, $selectedTester, $selectedExecutionPath, 
                                  $startDate, $endDate);
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
$hasRights = $currentUser->hasRight($db, 'testplan_metrics', $selectedProject);
if (!$hasRights) {
    $gui->warning_msg = lang_get('no_permissions');
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}

// Get test projects
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

// Get execution statuses
$gui->statuses = array(
    '' => lang_get('any'),
    'p' => lang_get('test_status_passed'),
    'f' => lang_get('test_status_failed'),
    'b' => lang_get('test_status_blocked'),
    'n' => lang_get('test_status_not_run')
);

// Pass filter parameters to template
$gui->selectedProject = $selectedProject;
$gui->selectedPlan = $selectedPlan;
$gui->selectedBuild = $selectedBuild;
$gui->selectedStatus = $selectedStatus;
$gui->selectedTester = $selectedTester;
$gui->selectedExecutionPath = $selectedExecutionPath;
$gui->startDate = $startDate;
$gui->endDate = $endDate;

// Display the optimized template
$templateCfg = templateConfiguration();
$smarty = new TLSmarty();
$smarty->assign('gui', $gui);

// Convert data to JSON for JavaScript
$testprojectsJson = json_encode($gui->testprojects);
$testplansJson = json_encode($gui->testplans);
$buildsJson = json_encode($gui->builds);

$smarty->assign('testprojects_json', $testprojectsJson);
$smarty->assign('testplans_json', $testplansJson);
$smarty->assign('builds_json', $buildsJson);

$smarty->display($templateCfg->template_dir . 'test_execution_summary_optimized.tpl');

/**
 * Get testers who have executed tests in the specified scope
 */
function getTesters($db, $projectId, $planId, $buildId) {
    $sql = "SELECT DISTINCT 
                u.id AS tester_id,
                u.login AS tester_login,
                u.first AS tester_firstname,
                u.last AS tester_lastname
            FROM executions e
            JOIN users u ON e.tester_id = u.id
            JOIN testplans tp ON e.testplan_id = tp.id
            WHERE 1=1";
    
    if ($projectId > 0) {
        $sql .= " AND tp.testproject_id = " . intval($projectId);
    }
    
    if ($planId > 0) {
        $sql .= " AND e.testplan_id = " . intval($planId);
    }
    
    if ($buildId > 0) {
        $sql .= " AND e.build_id = " . intval($buildId);
    }
    
    $sql .= " ORDER BY u.last, u.first, u.login";
    
    $result = $db->exec_query($sql);
    $testers = array();
    
    if ($result) {
        while ($row = $db->fetch_array($result)) {
            $testerName = '';
            if (!empty($row['tester_firstname']) && !empty($row['tester_lastname'])) {
                $testerName = $row['tester_firstname'] . ' ' . $row['tester_lastname'];
            } else {
                $testerName = $row['tester_login'];
            }
            
            $testers[] = array(
                'id' => $row['tester_id'],
                'name' => $testerName,
                'login' => $row['tester_login']
            );
        }
    }
    
    echo json_encode(['success' => true, 'testers' => $testers]);
}

/**
 * Run the execution report with all filters applied
 */
function runExecutionReport($db, $projectId, $planId, $buildId, $status, $testerId, $executionPath, $startDate, $endDate) {
    // Clean output buffer
    if (ob_get_length()) ob_clean();
    
    // Optimized query to handle duplicates - count as one if tcversion_id and execution_ts match
    $sql = "SELECT 
                e.id AS execution_id,
                e.status AS execution_status,
                e.testplan_id,
                tp.notes AS testplan_notes,
                e.build_id,
                b.name AS build_name,
                e.tcversion_id,
                tcv.summary AS tc_summary,
                nh_tc.id AS tc_id,
                nh_tc.name AS tc_name,
                parent_nh.id AS parent_suite_id,
                parent_nh.name AS parent_suite_name,
                e.execution_ts AS execution_timestamp,
                e.tester_id,
                u.login AS tester_login,
                u.first AS tester_firstname,
                u.last AS tester_lastname,
                tp.testproject_id AS project_id,
                tproj.notes AS project_notes,
                nhp.full_path AS execution_path
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
                        MIN(id) as min_id  -- Use the earliest ID to avoid duplicates
                    FROM executions
                    GROUP BY tcversion_id, build_id, testplan_id, execution_ts
                ) unique_exec 
                    ON e.tcversion_id = unique_exec.tcversion_id 
                    AND e.build_id = unique_exec.build_id 
                    AND e.testplan_id = unique_exec.testplan_id 
                    AND e.execution_ts = unique_exec.execution_ts
                    AND e.id = unique_exec.min_id  -- Ensure we get only one record per unique combination
                JOIN tcversions tcv ON e.tcversion_id = tcv.id
                JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
                JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
                JOIN testplans tp ON e.testplan_id = tp.id
                JOIN builds b ON e.build_id = b.id
                LEFT JOIN users u ON e.tester_id = u.id
                LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
                JOIN testprojects tproj ON tp.testproject_id = tproj.id
                LEFT JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id
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
    
    if (!empty($status)) {
        $sql .= " AND e.status = '" . addslashes($status) . "'";
    }
    
    if ($testerId > 0) {
        $sql .= " AND e.tester_id = " . intval($testerId);
    }
    
    if (!empty($startDate)) {
        $sql .= " AND e.execution_ts >= '" . addslashes($startDate . ' 00:00:00') . "'";
    }
    
    if (!empty($endDate)) {
        $sql .= " AND e.execution_ts <= '" . addslashes($endDate . ' 23:59:59') . "'";
    }
    
    if (!empty($executionPath)) {
        $sql .= " AND nhp.full_path LIKE '%" . addslashes($executionPath) . "%'";
    }
    
    $sql .= " ORDER BY tproj.notes, tp.notes, parent_nh.name, nh_tc.name, e.execution_ts DESC";
    
    $result = $db->exec_query($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'error' => 'Database query failed']);
        return;
    }
    
    $executions = array();
    $statusCounts = array('p' => 0, 'f' => 0, 'b' => 0, 'n' => 0);
    $totalExecutions = 0;
    
    while ($row = $db->fetch_array($result)) {
        $testerName = '';
        if (!empty($row['tester_firstname']) && !empty($row['tester_lastname'])) {
            $testerName = $row['tester_firstname'] . ' ' . $row['tester_lastname'];
        } else {
            $testerName = $row['tester_login'];
        }
        
        $executions[] = array(
            'execution_id' => $row['execution_id'],
            'status' => $row['execution_status'],
            'testplan_notes' => $row['testplan_notes'],
            'build_name' => $row['build_name'],
            'tc_summary' => $row['tc_summary'],
            'tc_name' => $row['tc_name'],
            'parent_suite_name' => $row['parent_suite_name'],
            'execution_timestamp' => $row['execution_timestamp'],
            'tester_name' => $testerName,
            'project_notes' => $row['project_notes'],
            'execution_path' => $row['execution_path']
        );
        
        // Count by status
        if (isset($statusCounts[$row['execution_status']])) {
            $statusCounts[$row['execution_status']]++;
        }
        $totalExecutions++;
    }
    
    // Calculate percentages
    $statusPercentages = array();
    foreach ($statusCounts as $status => $count) {
        $statusPercentages[$status] = $totalExecutions > 0 ? round(($count / $totalExecutions) * 100, 2) : 0;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $executions,
        'summary' => array(
            'totalExecutions' => $totalExecutions,
            'statusCounts' => $statusCounts,
            'statusPercentages' => $statusPercentages
        )
    ]);
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
?>
