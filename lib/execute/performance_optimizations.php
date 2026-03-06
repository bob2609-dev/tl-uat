<?php
/**
 * Performance optimization utilities for TestLink
 * 
 * This file contains cached versions of expensive database operations
 * and performance monitoring utilities.
 */

/**
 * Performance monitoring class
 */
class PerformanceMonitor {
    private static $start_time;
    private static $queries = [];
    
    public static function start() {
        self::$start_time = microtime(true);
    }
    
    public static function logQuery($sql, $time) {
        self::$queries[] = [
            'sql' => $sql,
            'time' => $time,
            'timestamp' => microtime(true)
        ];
    }
    
    public static function getReport() {
        $total_time = microtime(true) - self::$start_time;
        $query_time = array_sum(array_column(self::$queries, 'time'));
        
        return [
            'total_time' => $total_time,
            'query_time' => $query_time,
            'query_count' => count(self::$queries),
            'php_time' => $total_time - $query_time,
            'queries' => self::$queries
        ];
    }
}

/**
 * Enhanced testplan class with caching
 */
class CachedTestPlan extends testplan {
    private static $build_cache = [];
    private static $platform_cache = [];
    private static $cache_ttl = 300; // 5 minutes
    
    public function get_max_build_id_cached($tplan_id, $active = 1, $open = 1) {
        $cache_key = $tplan_id . '_' . $active . '_' . $open;
        
        if (isset(self::$build_cache[$cache_key])) {
            $cached = self::$build_cache[$cache_key];
            if ((time() - $cached['timestamp']) < self::$cache_ttl) {
                return $cached['data'];
            }
        }
        
        $result = $this->get_max_build_id($tplan_id, $active, $open);
        
        self::$build_cache[$cache_key] = [
            'data' => $result,
            'timestamp' => time()
        ];
        
        return $result;
    }
    
    public function getPlatforms_cached($tplan_id) {
        $cache_key = 'platforms_' . $tplan_id;
        
        if (isset(self::$platform_cache[$cache_key])) {
            $cached = self::$platform_cache[$cache_key];
            if ((time() - $cached['timestamp']) < self::$cache_ttl) {
                return $cached['data'];
            }
        }
        
        $result = $this->getPlatforms($tplan_id);
        
        self::$platform_cache[$cache_key] = [
            'data' => $result,
            'timestamp' => time()
        ];
        
        return $result;
    }
}

/**
 * Database connection pool for better performance
 */
class DatabasePool {
    private static $connections = [];
    private static $max_connections = 5;
    
    public static function getConnection($config = null) {
        if (count(self::$connections) < self::$max_connections) {
            // Use TestLink's database configuration
            $db_config = config_get('db');
            
            $conn = new mysqli(
                $config['host'] ?? $db_config['host'],
                $config['user'] ?? $db_config['user'],
                $config['password'] ?? $db_config['password'],
                $config['database'] ?? $db_config['name'],
                $config['port'] ?? $db_config['port'] ?? 3306
            );
            
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
            // Optimize connection settings
            $conn->query("SET SESSION wait_timeout = 300");
            $conn->query("SET SESSION interactive_timeout = 300");
            $conn->query("SET SESSION max_execution_time = 120");
            
            self::$connections[] = $conn;
            return $conn;
        }
        
        // Return existing connection
        return self::$connections[array_rand(self::$connections)];
    }
    
    public static function closeAll() {
        foreach (self::$connections as $conn) {
            $conn->close();
        }
        self::$connections = [];
    }
}

/**
 * Custom field cache manager
 */
class CustomFieldCache {
    private static $cache = [];
    private static $cache_ttl = 600; // 10 minutes
    
    public static function get($key) {
        if (isset(self::$cache[$key])) {
            $cached = self::$cache[$key];
            if ((time() - $cached['timestamp']) < self::$cache_ttl) {
                return $cached['data'];
            }
        }
        return null;
    }
    
    public static function set($key, $data) {
        self::$cache[$key] = [
            'data' => $data,
            'timestamp' => time()
        ];
    }
    
    public static function clear() {
        self::$cache = [];
    }
}

/**
 * Optimized tree generator with lazy loading
 */
class OptimizedTreeGenerator {
    private $db;
    private $cache;
    
    public function __construct($db) {
        $this->db = $db;
        $this->cache = new CustomFieldCache();
    }
    
    public function generateTree($context, $filters, $options, $limit = 1000) {
        $cache_key = 'tree_' . md5(serialize($context) . serialize($filters) . $limit);
        
        $cached = $this->cache->get($cache_key);
        if ($cached) {
            return $cached;
        }
        
        // Implement pagination for large trees
        $offset = isset($_GET['tree_offset']) ? intval($_GET['tree_offset']) : 0;
        
        // Modified query with LIMIT
        $sql = $this->buildOptimizedTreeQuery($context, $filters, $options, $limit, $offset);
        
        $start_time = microtime(true);
        $result = $this->db->fetchRowsIntoMap($sql, 'id');
        $query_time = microtime(true) - $start_time;
        
        PerformanceMonitor::logQuery($sql, $query_time);
        
        $tree_data = $this->processTreeData($result, $context, $filters);
        
        $this->cache->set($cache_key, $tree_data);
        
        return $tree_data;
    }
    
    private function buildOptimizedTreeQuery($context, $filters, $options, $limit, $offset) {
        // Build optimized query with proper indexing hints
        $sql = "SELECT 
                    n.id, n.name, n.node_type_id, n.parent_id,
                    COALESCE(exec_status, 'n') as exec_status,
                    COALESCE(execution_count, 0) as execution_count
                FROM nodes_hierarchy n
                LEFT JOIN (
                    SELECT 
                        tc.id as tcase_id,
                        e.status as exec_status,
                        COUNT(e.id) as execution_count
                    FROM testplan_tcversions tc
                    LEFT JOIN executions e ON tc.id = e.tcversion_id
                    WHERE tc.testplan_id = {$context['tplan_id']}
                    " . (isset($filters->setting_build) ? "AND (e.build_id = {$filters->setting_build} OR e.build_id IS NULL)" : "") . "
                    " . (isset($filters->setting_platform) ? "AND (e.platform_id = {$filters->setting_platform} OR e.platform_id IS NULL)" : "") . "
                    GROUP BY tc.id, e.status
                ) exec_data ON n.id = exec_data.tcase_id
                WHERE n.parent_id IN (
                    SELECT id FROM nodes_hierarchy 
                    WHERE parent_id = {$context['tproject_id']}
                )
                ORDER BY n.name
                LIMIT $limit OFFSET $offset";
        
        return $sql;
    }
    
    private function processTreeData($result, $context, $filters) {
        // Process and organize tree data efficiently
        $tree = [];
        $node_map = [];
        
        foreach ($result as $node_id => $node_data) {
            $node_map[$node_id] = $node_data;
            
            if ($node_data['node_type_id'] == 3) { // Test case
                $tree['testcases'][] = $this->formatTestCase($node_data, $context);
            } else {
                $tree['testsuites'][] = $this->formatTestSuite($node_data, $context);
            }
        }
        
        return $tree;
    }
    
    private function formatTestCase($data, $context) {
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'exec_status' => $data['exec_status'],
            'execution_count' => $data['execution_count'],
            'type' => 'testcase'
        ];
    }
    
    private function formatTestSuite($data, $context) {
        return [
            'id' => $data['id'],
            'name' => $data['name'],
            'type' => 'testsuite'
        ];
    }
}

/**
 * Lazy loading custom field manager
 */
class LazyCustomFieldManager {
    private $db;
    private $cache;
    private $loaded_fields = [];
    
    public function __construct($db) {
        $this->db = $db;
        $this->cache = new CustomFieldCache();
    }
    
    public function getCustomFields($node_id, $scope = 'design', $node_type = 'testcase') {
        $cache_key = "cf_{$scope}_{$node_type}_{$node_id}";
        
        if (isset($this->loaded_fields[$cache_key])) {
            return $this->loaded_fields[$cache_key];
        }
        
        $cached = $this->cache->get($cache_key);
        if ($cached) {
            $this->loaded_fields[$cache_key] = $cached;
            return $cached;
        }
        
        // Optimized query with proper indexing
        $sql = "SELECT 
                    cf.name, cf.label, cf.type,
                    cfv.value, cfv.node_id
                FROM custom_fields cf
                LEFT JOIN cfield_design_values cfv ON cf.id = cfv.field_id
                WHERE cfv.node_id = {$node_id}
                AND cf.scope = '{$scope}'
                AND cf.show_on_execution = 1
                ORDER BY cf.display_order";
        
        $start_time = microtime(true);
        $result = $this->db->fetchRowsIntoMap($sql, 'id');
        $query_time = microtime(true) - $start_time;
        
        PerformanceMonitor::logQuery($sql, $query_time);
        
        $this->loaded_fields[$cache_key] = $result;
        $this->cache->set($cache_key, $result);
        
        return $result;
    }
}

// Initialize performance monitoring
PerformanceMonitor::start();

// Register shutdown function to log performance
register_shutdown_function(function() {
    $report = PerformanceMonitor::getReport();
    error_log("Performance Report: " . json_encode($report));
});
?>
