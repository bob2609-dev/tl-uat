<?php
/**
 * Tester Execution Report - Breakdown Version
 * 
 * This version shows individual execution details for each user,
 * rather than consolidated summary rows.
 * 
 * Features:
 * - Shows each execution as a separate row
 * - Includes execution date, status, and duration
 * - Maintains all filters from professional report
 * - Uses latest execution only logic
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

// Enable comprehensive logging
function logDebug($message) {
    $logFile = __DIR__ . '/breakdown_report_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] {$message}\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

logDebug("=== SCRIPT START ===");
logDebug("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
logDebug("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
logDebug("GET params: " . json_encode($_GET));
logDebug("POST params: " . json_encode($_POST));

/**
 * Calculate summary statistics from breakdown data
 */
function calculateSummaryStats($data) {
    logDebug("=== calculateSummaryStats START ===");
    
    $summary = array(
        'total_testers' => 0,
        'total_assigned' => 0,
        'total_executed' => 0,
        'total_passed' => 0,
        'total_failed' => 0,
        'total_blocked' => 0,
        'total_not_executed' => 0,
        'overall_pass_rate' => 0
    );
    
    $uniqueTesters = array();
    $userMaxExecuted = array(); // Track max executed per user to avoid duplication
    $userMaxAssigned = array(); // Track max assigned per user
    
    foreach ($data as $row) {
        // Count unique testers
        if (!in_array($row['tester_id'], $uniqueTesters)) {
            $uniqueTesters[] = $row['tester_id'];
        }
        
        // Track maximum actual executed per user (passed + failed only, blocked excluded)
        if (!isset($userMaxExecuted[$row['tester_id']]) || $row['actual_executed'] > $userMaxExecuted[$row['tester_id']]) {
            $userMaxExecuted[$row['tester_id']] = $row['actual_executed'];
        }
        
        // Track maximum assigned per user
        if (!isset($userMaxAssigned[$row['tester_id']]) || $row['assigned_testcases'] > $userMaxAssigned[$row['tester_id']]) {
            $userMaxAssigned[$row['tester_id']] = $row['assigned_testcases'];
        }
        
        // Track maximum not executed per user (most recent date)
        if (!isset($userMaxNotExecuted[$row['tester_id']]) || $row['assigned_not_run'] > $userMaxNotExecuted[$row['tester_id']]) {
            $userMaxNotExecuted[$row['tester_id']] = max(0, $row['assigned_not_run']); // Ensure non-negative
        }
    }
    
    $summary['total_testers'] = count($uniqueTesters);
    $summary['total_assigned'] = array_sum($userMaxAssigned);
    $summary['total_executed'] = array_sum($userMaxExecuted);
    $summary['total_not_executed'] = array_sum($userMaxNotExecuted);
    
    // Calculate individual status totals from the most recent data per user
    $userStatusTotals = array();
    foreach ($data as $row) {
        $testerId = $row['tester_id'];
        if (!isset($userStatusTotals[$testerId])) {
            $userStatusTotals[$testerId] = array(
                'passed' => 0,
                'failed' => 0,
                'blocked' => 0
            );
        }
        
        // Use the maximum values per user (from most recent date)
        $userStatusTotals[$testerId]['passed'] = max($userStatusTotals[$testerId]['passed'], $row['passed_testcases']);
        $userStatusTotals[$testerId]['failed'] = max($userStatusTotals[$testerId]['failed'], $row['failed_testcases']);
        $userStatusTotals[$testerId]['blocked'] = max($userStatusTotals[$testerId]['blocked'], $row['blocked_testcases']);
    }
    
    foreach ($userStatusTotals as $totals) {
        $summary['total_passed'] += $totals['passed'];
        $summary['total_failed'] += $totals['failed'];
        $summary['total_blocked'] += $totals['blocked'];
    }
    
    // Calculate overall pass rate: (Passed / (Passed + Failed)) * 100
    // Note: Blocked is considered part of executions, so pass rate only considers passed vs failed
    $totalPassedAndFailed = $summary['total_passed'] + $summary['total_failed'];
    if ($totalPassedAndFailed > 0) {
        $summary['overall_pass_rate'] = round(($summary['total_passed'] / $totalPassedAndFailed) * 100, 2);
    }
    
    logDebug("Summary calculated: " . json_encode($summary));
    logDebug("=== calculateSummaryStats END SUCCESS ===");
    
    return $summary;
}

/**
 * Get detailed execution breakdown for testers
 */
function getTesterExecutionBreakdown($projectId, $userId, $startDate, $endDate, $includeNonAssigned, $db) {
    logDebug("=== getTesterExecutionBreakdown START ===");
    logDebug("Filters: project_id=$projectId, user_id=$userId, start_date=$startDate, end_date=$endDate, include_non_assigned=$includeNonAssigned");
    
    // Set default dates if not provided
    if (empty($startDate)) {
        $startDate = date('Y-m-d', strtotime('-30 days')); // Default to 30 days ago
    }
    if (empty($endDate)) {
        $endDate = date('Y-m-d'); // Default to today
    }
    
    logDebug("Using date range: $startDate to $endDate");
    
    try {
        // Build query for user+date aggregated breakdown
        $sql = "
            SELECT 
                u.id AS tester_id,
                CONCAT(u.first, ' ', u.last) AS tester_name,
                date_range.date AS execution_date,
                MAX(e.execution_ts) AS last_execution_time,
                
                -- Assignment counts (same for all dates per user)
                IFNULL(a.assigned_cnt, 0) AS assigned_testcases,
                
                -- Unique test case execution counts per date (count each test case only once per day)
                COUNT(DISTINCT e.tcversion_id) AS executed_testcases,
                COUNT(DISTINCT CASE WHEN e.status = 'p' THEN e.tcversion_id ELSE NULL END) AS passed_testcases,
                COUNT(DISTINCT CASE WHEN e.status = 'f' THEN e.tcversion_id ELSE NULL END) AS failed_testcases,
                COUNT(DISTINCT CASE WHEN e.status = 'b' THEN e.tcversion_id ELSE NULL END) AS blocked_testcases,
                
                -- Calculated fields
                0 AS assigned_not_run, -- Will be calculated in PHP
                0 AS pass_rate_percent, -- Will be calculated in PHP
                
                -- Additional info (use default values for users with no executions)
                COALESCE(tp.notes, 'No Test Plan') AS testplan_name,
                COALESCE(b.name, 'N/A') AS build_name,
                COALESCE(pl.name, 'N/A') AS platform_name
                
            FROM users u
            
            -- First get users with assignments
            INNER JOIN (
                SELECT DISTINCT ua.user_id
                FROM user_assignments ua
                JOIN testplan_tcversions tptc_a ON ua.feature_id = tptc_a.id
                JOIN testplans tp_a ON tptc_a.testplan_id = tp_a.id
                WHERE ua.type IN (1, 2) AND ua.status = 1
                    " . ($projectId > 0 ? "AND tp_a.testproject_id = $projectId" : "") . "
            ) assigned_users ON u.id = assigned_users.user_id
            
            -- Generate complete date range
            CROSS JOIN (
                SELECT DATE_ADD('$startDate', INTERVAL seq DAY) AS date
                FROM (
                    SELECT 0 AS seq UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION
                    SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION
                    SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25 UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION
                    SELECT 30 UNION SELECT 31 UNION SELECT 32 UNION SELECT 33 UNION SELECT 34 UNION SELECT 35 UNION SELECT 36 UNION SELECT 37 UNION SELECT 38 UNION SELECT 39 UNION
                    SELECT 40 UNION SELECT 41 UNION SELECT 42 UNION SELECT 43 UNION SELECT 44 UNION SELECT 45 UNION SELECT 46 UNION SELECT 47 UNION SELECT 48 UNION SELECT 49 UNION
                    SELECT 50 UNION SELECT 51 UNION SELECT 52 UNION SELECT 53 UNION SELECT 54 UNION SELECT 55 UNION SELECT 56 UNION SELECT 57 UNION SELECT 58 UNION SELECT 59 UNION
                    SELECT 60 UNION SELECT 61 UNION SELECT 62 UNION SELECT 63 UNION SELECT 64 UNION SELECT 65 UNION SELECT 66 UNION SELECT 67 UNION SELECT 68 UNION SELECT 69 UNION
                    SELECT 70 UNION SELECT 71 UNION SELECT 72 UNION SELECT 73 UNION SELECT 74 UNION SELECT 75 UNION SELECT 76 UNION SELECT 77 UNION SELECT 78 UNION SELECT 79 UNION
                    SELECT 80 UNION SELECT 81 UNION SELECT 82 UNION SELECT 83 UNION SELECT 84 UNION SELECT 85 UNION SELECT 86 UNION SELECT 87 UNION SELECT 88 UNION SELECT 89 UNION
                    SELECT 90 UNION SELECT 91 UNION SELECT 92 UNION SELECT 93 UNION SELECT 94 UNION SELECT 95 UNION SELECT 96 UNION SELECT 97 UNION SELECT 98 UNION SELECT 99
                ) numbers
                WHERE DATE_ADD('$startDate', INTERVAL seq DAY) <= '$endDate'
            ) date_range
            
            -- Then LEFT JOIN executions within date range
            LEFT JOIN executions e ON u.id = e.tester_id AND DATE(e.execution_ts) = date_range.date
            LEFT JOIN testplans tp ON e.testplan_id = tp.id
            LEFT JOIN builds b ON e.build_id = b.id
            LEFT JOIN platforms pl ON e.platform_id = pl.id
            LEFT JOIN tcversions tptc ON e.tcversion_id = tptc.id
            LEFT JOIN (
                -- Get assigned test cases per user
                SELECT 
                    ua.user_id, 
                    COUNT(DISTINCT ua.feature_id) AS assigned_cnt
                FROM user_assignments ua
                JOIN testplan_tcversions tptc_a ON ua.feature_id = tptc_a.id
                JOIN testplans tp_a ON tptc_a.testplan_id = tp_a.id
                WHERE ua.type IN (1, 2) AND ua.status = 1
                    " . ($projectId > 0 ? "AND tp_a.testproject_id = $projectId" : "") . "
                GROUP BY ua.user_id
            ) a ON a.user_id = u.id
            
            WHERE u.active = 1
                " . ($userId > 0 ? "AND u.id = $userId" : "") . "
                " . (!$includeNonAssigned ? "AND a.assigned_cnt > 0" : "") . "
            
            GROUP BY u.id, u.first, u.last, date_range.date, a.assigned_cnt, tp.notes, b.name, pl.name
            ORDER BY u.first, u.last, execution_date DESC, last_execution_time DESC
        ";
        
        logDebug("Executing BREAKDOWN query: " . $sql);
        
        $result = $db->exec_query($sql);
        
        if (!$result) {
            logDebug("Query failed: " . $db->error_msg());
            throw new Exception("Database query failed: " . $db->error_msg());
        }
        
        $data = array();
        
        // Track cumulative executions per user up to each date
        $userCumulativeExecutions = array();
        
        // First, collect all data and sort by date
        $allData = array();
        while ($row = $db->fetch_array($result)) {
            // Calculate pass rate: (Passed / (Passed + Failed)) * 100
            $totalExecuted = $row['passed_testcases'] + $row['failed_testcases'];
            if ($totalExecuted == 0) {
                $row['pass_rate_percent'] = 0;
            } else {
                $row['pass_rate_percent'] = round(($row['passed_testcases'] / $totalExecuted) * 100, 2);
            }
            
            // Format execution date
            $row['execution_date_formatted'] = date('Y-m-d', strtotime($row['execution_date']));
            
            // Handle NULL last_execution_time for users with no executions
            if ($row['last_execution_time'] !== null) {
                $row['last_execution_time_formatted'] = date('Y-m-d H:i:s', strtotime($row['last_execution_time']));
            } else {
                $row['last_execution_time_formatted'] = 'No executions';
            }
            
            $allData[] = $row;
        }
        
        // Sort data by user and date to calculate cumulative executions
        usort($allData, function($a, $b) {
            if ($a['tester_id'] != $b['tester_id']) {
                return $a['tester_id'] - $b['tester_id'];
            }
            return strcmp($a['execution_date'], $b['execution_date']);
        });
        
        // Calculate cumulative executions and assigned_not_run per date
        foreach ($allData as $row) {
            $testerId = $row['tester_id'];
            $executionDate = $row['execution_date'];
            
            // Initialize cumulative tracking for this user
            if (!isset($userCumulativeExecutions[$testerId])) {
                $userCumulativeExecutions[$testerId] = 0;
            }
            
            // Calculate actual executed (passed + failed only, blocked excluded)
            $actualExecuted = $row['passed_testcases'] + $row['failed_testcases'];
            
            // Add current day's actual executions to cumulative total
            $userCumulativeExecutions[$testerId] += $actualExecuted;
            
            // Calculate assigned_not_run for this specific date
            // Formula: Total assigned - (Passed + Failed) = not-executed
            // Note: Blocked is NOT counted as executed for display purposes
            $row['actual_executed'] = $actualExecuted;
            $row['assigned_not_run'] = $row['assigned_testcases'] - $userCumulativeExecutions[$testerId];
            
            $data[] = $row;
        }
        
        logDebug("Breakdown query returned " . count($data) . " rows");
        
        // Calculate summary statistics
        $summary = calculateSummaryStats($data);
        
        logDebug("=== getTesterExecutionBreakdown END SUCCESS ===");
        return array(
            'data' => $data,
            'summary' => $summary
        );
        
    } catch (Exception $e) {
        logDebug("=== getTesterExecutionBreakdown END ERROR ===");
        logDebug("Exception: " . $e->getMessage());
        logDebug("Stack trace: " . $e->getTraceAsString());
        throw $e;
    }
}

// Initialize session and check permissions
logDebug("Initializing session and checking permissions");
try {
    testlinkInitPage($db, false, false, false);
    $currentUser = $_SESSION['currentUser'];
    logDebug("Session initialized successfully. User ID: " . $currentUser->dbID);
} catch (Exception $e) {
    logDebug("Session initialization failed: " . $e->getMessage());
    throw $e;
}

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

logDebug("Parsed parameters: project_id=$selectedProject, user_id=$selectedUser, start_date=$startDate, end_date=$endDate, include_non_assigned=$includeNonAssigned");

// Load test projects for dropdown
logDebug("Loading test projects for dropdown");
try {
    $testProjectMgr = new testproject($db);
    $gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);
    logDebug("Loaded " . count($gui->testprojects) . " projects");

    // Clean project notes (remove HTML tags)
    foreach ($gui->testprojects as &$project) {
        $project->name = strip_tags($project->name);
    }
} catch (Exception $e) {
    logDebug("Error loading projects: " . $e->getMessage());
    $gui->testprojects = array();
}

// Load users for dropdown
logDebug("Loading users for dropdown");
try {
    $sql = "SELECT id, login, first, last FROM users WHERE active = 1 ORDER BY first, last";
    logDebug("Users query: " . $sql);
    $result = $db->exec_query($sql);
    
    $gui->users = array();
    while ($row = $db->fetch_array($result)) {
        $gui->users[] = $row;
    }
    logDebug("Loaded " . count($gui->users) . " users");
} catch (Exception $e) {
    logDebug("Error loading users: " . $e->getMessage());
    $gui->users = array();
}

// Handle AJAX requests
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == '1') {
    logDebug("=== AJAX REQUEST START ===");
    header('Content-Type: application/json');
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    logDebug("AJAX action: " . $action);
    
    try {
        switch ($action) {
            case 'get_initial_data':
                logDebug("Processing get_initial_data request");
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'projects' => $gui->testprojects,
                        'testers' => $gui->users
                    ]
                ]);
                logDebug("get_initial_data response sent successfully");
                break;
                
            case 'run_report':
                logDebug("Processing run_report request");
                logDebug("Running breakdown report with parameters: Project=$selectedProject, User=$selectedUser, Start=$startDate, End=$endDate, IncludeNonAssigned=$includeNonAssigned");
                $reportData = getTesterExecutionBreakdown($selectedProject, $selectedUser, $startDate, $endDate, $includeNonAssigned, $db);
                echo json_encode(['success' => true, 'data' => $reportData]);
                logDebug("run_report response sent successfully");
                break;
                
            default:
                logDebug("Unknown AJAX action: " . $action);
                echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
        }
    } catch (Exception $e) {
        logDebug("AJAX Exception: " . $e->getMessage());
        logDebug("Stack trace: " . $e->getTraceAsString());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    logDebug("=== AJAX REQUEST END ===");
    exit;
}

// Handle new simplified AJAX request
if (isset($_REQUEST['get_report']) && $_REQUEST['get_report'] == '1') {
    logDebug("=== SIMPLIFIED AJAX REQUEST START ===");
    header('Content-Type: application/json');
    
    try {
        $reportData = getTesterExecutionBreakdown($selectedProject, $selectedUser, $startDate, $endDate, $includeNonAssigned, $db);
        echo json_encode(['success' => true, 'data' => $reportData]);
        logDebug("Simplified AJAX request completed successfully");
    } catch (Exception $e) {
        logDebug("Simplified AJAX Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    logDebug("=== SIMPLIFIED AJAX REQUEST END ===");
    exit;
}

// If not AJAX, render as HTML page
logDebug("=== HTML PAGE RENDER START ===");
header('Content-Type: text/html; charset=UTF-8');

// Load data for HTML page
try {
    $testProjectMgr = new testproject($db);
    $gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);
    logDebug("HTML: Loaded " . count($gui->testprojects) . " projects for dropdown");

    // Clean project notes (remove HTML tags)
    foreach ($gui->testprojects as &$project) {
        $project->name = strip_tags($project->name);
    }
} catch (Exception $e) {
    logDebug("HTML: Error loading projects: " . $e->getMessage());
    $gui->testprojects = array();
}

// Load users for HTML page
try {
    $sql = "SELECT id, login, first, last FROM users WHERE active = 1 ORDER BY first, last";
    $result = $db->exec_query($sql);
    
    $gui->users = array();
    while ($row = $db->fetch_array($result)) {
        $gui->users[] = $row;
    }
    logDebug("HTML: Loaded " . count($gui->users) . " users for dropdown");
} catch (Exception $e) {
    logDebug("HTML: Error loading users: " . $e->getMessage());
    $gui->users = array();
}

logDebug("=== HTML PAGE RENDER END ===");
logDebug("=== SCRIPT END ===");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tester Execution Report - Breakdown</title>
    
    <script src="../../gui/javascript/jquery-3.6.0.min.js"></script>
    <script src="../../gui/javascript/select2.min.js"></script>
    <link href="../../gui/css/select2.min.css" rel="stylesheet" />
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
            color: #2c3e50;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #1B263B 0%, #2D3748 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }

        .filters {
            padding: 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .filter-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            align-items: end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #415A77;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .button-group {
            padding: 20px 30px;
            text-align: center;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .btn-primary {
            background: #415A77;
            color: white;
        }

        .btn-primary:hover {
            background: #34495e;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #2D6A4F;
            color: white;
        }

        .btn-success:hover {
            background: #1e5123;
            transform: translateY(-2px);
        }

        .results {
            padding: 30px;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #1B263B;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 70px;
            text-align: center;
        }

        .badge-passed {
            background: #2D6A4F;
            color: white;
        }

        .badge-failed {
            background: #9B2226;
            color: white;
        }

        .badge-blocked {
            background: #E09F3E;
            color: #212529;
        }

        .badge-not-run {
            background: #E0E1DD;
            color: #495057;
        }

        .badge-executed {
            background: #778DA9;
            color: white;
        }

        .loading {
            text-align: center;
            padding: 60px;
            color: #6c757d;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #415A77;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            margin: 20px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin: 20px;
        }

        .no-data {
            text-align: center;
            padding: 60px;
            color: #6c757d;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Tester Execution Report - Breakdown</h1>
        </div>

        <div class="filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="project_id">Project</label>
                    <select id="project_id" name="project_id">
                        <option value="">All Projects</option>
                        <?php foreach ($gui->testprojects as $project): ?>
                            <option value="<?php echo $project->id; ?>"><?php echo htmlspecialchars($project->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="user_id">Tester</label>
                    <select id="user_id" name="user_id">
                        <option value="">All Testers</option>
                        <?php foreach ($gui->users as $user): ?>
                            <option value="<?php echo $user->id; ?>"><?php echo htmlspecialchars($user->first . ' ' . $user->last); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="filter-row">
                <div class="filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date">
                </div>

                <div class="filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date">
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="include_non_assigned" name="include_non_assigned" checked>
                <label for="include_non_assigned">Include users with zero assignments</label>
            </div>
        </div>

        <div class="button-group">
            <button type="button" id="generate_report" class="btn btn-primary">🚀 Generate Report</button>
            <button type="button" id="clear_filters" class="btn btn-secondary">🔄 Clear Filters</button>
            <button type="button" id="export_csv" class="btn btn-success" style="display: none;">📊 Export to CSV</button>
        </div>

        <div class="results">
            <div id="loading" class="loading" style="display: none;">
                <div class="spinner"></div>
                <p>Loading execution details...</p>
            </div>
            
            <div id="error_message" class="error" style="display: none;"></div>
            <div id="success_message" class="success" style="display: none;"></div>
            
            <div id="results_table" class="table-container" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Tester ID</th>
                            <th>Tester Name</th>
                            <th>Execution ID</th>
                            <th>Test Case ID</th>
                            <th>Test Case Name</th>
                            <th>External ID</th>
                            <th>Importance</th>
                            <th>Status</th>
                            <th>Execution Date</th>
                            <th>Duration</th>
                            <th>Test Plan</th>
                            <th>Build</th>
                            <th>Platform</th>
                            <th>Version</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="table_body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for dropdowns
            $('#project_id, #user_id').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Event handlers
            $('#generate_report').click(generateReport);
            $('#clear_filters').click(clearFilters);
            $('#export_csv').click(exportToCSV);
        });

        function generateReport() {
            var params = {
                ajax: '1',
                action: 'run_report',
                project_id: $('#project_id').val(),
                user_id: $('#user_id').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                include_non_assigned: $('#include_non_assigned').is(':checked')
            };

            showLoading();
            hideMessages();

            $.ajax({
                url: 'tester_execution_report_breakdown_new.php',
                type: 'POST',
                data: params,
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    
                    if (response.success) {
                        displayResults(response.data);
                        showSuccess('Execution breakdown loaded successfully');
                    } else {
                        showError('Failed to generate report: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    // Try to extract error message from response if it's HTML
                    var responseText = xhr.responseText;
                    if (responseText && responseText.includes('<pre>')) {
                        var match = responseText.match(/<pre[^>]*>([\s\S]*?)<\/pre>/);
                        if (match) {
                            showError('Server error: ' + match[1].trim());
                            return;
                        }
                    }
                    showError('Failed to generate report: ' + error);
                }
            });
        }

        function displayResults(data) {
            var $tbody = $('#table_body');
            $tbody.empty();

            if (data.length === 0) {
                $tbody.append('<tr><td colspan="15" class="no-data">No execution data found for selected criteria.</td></tr>');
                $('#results_table').show();
                return;
            }

            $.each(data, function(index, row) {
                var $tr = $('<tr>');
                
                $tr.append('<td>' + (row.tester_id || '') + '</td>');
                $tr.append('<td>' + row.tester_name + '</td>');
                $tr.append('<td>' + row.execution_id + '</td>');
                $tr.append('<td>' + row.testcase_id + '</td>');
                $tr.append('<td>' + (row.testcase_name || '') + '</td>');
                $tr.append('<td>' + (row.testcase_external_id || '') + '</td>');
                $tr.append('<td>' + (row.testcase_importance || '') + '</td>');
                $tr.append('<td><span class="status-badge ' + row.status_class + '">' + row.status_text + '</span></td>');
                $tr.append('<td>' + row.execution_date_formatted + '</td>');
                $tr.append('<td>' + row.execution_duration_formatted + '</td>');
                $tr.append('<td>' + (row.testplan_name || '') + '</td>');
                $tr.append('<td>' + (row.build_name || '') + '</td>');
                $tr.append('<td>' + (row.platform_name || '') + '</td>');
                $tr.append('<td>' + (row.testcase_version || '') + '</td>');
                $tr.append('<td>' + (row.execution_notes || '') + '</td>');
                
                $tbody.append($tr);
            });

            $('#results_table').show();
            $('#export_csv').show();
        }

        function clearFilters() {
            $('#project_id').val('').trigger('change');
            $('#user_id').val('').trigger('change');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#include_non_assigned').prop('checked', true);
            $('#results_table').hide();
            $('#export_csv').hide();
            hideMessages();
        }

        function exportToCSV() {
            // Get current table data
            var tableData = [];
            $('#table_body tr').each(function() {
                var row = [];
                $(this).find('td').each(function() {
                    row.push($(this).text());
                });
                tableData.push(row);
            });

            if (tableData.length === 0) {
                showError('No data to export');
                return;
            }

            // Create CSV content
            var headers = ['Tester ID', 'Tester Name', 'Execution ID', 'Test Case ID', 'Test Case Name', 'External ID', 'Importance', 'Status', 'Execution Date', 'Duration', 'Test Plan', 'Build', 'Platform', 'Version', 'Notes'];
            var csvContent = headers.join(',') + '\n';

            tableData.forEach(function(row) {
                // Escape commas and quotes in data
                var escapedRow = row.map(function(cell) {
                    cell = cell.toString().replace(/"/g, '""'); // Escape quotes
                    if (cell.includes(',') || cell.includes('"')) {
                        cell = '"' + cell + '"'; // Wrap in quotes if contains comma or quote
                    }
                    return cell;
                });
                csvContent += escapedRow.join(',') + '\n';
            });

            // Create download
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            
            // Generate filename with current date
            var today = new Date();
            var filename = 'Tester_Execution_Breakdown_' + today.getFullYear() + '-' + 
                          String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                          String(today.getDate()).padStart(2, '0') + '.csv';
            
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function showLoading() {
            $('#loading').show();
        }

        function hideLoading() {
            $('#loading').hide();
        }

        function showError(message) {
            $('#error_message').text(message).show();
            $('#success_message').hide();
        }

        function showSuccess(message) {
            $('#success_message').text(message).show();
            $('#error_message').hide();
        }

        function hideMessages() {
            $('#error_message').hide();
            $('#success_message').hide();
        }
    </script>
</body>
</html>
