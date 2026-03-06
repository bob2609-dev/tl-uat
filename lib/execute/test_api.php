<?php
// Simple test script to debug the API
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header
header('Content-Type: application/json');

// Initialize response
$response = array(
    'success' => false,
    'message' => '',
    'debug' => array()
);

try {
    // Test basic PHP functionality
    $response['debug'][] = 'PHP version: ' . phpversion();
    $response['debug'][] = 'Current working directory: ' . getcwd();
    
    // Test file inclusion
    if (file_exists('../../config.inc.php')) {
        require_once('../../config.inc.php');
        $response['debug'][] = 'config.inc.php loaded successfully';
    } else {
        throw new Exception('config.inc.php not found');
    }
    
    if (file_exists('../lib/functions/common.php')) {
        require_once('../lib/functions/common.php');
        $response['debug'][] = 'common.php loaded successfully';
    } else {
        throw new Exception('common.php not found');
    }
    
    // Test database connection
    $db = new database(DB_TYPE);
    doDBConnect($db, database::ONERROREXIT);
    if ($db) {
        $response['debug'][] = 'Database connection successful';
        
        // Test table existence
        $sql = "SHOW TABLES LIKE 'custom_bugtrack_integrations'";
        $result = $db->fetchFirstRow($sql);
        if ($result) {
            $response['debug'][] = 'custom_bugtrack_integrations table exists';
        } else {
            $response['debug'][] = 'custom_bugtrack_integrations table does NOT exist';
        }
        
        $sql = "SHOW TABLES LIKE 'custom_bugtrack_project_mapping'";
        $result = $db->fetchFirstRow($sql);
        if ($result) {
            $response['debug'][] = 'custom_bugtrack_project_mapping table exists';
        } else {
            $response['debug'][] = 'custom_bugtrack_project_mapping table does NOT exist';
        }
        
    } else {
        throw new Exception('Database connection failed');
    }
    
    $response['success'] = true;
    $response['message'] = 'Test completed successfully';
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    $response['debug'][] = 'Exception: ' . $e->getTraceAsString();
}

// Clear any previous output
if (ob_get_length()) {
    ob_clean();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
