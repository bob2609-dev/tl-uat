<?php
// Ultra-simple debug test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Basic PHP Test</h3>";
echo "<p>✅ PHP is working</p>";

// Test basic database connection without TestLink includes
echo "<h3>Testing Direct Database Connection</h3>";

try {
    // Try direct MySQL connection with correct credentials
    $conn = new mysqli('localhost', 'tl_uat', 'tl_uat269', 'tl_uat');
    
    if ($conn->connect_error) {
        echo "<p style='color: red;'>Direct MySQL connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Direct MySQL connection successful</p>";
        
        // Test if view exists
        $result = $conn->query("SHOW TABLES LIKE 'vw_tester_execution_report'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ View 'vw_tester_execution_report' exists</p>";
            
            // Test the view query
            $testQuery = $conn->query("SELECT COUNT(*) as count FROM vw_tester_execution_report WHERE testproject_id = 242099");
            if ($testQuery) {
                $row = $testQuery->fetch_assoc();
                echo "<p style='color: green;'>✅ View query successful: " . $row['count'] . " records found</p>";
            } else {
                echo "<p style='color: red;'>❌ View query failed: " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ View 'vw_tester_execution_report' does not exist</p>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<h3>Test Complete</h3>";
?>
