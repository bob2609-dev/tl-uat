<?php
/**
 * Enhanced Bugs Viewer - with manual database configuration and improved status display
 */

// Load database configuration
require_once(__DIR__ . '/config_db.inc.php');

// Define constants for SimpleDBHandler
// define('DB_TYPE', 'mysql');
// define('DB_HOST', $db_host);
// define('DB_USER', $db_user);
// define('DB_PASS', $db_pass);
// define('DB_NAME', $db_name);
// define('DB_TABLE_PREFIX', '');

// Database handler class
class SimpleDBHandler {
    private $connection;
    
    public function __construct() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }
    
    public function exec_query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            error_log("SQL Error: " . $this->connection->error);
            return false;
        }
        
        if (!empty($params)) {
            $types = str_repeat('s', count($params)); // assuming all params are strings
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
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
$db = new SimpleDBHandler();
$user = new SimpleUser();

// Check rights (simplified version)
if (!checkRights($db, $user)) {
    die("Access denied - insufficient rights");
}

$args = init_args($db);
$bugs = getBugs($db, $args);

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

// Output HTML
echo "<!DOCTYPE html>
<html>
<head>
    <title>Execution Bugs</title>
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
        .toggle-details { color: #1a73e8; text-decoration: underline; cursor: pointer; }
        .no-results { padding: 20px; text-align: center; background-color: #fff8e1; border-radius: 5px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .last-updated { font-size: 0.9em; color: #666; text-align: right; margin-bottom: 10px; }
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
            <h1>Execution Bugs</h1>
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
            </form>
        </div>
        
        <div class='count'>Bugs Found: " . count($bugs) . "</div>";

if (count($bugs) > 0) {
    echo "<div style='margin-bottom: 10px;'>
            <button id='toggleAllBtn' class='button' onclick='toggleAllDetails()'>Show All Details</button>
          </div>
          <div style='overflow-x: auto;'>
          <table>
            <tr>
                <th width='10%'>Issue ID</th>
                <th width='25%'>Test Case</th>
                <th width='15%'>Build</th>
                <th width='15%'>Execution Date</th>
                <th width='10%'>Status</th>
                <th width='10%'>Details</th>
            </tr>";
    
    foreach ($bugs as $bug) {
        $statusClass = strtolower(substr(translateStatus($bug['execution_status']), 0, 6));
        echo "<tr>
                <td><a href='https://support.profinch.com/issues/" . htmlspecialchars($bug['bug_id']) . "' target='_blank'>" . 
                     htmlspecialchars($bug['bug_id']) . "</a></td>
                <td>" . htmlspecialchars($bug['testcase_name']) . "</td>
                <td>" . htmlspecialchars($bug['build_name']) . "</td>
                <td>" . htmlspecialchars($bug['execution_ts']) . "</td>
                <td class='status-{$statusClass}'>" . translateStatus($bug['execution_status']) . "</td>
                <td><span class='toggle-details' onclick='toggleDetails(" . $bug['execution_id'] . ")'>Show/Hide</span></td>
              </tr>
              <tr id='details_" . $bug['execution_id'] . "' class='details-row'>
                <td colspan='6'>
                    <div><b>Test Plan:</b> " . htmlspecialchars($bug['testplan_name']) . "</div>
                    <div><b>Tester:</b> " . htmlspecialchars($bug['tester_login']) . "</div>";
                    
        if (!empty($bug['execution_notes'])) {
            echo "<div><b>Execution Notes:</b> " . nl2br(htmlspecialchars($bug['execution_notes'])) . "</div>";
        }
        
        echo "</td></tr>";
    }
    
    echo "</table></div>";
} else {
    echo "<div class='no-results'>
            <p>No bugs found matching your search criteria.</p>
            <p>Try adjusting your search parameters.</p>
          </div>";
}

echo "</div></body></html>";

// Close database connection
$db->close();

/**
 * Get bugs based on search criteria
 */
function getBugs(&$dbHandler, $argsObj) {
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
            
            -- WHERE E.status IN ('F', 'B')"
            ; // Only show failed and blocked by default
    
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
    
    $sql .= " ORDER BY E.execution_ts DESC";
    
    $result = $dbHandler->exec_query($sql, $params);
    
    if ($result) {
        return $dbHandler->fetch_array($result);
    }
    
    return array();
}

/**
 * Initialize user input
 */
function init_args(&$dbHandler) {
    $args = new stdClass();
    
    $args->bug_id = isset($_REQUEST['bug_id']) ? trim($_REQUEST['bug_id']) : '';
    $args->testcase_name = isset($_REQUEST['testcase_name']) ? trim($_REQUEST['testcase_name']) : '';
    $args->build_name = isset($_REQUEST['build_name']) ? trim($_REQUEST['build_name']) : '';
    $args->dateFrom = isset($_REQUEST['dateFrom']) ? trim($_REQUEST['dateFrom']) : '';
    $args->dateTo = isset($_REQUEST['dateTo']) ? trim($_REQUEST['dateTo']) : '';
    
    return $args;
}

/**
 * Checks the user rights for viewing the page
 */
function checkRights(&$db, &$user) {
    return $user->hasRight($db, 'testplan_execute') || $user->hasRight($db, 'testplan_metrics');
}