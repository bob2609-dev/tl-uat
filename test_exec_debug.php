<?php
/**
 * Debug script to test execSetResults initialization
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Testing execSetResults Initialization ===\n";

try {
    // Initialize TestLink environment using the same pattern as the working API
    define('NOCRYPT', true);
    require_once(dirname(__FILE__) . '/config.inc.php');
    require_once(dirname(__FILE__) . '/lib/functions/common.php');
    
    // Use the proven database initialization pattern from redmine_status_api.php
    $db = new database(DB_TYPE);
    doDBConnect($db, database::ONERROREXIT);
    
    echo "✅ Database initialized successfully\n";
    
    // Test database connection
    $result = $db->exec_query("SELECT 1");
    if ($result) {
        echo "✅ Database query works\n";
    } else {
        echo "❌ Database query failed\n";
    }
    
    // Test the database object type
    echo "Database type: " . gettype($db) . "\n";
    echo "Database class: " . get_class($db) . "\n";
    
    // Test creating integration database object
    $integrationDB = new database(DB_TYPE);
    doDBConnect($integrationDB, database::ONERROREXIT);
    
    echo "✅ Integration database initialized successfully\n";
    echo "Integration DB type: " . gettype($integrationDB) . "\n";
    echo "Integration DB class: " . get_class($integrationDB) . "\n";
    
    // Test the integration function
    require_once(dirname(__FILE__) . '/lib/execute/custom_issue_integration_safe.php');
    
    $integrations = getIntegrationsForProject($integrationDB, 242099);
    echo "✅ Integration function works, found " . count($integrations) . " integrations\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "=== Test Complete ===\n";
?>
