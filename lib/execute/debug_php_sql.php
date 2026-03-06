<?php
// Debug script to test the actual SQL generation from the PHP report
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>PHP SQL Generation Debug</h3>";

// Simulate the PHP logic
$projectId = 242099;
$planId = 0;
$buildId = 0;
$testerId = 0;
$reportType = 'all';
$startDate = '2025-01-01';  // Test with 2025 dates
$endDate = '2026-12-31';    // Test with 2026 dates

echo "<h4>Test Parameters:</h4>";
echo "<p>Project ID: $projectId</p>";
echo "<p>Start Date: $startDate</p>";
echo "<p>End Date: $endDate</p>";

// Simulate the SQL generation from the PHP
if ($reportType === 'all') {
    $sql = "
        SELECT 
            ROW_NUMBER() OVER (ORDER BY tester_name) AS serial_no,
            tester_id,
            tester_name,
            total_assigned,
            total_executions,
            passed,
            failed,
            blocked,
            not_run,
            last_execution
        FROM vw_tester_execution_report 
        WHERE (testproject_id = " . intval($projectId) . " OR testproject_id IS NULL)";
    
    // Add tester filter if specified
    if ($testerId > 0) {
        $sql .= " AND tester_id = " . intval($testerId);
    }
    
    $sql .= "
        UNION ALL
        
        SELECT 
            NULL AS serial_no,
            NULL AS tester_id,
            'TOTAL' AS tester_name,
            SUM(total_assigned) AS total_assigned,
            SUM(total_executions) AS total_executions,
            SUM(passed) AS passed,
            SUM(failed) AS failed,
            SUM(blocked) AS blocked,
            SUM(not_run) AS not_run,
            MAX(last_execution) AS last_execution
        FROM vw_tester_execution_report 
        WHERE (testproject_id = " . intval($projectId) . " OR testproject_id IS NULL)";
    
    // Add tester filter if specified
    if ($testerId > 0) {
        $sql .= " AND tester_id = " . intval($testerId);
    }
}

echo "<h4>Original SQL:</h4>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

// Apply additional filters (build, date) - same logic as PHP
$whereConditions = array();

if ($planId > 0) {
    $whereConditions[] = "plan_id = " . intval($planId);
}

if ($buildId > 0) {
    $whereConditions[] = "build_id = " . intval($buildId);
}

if (!empty($startDate)) {
    $whereConditions[] = "last_execution >= '" . addslashes($startDate . ' 00:00:00') . "'";
}

if (!empty($endDate)) {
    $whereConditions[] = "last_execution <= '" . addslashes($endDate . ' 23:59:59') . "'";
}

echo "<h4>Where Conditions:</h4>";
echo "<pre>" . print_r($whereConditions, true) . "</pre>";

// Apply filters to both parts of the UNION
if (!empty($whereConditions)) {
    $additionalWhere = " AND " . implode(' AND ', $whereConditions);
    echo "<h4>Additional Where Clause:</h4>";
    echo "<pre>" . htmlspecialchars($additionalWhere) . "</pre>";
    
    $sql = str_replace("WHERE (testproject_id", "WHERE (testproject_id" . $additionalWhere, $sql);
    $sql = str_replace("WHERE testproject_id", "WHERE testproject_id" . $additionalWhere, $sql);
}

echo "<h4>Final SQL:</h4>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

// Test the SQL
try {
    $conn = new mysqli('localhost', 'tl_uat', 'tl_uat269', 'tl_uat');
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
        exit;
    }
    
    echo "<h4>SQL Execution Result:</h4>";
    $result = $conn->query($sql);
    
    if ($result) {
        $count = $result->num_rows;
        echo "<p style='color: green;'>✅ SQL executed successfully</p>";
        echo "<p>Number of results: $count</p>";
        
        if ($count > 0) {
            echo "<table border='1'><tr><th>Tester</th><th>Assigned</th><th>Passed</th><th>Failed</th><th>Blocked</th><th>Not Run</th><th>Last Execution</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['tester_name'] . "</td>";
                echo "<td>" . $row['total_assigned'] . "</td>";
                echo "<td>" . $row['passed'] . "</td>";
                echo "<td>" . $row['failed'] . "</td>";
                echo "<td>" . $row['blocked'] . "</td>";
                echo "<td>" . $row['not_run'] . "</td>";
                echo "<td>" . $row['last_execution'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>❌ SQL Error: " . $conn->error . "</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
