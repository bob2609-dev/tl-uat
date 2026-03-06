<?php
/**
 * Minimal test to isolate the issue
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

echo "=== Minimal API Test ===\n";

// Test database initialization only
try {
    define('NOCRYPT', true);
    require_once(dirname(__FILE__) . '/../../config.inc.php');
    require_once(dirname(__FILE__) . '/../functions/common.php');
    
    testlinkInitPage($db, false, false, "checkRights");
    
    echo "Database initialized successfully\n";
    
    // Test a simple query
    $result = $db->exec_query("SELECT 1");
    if ($result) {
        echo "Database query works\n";
    } else {
        echo "Database query failed\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

// Test including the integration functions
try {
    require_once(dirname(__FILE__) . '/custom_issue_integration_safe.php');
    echo "custom_issue_integration_safe.php included successfully\n";
    
    // Test calling the function
    $integrations = getIntegrationsForProject($db, 242099);
    echo "getIntegrationsForProject returned: " . print_r($integrations, true) . "\n";
    
} catch (Exception $e) {
    echo "ERROR in integration functions: " . $e->getMessage() . "\n";
}
?>
