<?php
// Debug script to test date filtering issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Date Filter Debug Test</h3>";

try {
    // Direct database connection
    $conn = new mysqli('localhost', 'tl_uat', 'tl_uat269', 'tl_uat');
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    // Test 1: Check if view has data
    echo "<h4>Test 1: View Data Check</h4>";
    $result = $conn->query("SELECT COUNT(*) as count FROM vw_tester_execution_report WHERE testproject_id = 242099");
    $row = $result->fetch_assoc();
    echo "<p>Total records in view: " . $row['count'] . "</p>";
    
    // Test 2: Check last_execution dates
    echo "<h4>Test 2: Last Execution Dates</h4>";
    $result = $conn->query("SELECT last_execution FROM vw_tester_execution_report WHERE testproject_id = 242099 AND last_execution IS NOT NULL LIMIT 5");
    echo "<table border='1'><tr><th>Last Execution Date</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['last_execution'] . "</td></tr>";
    }
    echo "</table>";
    
    // Test 3: Test with specific date range
    echo "<h4>Test 3: Date Range Filter (2024)</h4>";
    $startDate = '2024-01-01';
    $endDate = '2024-12-31';
    
    $sql = "SELECT COUNT(*) as count 
            FROM vw_tester_execution_report 
            WHERE testproject_id = 242099 
            AND last_execution >= '$startDate 00:00:00' 
            AND last_execution <= '$endDate 23:59:59'";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p>Records with 2024 date filter ($startDate to $endDate): " . $row['count'] . "</p>";
    
    // Test 3b: Test with 2025 dates
    echo "<h4>Test 3b: Date Range Filter (2025)</h4>";
    $startDate2025 = '2025-01-01';
    $endDate2025 = '2025-12-31';
    
    $sql = "SELECT COUNT(*) as count 
            FROM vw_tester_execution_report 
            WHERE testproject_id = 242099 
            AND last_execution >= '$startDate2025 00:00:00' 
            AND last_execution <= '$endDate2025 23:59:59'";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p>Records with 2025 date filter ($startDate2025 to $endDate2025): " . $row['count'] . "</p>";
    
    // Test 3c: Test with today's date
    echo "<h4>Test 3c: Today's Date Filter</h4>";
    $today = date('Y-m-d');
    echo "<p>Today's date: $today</p>";
    $sql = "SELECT COUNT(*) as count 
            FROM vw_tester_execution_report 
            WHERE testproject_id = 242099 
            AND last_execution >= '$today 00:00:00' 
            AND last_execution <= '$today 23:59:59'";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p>Records with today's filter ($today): " . $row['count'] . "</p>";
    
    // Test 3d: Test with 2026 dates
    echo "<h4>Test 3d: 2026 Date Range Filter</h4>";
    $startDate2026 = '2026-01-01';
    $endDate2026 = '2026-12-31';
    
    $sql = "SELECT COUNT(*) as count 
            FROM vw_tester_execution_report 
            WHERE testproject_id = 242099 
            AND last_execution >= '$startDate2026 00:00:00' 
            AND last_execution <= '$endDate2026 23:59:59'";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p>Records with 2026 date filter ($startDate2026 to $endDate2026): " . $row['count'] . "</p>";
    
    // Test 3e: Check NULL last_execution
    echo "<h4>Test 3e: NULL Last Execution Check</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM vw_tester_execution_report 
            WHERE testproject_id = 242099 
            AND last_execution IS NULL";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p>Records with NULL last_execution: " . $row['count'] . "</p>";
    
    // Test 4: Check raw executions table
    echo "<h4>Test 4: Raw Executions Table</h4>";
    $result = $conn->query("SELECT COUNT(*) as count FROM executions WHERE execution_ts >= '$startDate 00:00:00' AND execution_ts <= '$endDate 23:59:59'");
    $row = $result->fetch_assoc();
    echo "<p>Raw executions in date range: " . $row['count'] . "</p>";
    
    // Test 5: Check date format
    echo "<h4>Test 5: Date Format Check</h4>";
    $result = $conn->query("SELECT execution_ts FROM executions ORDER BY execution_ts DESC LIMIT 3");
    echo "<table border='1'><tr><th>Execution Timestamp</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['execution_ts'] . "</td></tr>";
    }
    echo "</table>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
