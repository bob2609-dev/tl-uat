<?php
/**
 * Tester Execution Report - Standalone Backend
 * 
 * AJAX endpoints for the standalone HTML interface
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Initialize session and check permissions
testlinkInitPage($db, false, false, false); // Don't redirect, don't check security
$currentUser = $_SESSION['currentUser'];

// Check if this is an AJAX request
if (isset($_REQUEST['ajax'])) {
    // Set proper headers for JSON response
    header('Content-Type: application/json');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Check if user is logged in
    if (!isset($currentUser) || !$currentUser->dbID) {
        echo json_encode(['success' => false, 'error' => 'User not logged in or session expired']);
        exit;
    }
    
    try {
        // Get filter parameters
        $selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
        $selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
        $selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
        $selectedTester = isset($_REQUEST['tester_id']) ? intval($_REQUEST['tester_id']) : 0;
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

        // Get initial testers list
        $gui->testers = array();
        try {
            $sql = "SELECT DISTINCT u.id, u.login, u.first, u.last 
                    FROM users u
                    JOIN user_assignments ua ON u.id = ua.user_id
                    JOIN testplan_tcversions tptc ON ua.feature_id = tptc.tcversion_id
                    JOIN testplans tp ON tptc.testplan_id = tp.id
                    WHERE ua.type IN (1, 2)  -- Test case assignment types
                    AND tp.testproject_id IN (
                        SELECT id FROM testprojects 
                        WHERE id IN (SELECT testproject_id FROM user_testproject_roles WHERE user_id = " . $currentUser->dbID . ")
                    )
                    ORDER BY u.first, u.last";
            
            $result = $db->exec_query($sql);
            if ($result) {
                while ($row = $db->fetch_array($result)) {
                    $gui->testers[] = array(
                        'id' => $row['id'],
                        'login' => $row['login'],
                        'first' => $row['first'],
                        'last' => $row['last'],
                        'name' => $row['first'] . ' ' . $row['last']
                    );
                }
            }
        } catch (Exception $e) {
            logError('Error loading testers: ' . $e->getMessage());
        }
        
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
                runTesterReport($db, $selectedProject, $selectedPlan, $selectedBuild, $selectedTester, $startDate, $endDate);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Initialize GUI object for non-AJAX requests
$gui = new stdClass();
$gui->pageTitle = 'Tester Execution Report';
$gui->warning_msg = '';

// Get filter parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedTester = isset($_REQUEST['tester_id']) ? intval($_REQUEST['tester_id']) : 0;
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';

// Initialize data for non-AJAX requests
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

// Get initial testers list
$gui->testers = array();
try {
    $sql = "SELECT DISTINCT u.id, u.login, u.first, u.last 
            FROM users u
            JOIN user_assignments ua ON u.id = ua.user_id
            JOIN testplan_tcversions tptc ON ua.feature_id = tptc.tcversion_id
            JOIN testplans tp ON tptc.testplan_id = tp.id
            WHERE ua.type IN (1, 2)  -- Test case assignment types
            AND tp.testproject_id IN (
                SELECT id FROM testprojects 
                WHERE id IN (SELECT testproject_id FROM user_testproject_roles WHERE user_id = " . $currentUser->dbID . ")
            )
            ORDER BY u.first, u.last";
    
    $result = $db->exec_query($sql);
    if ($result) {
        while ($row = $db->fetch_array($result)) {
            $gui->testers[] = array(
                'id' => $row['id'],
                'login' => $row['login'],
                'first' => $row['first'],
                'last' => $row['last'],
                'name' => $row['first'] . ' ' . $row['last']
            );
        }
    }
} catch (Exception $e) {
    logError('Error loading testers: ' . $e->getMessage());
}

// Check permissions
if (!$currentUser->hasRight($db, 'testplan_metrics')) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

/**
 * Get initial data for dropdowns (projects, test plans, builds, testers)
 */
function getInitialData($db, $gui) {
    echo json_encode([
        'success' => true,
        'data' => array(
            'projects' => $gui->testprojects,
            'testplans' => $gui->testplans,
            'builds' => $gui->builds,
            'testers' => $gui->testers
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
 * Get testers for specific project/test plan/build
 */
function getTesters($db, $projectId, $planId, $buildId) {
    $testers = array();
    
    try {
        // Get testers who have assignments (whether executed or not)
        $sql = "SELECT DISTINCT u.id, u.login, u.first, u.last 
                FROM users u
                JOIN user_assignments ua ON u.id = ua.user_id
                JOIN testplan_tcversions tptc ON ua.feature_id = tptc.tcversion_id
                JOIN testplans tp ON tptc.testplan_id = tp.id
                WHERE ua.type IN (1, 2)  -- Test case assignment types";
        
        if ($projectId > 0) {
            $sql .= " AND tp.testproject_id = " . intval($projectId);
        }
        
        if ($planId > 0) {
            $sql .= " AND tptc.testplan_id = " . intval($planId);
        }
        
        if ($buildId > 0) {
            $sql .= " AND (ua.build_id = " . intval($buildId) . " OR ua.build_id = 0 OR ua.build_id IS NULL)";
        }
        
        $sql .= " ORDER BY u.first, u.last";
        
        $result = $db->exec_query($sql);
        if ($result) {
            while ($row = $db->fetch_array($result)) {
                $testers[] = array(
                    'id' => $row['id'],
                    'login' => $row['login'],
                    'first' => $row['first'],
                    'last' => $row['last'],
                    'name' => $row['first'] . ' ' . $row['last']
                );
            }
        }
    } catch (Exception $e) {
        logError('Error loading filtered testers: ' . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'testers' => $testers
    ]);
}

/**
 * Run the tester execution report with all filters applied
 */
function runTesterReport($db, $projectId, $planId, $buildId, $testerId, $startDate, $endDate) {
    // Clean output buffer
    if (ob_get_length()) ob_clean();
    
    // Add debugging
    error_log("=== runTesterReport DEBUG ===");
    error_log("Project ID: " . $projectId);
    error_log("Plan ID: " . $planId);
    error_log("Build ID: " . $buildId);
    error_log("Tester ID: " . $testerId);
    
    // Completely restructured query - start from test plan assignments, not executions
    // This ensures we find all testers and their assigned test cases, whether executed or not
    $sql = "SELECT 
                u.id AS tester_id,
                u.login AS tester_login,
                u.first AS tester_first,
                u.last AS tester_last,
                CONCAT(u.first, ' ', u.last) AS tester_name,
                COUNT(DISTINCT tptc.tcversion_id) AS total_assigned,
                COUNT(DISTINCT CASE WHEN e.id IS NOT NULL THEN tptc.tcversion_id END) AS total_executions,
                COUNT(DISTINCT CASE WHEN e.status = 'p' THEN tptc.tcversion_id END) AS passed,
                COUNT(DISTINCT CASE WHEN e.status = 'f' THEN tptc.tcversion_id END) AS failed,
                COUNT(DISTINCT CASE WHEN e.status = 'b' THEN tptc.tcversion_id END) AS blocked,
                COUNT(DISTINCT CASE WHEN e.id IS NULL THEN tptc.tcversion_id END) AS not_run,
                MAX(e.execution_ts) AS last_execution
            FROM 
                -- Start with all test case assignments to test plans
                testplan_tcversions tptc
                JOIN testplans tp ON tptc.testplan_id = tp.id
                JOIN testprojects tproj ON tp.testproject_id = tproj.id
                -- LEFT JOIN with user assignments to get assigned testers
                LEFT JOIN user_assignments ua ON ua.feature_id = tptc.tcversion_id 
                    AND ua.type IN (1, 2) 
                    AND ua.status = 1
                -- LEFT JOIN with users to get tester details
                LEFT JOIN users u ON ua.user_id = u.id
                -- LEFT JOIN with executions to find actual executions
                LEFT JOIN executions e ON e.tcversion_id = tptc.tcversion_id 
                    AND e.testplan_id = tptc.testplan_id 
                    AND e.platform_id = tptc.platform_id
                    AND (ua.build_id = e.build_id OR ua.build_id = 0 OR ua.build_id IS NULL)
            WHERE 1=1";
    
    // Apply filters
    if ($projectId > 0) {
        $sql .= " AND tp.testproject_id = " . intval($projectId);
        error_log("Added project filter: " . intval($projectId));
    }
    
    if ($planId > 0) {
        $sql .= " AND tptc.testplan_id = " . intval($planId);
        error_log("Added plan filter: " . intval($planId));
    }
    
    if ($buildId > 0) {
        $sql .= " AND (ua.build_id = " . intval($buildId) . " OR ua.build_id = 0 OR ua.build_id IS NULL)";
        error_log("Added build filter: " . intval($buildId));
    }
    
    if ($testerId > 0) {
        $sql .= " AND ua.user_id = " . intval($testerId);
        error_log("Added tester filter: " . intval($testerId));
    }
    
    // Apply date filters to executions only
    if (!empty($startDate)) {
        $sql .= " AND (e.execution_ts IS NULL OR e.execution_ts >= '" . addslashes($startDate . ' 00:00:00') . "')";
        error_log("Added start date filter: " . $startDate);
    }
    
    if (!empty($endDate)) {
        $sql .= " AND (e.execution_ts IS NULL OR e.execution_ts <= '" . addslashes($endDate . ' 23:59:59') . "')";
        error_log("Added end date filter: " . $endDate);
    }
    
    // Only include testers that have assignments
    $sql .= " AND u.id IS NOT NULL";
    
    $sql .= " GROUP BY u.id, u.login, u.first, u.last
              HAVING total_assigned > 0
              ORDER BY u.first, u.last";
    
    error_log("Final SQL: " . $sql);
    
    try {
        $result = $db->exec_query($sql);
        error_log("Query executed, result: " . ($result ? "success" : "failed"));
        
        if ($result) {
            $testers = array();
            $totalTesters = 0;
            $totalPassed = 0;
            $totalFailed = 0;
            $totalBlocked = 0;
            $totalNotRun = 0;
            
            while ($row = $db->fetch_array($result)) {
                error_log("Found tester: " . $row['tester_name'] . " (ID: " . $row['tester_id'] . ")");
                
                $testers[] = array(
                    'tester_id' => $row['tester_id'],
                    'tester_login' => $row['tester_login'],
                    'tester_first' => $row['tester_first'],
                    'tester_last' => $row['tester_last'],
                    'tester_name' => $row['tester_name'],
                    'total_executions' => intval($row['total_executions']),
                    'passed' => intval($row['passed']),
                    'failed' => intval($row['failed']),
                    'blocked' => intval($row['blocked']),
                    'not_run' => intval($row['not_run']),
                    'last_execution' => $row['last_execution']
                );
                
                $totalTesters++;
                $totalPassed += intval($row['passed']);
                $totalFailed += intval($row['failed']);
                $totalBlocked += intval($row['blocked']);
                $totalNotRun += intval($row['not_run']);
            }
            
            error_log("Total testers found: " . $totalTesters);
            error_log("Testers array count: " . count($testers));
            
            $response = array(
                'success' => true,
                'data' => $testers,
                'summary' => array(
                    'totalTesters' => $totalTesters,
                    'totalPassed' => $totalPassed,
                    'totalFailed' => $totalFailed,
                    'totalBlocked' => $totalBlocked,
                    'totalNotRun' => $totalNotRun
                )
            );
            
            error_log("JSON response: " . json_encode($response));
            
            echo json_encode($response);
        } else {
            error_log("Query failed: " . $db->error_msg());
            echo json_encode(['success' => false, 'error' => 'Database query failed']);
        }
    } catch (Exception $e) {
        error_log("Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/**
 * Simple error logging function
 */
function logError($message) {
    error_log('[Tester Execution Report] ' . $message);
}
?>
