<?php
/**
 * Test Execution Summary 
 * 
 * This file displays a summary of test case executions including status,
 * tester information, and timestamps, grouped according to the directory tree.
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
$gui->pageTitle = 'Test Execution Summary';
$gui->warning_msg = '';
$gui->tproject_id = isset($args->tproject_id) ? $args->tproject_id : 0;

// Check if user has proper rights
$hasRights = $currentUser->hasRight($db, 'testplan_metrics', $gui->tproject_id);
if (!$hasRights) {
    $gui->warning_msg = lang_get('no_permissions');
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}

// Get test project list for selection
$testProjectMgr = new testproject($db);
$gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);

// Get filter parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedStatus = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';

// Get test plans for the selected project
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

// Build SQL query directly based on the actual database schema
$sql = "SELECT 
            e.id AS execution_id,
            e.status AS execution_status,
            e.testplan_id,
            tp.notes AS testplan_notes,
            e.build_id,
            b.name AS build_name,
            b.notes AS build_notes,
            e.platform_id,
            p.name AS platform_name,
            p.notes AS platform_notes,
            e.tcversion_id,
            tcv.version AS tc_version,
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
            tproj.notes AS project_notes
        FROM 
            executions e
            JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
                  FROM executions
                  GROUP BY tcversion_id, build_id, testplan_id) latest_e 
                ON e.tcversion_id = latest_e.tcversion_id 
                AND e.build_id = latest_e.build_id 
                AND e.testplan_id = latest_e.testplan_id 
                AND e.execution_ts = latest_e.latest_exec_ts
            JOIN tcversions tcv ON e.tcversion_id = tcv.id
            JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
            JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
            JOIN testplans tp ON e.testplan_id = tp.id
            JOIN builds b ON e.build_id = b.id
            LEFT JOIN platforms p ON e.platform_id = p.id
            LEFT JOIN users u ON e.tester_id = u.id
            LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
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

if (!empty($startDate)) {
    $sql .= " AND e.execution_ts >= '" . $db->prepare_string($startDate . ' 00:00:00') . "'";
}

if (!empty($endDate)) {
    $sql .= " AND e.execution_ts <= '" . $db->prepare_string($endDate . ' 23:59:59') . "'";
}

// Order by hierarchy
$sql .= " ORDER BY tproj.notes, tp.notes, parent_nh.name, nh_tc.name, e.execution_ts DESC";

// Execute query
$result = $db->exec_query($sql);

if (!$result) {
    $gui->warning_msg = "Error executing query: " . $db->error_msg();
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}

// Initialize counters and data structures
$totalExecutions = 0;
$statusCounts = array(
    'p' => 0,  // Passed
    'f' => 0,  // Failed
    'b' => 0,  // Blocked
    'n' => 0   // Not Run
);
$testerCounts = array();
$suiteCounts = array();
$hierarchicalData = array();

// Process results into a hierarchical structure
while ($row = $db->fetch_array($result)) {
    $totalExecutions++;
    
    // Count by status
    if (isset($row['execution_status'])) {
        $status = $row['execution_status'];
        if (isset($statusCounts[$status])) {
            $statusCounts[$status]++;
        }
    }
    
    // Count by tester
    $testerId = $row['tester_id'];
    $testerName = '';
    
    // Create tester name based on available fields
    if (!empty($row['tester_firstname']) && !empty($row['tester_lastname'])) {
        $testerName = $row['tester_firstname'] . ' ' . $row['tester_lastname'];
    } elseif (!empty($row['tester_login'])) {
        $testerName = $row['tester_login'];
    } else {
        $testerName = "User ID: " . $testerId;
    }
    
    if (!isset($testerCounts[$testerId])) {
        $testerCounts[$testerId] = array(
            'name' => $testerName,
            'count' => 0
        );
    }
    $testerCounts[$testerId]['count']++;
    
    // Group by project/plan/suite hierarchy
    $projectId = $row['project_id'];
    $testplanId = $row['testplan_id'];
    $suiteId = $row['parent_suite_id'] ?? 0;
    $suiteName = $row['parent_suite_name'] ?? 'No Suite';
    
    // Use notes for project name
    $projectNotes = strip_tags($row['project_notes'] ?? 'Unknown Project');
    
    if (!isset($hierarchicalData[$projectId])) {
        $hierarchicalData[$projectId] = array(
            'name' => $projectNotes,
            'testplans' => array()
        );
    }
    
    // Use notes for testplan name
    $testplanNotes = strip_tags($row['testplan_notes'] ?? 'Unknown Test Plan');
    
    if (!isset($hierarchicalData[$projectId]['testplans'][$testplanId])) {
        $hierarchicalData[$projectId]['testplans'][$testplanId] = array(
            'name' => $testplanNotes,
            'suites' => array()
        );
    }
    
    if (!isset($hierarchicalData[$projectId]['testplans'][$testplanId]['suites'][$suiteId])) {
        $hierarchicalData[$projectId]['testplans'][$testplanId]['suites'][$suiteId] = array(
            'name' => $suiteName,
            'executions' => array()
        );
    }
    
    // Count by suite
    if (!isset($suiteCounts[$suiteId])) {
        $suiteCounts[$suiteId] = array(
            'name' => $suiteName,
            'count' => 0,
            'statuses' => array('p' => 0, 'f' => 0, 'b' => 0, 'n' => 0)
        );
    }
    $suiteCounts[$suiteId]['count']++;
    if (isset($row['execution_status']) && isset($suiteCounts[$suiteId]['statuses'][$row['execution_status']])) {
        $suiteCounts[$suiteId]['statuses'][$row['execution_status']]++;
    }
    
    // Add the execution to the structure
    $hierarchicalData[$projectId]['testplans'][$testplanId]['suites'][$suiteId]['executions'][] = $row;
}

// Assign data to the template
$gui->data = $hierarchicalData;
$gui->totalExecutions = $totalExecutions;
$gui->statusCounts = $statusCounts;
$gui->testerCounts = $testerCounts;
$gui->suiteCounts = $suiteCounts;
$gui->selectedProject = $selectedProject;
$gui->selectedPlan = $selectedPlan;
$gui->selectedBuild = $selectedBuild;
$gui->selectedStatus = $selectedStatus;
$gui->startDate = $startDate;
$gui->endDate = $endDate;

// Calculate pass rate
$gui->passRate = 0;
if ($totalExecutions > 0 && isset($statusCounts['p'])) {
    $gui->passRate = round(($statusCounts['p'] / $totalExecutions) * 100, 2);
}

// Initialize Smarty template engine
$templateCfg = templateConfiguration();
$smarty = new TLSmarty();

$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

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