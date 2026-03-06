<?php
/**
 * Most basic test to isolate the 500 error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

echo "=== Basic PHP Test ===\n\n";

try {
    echo "✓ PHP is working\n";
    echo "✓ No syntax errors\n";
    
    // Use TestLink's standard database initialization
    define('NOCRYPT', true);
    require_once('../../config.inc.php');
    require_once('../functions/common.php');
    
    testlinkInitPage($db, false, false, null);
    
    if ($db) {
        echo "✓ Database object created\n";
    } else {
        echo "✗ Database connection failed\n";
    }
    
    echo "✓ Test completed successfully\n";
    
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
    echo "✗ Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
