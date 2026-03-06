<?php
/**
 * AJAX Endpoint for Suite Execution Summary
 * Handles dynamic data loading without page refreshes
 */
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Set content type to JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Initialize database using TestLink's standard way
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];

// Check permissions
if (!is_object($currentUser) || !$currentUser->hasRight($db, 'testplan_metrics')) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Get action parameter
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Debug log
error_log("AJAX Request - Action: " . $action . " - Request data: " . print_r($_REQUEST, true));

// Handle different AJAX requests
switch ($action) {
    case 'get_plans':
        getTestPlans($db);
        break;
    case 'get_builds':
        getBuilds($db);
        break;
    case 'run_report':
        runReport($db);
        break;
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action: ' . $action]);
        break;
}

function getTestPlans($db) {
    $projectId = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
    
    if ($projectId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
        return;
    }
    
    try {
        $tplanSql = 'SELECT id, notes FROM testplans WHERE testproject_id = ' . $projectId . ' AND active = 1 ORDER BY notes';
        $tplanResult = $db->exec_query($tplanSql);
        
        $plans = array();
        if ($tplanResult) {
            while ($row = $db->fetch_array($tplanResult)) {
                // Clean HTML tags and format properly
                $planName = strip_tags($row['notes']);
                $planName = html_entity_decode($planName, ENT_QUOTES, 'UTF-8');
                $planName = trim($planName);
                if (empty($planName)) {
                    $planName = 'Test Plan #' . $row['id'];
                }
                $plans[] = array(
                    'id' => $row['id'],
                    'name' => $planName
                );
            }
        }
        
        echo json_encode(['success' => true, 'plans' => $plans]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

function getBuilds($db) {
    $planId = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
    
    if ($planId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid test plan ID']);
        return;
    }
    
    try {
        $buildSql = 'SELECT id, name FROM builds WHERE testplan_id = ' . $planId . ' AND active = 1 ORDER BY name';
        $buildResult = $db->exec_query($buildSql);
        
        $builds = array();
        if ($buildResult) {
            while ($row = $db->fetch_array($buildResult)) {
                // Clean HTML tags and format properly
                $buildName = strip_tags($row['name']);
                $buildName = html_entity_decode($buildName, ENT_QUOTES, 'UTF-8');
                $buildName = trim($buildName);
                if (empty($buildName)) {
                    $buildName = 'Build #' . $row['id'];
                }
                $builds[] = array(
                    'id' => $row['id'],
                    'name' => $buildName
                );
            }
        }
        
        echo json_encode(['success' => true, 'builds' => $builds]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}

function runReport($db) {
    // Get filter parameters
    $selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
    $selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
    $selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
    $selectedStatus = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
    $selectedExecutionPath = isset($_REQUEST['execution_path']) ? trim($_REQUEST['execution_path']) : '';
    $startDate = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
    $endDate = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '';
    
    // Validate parameters
    if ($selectedProject <= 0) {
        echo json_encode(['success' => false, 'error' => 'Please select a project']);
        return;
    }
    
    $hasAdditionalFilters = ($selectedPlan > 0) || ($selectedBuild > 0) || ($selectedStatus !== '') || ($selectedExecutionPath !== '') || ($startDate !== '') || ($endDate !== '');
    
    if (!$hasAdditionalFilters) {
        echo json_encode(['success' => false, 'error' => 'Please add at least one additional filter']);
        return;
    }
    
    try {
        // Build stored procedure call
        $statusParam = ($selectedStatus !== '') ? "'" . addslashes($selectedStatus) . "'" : "NULL";
        $pathParam = ($selectedExecutionPath !== '') ? "'" . addslashes($selectedExecutionPath) . "'" : "NULL";
        $startParam = ($startDate !== '') ? "'" . addslashes($startDate . ' 00:00:00') . "'" : "NULL";
        $endParam = ($endDate !== '') ? "'" . addslashes($endDate . ' 23:59:59') . "'" : "NULL";
        
        $call = 'CALL suite_execution_summary(' . $selectedProject . ', ' . $selectedPlan . ', ' . $selectedBuild . ', ' . $statusParam . ', ' . $pathParam . ', ' . $startParam . ', ' . $endParam . ')';
        
        $result = $db->exec_query($call);
        
        if (!$result) {
            $errorMsg = method_exists($db, 'error_msg') ? $db->error_msg() : 'Unknown error';
            echo json_encode(['success' => false, 'error' => 'Error executing stored procedure: ' . $errorMsg]);
            return;
        }
        
        // Fetch data
        $suiteData = array();
        $totalTestCases = 0;
        $totalPassed = 0;
        $totalFailed = 0;
        $totalBlocked = 0;
        $totalNotRun = 0;
        
        while ($row = $db->fetch_array($result)) {
            $suiteData[] = array(
                'test_path' => $row['test_path'],
                'total_testcases' => isset($row['total_testcases']) ? $row['total_testcases'] : 0,
                'testcase_count' => isset($row['testcase_count']) ? $row['testcase_count'] : 0,
                'passed_count' => isset($row['passed_count']) ? $row['passed_count'] : 0,
                'failed_count' => isset($row['failed_count']) ? $row['failed_count'] : 0,
                'blocked_count' => isset($row['blocked_count']) ? $row['blocked_count'] : 0,
                'not_run_count' => isset($row['not_run_count']) ? $row['not_run_count'] : 0,
                'pass_rate' => isset($row['pass_rate']) ? $row['pass_rate'] : 0,
                'fail_rate' => isset($row['fail_rate']) ? $row['fail_rate'] : 0,
                'block_rate' => isset($row['block_rate']) ? $row['block_rate'] : 0,
                'pending_rate' => isset($row['pending_rate']) ? $row['pending_rate'] : 0,
            );
            
            $totalTestCases += (int)$row['testcase_count'];
            $totalPassed += (int)$row['passed_count'];
            $totalFailed += (int)$row['failed_count'];
            $totalBlocked += (int)$row['blocked_count'];
            $totalNotRun += (int)$row['not_run_count'];
        }
        
        // Calculate summary
        $totalExecuted = $totalPassed + $totalFailed;
        $overallPassRate = $totalExecuted > 0 ? round(($totalPassed / $totalExecuted) * 100, 2) : 0;
        $overallFailRate = $totalExecuted > 0 ? round(($totalFailed / $totalExecuted) * 100, 2) : 0;
        $overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked / $totalTestCases) * 100, 2) : 0;
        $totalNonBlocked = $totalTestCases - $totalBlocked;
        $overallPendingRate = $totalNonBlocked > 0 ? round(($totalNotRun / $totalNonBlocked) * 100, 2) : 0;
        
        $summary = array(
            'totalTestCases' => $totalTestCases,
            'totalPassed' => $totalPassed,
            'totalFailed' => $totalFailed,
            'totalBlocked' => $totalBlocked,
            'totalNotRun' => $totalNotRun,
            'overallPassRate' => $overallPassRate,
            'overallFailRate' => $overallFailRate,
            'overallBlockRate' => $overallBlockRate,
            'overallPendingRate' => $overallPendingRate
        );
        
        echo json_encode([
            'success' => true,
            'data' => $suiteData,
            'summary' => $summary,
            'rowCount' => count($suiteData)
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
    }
}
?>
