<?php
/**
 * Direct Query Logger - No Database Wrapping
 * Captures queries by extending the original database class
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$log_file = __DIR__ . '/direct_query_log.txt';
$performance_log = __DIR__ . '/direct_performance_log.txt';

// Global flag to prevent recursive logging
$GLOBALS['query_logger_active'] = false;

function logQueryDirect($query, $time, $caller = 'unknown') {
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

// Extend the database class directly
class DirectLoggedDatabase extends database {
    
    public function exec_query($p_query, $p_limit = -1, $p_offset = -1) {
        // Skip logging queries
        if (strpos($p_query, 'INSERT INTO transactions') !== false || 
            strpos($p_query, 'INSERT INTO events') !== false ||
            strpos($p_query, 'UPDATE events') !== false) {
            return parent::exec_query($p_query, $p_limit, $p_offset);
        }
        
        $start = microtime(true);
        $result = parent::exec_query($p_query, $p_limit, $p_offset);
        $time = microtime(true) - $start;
        
        logQueryDirect($p_query, $time, 'SQL');
        
        return $result;
    }
    
    public function fetchRowsIntoMap($sql, $column, $cumulative = 0, $limit = -1, $col2implode = '') {
        if (strpos($sql, 'transactions') !== false || strpos($sql, 'events') !== false) {
            return parent::fetchRowsIntoMap($sql, $column, $cumulative, $limit, $col2implode);
        }
        
        $start = microtime(true);
        $result = parent::fetchRowsIntoMap($sql, $column, $cumulative, $limit, $col2implode);
        $time = microtime(true) - $start;
        
        logQueryDirect($sql, $time, 'SQL');
        
        return $result;
    }
    
    public function fetchRowsIntoMapAddRC($sql, $column, $cumulative = 0) {
        if (strpos($sql, 'transactions') !== false || strpos($sql, 'events') !== false) {
            return parent::fetchRowsIntoMapAddRC($sql, $column, $cumulative);
        }
        
        $start = microtime(true);
        $result = parent::fetchRowsIntoMapAddRC($sql, $column, $cumulative);
        $time = microtime(true) - $start;
        
        logQueryDirect($sql, $time, 'SQL');
        
        return $result;
    }
    
    public function get_recordset($sql, $fetch_mode = null, $limit = -1, $start = -1) {
        if (strpos($sql, 'transactions') !== false || strpos($sql, 'events') !== false) {
            return parent::get_recordset($sql, $fetch_mode, $limit, $start);
        }
        
        $start_time = microtime(true);
        $result = parent::get_recordset($sql, $fetch_mode, $limit, $start);
        $time = microtime(true) - $start_time;
        
        logQueryDirect($sql, $time, 'SQL');
        
        return $result;
    }
}

// Initialize logging
echo "<!-- Direct Query Logger Initialized -->\n";
echo "<!-- Log File: {$log_file} -->\n";

// Clear logs for fresh start
file_put_contents($log_file, "# Direct Query Log - Started at " . date('Y-m-d H:i:s') . "\n\n");
file_put_contents($performance_log, "# Performance Log - Started at " . date('Y-m-d H:i:s') . "\n\n");

?>
