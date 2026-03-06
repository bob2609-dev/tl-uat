<?php
/**
 * Query Logger - Database Replacement Approach
 * Replaces the database object directly to avoid wrapper issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$log_file = __DIR__ . '/replace_query_log.txt';
$performance_log = __DIR__ . '/replace_performance_log.txt';

// Global flag to prevent recursive logging
$GLOBALS['query_logger_active'] = false;

function logQueryReplace($query, $time, $caller = 'unknown') {
    global $log_file, $performance_log;
    
    // Prevent recursive logging
    if ($GLOBALS['query_logger_active']) {
        return;
    }
    
    $GLOBALS['query_logger_active'] = true;
    
    $timestamp = date('Y-m-d H:i:s') . '.' . substr(microtime(), 2, 3);
    $execution_time = number_format($time * 1000, 2) . 'ms';
    
    // Format log entry
    $log_entry = "[{$timestamp}] [{$execution_time}] [{$caller}] {$query}\n";
    
    // Write to logs
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    file_put_contents($performance_log, "[{$timestamp}] [{$caller}] {$execution_time}ms\n", FILE_APPEND | LOCK_EX);
    
    $GLOBALS['query_logger_active'] = false;
}

// Simple replacement function
function createLoggedDatabase($original_db) {
    // Create a new database object with logging
    $logged_db = new stdClass();
    
    // Copy all properties from original
    foreach ($original_db as $key => $value) {
        $logged_db->$key = $value;
    }
    
    // Override key methods with logging
    $logged_db->exec_query = function($query, $params = null) use ($original_db) {
        // Skip logging queries
        if (strpos($query, 'INSERT INTO transactions') !== false || 
            strpos($query, 'INSERT INTO events') !== false ||
            strpos($query, 'UPDATE events') !== false) {
            return $original_db->exec_query($query, $params);
        }
        
        $start = microtime(true);
        $result = $original_db->exec_query($query, $params);
        $time = microtime(true) - $start;
        
        logQueryReplace($query, $time, 'SQL');
        
        return $result;
    };
    
    $logged_db->fetchRowsIntoMap = function($query, $key_field, $params = null) use ($original_db) {
        if (strpos($query, 'transactions') !== false || strpos($query, 'events') !== false) {
            return $original_db->fetchRowsIntoMap($query, $key_field, $params);
        }
        
        $start = microtime(true);
        $result = $original_db->fetchRowsIntoMap($query, $key_field, $params);
        $time = microtime(true) - $start;
        
        logQueryReplace($query, $time, 'SQL');
        
        return $result;
    };
    
    $logged_db->get_recordset = function($query, $params = null) use ($original_db) {
        if (strpos($query, 'transactions') !== false || strpos($query, 'events') !== false) {
            return $original_db->get_recordset($query, $params);
        }
        
        $start = microtime(true);
        $result = $original_db->get_recordset($query, $params);
        $time = microtime(true) - $start;
        
        logQueryReplace($query, $time, 'SQL');
        
        return $result;
    };
    
    return $logged_db;
}

// Initialize logging
echo "<!-- Replace Query Logger Initialized -->\n";
echo "<!-- Log File: {$log_file} -->\n";

// Clear logs for fresh start
file_put_contents($log_file, "# Replace Query Log - Started at " . date('Y-m-d H:i:s') . "\n\n");
file_put_contents($performance_log, "# Performance Log - Started at " . date('Y-m-d H:i:s') . "\n\n");

?>
