<?php
/**
 * AJAX endpoint to save integration data
 */
require_once('../../config.inc.php');
require_once('common.php');

header('Content-Type: application/json');

// Get integration ID
$integrationId = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($integrationId <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid integration ID'
    ]);
    exit;
}

try {
    // Initialize database
    $db = new database(DB_TYPE);
    doDBConnect($db, database::ONERROREXIT);
    
    // Get form data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : 'redmine';
    $apiEndpoint = isset($_POST['api_endpoint']) ? trim($_POST['api_endpoint']) : '';
    $apiKey = isset($_POST['api_key']) ? trim($_POST['api_key']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $projectKey = isset($_POST['project_key']) ? trim($_POST['project_key']) : '';
    
    // Validate required fields
    if (empty($name) || empty($apiEndpoint) || empty($apiKey)) {
        echo json_encode([
            'success' => false,
            'error' => 'Name, API Endpoint, and API Key are required'
        ]);
        exit;
    }
    
    // Update integration
    $sql = "UPDATE custom_bugtrack_integrations SET 
                name = '" . $db->prepare_string($name) . "',
                type = '" . $db->prepare_string($type) . "',
                api_endpoint = '" . $db->prepare_string($apiEndpoint) . "',
                api_key = '" . $db->prepare_string($apiKey) . "',
                username = '" . $db->prepare_string($username) . "',
                password = '" . $db->prepare_string($password) . "',
                project_key = '" . $db->prepare_string($projectKey) . "',
                updated_by = '" . $db->prepare_string($_SESSION['userID']) . "',
                updated_on = NOW()
            WHERE id = $integrationId";
    
    $result = $db->exec_query($sql);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Integration updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update integration: ' . $db->error_msg()
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ]);
}
?>
