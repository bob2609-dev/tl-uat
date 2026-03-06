<?php
/**
 * Simple Query Logger - No MySQL General Log Dependencies
 * Focuses on PHP method interception only
 */

class SimpleQueryLogger {
    private static $log_file = null;
    private static $query_count = 0;
    private static $total_time = 0;
    private static $start_time = null;
    
    public static function init() {
        self::$log_file = __DIR__ . '/simple_query_log.txt';
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
        
        // Clean up SQL for better readability
        $clean_sql = preg_replace('/\s+/', ' ', trim($sql));
        $clean_sql = str_replace("\n", ' ', $clean_sql);
        $clean_sql = str_replace("\t", ' ', $clean_sql);
        
        $log_entry = sprintf(
            "[%s] Query #%d - %.4fs\nSQL: %s\n",
            $timestamp,
            self::$query_count,
            $execution_time,
            $clean_sql
        );
        
        if (!empty($params)) {
            $log_entry .= "Params: " . json_encode($params) . "\n";
        }
        
        $log_entry .= "\n";
        
        file_put_contents(self::$log_file, $log_entry, FILE_APPEND);
        
        // Highlight slow queries (> 0.5 seconds)
        if ($execution_time > 0.5) {
            file_put_contents(self::$log_file, 
                "*** SLOW QUERY (>0.5s): {$execution_time}s ***\n\n", 
                FILE_APPEND
            );
        }
        
        // Highlight very slow queries (> 2 seconds)
        if ($execution_time > 2.0) {
            file_put_contents(self::$log_file, 
                "*** VERY SLOW QUERY (>2s): {$execution_time}s ***\n\n", 
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
    
    public static function analyzeQueries() {
        if (!file_exists(self::$log_file)) {
            return "No query log found.";
        }
        
        $content = file_get_contents(self::$log_file);
        $lines = explode("\n", $content);
        
        $slow_queries = [];
        $total_queries = 0;
        $total_time = 0;
        
        foreach ($lines as $line) {
            if (strpos($line, 'Query #') !== false) {
                $total_queries++;
            }
            if (preg_match('/- (\d+\.\d{4})s/', $line, $matches)) {
                $total_time += floatval($matches[1]);
            }
            if (strpos($line, 'SLOW QUERY') !== false && preg_match('/SQL: (.+)/', $line, $matches)) {
                $slow_queries[] = trim($matches[1]);
            }
        }
        
        $analysis = "=== Query Analysis ===\n";
        $analysis .= "Total Queries: {$total_queries}\n";
        $analysis .= "Total Time: " . number_format($total_time, 4) . "s\n";
        $analysis .= "Average Time: " . number_format($total_queries > 0 ? $total_time / $total_queries : 0, 4) . "s\n";
        $analysis .= "Slow Queries: " . count($slow_queries) . "\n\n";
        
        if (!empty($slow_queries)) {
            $analysis .= "=== Slow Queries Found ===\n";
            foreach ($slow_queries as $i => $query) {
                $analysis .= ($i + 1) . ". " . substr($query, 0, 200) . "...\n\n";
            }
        }
        
        return $analysis;
    }
}

// Auto-register shutdown function to log summary
register_shutdown_function(function() {
    SimpleQueryLogger::logSummary();
});

?>
