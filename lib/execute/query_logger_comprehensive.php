<?php
/**
 * Comprehensive Query Logger
 * Captures ALL database queries from execNavigator.php and execDashboard.php
 * Helps identify performance bottlenecks
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$log_file = __DIR__ . '/comprehensive_query_log.txt';
$performance_log = __DIR__ . '/query_performance_log.txt';

function logQuery($query, $time, $caller = 'unknown', $params = array()) {
    global $log_file, $performance_log;
    
    $timestamp = date('Y-m-d H:i:s') . '.' . substr(microtime(), 2, 3);
    $execution_time = number_format($time * 1000, 2) . 'ms';
    
    // Format log entry
    $log_entry = "[{$timestamp}] [{$execution_time}] [{$caller}] {$query}";
    
    if (!empty($params)) {
        $log_entry .= " | Params: " . json_encode($params);
    }
    $log_entry .= "\n";
    
    // Write to comprehensive log
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    
    // Write performance summary
    $perf_entry = "[{$timestamp}] [{$caller}] {$execution_time}ms\n";
    file_put_contents($performance_log, $perf_entry, FILE_APPEND | LOCK_EX);
}

function logMethodCall($method, $args, $time, $result_count = 0) {
    global $log_file, $performance_log;
    
    $timestamp = date('Y-m-d H:i:s') . '.' . substr(microtime(), 2, 3);
    $execution_time = number_format($time * 1000, 2) . 'ms';
    
    $args_str = is_array($args) ? json_encode($args) : $args;
    $log_entry = "[{$timestamp}] [METHOD] [{$execution_time}] {$method}({$args_str}) -> {$result_count} results\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    file_put_contents($performance_log, $log_entry, FILE_APPEND | LOCK_EX);
}

// Override database methods to capture all queries
class LoggedDatabase {
    private $db;
    private $logging_enabled = true;
    
    public function __construct($original_db) {
        $this->db = $original_db;
    }
    
    public function exec_query($query, $params = null) {
        // Disable logging for logger operations to prevent infinite loops
        if (strpos($query, 'INSERT INTO transactions') !== false || 
            strpos($query, 'INSERT INTO events') !== false ||
            strpos($query, 'transactions') !== false ||
            strpos($query, 'events') !== false) {
            return $this->db->exec_query($query, $params);
        }
        
        $start = microtime(true);
        $result = $this->db->exec_query($query, $params);
        $time = microtime(true) - $start;
        
        // Log the actual SQL query
        if ($this->logging_enabled) {
            logQuery($query, $time, 'SQL', $params);
        }
        
        return $result;
    }
    
    public function fetchRowsIntoMap($query, $key_field, $params = null) {
        // Disable logging for logger operations
        if (strpos($query, 'transactions') !== false || strpos($query, 'events') !== false) {
            return $this->db->fetchRowsIntoMap($query, $key_field, $params);
        }
        
        $start = microtime(true);
        $result = $this->db->fetchRowsIntoMap($query, $key_field, $params);
        $time = microtime(true) - $start;
        
        // Log the actual SQL query
        if ($this->logging_enabled) {
            logQuery($query, $time, 'SQL', $params);
        }
        
        return $result;
    }
    
    public function fetchRowsIntoMapAddRC($query, $key_field, $params = null) {
        // Disable logging for logger operations
        if (strpos($query, 'transactions') !== false || strpos($query, 'events') !== false) {
            return $this->db->fetchRowsIntoMapAddRC($query, $key_field, $params);
        }
        
        $start = microtime(true);
        $result = $this->db->fetchRowsIntoMapAddRC($query, $key_field, $params);
        $time = microtime(true) - $start;
        
        // Log the actual SQL query
        if ($this->logging_enabled) {
            logQuery($query, $time, 'SQL', $params);
        }
        
        return $result;
    }
    
    public function get_recordset($query, $params = null) {
        // Disable logging for logger operations
        if (strpos($query, 'transactions') !== false || strpos($query, 'events') !== false) {
            return $this->db->get_recordset($query, $params);
        }
        
        $start = microtime(true);
        $result = $this->db->get_recordset($query, $params);
        $time = microtime(true) - $start;
        
        // Log the actual SQL query
        if ($this->logging_enabled) {
            logQuery($query, $time, 'SQL', $params);
        }
        
        return $result;
    }
    
    // Method to control logging
    public function enableLogging() {
        $this->logging_enabled = true;
    }
    
    public function disableLogging() {
        $this->logging_enabled = false;
    }
    
    // Pass through all other method calls
    public function __call($method, $args) {
        $start = microtime(true);
        
        // Call original method
        $result = call_user_func_array(array($this->db, $method), $args);
        
        $time = microtime(true) - $start;
        
        // Log the call
        if ($this->logging_enabled) {
            logMethodCall($method, $args, $time, 
                is_array($result) ? count($result) : (is_object($result) ? 1 : 0));
        }
        
        return $result;
    }
    
    public function __get($property) {
        return $this->db->$property;
    }
    
    public function __set($property, $value) {
        return $this->db->$property = $value;
    }
}

// Initialize logging
echo "<!-- Comprehensive Query Logger Initialized -->\n";
echo "<!-- Log File: {$log_file} -->\n";
echo "<!-- Performance Log: {$performance_log} -->\n";

// Clear logs for fresh start
file_put_contents($log_file, "# Comprehensive Query Log - Started at " . date('Y-m-d H:i:s') . "\n\n");
file_put_contents($performance_log, "# Performance Log - Started at " . date('Y-m-d H:i:s') . "\n\n");

?>
