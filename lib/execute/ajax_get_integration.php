<?php
/**
 * AJAX endpoint to get integration data for editing
 */
require_once('../../config.inc.php');
require_once('common.php');

header('Content-Type: application/json');

// Get integration ID
$integrationId = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
    
    // Get integration data
    $sql = "SELECT i.* FROM custom_bugtrack_integrations i 
            WHERE i.id = $integrationId AND i.is_active = 1";
    
    $result = $db->exec_query($sql);
    
    if ($result) {
        $integration = $db->fetch_array($result);
        
        if ($integration) {
            echo json_encode([
                'success' => true,
                'integration' => $integration
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Integration not found'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $db->error_msg()
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Exception: ' . $e->getMessage()
    ]);
}
?>
