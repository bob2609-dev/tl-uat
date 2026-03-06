<?php
// Simple test to identify the error
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Starting test...\n";

try {
    // Test database connection
    require_once('config.inc.php');
    require_once('common.php');
    echo "Config loaded...\n";
    
    $db = new database(DB_TYPE);
    echo "Database object created...\n";
    
    doDBConnect($db, database::ONERROREXIT);
    echo "Database connected...\n";
    
    // Test loading custom integration step by step
    echo "About to load custom integration...\n";
    require_once('lib/execute/custom_issue_integration.php');
    echo "Custom integration loaded successfully...\n";
    
    // Test a simple function
    echo "About to test getCustomIntegrationForProject function...\n";
    $result = getCustomIntegrationForProject($db, 1); // Test with project ID 1
    echo "Function test completed, result: " . ($result ? 'found' : 'not found') . "\n";
    
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
}
?>
