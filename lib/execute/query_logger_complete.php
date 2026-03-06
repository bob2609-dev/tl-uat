<?php
/**
 * Complete Query Logger - Full Method Override
 * Overrides all database methods to capture queries without interference
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$log_file = __DIR__ . '/complete_query_log.txt';
$performance_log = __DIR__ . '/complete_performance_log.txt';

// Global flag to prevent recursive logging
$GLOBALS['query_logger_active'] = false;

function logQueryComplete($query, $time, $caller = 'unknown') {
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

// Complete database wrapper with all methods
class CompleteLoggedDatabase {
    private $db;
    
    public function __construct($original_db) {
        $this->db = $original_db;
    }
    
    // Override all key database methods
    public function exec_query($p_query, $p_limit = -1, $p_offset = -1) {
        // Skip logging queries
        if (strpos($p_query, 'INSERT INTO transactions') !== false || 
            strpos($p_query, 'INSERT INTO events') !== false ||
            strpos($p_query, 'UPDATE events') !== false) {
            return $this->db->exec_query($p_query, $p_limit, $p_offset);
        }
        
        $start = microtime(true);
        $result = $this->db->exec_query($p_query, $p_limit, $p_offset);
        $time = microtime(true) - $start;
        
        logQueryComplete($p_query, $time, 'SQL');
        
        return $result;
    }
    
    public function fetchRowsIntoMap($sql, $column, $cumulative = 0, $limit = -1, $col2implode = '') {
        if (strpos($sql, 'transactions') !== false || strpos($sql, 'events') !== false) {
            return $this->db->fetchRowsIntoMap($sql, $column, $cumulative, $limit, $col2implode);
        }
        
        $start = microtime(true);
        $result = $this->db->fetchRowsIntoMap($sql, $column, $cumulative, $limit, $col2implode);
        $time = microtime(true) - $start;
        
        logQueryComplete($sql, $time, 'SQL');
        
        return $result;
    }
    
    public function fetchRowsIntoMapAddRC($sql, $column, $cumulative = 0) {
        if (strpos($sql, 'transactions') !== false || strpos($sql, 'events') !== false) {
            return $this->db->fetchRowsIntoMapAddRC($sql, $column, $cumulative);
        }
        
        $start = microtime(true);
        $result = $this->db->fetchRowsIntoMapAddRC($sql, $column, $cumulative);
        $time = microtime(true) - $start;
        
        logQueryComplete($sql, $time, 'SQL');
        
        return $result;
    }
    
    public function get_recordset($sql, $fetch_mode = null, $limit = -1, $start = -1) {
        if (strpos($sql, 'transactions') !== false || strpos($sql, 'events') !== false) {
            return $this->db->get_recordset($sql, $fetch_mode, $limit, $start);
        }
        
        $start_time = microtime(true);
        $result = $this->db->get_recordset($sql, $fetch_mode, $limit, $start);
        $time = microtime(true) - $start_time;
        
        logQueryComplete($sql, $time, 'SQL');
        
        return $result;
    }
    
    public function fetchColumnsIntoMap($sql, $key_column, $value_column, $cumulative = 0, $limit = -1) {
        if (strpos($sql, 'transactions') !== false || strpos($sql, 'events') !== false) {
            return $this->db->fetchColumnsIntoMap($sql, $key_column, $value_column, $cumulative, $limit);
        }
        
        $start = microtime(true);
        $result = $this->db->fetchColumnsIntoMap($sql, $key_column, $value_column, $cumulative, $limit);
        $time = microtime(true) - $start;
        
        logQueryComplete($sql, $time, 'SQL');
        
        return $result;
    }
    
    // Magic method to handle all other method calls
    public function __call($method, $args) {
        return call_user_func_array(array($this->db, $method), $args);
    }
    
    // Magic method to handle property access
    public function __get($property) {
        return $this->db->$property;
    }
    
    public function __set($property, $value) {
        return $this->db->$property = $value;
    }
}

// Initialize logging
echo "<!-- Complete Query Logger Initialized -->\n";
echo "<!-- Log File: {$log_file} -->\n";

// Clear logs for fresh start
file_put_contents($log_file, "# Complete Query Log - Started at " . date('Y-m-d H:i:s') . "\n\n");
file_put_contents($performance_log, "# Performance Log - Started at " . date('Y-m-d H:i:s') . "\n\n");

?>
