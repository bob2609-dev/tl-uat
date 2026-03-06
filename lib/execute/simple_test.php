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
    
    // Test basic query
    $sql = "SELECT COUNT(*) as count FROM custom_bugtrack_integrations";
    $result = $db->exec_query($sql);
    $row = $db->fetch_array($result);
    
    $response = array(
        'success' => true,
        'message' => 'Simple test completed',
        'data' => array(
            'db_connected' => !is_null($db),
            'table_count' => intval($row['count'] ?? 0),
            'sql_executed' => true
        )
    );
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ));
}
?>
