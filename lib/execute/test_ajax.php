<?php
/**
 * Simple test script for AJAX endpoint
 */
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Set content type to JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Initialize database using TestLink's standard way
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];

// Check permissions
if (!is_object($currentUser) || !$currentUser->hasRight($db, 'testplan_metrics')) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

// Test database connection
try {
    $result = $db->exec_query('SELECT 1 as test');
    if ($result) {
        $row = $db->fetch_array($result);
        echo json_encode([
            'success' => true, 
            'message' => 'Database connection working',
            'test_result' => $row['test'],
            'user' => $currentUser->login,
            'has_right' => $currentUser->hasRight($db, 'testplan_metrics')
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database query failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
}
?>
