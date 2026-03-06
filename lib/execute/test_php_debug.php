<?php
// Simple PHP test to isolate the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>PHP Debug Test</h3>";

// Test basic PHP functionality
echo "<p>PHP is working</p>";

// Test file includes
try {
    require_once dirname(__FILE__) . '/../../config.inc.php';
    echo "<p style='color: green;'>config.inc.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>config.inc.php failed: " . $e->getMessage() . "</p>";
}

try {
    require_once dirname(__FILE__) . '/../../common.php';
    echo "<p style='color: green;'>common.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>common.php failed: " . $e->getMessage() . "</p>";
}

// Test database connection
try {
    // Use TestLink's standard database initialization
    $db = new database();
    echo "<p style='color: green;'>Database object created</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Database creation failed: " . $e->getMessage() . "</p>";
}

// Test parameters
$projectId = 242099;

// Test simple query
if (isset($db)) {
    try {
        $simpleTest = $db->exec_query("SELECT 1 as test");
        if ($simpleTest) {
            echo "<p style='color: green;'>Basic query successful</p>";
        } else {
            echo "<p style='color: red;'>Basic query failed</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Query exception: " . $e->getMessage() . "</p>";
    }
    
    // Test view existence
    try {
        $viewCheck = $db->exec_query("SHOW TABLES LIKE 'vw_tester_execution_report'");
        if ($viewCheck && $db->num_rows($viewCheck) > 0) {
            echo "<p style='color: green;'>View 'vw_tester_execution_report' exists</p>";
        } else {
            echo "<p style='color: red;'>View 'vw_tester_execution_report' does not exist</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>View check failed: " . $e->getMessage() . "</p>";
    }
}

echo "<h3>Debug Complete</h3>";
?>
