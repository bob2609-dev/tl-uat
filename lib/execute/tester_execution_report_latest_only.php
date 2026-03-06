<?php
/**
 * Tester Execution Report - Latest Execution Only
 * 
 * This version counts each test case only once, considering only the status
 * of the latest execution for each test case per tester.
 * 
 * Key difference from standard report:
 * - If a test case was executed multiple times by the same tester,
 *   only the latest execution status is counted
 * - This prevents double-counting and provides more accurate metrics
 */

require_once('../../config.inc.php');
require_once('common.php');

// Initialize session and check permissions
testlinkInitPage($db, false, false, false);
$currentUser = $_SESSION['currentUser'];

// Initialize GUI object
$gui = new stdClass();

// Get filter parameters - handle both old and new parameter names
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedUser = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 
               (isset($_REQUEST['tester_id']) ? intval($_REQUEST['tester_id']) : 0);
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';
$includeNonAssigned = isset($_REQUEST['include_non_assigned']) ? ($_REQUEST['include_non_assigned'] === 'true' || $_REQUEST['include_non_assigned'] === '1') : 
                     (isset($_REQUEST['report_type']) && $_REQUEST['report_type'] === 'all');

// Load test projects for dropdown
$testProjectMgr = new testproject($db);
$gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);

// Clean project notes (remove HTML tags)
foreach ($gui->testprojects as &$project) {
    $project->name = strip_tags($project->name);
}

// Load users for dropdown
try {
    $sql = "SELECT id, login, first, last FROM users WHERE active = 1 ORDER BY first, last";
    $result = $db->exec_query($sql);
    
    $gui->users = array();
    while ($row = $db->fetch_array($result)) {
        $gui->users[] = $row;
    }
} catch (Exception $e) {
    error_log("Error loading users: " . $e->getMessage());
    $gui->users = array();
}

// Handle AJAX requests
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == '1') {
    header('Content-Type: application/json');
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    
    // Debug logging
    error_log("AJAX request received - Action: " . $action);
    error_log("Request data: " . print_r($_REQUEST, true));
    
    try {
        switch ($action) {
            case 'get_initial_data':
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'projects' => $gui->testprojects,
                        'testers' => $gui->users
                    ]
                ]);
                break;
                
            case 'run_report':
                error_log("Running LATEST EXECUTIONS ONLY report with parameters: Project=$selectedProject, User=$selectedUser, Start=$startDate, End=$endDate, IncludeNonAssigned=$includeNonAssigned");
                $reportData = getTesterReportDataLatestOnly($db, $selectedProject, $selectedUser, $startDate, $endDate, $includeNonAssigned);
                echo json_encode(['success' => true, 'data' => $reportData]);
                break;
                
            default:
                error_log("Unknown action: " . $action);
                echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
        }
    } catch (Exception $e) {
        error_log("AJAX Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Handle the new simplified AJAX request
if (isset($_REQUEST['get_report']) && $_REQUEST['get_report'] == '1') {
    header('Content-Type: application/json');
    
    try {
        $reportData = getTesterReportDataLatestOnly($db, $selectedProject, $selectedUser, $startDate, $endDate, $includeNonAssigned);
        echo json_encode(['success' => true, 'data' => $reportData]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Return dropdown data for AJAX requests
header('Content-Type: application/json');

// Debug logging
error_log("Returning dropdown data: " . count($gui->testprojects) . " projects, " . count($gui->users) . " users");

echo json_encode([
    'success' => true,
    'testprojects' => $gui->testprojects,
    'users' => $gui->users
]);

/**
 * Debug logging function for latest only report
 */
function logLatestOnlyDebug($message) {
    $logFile = __DIR__ . '/latest_only_report_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    $result = file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    if ($result === false) {
        error_log("Failed to write to latest only report log: $message");
    }
}

/**
 * Get tester report data with latest execution only logic
 * 
 * This function ensures each test case is counted only once per tester,
 * considering only the status of the latest execution.
 */
function getTesterReportDataLatestOnly($db, $projectId, $userId, $startDate, $endDate, $includeNonAssigned) {
    
    logLatestOnlyDebug("=== getTesterReportDataLatestOnly START ===");
    logLatestOnlyDebug("Parameters: Project=$projectId, User=$userId, Start=$startDate, End=$endDate, IncludeNonAssigned=$includeNonAssigned");
    error_log("=== LATEST ONLY REPORT DEBUG START ===");
    error_log("Parameters: Project=$projectId, User=$userId, Start=$startDate, End=$endDate, IncludeNonAssigned=$includeNonAssigned");
    
    try {
        // Build query with latest execution logic and enhanced "not run" calculation
        $sql = "
            SELECT 
                u.id AS tester_id,
                CONCAT(u.first, ' ', u.last) AS tester_name,
                
                IFNULL(a.assigned_cnt, 0) AS assigned_testcases,
                IFNULL(e.executed_cnt, 0) AS executed_testcases,
                IFNULL(e.pass_cnt, 0) AS passed_testcases,
                IFNULL(e.fail_cnt, 0) AS failed_testcases,
                IFNULL(e.blocked_cnt, 0) AS blocked_testcases,
                
                GREATEST(IFNULL(a.assigned_cnt,0) - IFNULL(before_start.executed_cnt,0), 0) AS assigned_not_run,
                
                NULL AS pass_rate_percent, -- Calculated in PHP
                
                e.last_execution_date
                
            FROM users u
            
            LEFT JOIN (
                SELECT 
                    ua.user_id, 
                    COUNT(DISTINCT ua.feature_id) AS assigned_cnt
                FROM user_assignments ua
                JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
                JOIN testplans tp ON tptc.testplan_id = tp.id
                WHERE ua.type IN (1, 2) AND ua.status = 1
                    " . ($projectId > 0 ? "AND tp.testproject_id = $projectId" : "") . "
                GROUP BY ua.user_id
            ) a ON a.user_id = u.id
            
            LEFT JOIN (
                -- Latest execution only logic (current period)
                SELECT
                    latest_exec.tester_id,
                    COUNT(*) AS executed_cnt,
                    SUM(latest_exec.status = 'p') AS pass_cnt,
                    SUM(latest_exec.status = 'f') AS fail_cnt,
                    SUM(latest_exec.status = 'b') AS blocked_cnt,
                    MAX(latest_exec.execution_ts) AS last_execution_date
                FROM (
                    -- Get only the latest execution for each test case per tester
                    SELECT 
                        e1.tester_id,
                        e1.tcversion_id,
                        e1.status,
                        e1.execution_ts,
                        ROW_NUMBER() OVER (
                            PARTITION BY e1.tester_id, e1.tcversion_id 
                            ORDER BY e1.execution_ts DESC
                        ) AS rn
                    FROM executions e1
                    JOIN testplans tp ON e1.testplan_id = tp.id
                    WHERE 1=1
                        " . ($projectId > 0 ? "AND tp.testproject_id = $projectId" : "") . "
                        " . (!empty($startDate) || !empty($endDate) ? "AND " : "") . "
                        " . (!empty($startDate) ? "e1.execution_ts >= '$startDate'" : "") . "
                        " . (!empty($startDate) && !empty($endDate) ? " AND " : "") . "
                        " . (!empty($endDate) ? "e1.execution_ts <= '$endDate'" : "") . "
                ) latest_exec
                WHERE latest_exec.rn = 1 -- Only get the latest execution
                GROUP BY latest_exec.tester_id
            ) e ON e.tester_id = u.id
            
            LEFT JOIN (
                -- Latest execution only logic (before start date)
                SELECT
                    latest_exec.tester_id,
                    COUNT(*) AS executed_cnt
                FROM (
                    -- Get only the latest execution for each test case per tester
                    SELECT 
                        e1.tester_id,
                        e1.tcversion_id,
                        e1.status,
                        e1.execution_ts,
                        ROW_NUMBER() OVER (
                            PARTITION BY e1.tester_id, e1.tcversion_id 
                            ORDER BY e1.execution_ts DESC
                        ) AS rn
                    FROM executions e1
                    JOIN testplans tp ON e1.testplan_id = tp.id
                    WHERE 1=1
                        " . ($projectId > 0 ? "AND tp.testproject_id = $projectId" : "") . "
                        " . (!empty($startDate) ? "AND e1.execution_ts < '$startDate'" : "") . "
                ) latest_exec
                WHERE latest_exec.rn = 1 -- Only get the latest execution
                GROUP BY latest_exec.tester_id
            ) before_start ON before_start.tester_id = u.id
            
            WHERE u.active = 1
                AND (a.user_id IS NOT NULL OR e.tester_id IS NOT NULL)
                " . ($userId > 0 ? "AND u.id = $userId" : "") . "
                " . (!$includeNonAssigned ? "HAVING assigned_testcases > 0" : "") . "
            
            ORDER BY tester_name
        ";
        
        logLatestOnlyDebug("Executing enhanced query: " . substr($sql, 0, 500) . "...");
        error_log("LATEST ONLY REPORT SQL: " . substr($sql, 0, 500) . "...");
        
        // Add debug to check before_start executions (matching main query logic)
        $beforeStartSql = "
            SELECT COUNT(*) as before_start_executions
            FROM (
                SELECT 
                    e1.tester_id,
                    e1.tcversion_id,
                    ROW_NUMBER() OVER (
                        PARTITION BY e1.tester_id, e1.tcversion_id 
                        ORDER BY e1.execution_ts DESC
                    ) AS rn
                FROM executions e1
                JOIN testplans tp ON e1.testplan_id = tp.id
                WHERE tp.testproject_id = $projectId
                  " . (!empty($startDate) ? "AND e1.execution_ts < '$startDate'" : "") . "
            ) latest_exec
            WHERE latest_exec.rn = 1
        ";
        
        $beforeStartResult = $db->exec_query($beforeStartSql);
        if ($beforeStartResult) {
            $beforeStartRow = $db->fetch_array($beforeStartResult);
            logLatestOnlyDebug("DEBUG BEFORE START: " . $beforeStartRow['before_start_executions'] . " executions before $startDate");
            error_log("LATEST ONLY REPORT DEBUG BEFORE START: " . $beforeStartRow['before_start_executions'] . " executions before $startDate");
        }
        
        $result = $db->exec_query($sql);
        
        if (!$result) {
            $errorMsg = "Query failed: " . $db->error_msg();
            logLatestOnlyDebug($errorMsg);
            error_log("LATEST ONLY REPORT ERROR: " . $errorMsg);
            throw new Exception("Database query failed: " . $db->error_msg());
        }
        
        logLatestOnlyDebug("Query executed successfully, checking results...");
        error_log("LATEST ONLY REPORT: Query executed successfully");
        
        $data = array();
        $totals = array(
            'tester_id' => null,
            'tester_name' => 'TOTAL',
            'assigned_testcases' => 0,
            'executed_testcases' => 0,
            'passed_testcases' => 0,
            'failed_testcases' => 0,
            'blocked_testcases' => 0,
            'assigned_not_run' => 0,
            'pass_rate_percent' => 0,
            'last_execution_date' => null
        );
        
        // Process results and calculate pass rate
        $rowCount = 0;
        while ($row = $db->fetch_array($result)) {
            $rowCount++;
            // Calculate pass rate in PHP - if no executions, pass rate should be 0%
            $totalExecuted = $row['passed_testcases'] + $row['failed_testcases'] + $row['blocked_testcases'];
            if ($totalExecuted == 0) {
                $row['pass_rate_percent'] = 0;
            } else {
                $row['pass_rate_percent'] = round(($row['passed_testcases'] / $totalExecuted) * 100, 2);
            }
            
            $data[] = $row;
            
            // Log individual user results
            $debugMsg = "User: " . $row['tester_name'] . 
                " | Assigned: " . $row['assigned_testcases'] . 
                " | Executed: " . $row['executed_testcases'] . 
                " | Passed: " . $row['passed_testcases'] . 
                " | Failed: " . $row['failed_testcases'] . 
                " | Blocked: " . $row['blocked_testcases'] . 
                " | Not Run: " . $row['assigned_not_run'] . 
                " | Pass Rate: " . $row['pass_rate_percent'] . "%";
            logLatestOnlyDebug($debugMsg);
            error_log("LATEST ONLY REPORT USER: " . $debugMsg);
            
            // Accumulate totals
            $totals['assigned_testcases'] += $row['assigned_testcases'];
            $totals['executed_testcases'] += $row['executed_testcases'];
            $totals['passed_testcases'] += $row['passed_testcases'];
            $totals['failed_testcases'] += $row['failed_testcases'];
            $totals['blocked_testcases'] += $row['blocked_testcases'];
            $totals['assigned_not_run'] += $row['assigned_not_run'];
        }
        
        // Calculate totals pass rate
        $totalPassed = $totals['passed_testcases'];
        $totalFailed = $totals['failed_testcases'];
        $totals['pass_rate_percent'] = ($totalPassed + $totalFailed) > 0 ? round(($totalPassed / ($totalPassed + $totalFailed)) * 100, 2) : 0;
        
        // Add totals row
        $data[] = $totals;
        
        // Log totals and final results
        $totalDebugMsg = "TOTALS: Assigned: " . $totals['assigned_testcases'] . 
            " | Executed: " . $totals['executed_testcases'] . 
            " | Passed: " . $totals['passed_testcases'] . 
            " | Failed: " . $totals['failed_testcases'] . 
            " | Blocked: " . $totals['blocked_testcases'] . 
            " | Not Run: " . $totals['assigned_not_run'] . 
            " | Pass Rate: " . $totals['pass_rate_percent'] . "%";
        
        logLatestOnlyDebug($totalDebugMsg);
        error_log("LATEST ONLY REPORT TOTALS: " . $totalDebugMsg);
        
        logLatestOnlyDebug("Query returned " . count($data) . " rows (including totals)");
        logLatestOnlyDebug("Processed $rowCount individual user rows + 1 totals row");
        logLatestOnlyDebug("=== getTesterReportDataLatestOnly END SUCCESS ===");
        error_log("LATEST ONLY REPORT: Query returned " . count($data) . " rows, processed $rowCount users");
        error_log("=== LATEST ONLY REPORT DEBUG END ===");
        return $data;
        
    } catch (Exception $e) {
        logLatestOnlyDebug("=== getTesterReportDataLatestOnly END ERROR ===");
        logLatestOnlyDebug("Exception: " . $e->getMessage());
        logLatestOnlyDebug("Stack trace: " . $e->getTraceAsString());
        error_log("LATEST ONLY REPORT ERROR: " . $e->getMessage());
        throw $e;
    }
}

?>
