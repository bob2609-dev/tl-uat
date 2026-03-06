<?php
// Debug script to test the corrected view and identify SQL errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Corrected View Debug Test</h3>";

try {
    // Direct database connection
    $conn = new mysqli('localhost', 'tl_uat', 'tl_uat269', 'tl_uat');
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    // Test 1: Check if corrected view exists
    echo "<h4>Test 1: Check if vw_tester_execution_report_corrected exists</h4>";
    $result = $conn->query("SHOW TABLES LIKE 'vw_tester_execution_report_corrected'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✅ Corrected view exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Corrected view does not exist - need to create it first</p>";
        
        // Test if original view exists
        $result = $conn->query("SHOW TABLES LIKE 'vw_tester_execution_report'");
        if ($result->num_rows > 0) {
            echo "<p style='color: orange;'>⚠️ Original view exists - should create corrected view</p>";
        }
        exit;
    }
    
    // Test 2: Test basic query on corrected view
    echo "<h4>Test 2: Basic query on corrected view</h4>";
    $sql = "SELECT COUNT(*) as count FROM vw_tester_execution_report_corrected";
    echo "<p>SQL: " . $sql . "</p>";
    
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p style='color: green;'>✅ Basic query successful: " . $row['count'] . " records</p>";
    } else {
        echo "<p style='color: red;'>❌ Basic query failed: " . $conn->error . "</p>";
        exit;
    }
    
    // Test 3: Test query similar to PHP report
    echo "<h4>Test 3: PHP-style query on corrected view</h4>";
    $projectId = 242099;
    $sql = "SELECT tester_id, tester_name, total_assigned, passed, failed, blocked, not_run 
            FROM vw_tester_execution_report_corrected 
            WHERE testproject_id = " . intval($projectId) . " 
            LIMIT 5";
    echo "<p>SQL: " . $sql . "</p>";
    
    $result = $conn->query($sql);
    if ($result) {
        echo "<p style='color: green;'>✅ PHP-style query successful</p>";
        echo "<table border='1'><tr><th>Tester</th><th>Assigned</th><th>Passed</th><th>Failed</th><th>Blocked</th><th>Not Run</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['tester_name'] . "</td>";
            echo "<td>" . $row['total_assigned'] . "</td>";
            echo "<td>" . $row['passed'] . "</td>";
            echo "<td>" . $row['failed'] . "</td>";
            echo "<td>" . $row['blocked'] . "</td>";
            echo "<td>" . $row['not_run'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ PHP-style query failed: " . $conn->error . "</p>";
    }
    
    // Test 4: Test UNION query like PHP generates
    echo "<h4>Test 4: UNION query like PHP</h4>";
    $sql = "
        SELECT 
            tester_id,
            tester_name,
            total_assigned,
            passed,
            failed,
            blocked,
            not_run,
            last_execution
        FROM vw_tester_execution_report_corrected 
        WHERE testproject_id = " . intval($projectId) . "
        LIMIT 3
        
        UNION ALL
        
        SELECT 
            NULL AS tester_id,
            'TOTAL' AS tester_name,
            SUM(total_assigned) AS total_assigned,
            SUM(passed) AS passed,
            SUM(failed) AS failed,
            SUM(blocked) AS blocked,
            SUM(not_run) AS not_run,
            MAX(last_execution) AS last_execution
        FROM vw_tester_execution_report_corrected 
        WHERE testproject_id = " . intval($projectId);
    
    echo "<p>SQL: " . htmlspecialchars($sql) . "</p>";
    
    $result = $conn->query($sql);
    if ($result) {
        echo "<p style='color: green;'>✅ UNION query successful</p>";
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
    } else {
        echo "<p style='color: red;'>❌ UNION query failed: " . $conn->error . "</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
