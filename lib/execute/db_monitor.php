<?php
/**
 * Database Query Monitor
 * Intercepts and logs all database queries with execution time
 */

class DBMonitor {
    private static $original_db = null;
    private static $is_monitoring = false;
    
    public static function startMonitoring(&$db) {
        if (self::$is_monitoring) {
            return;
        }
        
        self::$original_db = $db;
        self::$is_monitoring = true;
        
        // Override multiple database methods to catch all queries
        
        // Override exec_query method
        if (method_exists($db, 'exec_query')) {
            $db->original_exec_query = $db->exec_query;
            $db->exec_query = function($sql, $params = []) {
                $start_time = microtime(true);
                $result = call_user_func_array([$this, 'original_exec_query'], [$sql, $params]);
                $execution_time = microtime(true) - $start_time;
                
                SimpleQueryLogger::logQuery($sql, $params, $execution_time);
                return $result;
            };
        }
        
        // Override fetchRowsIntoMap method
        if (method_exists($db, 'fetchRowsIntoMap')) {
            $db->original_fetchRowsIntoMap = $db->fetchRowsIntoMap;
            $db->fetchRowsIntoMap = function($sql, $key, $params = [], $limit = null, $offset = null) {
                $start_time = microtime(true);
                $result = call_user_func_array([$this, 'original_fetchRowsIntoMap'], [$sql, $key, $params, $limit, $offset]);
                $execution_time = microtime(true) - $start_time;
                
                SimpleQueryLogger::logQuery($sql, $params, $execution_time);
                return $result;
            };
        }
        
        // Override get_recordset method
        if (method_exists($db, 'get_recordset')) {
            $db->original_get_recordset = $db->get_recordset;
            $db->get_recordset = function($sql, $params = []) {
                $start_time = microtime(true);
                $result = call_user_func_array([$this, 'original_get_recordset'], [$sql, $params]);
                $execution_time = microtime(true) - $start_time;
                
                SimpleQueryLogger::logQuery($sql, $params, $execution_time);
                return $result;
            };
        }
        
        // Override exec method (for raw SQL execution)
        if (method_exists($db, 'exec')) {
            $db->original_exec = $db->exec;
            $db->exec = function($sql) {
                $start_time = microtime(true);
                $result = call_user_func_array([$this, 'original_exec'], [$sql]);
                $execution_time = microtime(true) - $start_time;
                
                QueryLogger::logQuery($sql, [], $execution_time);
                return $result;
            };
        }
        
        // Override query method (for PDO-style queries)
        if (method_exists($db, 'query')) {
            $db->original_query = $db->query;
            $db->query = function($sql) {
                $start_time = microtime(true);
                $result = call_user_func_array([$this, 'original_query'], [$sql]);
                $execution_time = microtime(true) - $start_time;
                
                QueryLogger::logQuery($sql, [], $execution_time);
                return $result;
            };
        }
        
        // Override prepare+execute pattern by monitoring PDO if available
        if (property_exists($db, 'db') && $db->db instanceof PDO) {
            $pdo = $db->db;
            $pdo->original_prepare = $pdo->prepare;
            $pdo->prepare = function($sql) use ($pdo) {
                $start_time = microtime(true);
                $stmt = call_user_func_array([$pdo, 'original_prepare'], [$sql]);
                $execution_time = microtime(true) - $start_time;
                
                QueryLogger::logQuery("PREPARE: " . $sql, [], $execution_time);
                
                // Also monitor execute
                if (method_exists($stmt, 'execute')) {
                    $stmt->original_execute = $stmt->execute;
                    $stmt->execute = function($params = []) use ($stmt) {
                        $start_time = microtime(true);
                        $result = call_user_func_array([$stmt, 'original_execute'], [$params]);
                        $execution_time = microtime(true) - $start_time;
                        
                        QueryLogger::logQuery("EXECUTE", $params, $execution_time);
                        return $result;
                    };
                }
                
                return $stmt;
            };
        }
        
        // Log that monitoring started
        QueryLogger::logQuery("=== DATABASE MONITORING STARTED ===", [], 0);
    }
    
    public static function logQueryDirectly($sql, $params = []) {
        $start_time = microtime(true);
        
        // Execute the query using original methods
        if (self::$original_db && method_exists(self::$original_db, 'exec_query')) {
            $result = self::$original_db->exec_query($sql, $params);
        } elseif (self::$original_db && method_exists(self::$original_db, 'exec')) {
            $result = self::$original_db->exec($sql);
        } else {
            $result = false;
        }
        
        $execution_time = microtime(true) - $start_time;
        SimpleQueryLogger::logQuery($sql, $params, $execution_time);
        
        return $result;
    }
}

?>
