<?php
// Include PHP 8 compatibility layer
// require_once(dirname(__DIR__) . '/../custom/inc/php8_init.php');
/**
 * Enhanced Bugs Viewer - with manual database configuration and improved status display
 */

// Load database configuration
require_once(dirname(__DIR__) . '/../config_db.inc.php');
// DB_TYPE, DB_HOST, DB_USER, DB_PASS, DB_NAME and DB_TABLE_PREFIX are now available from config_db.inc.php
// CSRF protection for form and AJAX
require_once(dirname(__DIR__) . '/functions/csrf.php');

// Logging class
class ExecBugLogger {
    private static $logFile = null;
    
    private static function getLogFile() {
        if (self::$logFile === null) {
            self::$logFile = __DIR__ . '/execBug.log';
        }
        return self::$logFile;
    }
    
    public static function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        
        $logFile = self::getLogFile();
        
        // Ensure log directory exists
        $logDir = dirname($logFile);
        if (!is_dir($logDir) && $logDir !== '.') {
            mkdir($logDir, 0755, true);
        }
        
        // Write to log file
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function logError($message) {
        self::log($message, 'ERROR');
    }
    
    public static function logWarning($message) {
        self::log($message, 'WARNING');
    }
    
    public static function logDebug($message) {
        self::log($message, 'DEBUG');
    }
}

// Database handler class
class SimpleDBHandler {
    private $connection;
    
    public function __construct() {
        ExecBugLogger::log("Attempting database connection to " . DB_HOST);
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->connection->connect_error) {
            ExecBugLogger::logError("Database connection failed: " . $this->connection->connect_error);
            die("Connection failed: " . $this->connection->connect_error);
        }
        ExecBugLogger::log("Database connection established successfully");
    }
    
    public function exec_query($sql, $params = []) {
        ExecBugLogger::logDebug("Executing SQL: " . substr($sql, 0, 100) . (strlen($sql) > 100 ? '...' : '') . " with " . count($params) . " parameters");
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            ExecBugLogger::logError("SQL Error: " . $this->connection->error);
            error_log("SQL Error: " . $this->connection->error);
            return false;
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // assuming all params are strings
            $stmt->bind_param($types, ...$params);
            ExecBugLogger::logDebug("Bound parameters: " . implode(', ', $params));
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        ExecBugLogger::logDebug("Query executed successfully");
        return $result;
    }
    
    public function fetch_array($result) {
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Mock user object with basic rights checking
class SimpleUser {
    public function hasRight($db, $right) {
        // For this example, we'll grant all rights
        // In production, implement proper rights checking
        return true;
    }
}

// Initialize database and user
ExecBugLogger::log("=== Starting bugs_view.php execution ===");
ExecBugLogger::log("Initializing database and user objects");
$db = new SimpleDBHandler();
$user = new SimpleUser();

// Check rights (simplified version)
ExecBugLogger::log("Checking user rights");
if (!checkRights($db, $user)) {
    ExecBugLogger::logError("Access denied - insufficient rights");
    die("Access denied - insufficient rights");
}
ExecBugLogger::log("User rights verified successfully");

$args = init_args($db);
ExecBugLogger::log("Initialized search arguments");

// Get pagination parameters
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$pageSize = isset($_GET['pageSize']) ? max(25, min(100, intval($_GET['pageSize']))) : 50;

// Get total count for pagination
$totalBugs = getBugCount($db, $args);
ExecBugLogger::log("Total bugs matching criteria: " . $totalBugs);

// Get bugs for current page only
$bugs = getBugs($db, $args, $currentPage, $pageSize);
ExecBugLogger::log("Retrieved " . count($bugs) . " bugs for page " . $currentPage . " (page size: " . $pageSize . ")");

// Function to translate status codes to human-readable format
function translateStatus($statusCode) {
    $statusMap = [
        'P' => 'Passed',
        'F' => 'Failed',
        'B' => 'Blocked',
        'N' => 'Not Run',
        'X' => 'Not Available',

        'p' => 'Passed',
        'f' => 'Failed',
        'b' => 'Blocked',
        'n' => 'Not Run',
        'x' => 'Not Available',
    ];
    
    return $statusMap[$statusCode] ?? $statusCode;
}

/**
 * Fetch Redmine issue status
 * @param string $issueId The Redmine issue ID
 * @return array|null Issue data or null if not found
 */
function getRedmineIssueStatus($issueId) {
    ExecBugLogger::log("Attempting to fetch Redmine status for issue: " . $issueId);
    
    // Redmine configuration - adjust these values as needed
    $redmineUrl = 'https://support.profinch.com';
    // $apiKey = ''; // You'll need to configure this
    $apiKey = 'a597e200f8923a85484e81ca81d731827b8dbf3d'; // You'll need to configure this
    // If no API key is configured, return mock data for testing
    if (empty($apiKey)) {
        ExecBugLogger::logWarning("Redmine API key not configured for issue: " . $issueId . ". Returning mock data.");
        // Return mock data for testing purposes
        return [
            'id' => $issueId,
            'status' => 'New',
            'priority' => 'Normal',
            'assigned_to' => 'Not Assigned',
            'updated_on' => date('Y-m-d\TH:i:s\Z')
        ];
    }
    
    $url = $redmineUrl . '/issues/' . $issueId . '.json';
    ExecBugLogger::logDebug("Fetching from URL: " . $url);
    
    // Use cURL for better reliability and error handling
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Skip SSL verification for now
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/json',
        'User-Agent: TestLink-BugViewer/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($response === false || !empty($curlError)) {
        ExecBugLogger::logError("cURL error for issue " . $issueId . ": " . $curlError);
        return [
            'id' => $issueId,
            'status' => 'Connection Error',
            'priority' => 'Unknown',
            'assigned_to' => 'Unknown',
            'updated_on' => null,
            'error' => true
        ];
    }
    
    if ($httpCode !== 200) {
        ExecBugLogger::logError("HTTP error " . $httpCode . " for issue " . $issueId);
        return [
            'id' => $issueId,
            'status' => 'HTTP ' . $httpCode,
            'priority' => 'Unknown',
            'assigned_to' => 'Unknown',
            'updated_on' => null,
            'error' => true
        ];
    }
    
    ExecBugLogger::logDebug("Received response from Redmine for issue: " . $issueId);
    ExecBugLogger::logDebug("Response length: " . strlen($response) . " characters");
    ExecBugLogger::logDebug("Response preview: " . substr($response, 0, 200));
    
    $data = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        ExecBugLogger::logError("JSON decode error for issue " . $issueId . ": " . json_last_error_msg());
        return [
            'id' => $issueId,
            'status' => 'JSON Error',
            'priority' => 'Unknown',
            'assigned_to' => 'Unknown',
            'updated_on' => null,
            'error' => true
        ];
    }
    
    if (isset($data['issue'])) {
        $issueData = [
            'id' => $data['issue']['id'],
            'status' => $data['issue']['status']['name'] ?? 'Unknown',
            'priority' => $data['issue']['priority']['name'] ?? 'Unknown',
            'assigned_to' => $data['issue']['assigned_to']['name'] ?? 'Unassigned',
            'updated_on' => $data['issue']['updated_on'] ?? null
        ];
        ExecBugLogger::log("Successfully fetched Redmine data for issue " . $issueId . ": Status = " . $issueData['status']);
        return $issueData;
    }
    
    // Log the full response if no issue data found
    ExecBugLogger::logWarning("No issue data found in Redmine response for issue: " . $issueId);
    ExecBugLogger::logDebug("Full response: " . $response);
    
    return [
        'id' => $issueId,
        'status' => 'Not Found',
        'priority' => 'Unknown',
        'assigned_to' => 'Unknown',
        'updated_on' => null,
        'error' => true
    ];
}

/**
 * Get the latest execution status for a test case
 * @param object $dbHandler Database handler
 * @param string $testcaseName Test case name
 * @return array|null Latest execution data or null if not found
 */
function getLatestTestCaseExecution($dbHandler, $testcaseName) {
    ExecBugLogger::log("Fetching latest execution for test case: " . $testcaseName);
    
    $sql = "SELECT 
                E.status as execution_status,
                E.execution_ts,
                E.tester_id,
                U.login as tester_login,
                B.name as build_name,
                NH_TPL.name as testplan_name
            FROM executions E
            JOIN tcversions TCV ON E.tcversion_id = TCV.id
            JOIN nodes_hierarchy NH_TCV ON TCV.id = NH_TCV.id
            JOIN nodes_hierarchy NH_TC ON NH_TCV.parent_id = NH_TC.id
            LEFT JOIN builds B ON E.build_id = B.id
            LEFT JOIN testplans TPL ON E.testplan_id = TPL.id
            LEFT JOIN nodes_hierarchy NH_TPL ON TPL.id = NH_TPL.id
            LEFT JOIN users U ON E.tester_id = U.id
            WHERE NH_TC.name = ?
            ORDER BY E.execution_ts DESC
            LIMIT 1";
    
    $result = $dbHandler->exec_query($sql, [$testcaseName]);
    
    if ($result) {
        $rows = $dbHandler->fetch_array($result);
        if (!empty($rows)) {
            ExecBugLogger::log("Found latest execution for " . $testcaseName . ": Status = " . $rows[0]['execution_status']);
            return $rows[0];
        } else {
            ExecBugLogger::logWarning("No execution history found for test case: " . $testcaseName);
            return null;
        }
    }
    
    ExecBugLogger::logError("Failed to query latest execution for test case: " . $testcaseName);
    return null;
}

/**
 * Check if a test case was later executed and passed after the bug execution
 * @param object $dbHandler Database handler
 * @param string $testcaseName Test case name
 * @param string $bugExecutionDate The execution date when the bug was found
 * @return array|null Later successful execution data or null if not found
 */
function checkTestCaseLaterPassed($dbHandler, $testcaseName, $bugExecutionDate) {
    ExecBugLogger::log("Checking if test case '" . $testcaseName . "' was later executed and passed after " . $bugExecutionDate);
    
    $sql = "SELECT 
                E.status as execution_status,
                E.execution_ts,
                U.login as tester_login,
                B.name as build_name,
                NH_TPL.name as testplan_name
            FROM executions E
            JOIN tcversions TCV ON E.tcversion_id = TCV.id
            JOIN nodes_hierarchy NH_TCV ON TCV.id = NH_TCV.id
            JOIN nodes_hierarchy NH_TC ON NH_TCV.parent_id = NH_TC.id
            LEFT JOIN builds B ON E.build_id = B.id
            LEFT JOIN testplans TPL ON E.testplan_id = TPL.id
            LEFT JOIN nodes_hierarchy NH_TPL ON TPL.id = NH_TPL.id
            LEFT JOIN users U ON E.tester_id = U.id
            WHERE NH_TC.name = ?
            AND E.execution_ts > ?
            AND E.status = 'p'
            ORDER BY E.execution_ts DESC
            LIMIT 1";
    
    $result = $dbHandler->exec_query($sql, [$testcaseName, $bugExecutionDate]);
    
    if ($result) {
        $rows = $dbHandler->fetch_array($result);
        if (!empty($rows)) {
            ExecBugLogger::log("Found later successful execution for " . $testcaseName . " on " . $rows[0]['execution_ts']);
            return $rows[0];
        } else {
            ExecBugLogger::logDebug("No later successful execution found for test case: " . $testcaseName);
            return null;
        }
    }
    
    ExecBugLogger::logError("Failed to query later executions for test case: " . $testcaseName);
    return null;
}

// Prepare CSRF tokens
$csrf_form_name = 'bugs_view_form_' . mt_rand(0, mt_getrandmax());
$csrf_form_token = csrfguard_generate_token($csrf_form_name);
$csrf_ajax_name = 'AJAX_Redmine_Status_' . mt_rand(0, mt_getrandmax());
$csrf_ajax_token = csrfguard_generate_token($csrf_ajax_name);

// Output HTML
echo "<!DOCTYPE html>
<html>
<head>
    <title>Execution Bugs Viewer</title>
    <script src=\"../../gui/javascript/jquery-3.6.0.min.js\"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .search-form { margin-bottom: 20px; padding: 10px; background: #f0f8ff; border-radius: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; position: sticky; top: 0; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .details-row { display: none; background-color: #f9f9f9; }
        .count { margin: 10px 0; font-weight: bold; font-size: 1.1em; }
        .button { padding: 8px 15px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .search-button { background-color: #4CAF50; color: white; }
        .clear-button { background-color: #f44336; color: white; }
        .status-passed { color: green; font-weight: bold; }
        .status-failed { color: red; font-weight: bold; }
        .status-blocked { color: orange; font-weight: bold; }
        .status-other { color: #555; font-weight: bold; }
        
        /* Redmine status styles */
        .status-new { color: #1f77b4; font-weight: bold; }
        .status-in-progress { color: #ff7f0e; font-weight: bold; }
        .status-resolved { color: #2ca02c; font-weight: bold; }
        .status-feedback { color: #d62728; font-weight: bold; }
        .status-closed { color: #9467bd; font-weight: bold; }
        .status-rejected { color: #8c564b; font-weight: bold; }
        .status-assigned { color: #e377c2; font-weight: bold; }
        .status-reopened { color: #7f7f7f; font-weight: bold; }
        
        /* Latest execution status styles */
        .status-not-ru { color: #666; font-style: italic; }
        .status-not-av { color: #999; font-style: italic; }
        .toggle-details { color: #1a73e8; text-decoration: underline; cursor: pointer; }
        .no-results { padding: 20px; text-align: center; background-color: #fff8e1; border-radius: 5px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .last-updated { font-size: 0.9em; color: #666; text-align: right; margin-bottom: 10px; }
        
        /* Pagination Styles */
        .pagination-container {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .pagination-info {
            font-weight: bold;
            color: #333;
        }
        
        .pagination-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .pagination-controls button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            color: #333;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.2s;
        }
        
        .pagination-controls button:hover:not(:disabled) {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .pagination-controls button:disabled {
            background: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        .pagination-controls .page-btn.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .page-size-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .page-size-control select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
            font-size: 14px;
        }
        
        /* Quick search input */
        #search-input {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            width: 200px;
        }
        
        /* Loading animation */
        .status-loading {
            position: relative;
        }
        
        .status-loading::after {
            content: '';
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-left: 5px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #666;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        function clearForm() {
            document.getElementById('bug_id').value = '';
            document.getElementById('testcase_name').value = '';
            document.getElementById('build_name').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            document.getElementById('bug_search_form').submit();
        }
        
        function toggleDetails(id) {
            var row = document.getElementById('details_'+id);
            row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row';
        }
        
        function toggleAllDetails() {
            var detailsRows = document.querySelectorAll('.details-row');
            var shouldShow = detailsRows[0].style.display !== 'table-row';
            
            detailsRows.forEach(function(row) {
                row.style.display = shouldShow ? 'table-row' : 'none';
            });
            
            document.getElementById('toggleAllBtn').textContent = shouldShow ? 'Hide All Details' : 'Show All Details';
        }
    </script>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Execution Bugs Viewer</h1>
            <div class='last-updated'>Last updated: " . date('Y-m-d H:i:s') . "</div>
        </div>
        
        <div class='search-form'>
            <form method='post' id='bug_search_form'>
                <table>
                    <tr>
                        <td width='15%'>Issue ID:</td>
                        <td width='35%'><input type='text' name='bug_id' id='bug_id' value='" . htmlspecialchars($args->bug_id) . "' class='form-input'></td>
                        <td width='15%'>Test Case:</td>
                        <td width='35%'><input type='text' name='testcase_name' id='testcase_name' value='" . htmlspecialchars($args->testcase_name) . "' class='form-input'></td>
                    </tr>
                    <tr>
                        <td>Build:</td>
                        <td><input type='text' name='build_name' id='build_name' value='" . htmlspecialchars($args->build_name) . "' class='form-input'></td>
                        <td>Date From:</td>
                        <td><input type='date' name='dateFrom' id='dateFrom' value='" . htmlspecialchars($args->dateFrom) . "' class='form-input'></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Date To:</td>
                        <td><input type='date' name='dateTo' id='dateTo' value='" . htmlspecialchars($args->dateTo) . "' class='form-input'></td>
                    </tr>
                    <tr>
                        <td colspan='4' style='text-align: center;'>
                            <input type='submit' class='button search-button' value='Search'>
                            <input type='button' class='button clear-button' value='Clear' onclick='clearForm();'>
                        </td>
                    </tr>
                </table>
                <input type='hidden' name='CSRFName' value='" . htmlspecialchars($csrf_form_name, ENT_QUOTES, 'UTF-8') . "'>
                <input type='hidden' name='CSRFToken' value='" . htmlspecialchars($csrf_form_token, ENT_QUOTES, 'UTF-8') . "'>
            </form>
        </div>
        
        <div class='count'>Bugs Found: " . count($bugs) . "</div>";

if (count($bugs) > 0) {
    ExecBugLogger::log("Generating table for " . count($bugs) . " bugs");
    echo "<div style='margin-bottom: 10px;'>
            <button id='toggleAllBtn' class='button' onclick='toggleAllDetails()'>Show All Details</button>
          </div>
          <div style='overflow-x: auto;'>
          <table>
            <tr>
                <th width='8%'>Issue ID</th>
                <th width='12%'>Redmine Status</th>
                <th width='20%'>Test Case</th>
                <th width='12%'>Latest TC Status</th>
                <th width='12%'>Later Passed?</th>
                <th width='12%'>Build</th>
                <th width='14%'>Execution Date</th>
                <th width='10%'>Details</th>
            </tr>";
    
    $bugCount = 0;
    foreach ($bugs as $bug) {
        $bugCount++;
        ExecBugLogger::log("Processing bug #{$bugCount}: " . $bug['bug_id'] . " for test case: " . $bug['testcase_name']);
        
        // Get latest test case execution status
        ExecBugLogger::logDebug("Fetching latest execution for test case: " . $bug['testcase_name']);
        $latestExecution = getLatestTestCaseExecution($db, $bug['testcase_name']);
        $latestStatusDisplay = $latestExecution ? translateStatus($latestExecution['execution_status']) : 'No Recent Execution';
        $latestStatusClass = $latestExecution ? 'status-' . strtolower($latestExecution['execution_status']) : 'status-unknown';
        
        // Check if test case was later executed and passed
        $laterPassed = checkTestCaseLaterPassed($db, $bug['testcase_name'], $bug['execution_ts']);
        $laterPassedDisplay = $laterPassed ? '✓ Yes (' . date('Y-m-d', strtotime($laterPassed['execution_ts'])) . ')' : 'No';
        $laterPassedClass = $laterPassed ? 'status-passed' : 'status-no';
        
        echo "<tr data-bug-id='" . htmlspecialchars($bug['bug_id']) . "' data-execution-id='" . $bug['execution_id'] . "'>
                <td><a href='https://support.profinch.com/issues/" . htmlspecialchars($bug['bug_id']) . "' target='_blank'>" . 
                     htmlspecialchars($bug['bug_id']) . "</a></td>
                <td class='redmine-status-cell' data-bug-id='" . htmlspecialchars($bug['bug_id']) . "'>
                    <span class='status-loading'>Loading...</span>
                </td>
                <td>" . htmlspecialchars($bug['testcase_name']) . "</td>
                <td class='{$latestStatusClass}'>" . $latestStatusDisplay . "</td>
                <td class='{$laterPassedClass}'>" . $laterPassedDisplay . "</td>
                <td>" . htmlspecialchars($bug['build_name']) . "</td>
                <td>" . htmlspecialchars($bug['execution_ts']) . "</td>
                <td><span class='toggle-details' onclick='toggleDetails(" . $bug['execution_id'] . ")'>Show/Hide</span></td>
              </tr>
              <tr id='details_" . $bug['execution_id'] . "' class='details-row'>
                <td colspan='8'>
                    <div><b>Test Plan:</b> " . htmlspecialchars($bug['testplan_name']) . "</div>
                    <div><b>Tester:</b> " . htmlspecialchars($bug['tester_login']) . "</div>
                    <div class='redmine-details' data-bug-id='" . htmlspecialchars($bug['bug_id']) . "'>
                        <!-- Redmine details will be loaded via AJAX -->
                    </div>";
                    
        // Add latest execution details if available
        if ($latestExecution) {
            echo "<div><b>Latest Execution Date:</b> " . htmlspecialchars($latestExecution['execution_ts']) . "</div>
                  <div><b>Latest Execution Build:</b> " . htmlspecialchars($latestExecution['build_name']) . "</div>";
        }
        
        // Add later passed details if available
        if ($laterPassed['passed']) {
            echo "<div><b>Later Passed On:</b> " . htmlspecialchars($laterPassed['date']) . "</div>
                  <div><b>Passed In Build:</b> " . htmlspecialchars($laterPassed['build']) . "</div>";
        }
        if (!empty($bug['execution_notes'])) {
            echo "<div><b>Execution Notes:</b> " . nl2br(htmlspecialchars($bug['execution_notes'])) . "</div>";
        }
        
        echo "</td></tr>";
    }
    
    echo "</table>
          </div>
          
          <!-- Pagination Controls -->
          <div class='pagination-container'>
              <div class='pagination-info'>
                  <span id='pagination-info'>Showing <span id='current-range'></span> of <span id='total-bugs'>" . $bugCount . "</span> bugs</span>
              </div>
              <div class='pagination-controls'>
                  <button id='prev-page' onclick='changePage(-1)'>« Previous</button>
                  <span id='page-numbers'></span>
                  <button id='next-page' onclick='changePage(1)'>Next »</button>
              </div>
              <div class='page-size-control'>
                  <label>Show: 
                      <select id='page-size' onchange='changePageSize()'>
                          <option value='25'>25</option>
                          <option value='50' selected>50</option>
                          <option value='100'>100</option>
                      </select>
                  </label>
              </div>
          </div>
        </div>
    ";
    
    ExecBugLogger::log("HTML table generation completed with " . $bugCount . " bugs displayed");
} else {
    echo "<div class='no-bugs'>No bugs found matching the search criteria.</div>";
    ExecBugLogger::log("No bugs found matching search criteria");
}

echo "
<script src='bugs_view_pagination.js'></script>
<script>
// Initialize pagination variables for server-side pagination
let totalBugs = " . $totalBugs . ";
let currentPage = " . $currentPage . ";
let pageSize = " . $pageSize . ";
let allRows = [];
let filteredRows = [];
let redmineStatusCache = {};

// Initial CSRF values for AJAX
window.CSRFNameAjax = '" . addslashes($csrf_ajax_name) . "';
window.CSRFTokenAjax = '" . addslashes($csrf_ajax_token) . "';

$(document).ready(function() {
    console.log('🚀 Initializing bugs view with server-side pagination');
    console.log('📊 Total bugs:', totalBugs, 'Current page:', currentPage, 'Page size:', pageSize);
    
    // Set the correct page size in the dropdown
    $('#page-size').val(pageSize);
    
    // Initialize pagination (server-side)
    initializePagination();
    
    // Load Redmine status for visible rows
    loadRedmineStatusesForVisibleBugs();
    
    // Setup search functionality
    setupSearch();
});


// All JavaScript functions are now in bugs_view_pagination.js
</script>

</body>
</html>";

ExecBugLogger::log("Script execution completed successfully");

// Close database connection
$db->close();
ExecBugLogger::log("Database connection closed");
ExecBugLogger::log("=== bugs_view.php execution completed ===");

/**
 * Get bugs based on search criteria with pagination
 */
function getBugs(&$dbHandler, $argsObj, $page = 1, $pageSize = 50) {
    ExecBugLogger::log("Building SQL query for bugs retrieval");
    
    $sql = "SELECT 
                EB.execution_id, 
                EB.bug_id,
                E.status as execution_status,
                E.execution_ts,
                E.build_id,
                E.testplan_id,
                NH_TC.name AS testcase_name,
                B.name AS build_name,
                NH_TPL.name AS testplan_name,
                U.login AS tester_login,
                E.notes AS execution_notes
            FROM execution_bugs EB
            JOIN executions E ON EB.execution_id = E.id
            JOIN tcversions TCV ON E.tcversion_id = TCV.id
            JOIN nodes_hierarchy NH_TCV ON TCV.id = NH_TCV.id
            JOIN nodes_hierarchy NH_TC ON NH_TCV.parent_id = NH_TC.id
            LEFT JOIN builds B ON E.build_id = B.id
            LEFT JOIN testplans TPL ON E.testplan_id = TPL.id
            LEFT JOIN nodes_hierarchy NH_TPL ON TPL.id = NH_TPL.id
            LEFT JOIN users U ON E.tester_id = U.id
            WHERE 1=1"; // Base WHERE clause to allow AND conditions
    
    $params = array();
    ExecBugLogger::logDebug("Base SQL query prepared");
    
    if (!empty($argsObj->bug_id)) {
        $sql .= " AND EB.bug_id LIKE ?";
        $params[] = '%' . $argsObj->bug_id . '%';
    }
    
    if (!empty($argsObj->testcase_name)) {
        $sql .= " AND NH_TC.name LIKE ?";
        $params[] = '%' . $argsObj->testcase_name . '%';
    }
    
    if (!empty($argsObj->build_name)) {
        $sql .= " AND B.name LIKE ?";
        $params[] = '%' . $argsObj->build_name . '%';
    }
    
    if (!empty($argsObj->dateFrom)) {
        $sql .= " AND E.execution_ts >= ?";
        $params[] = $argsObj->dateFrom . ' 00:00:00';
    }
    
    if (!empty($argsObj->dateTo)) {
        $sql .= " AND E.execution_ts <= ?";
        $params[] = $argsObj->dateTo . ' 23:59:59';
    }
    
    $sql .= " ORDER BY E.execution_ts DESC";
    
    // Add pagination
    $offset = ($page - 1) * $pageSize;
    $sql .= " LIMIT ? OFFSET ?";
    $params[] = $pageSize;
    $params[] = $offset;
    
    ExecBugLogger::log("Executing paginated query (page $page, size $pageSize) with " . count($params) . " parameters");
    $result = $dbHandler->exec_query($sql, $params);
    
    if ($result) {
        $bugs = $dbHandler->fetch_array($result);
        ExecBugLogger::log("Successfully retrieved " . count($bugs) . " bugs from database");
        return $bugs;
    }
    
    ExecBugLogger::logError("Failed to retrieve bugs from database");
    return array();
}

/**
 * Get total count of bugs based on search criteria (for pagination)
 */
function getBugCount(&$dbHandler, $argsObj) {
    ExecBugLogger::log("Building SQL query for bug count");
    
    $sql = "SELECT COUNT(*) as total
            FROM execution_bugs EB
            JOIN executions E ON EB.execution_id = E.id
            JOIN tcversions TCV ON E.tcversion_id = TCV.id
            JOIN nodes_hierarchy NH_TCV ON TCV.id = NH_TCV.id
            JOIN nodes_hierarchy NH_TC ON NH_TCV.parent_id = NH_TC.id
            LEFT JOIN builds B ON E.build_id = B.id
            LEFT JOIN testplans TPL ON E.testplan_id = TPL.id
            LEFT JOIN nodes_hierarchy NH_TPL ON TPL.id = NH_TPL.id
            LEFT JOIN users U ON E.tester_id = U.id
            WHERE 1=1";
    
    $params = array();
    
    if (!empty($argsObj->bug_id)) {
        $sql .= " AND EB.bug_id LIKE ?";
        $params[] = '%' . $argsObj->bug_id . '%';
    }
    
    if (!empty($argsObj->testcase_name)) {
        $sql .= " AND NH_TC.name LIKE ?";
        $params[] = '%' . $argsObj->testcase_name . '%';
    }
    
    if (!empty($argsObj->build_name)) {
        $sql .= " AND B.name LIKE ?";
        $params[] = '%' . $argsObj->build_name . '%';
    }
    
    if (!empty($argsObj->dateFrom)) {
        $sql .= " AND E.execution_ts >= ?";
        $params[] = $argsObj->dateFrom . ' 00:00:00';
    }
    
    if (!empty($argsObj->dateTo)) {
        $sql .= " AND E.execution_ts <= ?";
        $params[] = $argsObj->dateTo . ' 23:59:59';
    }
    
    ExecBugLogger::log("Executing count query with " . count($params) . " parameters");
    $result = $dbHandler->exec_query($sql, $params);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $total = $row['total'];
        ExecBugLogger::log("Total bugs matching criteria: " . $total);
        return $total;
    }
    
    ExecBugLogger::logError("Failed to get bug count from database");
    return 0;
}

/**
 * Initialize user input
 */
function init_args(&$dbHandler) {
    ExecBugLogger::log("Initializing search arguments from request");
    $args = new stdClass();
    
    $args->bug_id = isset($_REQUEST['bug_id']) ? trim($_REQUEST['bug_id']) : '';
    $args->testcase_name = isset($_REQUEST['testcase_name']) ? trim($_REQUEST['testcase_name']) : '';
    $args->build_name = isset($_REQUEST['build_name']) ? trim($_REQUEST['build_name']) : '';
    $args->dateFrom = isset($_REQUEST['dateFrom']) ? trim($_REQUEST['dateFrom']) : '';
    $args->dateTo = isset($_REQUEST['dateTo']) ? trim($_REQUEST['dateTo']) : '';
    
    ExecBugLogger::log("Search criteria - Bug ID: '" . $args->bug_id . "', Test Case: '" . $args->testcase_name . "', Build: '" . $args->build_name . "', Date From: '" . $args->dateFrom . "', Date To: '" . $args->dateTo . "'");
    
    return $args;
}

/**
 * Checks the user rights for viewing the page
 */
function checkRights(&$db, &$user) {
    return $user->hasRight($db, 'testplan_execute') || $user->hasRight($db, 'testplan_metrics');
}