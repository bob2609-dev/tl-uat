<?php
/**
 * Minimal test to isolate the exact issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    echo json_encode(array(
        'success' => true,
        'message' => 'Minimal test - PHP working',
        'step' => '1'
    ));
    exit;
    
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'step' => 'error'
    ));
}
?>
