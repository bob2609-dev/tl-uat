<?php
/**
 * Test the list_projects API endpoint directly
 */

// Disable debug output that interferes with JSON
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_STRICT);

define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

// Initialize database without rights check to avoid debug output
testlinkInitPage($db, false, false, null);

// Clean any output buffers
while (ob_get_level() > 0) {
    ob_end_clean();
}
if (ob_get_length()) {
    ob_clean();
}

// Set JSON headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

try {
    $sql = "SELECT id, name, notes, is_active, active
            FROM testprojects 
            WHERE is_active = 1 
            ORDER BY name";
    
    $result = $db->exec_query($sql);
    
    if (!$result) {
        echo json_encode(array(
            'success' => false,
            'message' => 'Failed to execute projects query'
        ));
        exit;
    }
    
    $projects = array();
    
    while ($row = $db->fetch_array($result)) {
        $projects[] = array(
            'id' => intval($row['id']),
            'name' => $row['name'],
            'notes' => $row['notes'],
            'is_active' => intval($row['is_active']),
            'active' => intval($row['active'])
        );
    }
    
    echo json_encode(array(
        'success' => true,
        'data' => $projects,
        'count' => count($projects)
    ));
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Error loading projects: ' . $e->getMessage()
    ));
}
?>
