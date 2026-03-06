<?php
/**
 * Simple integration test to isolate the exact error
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

// Set headers for JSON response
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Simple Integration Test ===\n";

try {
    // Use the exact same initialization as the main integrator
    define('NOCRYPT', true);
    require_once($_SERVER['DOCUMENT_ROOT'] . '/config.inc.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/functions/common.php');
    
    testlinkInitPage($db, false, false, "checkRights");
    
    echo "Database initialized successfully\n";
    
    // Get parameters
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    $tproject_id = isset($_REQUEST['tproject_id']) ? intval($_REQUEST['tproject_id']) : 0;
    
    echo "Action: $action\n";
    echo "Project ID: $tproject_id\n";
    
    // Test the specific case we need
    if ($action == 'list_integrations_for_project' && $tproject_id > 0) {
        echo "Testing list_integrations_for_project...\n";
        
        // Create a simple response array
        $response = array(
            'success' => true,
            'message' => 'Test successful',
            'data' => array(
                array('id' => 7, 'name' => 'Redmine1', 'type' => 'REDMINE')
            ),
            'debug' => array('Simple test completed')
        );
        
        echo "Response: " . json_encode($response) . "\n";
        
    } else {
        echo "Invalid request\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
?>
