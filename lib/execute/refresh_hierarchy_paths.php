<?php
/**
 * Script to refresh the node hierarchy paths table
 * This is called via AJAX from the test execution summary page
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once("../../config.inc.php");
require_once('common.php');
require_once('users.inc.php');

// Set appropriate headers for AJAX response
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Initialize session and check permissions
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];
$result = array('success' => false, 'message' => '');

// Check if user has proper rights (require admin permissions)
$hasRights = $currentUser->hasRight($db, 'testplan_metrics') && 
             $currentUser->hasRight($db, 'mgt_modify_tc');

if (!$hasRights) {
    $result['message'] = 'Insufficient permissions to refresh hierarchy paths.';
    echo json_encode($result);
    exit();
}

// Attempt to run the stored procedure
try {
    $refreshQuery = "CALL refresh_node_hierarchy_paths_v2()";
    
    // Execute query and handle both object and resource responses (different PHP/DB versions)
    $refreshResult = $db->exec_query($refreshQuery);
    
    // Check for success using different methods depending on return type
    $success = false;
    if (is_resource($refreshResult)) {
        // Old style resource result
        $success = $refreshResult !== false;
    } elseif (is_object($refreshResult)) {
        // New style object result
        $success = true; // Assuming if it's an object, it succeeded
    } else {
        // Boolean or other type
        $success = $refreshResult ? true : false;
    }
    
    if ($success) {
        $result['success'] = true;
        $result['message'] = 'Node hierarchy paths refreshed successfully.';
    } else {
        $result['message'] = 'Error refreshing node hierarchy paths: ' . $db->error_msg();
    }
} catch (Exception $e) {
    $result['message'] = 'Exception when refreshing node hierarchy paths: ' . $e->getMessage();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
?>
