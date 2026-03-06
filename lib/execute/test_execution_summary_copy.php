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
require_once('materialized_path_refresh.php'); // Include materialized path refresh functions

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

// Check if we need to refresh the materialized path table
// Force refresh on every page load as requested
$refreshInterval = 24; // hours (still used as a fallback)
$forceRefresh = true; // Always refresh on page load

// Add a timestamp parameter to the URL to prevent caching issues
$timestamp = time();
$gui->refresh_timestamp = $timestamp;

// Refresh the materialized path table
$refreshResult = refreshMaterializedPathTable($db, $refreshInterval, $forceRefresh);
if ($refreshResult) {
    $gui->info_msg = 'Materialized path table was refreshed at ' . date('H:i:s') . ' to ensure up-to-date hierarchy data.';
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
$selectedSuite = isset($_REQUEST['suite_id']) ? intval($_REQUEST['suite_id']) : 0; // New parameter for filtering by test suite

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

// First, let's get all the test suites hierarchy
$sql_hierarchy = "SELECT
                    nh1.id AS id,
                    nh1.name AS name,
                    nh1.parent_id AS parent_id,
                    COALESCE(nh2.name, '') AS parent_name,
                    nh1.node_type_id
                 FROM
                    nodes_hierarchy nh1
                 LEFT JOIN
                    nodes_hierarchy nh2 ON nh1.parent_id = nh2.id
                 WHERE
                    nh1.node_type_id = 2"; // 2 = test suite
                 
$result_hierarchy = $db->exec_query($sql_hierarchy);
$suiteHierarchy = array();

// Build a lookup array for suite hierarchy
// Note: $db->fetch_array returns associative array, not object as static analyzer may expect
if ($result_hierarchy) {
    while ($row = $db->fetch_array($result_hierarchy)) {
        $suiteHierarchy[$row['id']] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'parent_id' => $row['parent_id'],
            'parent_name' => $row['parent_name'],
            'children' => array(),
            'path' => array(),
            'path_names' => array()
        );
    }
}

// Process the hierarchy to build the complete paths
foreach ($suiteHierarchy as $id => &$suite) {
    // Build path from current node to root
    $path = array();
    $path_names = array();
    $currentId = $id;
    
    // Loop until we reach a node without a parent (or parent not in our hierarchy)
    while (isset($currentId) && isset($suiteHierarchy[$currentId])) {
        array_unshift($path, $currentId);
        array_unshift($path_names, $suiteHierarchy[$currentId]['name']);
        $currentId = $suiteHierarchy[$currentId]['parent_id'];
        
        // Avoid infinite loops
        if (in_array($currentId, $path)) {
            break;
        }
    }
    
    $suite['path'] = $path;
    $suite['path_names'] = $path_names;
    
    // Add this node to its parent's children array
    if ($suite['parent_id'] && isset($suiteHierarchy[$suite['parent_id']])) {
        $suiteHierarchy[$suite['parent_id']]['children'][] = $id;
    }
}

// Get all test suites for filtering
$testsuites = array(0 => lang_get('any'));
foreach ($suiteHierarchy as $id => $suite) {
    // Format name with indentation to show hierarchy
    $indentation = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', count($suite['path']) - 1);
    $testsuites[$id] = $indentation . htmlspecialchars($suite['name']);
}
$gui->testsuites = $testsuites;

// Build SQL query using our path-based view with optimizations and pagination
$countSQL = "SELECT COUNT(*) as total_count FROM test_execution_hierarchy_summary_optimized e";
// No need for join clause with view
$joinClause = "";

// Start with WHERE 1=1 so we can append conditions with AND
$whereClause = " WHERE 1=1";

if ($selectedProject) {
    $whereClause .= " AND e.project_id = $selectedProject";
}

if ($selectedPlan) {
    $whereClause .= " AND e.testplan_id = $selectedPlan";
}

if ($selectedBuild) {
    $whereClause .= " AND e.build_id = $selectedBuild";
}

if ($selectedSuite) {
    // Using our hierarchical levels from the path-based view
    $whereClause .= " AND (
        e.suite_id = $selectedSuite OR
        e.level1_id = $selectedSuite OR
        e.level2_id = $selectedSuite OR
        e.level3_id = $selectedSuite OR
        e.level4_id = $selectedSuite OR
        e.level5_id = $selectedSuite
    )";
}

if ($selectedStatus) {
    $whereClause .= " AND e.execution_status = '$selectedStatus'";
}

if ($startDate) {
    $whereClause .= " AND e.execution_date >= '$startDate 00:00:00'";
}

if ($endDate) {
    $whereClause .= " AND e.execution_date <= '$endDate 23:59:59'";
}

// Removed index hints as they don't exist in the optimized view
$indexHint = "";

// Get total count for pagination
$countFullSQL = $countSQL . $whereClause;

// Log the count SQL
$log_file = __DIR__ . '/sql_debug.log';
file_put_contents($log_file, "=== COUNT SQL QUERY ===\n" . $countFullSQL . "\n\n", FILE_APPEND);

try {
    $totalCountResult = $db->exec_query($countFullSQL);
    file_put_contents($log_file, "=== COUNT QUERY RESULT ===\nSuccess: " . ($totalCountResult ? 'true' : 'false') . "\n\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($log_file, "=== COUNT QUERY ERROR ===\n" . $e->getMessage() . "\n\n", FILE_APPEND);
}

$totalExecutionsCount = 0;
if ($totalCountResult && ($row = $totalCountResult->FetchRow())) {
    $totalExecutionsCount = $row['total_count'];
}
$gui->totalExecutionsCount = $totalExecutionsCount;

// Calculate pagination information
$gui->totalPages = ceil($totalExecutionsCount / $itemsPerPage);
if ($gui->totalPages == 0) {
    $gui->totalPages = 1; // At least one page even if empty
}

// Set up pagination to improve performance
$page = isset($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
$itemsPerPage = isset($_REQUEST['items_per_page']) ? intval($_REQUEST['items_per_page']) : 50;
$offset = ($page - 1) * $itemsPerPage;

// Add a reasonable overall limit to avoid server overload
$executionLimit = min($itemsPerPage, 1000);
$gui->page = $page;
$gui->itemsPerPage = $itemsPerPage;

// Main query with limit
$sql = "SELECT 
            e.execution_id,
            e.execution_status,
            e.execution_status_name,
            e.testplan_id,
            e.testplan_name,
            e.build_id,
            e.build_name,
            e.tc_version,
            e.execution_date AS execution_ts,
            e.execution_notes,
            e.tc_id,
            e.tc_name,
            e.suite_id AS parent_suite_id,
            e.suite_name AS parent_suite_name,
            e.tc_version,
            e.tc_summary,
            e.project_id AS testproject_id,
            e.project_name,
            e.tester_id,
            e.tester_login,
            e.tester_name,
            e.platform_id,
            e.platform_name,
            e.suite_path,
            e.level1_name,
            e.level2_name,
            e.level3_name,
            e.level4_name,
            e.level5_name
        FROM test_execution_hierarchy_summary_optimized e $indexHint
        $whereClause
        ORDER BY e.execution_date DESC
        LIMIT $offset, $executionLimit";

// Add a note to the GUI if we hit the limit
$gui->limit_warning = ($totalExecutionsCount > $executionLimit) ? 
    "Note: Showing $executionLimit of $totalExecutionsCount executions. Please use filters to narrow your search." : 
    "";

// No need for additional date filtering here as it's already in the whereClause

// Log the SQL query for debugging with optimization information
$log_file = dirname(__FILE__) . '/test_execution_summary_query.log';
$optimizationInfo = "\n=== OPTIMIZATION INFO ===\n";
$optimizationInfo .= "Using optimized view: test_execution_hierarchy_summary_optimized\n";
$optimizationInfo .= "Index hint: $indexHint\n";
$optimizationInfo .= "Pagination: Page $page, Items per page: $itemsPerPage\n";
$optimizationInfo .= "Total records: $totalExecutionsCount, Total pages: $gui->totalPages\n";

file_put_contents($log_file, "=== SQL QUERY [" . date('Y-m-d H:i:s') . "] ===\n" . $sql . $optimizationInfo . "\n\n", FILE_APPEND);

// Execute the query
try {
    $result = $db->exec_query($sql);
    file_put_contents($log_file, "=== QUERY RESULT ===\nSuccess: " . ($result ? 'true' : 'false') . "\n\n", FILE_APPEND);
} catch (Exception $e) {
    file_put_contents($log_file, "=== QUERY ERROR ===\n" . $e->getMessage() . "\n\n", FILE_APPEND);
    throw $e;
}
if ($result && !is_null($result)) {
    $totalExecutions = 0;
    // Make sure we have a valid result before continuing
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
    // Note: $db->fetch_array returns associative array, not object as static analyzer may expect
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
        
        // Get the full path of the suite if it exists in our hierarchy
        $suitePath = array();
        $suitePathNames = array();
        if (isset($suiteHierarchy[$suiteId])) {
            $suitePath = $suiteHierarchy[$suiteId]['path'];
            $suitePathNames = $suiteHierarchy[$suiteId]['path_names'];
        } else {
            $suitePath = array($suiteId);
            $suitePathNames = array($suiteName);
        }
        
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
                'path' => $suitePath,
                'path_names' => $suitePathNames,
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
}

// Add all necessary labels for the template
$labels = array();
$labels['project'] = lang_get('project');
$labels['testplan'] = lang_get('test_plan');
$labels['build'] = lang_get('build');
$labels['testsuite'] = lang_get('test_suite');
$labels['status'] = lang_get('status');
$labels['tester'] = lang_get('tester');
$labels['start_date'] = lang_get('start_date');
$labels['end_date'] = lang_get('end_date');
$labels['all'] = lang_get('all');
$labels['passed'] = lang_get('passed');
$labels['failed'] = lang_get('failed');
$labels['blocked'] = lang_get('blocked');
$labels['not_run'] = lang_get('not_run');
$labels['execution_overview'] = lang_get('execution_overview');
$labels['total_executions'] = lang_get('total_tc');
$labels['top_testers'] = lang_get('top_testers');
$labels['test_suite_progress'] = lang_get('test_suite_progress');
$labels['progress'] = lang_get('progress');
$labels['no_data'] = lang_get('no_data');
$labels['hierarchical_results'] = 'Hierarchical Results';
$labels['testcase'] = lang_get('test_case');
$labels['version'] = lang_get('version');
$labels['execution_ts'] = lang_get('execution_ts');
$labels['no_executions_found'] = lang_get('no_executions_found');
$labels['btn_apply'] = lang_get('btn_apply');
$labels['btn_reset'] = lang_get('btn_reset');

// Assign data to the template
$gui->labels = $labels;
$gui->pageTitle = "Test Execution Summary with Hierarchy";
$gui->data = $hierarchicalData;
$gui->totalExecutions = $totalExecutions;
$gui->statusCounts = $statusCounts;
$gui->testerCounts = $testerCounts;
$gui->suiteCounts = $suiteCounts;
$gui->selectedProject = $selectedProject;
$gui->selectedPlan = $selectedPlan;
$gui->selectedBuild = $selectedBuild;
$gui->selectedStatus = $selectedStatus;
$gui->selectedSuite = $selectedSuite;
$gui->startDate = $startDate;
$gui->endDate = $endDate;
$gui->order_by = isset($_REQUEST['order_by']) ? $_REQUEST['order_by'] : '';
$gui->order_dir = isset($_REQUEST['order_dir']) ? $_REQUEST['order_dir'] : 'ASC';

// Prepare suite hierarchy for display
$gui->suiteHierarchy = $suiteHierarchy;

// Calculate pass rate
$gui->passRate = 0;
if ($totalExecutions > 0 && isset($statusCounts['p'])) {
    $gui->passRate = round(($statusCounts['p'] / $totalExecutions) * 100, 2);
}

// Prepare the dropdown lists with performance optimization
$gui->projects = array();
$gui->testplans = array();
$gui->builds = array();
$gui->suites = array();

// Get list of projects - limit to 100 to improve performance
$sql = "SELECT id, notes as name FROM testprojects ORDER BY name LIMIT 100";
$result = $db->exec_query($sql);
if ($result && !is_null($result)) {
    while ($row = $db->fetch_array($result)) {
        $gui->projects[] = $row;
    }
}

// Get list of test plans only if a project is selected
if ($selectedProject) {
    $sql = "SELECT id, notes as name FROM testplans WHERE testproject_id = {$selectedProject} ORDER BY name LIMIT 100";
    $result = $db->exec_query($sql);
    if ($result && !is_null($result)) {
        while ($row = $db->fetch_array($result)) {
            $gui->testplans[] = $row;
        }
    }
}

// Get list of builds only if a test plan is selected
if ($selectedPlan) {
    $sql = "SELECT id, name FROM builds WHERE testplan_id = {$selectedPlan} ORDER BY name LIMIT 50";
    $result = $db->exec_query($sql);
    if ($result && !is_null($result)) {
        while ($row = $db->fetch_array($result)) {
            $gui->builds[] = $row;
        }
    }
}

// Optimize suite dropdown - only add top-level suites when no filter is applied
// This significantly reduces the amount of data to process
$flattenedSuites = array();
if (count($suiteHierarchy) < 100) {
    // If we have a reasonable number of suites, show the full hierarchy
    foreach ($suiteHierarchy as $suiteId => $suiteData) {
        $flattenedSuites[] = array(
            'id' => $suiteId,
            'name' => $suiteData['name'],
            'path_names' => $suiteData['path_names']
        );
    }
} else {
    // If we have too many suites, only show a subset
    $count = 0;
    foreach ($suiteHierarchy as $suiteId => $suiteData) {
        // Only show suites with parent path length of 1 (top level) or the selected suite and its direct children
        if (count($suiteData['path']) <= 1 || 
            ($selectedSuite && ($suiteId == $selectedSuite || 
                                (isset($suiteData['path']) && in_array($selectedSuite, $suiteData['path']))))) {
            $flattenedSuites[] = array(
                'id' => $suiteId,
                'name' => $suiteData['name'],
                'path_names' => $suiteData['path_names']
            );
            $count++;
            if ($count >= 100) break; // Limit to 100 suites maximum
        }
    }
}
$gui->suites = $flattenedSuites;

// Initialize Smarty template engine
$templateCfg = templateConfiguration();
$smarty = new TLSmarty();

// Correctly assign labels array directly to Smarty
// This is critical - the template uses $labels not $gui->labels
$smarty->assign('labels', $labels);
$smarty->assign('gui', $gui);

// Check if this is an AJAX request
$isAjax = isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1;

if ($isAjax) {
    // For AJAX requests, only display the data content part without headers or filters
    $smarty->assign('isAjax', true);
    // Use a partial template that only contains the results section
    $partialTemplate = str_replace('.tpl', '_partial.tpl', $templateCfg->default_template);
    
    // If partial template doesn't exist, we'll create simple output here
    ob_start();
    
    // Data section
    if (isset($gui->data) && !empty($gui->data)) {
        foreach ($gui->data as $projectId => $project) {
            // Output the hierarchical data
            include('test_execution_ajax_content.inc.php');
        }
    } else {
        echo '<p>'.$labels['no_executions_found'].'</p>';
    }
    
    // Pagination section
    if (isset($gui->page) && isset($gui->totalPages) && $gui->totalPages > 1) {
        include('test_execution_ajax_pagination.inc.php');
    }
    
    echo ob_get_clean();
} else {
    // For normal requests, display the full template
    $smarty->display($templateCfg->template_dir . $templateCfg->default_template);
}

/**
 * Initialize user input
 * 
 * @return object input parameters
 */
function init_args() {
    $args = new stdClass();
    $args->tproject_id = isset($_REQUEST['tproject_id']) ? intval($_REQUEST['tproject_id']) : 0;
    $args->suite_id = isset($_REQUEST['suite_id']) ? intval($_REQUEST['suite_id']) : 0;
    
    return $args;
}
?>
