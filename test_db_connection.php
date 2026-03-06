<?php
/**
 * Test database connection directly
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

echo "=== Database Connection Test ===\n";

// Test database configuration
try {
    require_once(dirname(__FILE__) . '/config_db.inc.php');
    echo "config_db.inc.php loaded successfully\n";
    
    $db = new database(DB_TYPE);
    echo "Database object created successfully\n";
    
    // Test a simple query
    $result = $db->exec_query("SELECT 1");
    if ($result) {
        echo "Database query executed successfully\n";
    } else {
        echo "Database query failed\n";
    }
    
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>
