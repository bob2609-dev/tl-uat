<?php
/**
 * Simple Query Logger - No Infinite Loop
 * Captures SQL queries without recursive logging
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$log_file = __DIR__ . '/simple_query_log.txt';
$performance_log = __DIR__ . '/simple_performance_log.txt';

// Global flag to prevent recursive logging
$GLOBALS['query_logger_active'] = false;

function logQuerySimple($query, $time, $caller = 'unknown') {
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

// Simple database wrapper
class SimpleLoggedDatabase {
    private $db;
    
    public function __construct($original_db) {
        $this->db = $original_db;
    }
    
    public function exec_query($query, $params = null) {
        $start = microtime(true);
        $result = $this->db->exec_query($query, $params);
        $time = microtime(true) - $start;
        
        // Log only if not a logger query
        if (strpos($query, 'INSERT INTO transactions') === false && 
            strpos($query, 'INSERT INTO events') === false) {
            logQuerySimple($query, $time, 'SQL');
        }
        
        return $result;
    }
    
    public function fetchRowsIntoMap($query, $key_field, $params = null) {
        $start = microtime(true);
        $result = $this->db->fetchRowsIntoMap($query, $key_field, $params);
        $time = microtime(true) - $start;
        
        if (strpos($query, 'transactions') === false && strpos($query, 'events') === false) {
            logQuerySimple($query, $time, 'SQL');
        }
        
        return $result;
    }
    
    public function fetchRowsIntoMapAddRC($query, $key_field, $params = null) {
        $start = microtime(true);
        $result = $this->db->fetchRowsIntoMapAddRC($query, $key_field, $params);
        $time = microtime(true) - $start;
        
        if (strpos($query, 'transactions') === false && strpos($query, 'events') === false) {
            logQuerySimple($query, $time, 'SQL');
        }
        
        return $result;
    }
    
    public function get_recordset($query, $params = null) {
        $start = microtime(true);
        $result = $this->db->get_recordset($query, $params);
        $time = microtime(true) - $start;
        
        if (strpos($query, 'transactions') === false && strpos($query, 'events') === false) {
            logQuerySimple($query, $time, 'SQL');
        }
        
        return $result;
    }
    
    // Pass through all other methods
    public function __call($method, $args) {
        return call_user_func_array(array($this->db, $method), $args);
    }
    
    public function __get($property) {
        return $this->db->$property;
    }
    
    public function __set($property, $value) {
        return $this->db->$property = $value;
    }
}

// Initialize logging
echo "<!-- Simple Query Logger Initialized -->\n";
echo "<!-- Log File: {$log_file} -->\n";

// Clear logs for fresh start
file_put_contents($log_file, "# Simple Query Log - Started at " . date('Y-m-d H:i:s') . "\n\n");
file_put_contents($performance_log, "# Performance Log - Started at " . date('Y-m-d H:i:s') . "\n\n");

?>
