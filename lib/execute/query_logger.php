<?php
/**
 * Query Logger for TestLink Performance Analysis
 * Logs all database queries with execution time
 */

class QueryLogger {
    private static $log_file = null;
    private static $query_count = 0;
    private static $total_time = 0;
    private static $start_time = null;
    
    public static function init() {
        self::$log_file = __DIR__ . '/query_log.txt';
        self::$start_time = microtime(true);
        
        // Clear previous log and start new session
        file_put_contents(self::$log_file, 
            "=== TestLink Query Log Session Started: " . date('Y-m-d H:i:s') . " ===\n\n"
        );
    }
    
    public static function logQuery($sql, $params = [], $execution_time = 0) {
        if (self::$log_file === null) {
            self::init();
        }
        
        self::$query_count++;
        self::$total_time += $execution_time;
        
        $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
        $log_entry = sprintf(
            "[%s] Query #%d - %.4fs\nSQL: %s\n",
            $timestamp,
            self::$query_count,
            $execution_time,
            $sql
        );
        
        if (!empty($params)) {
            $log_entry .= "Params: " . json_encode($params) . "\n";
        }
        
        $log_entry .= "\n";
        
        file_put_contents(self::$log_file, $log_entry, FILE_APPEND);
        
        // Log slow queries (> 1 second) with emphasis
        if ($execution_time > 1.0) {
            file_put_contents(self::$log_file, 
                "*** SLOW QUERY DETECTED: {$execution_time}s ***\n\n", 
                FILE_APPEND
            );
        }
    }
    
    public static function logSummary() {
        if (self::$log_file === null) {
            return;
        }
        
        $total_time = microtime(true) - self::$start_time;
        
        $summary = sprintf(
            "\n=== Query Summary ===\n" .
            "Total Queries: %d\n" .
            "Total Query Time: %.4fs\n" .
            "Average Query Time: %.4fs\n" .
            "Total Script Time: %.4fs\n" .
            "Query Time Percentage: %.1f%%\n" .
            "=== Session Ended: %s ===\n\n",
            self::$query_count,
            self::$total_time,
            self::$query_count > 0 ? self::$total_time / self::$query_count : 0,
            $total_time,
            $total_time > 0 ? (self::$total_time / $total_time) * 100 : 0,
            date('Y-m-d H:i:s')
        );
        
        file_put_contents(self::$log_file, $summary, FILE_APPEND);
    }
}

// Auto-register shutdown function to log summary
register_shutdown_function(function() {
    QueryLogger::logSummary();
});

?>
