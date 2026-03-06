<?php
/**
 * Test AJAX report endpoint to debug JSON issues
 */
error_reporting(E_ALL);
ini_set('display_errors', 0); // Disable display errors
ini_set('log_errors', 1);

// Clean any previous output
if (ob_get_length()) ob_clean();
ob_start();

define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Initialize database
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];

// Check permissions
if (!is_object($currentUser) || !$currentUser->hasRight($db, 'testplan_metrics')) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Get parameters
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedStatus = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
$selectedExecutionPath = isset($_REQUEST['execution_path']) ? trim($_REQUEST['execution_path']) : '';
$startDate = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
$endDate = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '';

// Validate project
if ($selectedProject <= 0) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Please select a project']);
    exit;
}

try {
    // Prepare parameters for stored procedure
    $statusParam = ($selectedStatus !== '') ? "'" . addslashes($selectedStatus) . "'" : "NULL";
    $pathParam = ($selectedExecutionPath !== '') ? "'" . addslashes($selectedExecutionPath) . "'" : "NULL";
    $startParam = ($startDate !== '') ? "'" . addslashes($startDate . ' 00:00:00') . "'" : "NULL";
    $endParam = ($endDate !== '') ? "'" . addslashes($endDate . ' 23:59:59') . "'" : "NULL";
    
    $call = 'CALL suite_execution_summary(' . intval($selectedProject) . ', ' . intval($selectedPlan) . ', ' . intval($selectedBuild) . ', ' . $statusParam . ', ' . $pathParam . ', ' . $startParam . ', ' . $endParam . ')';
    
    $result = $db->exec_query($call);
    if (!$result) {
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database query failed']);
        exit;
    }

    $suiteData = array();
    $totalTestCases = 0;
    $totalPassed = 0;
    $totalFailed = 0;
    $totalBlocked = 0;
    $totalNotRun = 0;

    if ($result) {
        while ($row = $db->fetch_array($result)) {
            $suiteData[] = array(
                'test_path' => $row['test_path'] ?? '',
                'total_testcases' => $row['total_testcases'] ?? 0,
                'testcase_count' => $row['testcase_count'] ?? 0,
                'passed_count' => $row['passed_count'] ?? 0,
                'failed_count' => $row['failed_count'] ?? 0,
                'blocked_count' => $row['blocked_count'] ?? 0,
                'not_run_count' => $row['not_run_count'] ?? 0,
                'pass_rate' => round($row['pass_rate'] ?? 0, 2),
                'fail_rate' => round($row['fail_rate'] ?? 0, 2),
                'block_rate' => round($row['block_rate'] ?? 0, 2),
                'pending_rate' => round($row['pending_rate'] ?? 0, 2)
            );
            
            $totalTestCases += $row['total_testcases'] ?? 0;
            $totalPassed += $row['passed_count'] ?? 0;
            $totalFailed += $row['failed_count'] ?? 0;
            $totalBlocked += $row['blocked_count'] ?? 0;
            $totalNotRun += $row['not_run_count'] ?? 0;
        }
    }
    
    $totalExecuted = $totalPassed + $totalFailed;
    $overallPassRate = $totalExecuted > 0 ? round(($totalPassed / $totalExecuted) * 100, 2) : 0;
    $overallFailRate = $totalExecuted > 0 ? round(($totalFailed / $totalExecuted) * 100, 2) : 0;
    $overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked / $totalTestCases) * 100, 2) : 0;
    $totalNonBlocked = $totalTestCases - $totalBlocked;
    $overallPendingRate = $totalNonBlocked > 0 ? round(($totalNotRun / $totalNonBlocked) * 100, 2) : 0;
    
    // Clean output buffer completely
    ob_end_clean();
    ob_start();
    
    header('Content-Type: application/json');
    $response = [
        'success' => true,
        'data' => $suiteData,
        'summary' => [
            'totalTestCases' => $totalTestCases,
            'totalPassed' => $totalPassed,
            'totalFailed' => $totalFailed,
            'totalBlocked' => $totalBlocked,
            'totalNotRun' => $totalNotRun,
            'overallPassRate' => $overallPassRate,
            'overallFailRate' => $overallFailRate,
            'overallBlockRate' => $overallBlockRate,
            'overallPendingRate' => $overallPendingRate
        ],
        'rowCount' => count($suiteData)
    ];
    
    echo json_encode($response);
    exit;
    
} catch (Exception $e) {
    ob_end_clean();
    ob_start();
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ]);
    exit;
}
?>
