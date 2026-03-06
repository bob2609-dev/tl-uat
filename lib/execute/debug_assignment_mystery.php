<?php
// Debug script to solve the user assignment count mystery
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>User Assignment Count Mystery</h3>";

try {
    $conn = new mysqli('localhost', 'tl_uat', 'tl_uat269', 'tl_uat');
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Connection failed: " . $conn->connect_error . "</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    $userId = 111;
    
    // Test 1: Raw user_assignments count (your query)
    echo "<h4>Test 1: Raw User Assignments</h4>";
    $sql = "SELECT au.first, au.last, au.login, count(*) 
            FROM user_assignments u 
            JOIN users au ON au.id = u.user_id 
            WHERE u.user_id = $userId 
            GROUP BY au.login";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>Raw count:</strong> " . $row['count(*)'] . " (User: " . $row['first'] . " " . $row['last'] . ")</p>";
    
    // Test 2: Check what types of assignments
    echo "<h4>Test 2: Assignment Types</h4>";
    $sql = "SELECT u.type, COUNT(*) as count 
            FROM user_assignments u 
            WHERE u.user_id = $userId 
            GROUP BY u.type";
    
    $result = $conn->query($sql);
    echo "<table border='1'><tr><th>Type</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['type'] . "</td><td>" . $row['count'] . "</td></tr>";
    }
    echo "</table>";
    
    // Test 3: View filtering (types 1,2 only)
    echo "<h4>Test 3: View Filtering (Types 1,2 only)</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM user_assignments u 
            WHERE u.user_id = $userId 
            AND u.type IN (1, 2)";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>Types 1,2 only:</strong> " . $row['count'] . "</p>";
    
    // Test 4: View filtering with status = 1
    echo "<h4>Test 4: View Filtering (Types 1,2 + Status = 1)</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM user_assignments u 
            WHERE u.user_id = $userId 
            AND u.type IN (1, 2) 
            AND u.status = 1";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>Types 1,2 + Status = 1:</strong> " . $row['count'] . "</p>";
    
    // Test 5: View filtering with testplan_tcversions join
    echo "<h4>Test 5: With testplan_tcversions Join</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM user_assignments u 
            JOIN testplan_tcversions tptc ON u.feature_id = tptc.id
            WHERE u.user_id = $userId 
            AND u.type IN (1, 2) 
            AND u.status = 1";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>With testplan_tcversions join:</strong> " . $row['count'] . "</p>";
    
    // Test 6: View filtering with testplans join
    echo "<h4>Test 6: With testplans Join</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM user_assignments u 
            JOIN testplan_tcversions tptc ON u.feature_id = tptc.id
            JOIN testplans tp ON tptc.testplan_id = tp.id
            WHERE u.user_id = $userId 
            AND u.type IN (1, 2) 
            AND u.status = 1";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>With testplans join:</strong> " . $row['count'] . "</p>";
    
    // Test 7: View filtering with project filter
    echo "<h4>Test 7: With Project Filter (242099)</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM user_assignments u 
            JOIN testplan_tcversions tptc ON u.feature_id = tptc.id
            JOIN testplans tp ON tptc.testplan_id = tp.id
            WHERE u.user_id = $userId 
            AND u.type IN (1, 2) 
            AND u.status = 1
            AND tp.testproject_id = 242099";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>With project filter:</strong> " . $row['count'] . "</p>";
    
    // Test 8: View filtering with NULL project check
    echo "<h4>Test 8: With NULL Project Check</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM user_assignments u 
            JOIN testplan_tcversions tptc ON u.feature_id = tptc.id
            JOIN testplans tp ON tptc.testplan_id = tp.id
            WHERE u.user_id = $userId 
            AND u.type IN (1, 2) 
            AND u.status = 1
            AND (tp.testproject_id = 242099 OR tp.testproject_id IS NULL)";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>With NULL project check:</strong> " . $row['count'] . "</p>";
    
    // Test 9: Actual view count for this user
    echo "<h4>Test 9: Actual View Count for User</h4>";
    $sql = "SELECT COUNT(*) as count 
            FROM vw_tester_execution_report 
            WHERE tester_id = $userId AND testproject_id = 242099";
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "<p><strong>View count:</strong> " . $row['count'] . "</p>";
    
    // Test 10: Check what projects this user has assignments in
    echo "<h4>Test 10: User's Project Distribution</h4>";
    $sql = "SELECT tp.testproject_id, COUNT(*) as count 
            FROM user_assignments u 
            JOIN testplan_tcversions tptc ON u.feature_id = tptc.id
            JOIN testplans tp ON tptc.testplan_id = tp.id
            WHERE u.user_id = $userId 
            AND u.type IN (1, 2) 
            AND u.status = 1
            GROUP BY tp.testproject_id
            ORDER BY count DESC";
    
    $result = $conn->query($sql);
    echo "<table border='1'><tr><th>Project ID</th><th>Count</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row['testproject_id'] . "</td><td>" . $row['count'] . "</td></tr>";
    }
    echo "</table>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
