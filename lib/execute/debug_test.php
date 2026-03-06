<?php
/**
 * Detailed debug test to find exact failure point
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/plain');

echo "=== Debug Test Started ===\n\n";

try {
    echo "Step 1: PHP working\n";
    
    echo "Step 2: Loading config.inc.php...\n";
    require_once('../../config.inc.php');
    echo "✓ config.inc.php loaded\n";
    
    echo "Step 3: Loading common.php...\n";
require_once('common.php');
    echo "✓ common.php loaded\n";
    
    echo "Step 4: Calling testlinkInitPage...\n";
    testlinkInitPage($db, false, false, null);
    echo "✓ testlinkInitPage completed\n";
    
    if ($db) {
        echo "✓ Database object created\n";
    } else {
        echo "✗ Database object is null\n";
        exit;
    }
    
    echo "Step 5: Testing simple query...\n";
    $sql = "SELECT 1 as test";
    $result = $db->exec_query($sql);
    echo "✓ Query executed\n";
    
    echo "Step 6: Fetching result...\n";
    $row = $db->fetch_array($result);
    echo "✓ Result fetched: " . print_r($row, true) . "\n";
    
    echo "Step 7: All tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Exception caught: " . $e->getMessage() . "\n";
    echo "✗ File: " . $e->getFile() . "\n";
    echo "✗ Line: " . $e->getLine() . "\n";
    echo "✗ Stack trace: " . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "✗ Error caught: " . $e->getMessage() . "\n";
    echo "✗ File: " . $e->getFile() . "\n";
    echo "✗ Line: " . $e->getLine() . "\n";
    echo "✗ Stack trace: " . $e->getTraceAsString() . "\n";
}
?>
