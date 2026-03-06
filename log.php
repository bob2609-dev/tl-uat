<?php
/**
 * Log handler for client-side logs
 */

// Make sure directory exists
$log_dir = __DIR__ . '/logs';
if (!file_exists($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Define log file path
$log_file = $log_dir . '/client_js.log';

// Get the request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data && isset($data['logs']) && is_array($data['logs'])) {
    $log_entries = [];
    $timestamp = date('Y-m-d H:i:s');
    
    foreach ($data['logs'] as $log) {
        $log_entries[] = "[{$timestamp}] [CLIENT] {$log}";
    }
    
    // Write to log file
    file_put_contents(
        $log_file, 
        implode("\n", $log_entries) . "\n", 
        FILE_APPEND
    );
    
    echo json_encode(['status' => 'success', 'logged' => count($log_entries)]);
} else {
    // Log the error
    $error = [
        'timestamp' => date('Y-m-d H:i:s'),
        'error' => 'Invalid log data',
        'input' => $input
    ];
    
    file_put_contents(
        $log_file,
        json_encode($error) . "\n",
        FILE_APPEND
    );
    
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid log data']);
}
