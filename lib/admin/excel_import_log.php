<?php
/**
 * Simple logging functions for Excel Import tool
 */

/**
 * Write a message to the log file
 * @param string $message Message to log
 * @param string $level Log level (INFO, ERROR, DEBUG)
 */
function excel_import_log($message, $level = 'INFO') {
    $log_dir = dirname(__FILE__);
    $log_file = $log_dir . '/excel_import.log';
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$level] $message\n";
    
    // Append to log file
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

/**
 * Log a detailed dump of an object or array
 * @param mixed $data Data to dump
 * @param string $label Label for the data
 */
function excel_import_debug($data, $label = 'DEBUG') {
    ob_start();
    echo "===== $label =====\n";
    var_export($data);
    echo "\n===================\n";
    $debug_output = ob_get_clean();
    
    excel_import_log($debug_output, 'DEBUG');
}
