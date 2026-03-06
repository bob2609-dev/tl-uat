<?php
// Simple test API to debug database connection
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: application/json; charset=UTF-8');

echo json_encode([
    'test' => 'API is working',
    'execution_id' => isset($_GET['execution_id']) ? $_GET['execution_id'] : 'not set',
    'timestamp' => date('Y-m-d H:i:s')
]);
?>
