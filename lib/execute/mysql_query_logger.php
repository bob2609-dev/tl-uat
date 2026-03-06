<?php
/**
 * MySQL Query Logger
 * Alternative method to log queries using MySQL general log
 */

class MySQLQueryLogger {
    private static $enabled = false;
    private static $log_file = null;
    
    public static function enableLogging(&$db) {
        if (self::$enabled) {
            return;
        }
        
        self::$enabled = true;
        self::$log_file = __DIR__ . '/mysql_queries.txt';
        
        // Enable MySQL general log temporarily
        try {
            // Check if we have permission to enable general log
            $check_sql = "SHOW VARIABLES LIKE 'general_log%'";
            $result = $db->exec_query($check_sql);
            
            if ($result) {
                // Enable general log for this session
                $enable_sql = "SET SESSION general_log = 'ON'";
                $db->exec_query($enable_sql);
                
                // Set log file to our custom location
                $log_file_path = self::$log_file;
                $set_log_sql = "SET SESSION general_log_file = '{$log_file_path}'";
                $db->exec_query($set_log_sql);
                
                // Log that we enabled MySQL logging
                file_put_contents(self::$log_file, 
                    "=== MySQL Query Logging Enabled: " . date('Y-m-d H:i:s') . " ===\n\n", 
                    FILE_APPEND
                );
            }
        } catch (Exception $e) {
            // Fallback to file-based logging if MySQL general log fails
            self::enableFileBasedLogging($db);
        }
    }
    
    private static function enableFileBasedLogging(&$db) {
        // This is a fallback method - we'll log what we can capture
        file_put_contents(self::$log_file, 
            "=== File-Based Query Logging Enabled: " . date('Y-m-d H:i:s') . " ===\n" .
            "Note: MySQL general log not available, using method interception only.\n\n", 
            FILE_APPEND
        );
    }
    
    public static function disableLogging(&$db) {
        if (!self::$enabled) {
            return;
        }
        
        try {
            // Disable general log
            $disable_sql = "SET SESSION general_log = 'OFF'";
            $db->exec_query($disable_sql);
            
            file_put_contents(self::$log_file, 
                "\n=== MySQL Query Logging Disabled: " . date('Y-m-d H:i:s') . " ===\n", 
                FILE_APPEND
            );
        } catch (Exception $e) {
            // Ignore errors during cleanup
        }
        
        self::$enabled = false;
    }
}

// Auto-disable logging on script end
register_shutdown_function(function() {
    global $db;
    if (isset($db)) {
        MySQLQueryLogger::disableLogging($db);
    }
});

?>
