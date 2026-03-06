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
$selectedExecutionPath = isset($_REQUEST['execution_path']) ? trim($_REQUEST['execution_path']) : '';
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
    // Using standard API method for getting builds
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
$gui->selectedExecutionPath = $selectedExecutionPath;
$gui->startDate = $startDate;
$gui->endDate = $endDate;

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
            tproj.notes AS project_notes,
            nhp.full_path AS execution_path
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
            LEFT JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id
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

if ($selectedExecutionPath !== '') {
    $sql .= " AND nhp.full_path LIKE '%" . $db->prepare_string($selectedExecutionPath) . "%'";
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

// Initialize counters and data structures - will use the improved approach
$totalExecutions = 0;
$statusCounts = array(
    'p' => 0,  // Passed
    'f' => 0,  // Failed
    'b' => 0,  // Blocked
    'n' => 0   // Not Run
);
$testerCounts = array();
$suiteCounts = array(); // Will be overwritten by the new query
$hierarchicalData = array();

// Process results for building the hierarchical data structure and tester counts
while ($row = $db->fetch_array($result)) {
    // Count by status for the hierarchy display (this will be different from the overall counts)
    // that we'll calculate later using our test case-centric approach
    if (isset($row['execution_status'])) {
        $status = $row['execution_status'];
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
        // Get the parent_id for this suite to retrieve its path
        $suitePath = '';
        if ($suiteId > 0) {
            $parentQuery = "SELECT parent_id FROM nodes_hierarchy WHERE id = " . intval($suiteId);
            $parentResult = $db->exec_query($parentQuery);
            $parentRow = $db->fetch_array($parentResult);
            $parentId = $parentRow ? $parentRow['parent_id'] : null;
            
            if ($parentId) {
                $pathQuery = "SELECT full_path FROM node_hierarchy_paths_v2 WHERE node_id = " . intval($parentId);
                $pathQueryResult = $db->exec_query($pathQuery);
                $pathRow = $db->fetch_array($pathQueryResult);
                $suitePath = $pathRow ? $pathRow['full_path'] : '';
            }
        }
        
        $hierarchicalData[$projectId]['testplans'][$testplanId]['suites'][$suiteId] = array(
            'name' => $suiteName,
            'path' => $suitePath,
            'executions' => array()
        );
    }
    
    // Count by suite
    if (!isset($suiteCounts[$suiteId])) {
        // Get the suite path from the hierarchical data structure
        $suitePath = '';
        if (isset($hierarchicalData[$projectId]['testplans'][$testplanId]['suites'][$suiteId]['path'])) {
            $suitePath = $hierarchicalData[$projectId]['testplans'][$testplanId]['suites'][$suiteId]['path'];
        }
        
        $suiteCounts[$suiteId] = array(
            'name' => $suiteName,
            'path' => $suitePath,
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
// Calculate accurate metrics including untested test cases
$overallMetricsSql = "WITH all_testcase_versions AS (
    -- Get all test case versions from the nodes_hierarchy and tcversions tables
    SELECT 
        tcv.id AS tcversion_id,
        tc.id AS tc_id, 
        tc.parent_id AS suite_id,
        tcversion.version,
        tcversion.tc_external_id
    FROM 
        nodes_hierarchy tcv
        JOIN nodes_hierarchy tc ON tc.id = tcv.parent_id
        JOIN tcversions tcversion ON tcversion.id = tcv.id
    WHERE 
        tcv.node_type_id = 4  -- Test case versions
        AND tc.node_type_id = 3  -- Test cases
        AND tcversion.active = 1
),

-- Get the latest execution for each test case version if it exists
latest_executions AS (
    SELECT 
        atcv.tcversion_id,
        atcv.tc_id,
        atcv.suite_id,
        e.status
    FROM 
        all_testcase_versions atcv
    LEFT JOIN (
        -- Get the latest execution for each test case version
        SELECT 
            e.tcversion_id, 
            e.status,
            e.testplan_id,
            e.build_id
        FROM 
            executions e
        JOIN (
            SELECT 
                tcversion_id, 
                build_id, 
                testplan_id, 
                MAX(execution_ts) AS latest_exec_ts
            FROM 
                executions
            GROUP BY 
                tcversion_id, build_id, testplan_id
        ) latest ON e.tcversion_id = latest.tcversion_id 
          AND e.build_id = latest.build_id 
          AND e.testplan_id = latest.testplan_id 
          AND e.execution_ts = latest.latest_exec_ts
        WHERE 1=1";

// Apply filters to executions
if ($selectedProject > 0) {
    $overallMetricsSql .= " AND e.testplan_id IN (SELECT id FROM testplans WHERE testproject_id = " . intval($selectedProject) . ")";
}

if ($selectedPlan > 0) {
    $overallMetricsSql .= " AND e.testplan_id = " . intval($selectedPlan);
}

if ($selectedBuild > 0) {
    $overallMetricsSql .= " AND e.build_id = " . intval($selectedBuild);
}

if ($selectedStatus !== '') {
    $overallMetricsSql .= " AND e.status = '" . $db->prepare_string($selectedStatus) . "'";
}

if ($startDate !== '') {
    $overallMetricsSql .= " AND e.execution_ts >= '" . $db->prepare_string($startDate) . " 00:00:00'";
}

if ($endDate !== '') {
    $overallMetricsSql .= " AND e.execution_ts <= '" . $db->prepare_string($endDate) . " 23:59:59'";
}

// Continue the query to join executions to test cases
$overallMetricsSql .= ") e ON atcv.tcversion_id = e.tcversion_id
)

-- Main query to aggregate overall metrics
SELECT 
    COUNT(DISTINCT le.tc_id) as testcase_count,
    SUM(CASE WHEN le.status = 'p' THEN 1 ELSE 0 END) as passed_count,
    SUM(CASE WHEN le.status = 'f' THEN 1 ELSE 0 END) as failed_count,
    SUM(CASE WHEN le.status = 'b' THEN 1 ELSE 0 END) as blocked_count,
    SUM(CASE WHEN le.status IS NULL OR le.status = 'n' THEN 1 ELSE 0 END) as not_run_count
FROM 
    latest_executions le";

$overallMetricsResult = $db->exec_query($overallMetricsSql);
$overallMetrics = $db->fetch_array($overallMetricsResult);

// Update the status counts with the new accurate data
if ($overallMetrics) {
    $totalTestCases = $overallMetrics['testcase_count'];
    $statusCounts = array(
        'p' => $overallMetrics['passed_count'],
        'f' => $overallMetrics['failed_count'],
        'b' => $overallMetrics['blocked_count'],
        'n' => $overallMetrics['not_run_count']
    );
    $totalExecutions = $totalTestCases;
}

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
$gui->pathDetails = $pathDetails; // Assign path details to gui for template use

// Calculate pass rate based on actual executions (not including untested cases)
$gui->passRate = 0;
if (($statusCounts['p'] + $statusCounts['f']) > 0) {
    $gui->passRate = round(($statusCounts['p'] / ($statusCounts['p'] + $statusCounts['f'])) * 100, 2);
}

// Note: The stored procedure refresh_node_hierarchy_paths_v2() should be run manually through MySQL
// before using this page to ensure hierarchy data is current

// Path details are already populated above from hierarchical data

// Build SQL query for overall metrics calculation
$overallMetricsSql = "WITH all_testcase_versions AS (
    -- Get all test case versions from the nodes_hierarchy and tcversions tables
    SELECT 
        tcv.id AS tcversion_id,
        tc.id AS tc_id, 
        tc.parent_id AS suite_id,
        tcversion.version,
        tcversion.tc_external_id
    FROM 
        nodes_hierarchy tcv
        JOIN nodes_hierarchy tc ON tc.id = tcv.parent_id
        JOIN tcversions tcversion ON tcversion.id = tcv.id
    WHERE 
        tcv.node_type_id = 4  -- Test case versions
        AND tc.node_type_id = 3  -- Test cases
        AND tcversion.active = 1
),

-- Get the latest execution for each test case version if it exists
latest_executions AS (
    SELECT 
        atcv.tcversion_id,
        atcv.tc_id,
        atcv.suite_id,
        e.status,
        e.id AS execution_id,
        atcv.tc_external_id
    FROM 
        all_testcase_versions atcv
    LEFT JOIN (
        -- Get the latest execution for each test case version
        SELECT 
            e.tcversion_id, 
            e.id, 
            e.status,
            e.testplan_id,
            e.build_id,
            e.execution_ts
        FROM 
            executions e
        JOIN (
            SELECT 
                tcversion_id, 
                build_id, 
                testplan_id, 
                MAX(execution_ts) AS latest_exec_ts
            FROM 
                executions
            GROUP BY 
                tcversion_id, build_id, testplan_id
        ) latest ON e.tcversion_id = latest.tcversion_id 
          AND e.build_id = latest.build_id 
          AND e.testplan_id = latest.testplan_id 
          AND e.execution_ts = latest.latest_exec_ts
        WHERE 1=1";

// Apply filters to executions in overallMetricsSql
if ($selectedProject > 0) {
    $overallMetricsSql .= " AND e.testplan_id IN (SELECT id FROM testplans WHERE testproject_id = " . intval($selectedProject) . ")";
}

if ($selectedPlan > 0) {
    $overallMetricsSql .= " AND e.testplan_id = " . intval($selectedPlan);
}

if ($selectedBuild > 0) {
    $overallMetricsSql .= " AND e.build_id = " . intval($selectedBuild);
}

if ($selectedStatus !== '') {
    $overallMetricsSql .= " AND e.status = '" . $db->prepare_string($selectedStatus) . "'";
}

// Execution path filtering is handled in the main query, not in overall metrics
// Removing this filter to avoid database errors with table alias issues

if ($startDate !== '') {
    $overallMetricsSql .= " AND e.execution_ts >= '" . $db->prepare_string($startDate) . " 00:00:00'";
}

if ($endDate !== '') {
    $overallMetricsSql .= " AND e.execution_ts <= '" . $db->prepare_string($endDate) . " 23:59:59'";
}

// Continue the query to join executions to test cases
$overallMetricsSql .= ") e ON atcv.tcversion_id = e.tcversion_id
)

-- Main query to aggregate overall metrics
SELECT 
    COUNT(DISTINCT le.tc_id) as testcase_count,
    SUM(CASE WHEN le.status = 'p' THEN 1 ELSE 0 END) as passed_count,
    SUM(CASE WHEN le.status = 'f' THEN 1 ELSE 0 END) as failed_count,
    SUM(CASE WHEN le.status = 'b' THEN 1 ELSE 0 END) as blocked_count,
    SUM(CASE WHEN le.status IS NULL OR le.status = 'n' THEN 1 ELSE 0 END) as not_run_count
FROM 
    latest_executions le";

$overallMetricsResult = $db->exec_query($overallMetricsSql);
$overallMetrics = $db->fetch_array($overallMetricsResult);

// Update the status counts with the new accurate data
if ($overallMetrics) {
    $totalTestCases = $overallMetrics['testcase_count'];
    $statusCounts = array(
        'p' => $overallMetrics['passed_count'],
        'f' => $overallMetrics['failed_count'],
        'b' => $overallMetrics['blocked_count'],
        'n' => $overallMetrics['not_run_count']
    );
} else {
    $totalTestCases = 0;
    $statusCounts = array('p' => 0, 'f' => 0, 'b' => 0, 'n' => 0);
}

// Calculate pass rate
$totalExecuted = $statusCounts['p'] + $statusCounts['f'] + $statusCounts['b'];
$passRate = $totalExecuted > 0 ? round(($statusCounts['p'] / $totalExecuted) * 100, 1) : 0;

// Suite counts are already populated from the main processing loop above
// No need to reinitialize the suiteCounts array

// Build path details array from hierarchical data
$pathDetails = array();

// Process hierarchical data to create path-based metrics
foreach ($hierarchicalData as $projectId => $project) {
    foreach ($project['testplans'] as $testplanId => $testplan) {
        foreach ($testplan['suites'] as $suiteId => $suite) {
            if (!empty($suite['path']) && !empty($suite['executions'])) {
                $path = $suite['path'];
                
                if (!isset($pathDetails[$path])) {
                    $pathDetails[$path] = array(
                        'full_path' => $path,
                        'testcase_count' => 0,
                        'passed_count' => 0,
                        'failed_count' => 0,
                        'blocked_count' => 0,
                        'not_run_count' => 0,
                        'pass_rate' => 0,
                        'fail_rate' => 0,
                        'block_rate' => 0,
                        'pending_rate' => 0
                    );
                }
                
                // Count executions by status for this path
                foreach ($suite['executions'] as $execution) {
                    $pathDetails[$path]['testcase_count']++;
                    
                    $status = $execution['execution_status'] ?? 'n';
                    switch ($status) {
                        case 'p':
                            $pathDetails[$path]['passed_count']++;
                            break;
                        case 'f':
                            $pathDetails[$path]['failed_count']++;
                            break;
                        case 'b':
                            $pathDetails[$path]['blocked_count']++;
                            break;
                        default:
                            $pathDetails[$path]['not_run_count']++;
                            break;
                    }
                }
            }
        }
    }
}

// Calculate rates for each path
foreach ($pathDetails as $path => &$details) {
    $total = $details['testcase_count'];
    $executed = $details['passed_count'] + $details['failed_count'];
    
    if ($executed > 0) {
        $details['pass_rate'] = round(($details['passed_count'] / $executed) * 100, 2);
        $details['fail_rate'] = round(($details['failed_count'] / $executed) * 100, 2);
    }
    
    if ($total > 0) {
        $details['block_rate'] = round(($details['blocked_count'] / $total) * 100, 2);
        $nonBlocked = $total - $details['blocked_count'];
        if ($nonBlocked > 0) {
            $details['pending_rate'] = round(($details['not_run_count'] / $nonBlocked) * 100, 2);
        }
    }
}

// Assign GUI data for template

$gui->pathDetails = $pathDetails;
$gui->suiteCounts = $suiteCounts;

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