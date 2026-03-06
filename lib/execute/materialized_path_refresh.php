<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Functions to refresh the materialized path table for test execution summary
 * 
 * @filesource materialized_path_refresh.php
 */

/**
 * Sanitize error messages to prevent path disclosure
 * 
 * @param string $errorMsg Error message to sanitize
 * @return string Sanitized error message
 */
function sanitizeErrorMessage($errorMsg) {
    // Get server paths that should be hidden
    $paths = array(
        dirname(__FILE__),
        realpath(dirname(__FILE__)),
        $_SERVER['DOCUMENT_ROOT'] ?? '',
        str_replace('/', '\\', $_SERVER['DOCUMENT_ROOT'] ?? '')
    );
    
    // Replace all instances of these paths with [REDACTED_PATH]
    return str_replace($paths, '[REDACTED_PATH]', $errorMsg);
}

/**
 * Function to refresh the materialized path table
 * This ensures the hierarchy data is up-to-date
 * 
 * @param object $db Database connection object
 * @param int $refreshInterval Hours between refreshes (default 24)
 * @param bool $forceRefresh Force refresh regardless of time
 * @return bool True if refresh was performed, false otherwise
 */
function refreshMaterializedPathTable($db, $refreshInterval = 24, $forceRefresh = false) {
    $log_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_execution_summary_query.log';
    
    // Helper function to log SQL and capture database error details
    function logSqlExecution($sql, $log_file, $message = '') {
        file_put_contents($log_file, "\n=== SQL EXECUTION [" . date('Y-m-d H:i:s') . "] ===\n", FILE_APPEND);
        file_put_contents($log_file, $message . "\n" . $sql . "\n", FILE_APPEND);
    }
    
    // Test database connection with a simple query
    file_put_contents($log_file, "\n=== VALIDATING DATABASE CONNECTION [" . date('Y-m-d H:i:s') . "] ===\n", FILE_APPEND);
    try {
        // Check if we can query a simple table
        $testSql = "SHOW TABLES";
        logSqlExecution($testSql, $log_file, "Testing database connection");
        $testResult = $db->exec_query($testSql);
        if (!$testResult) {
            file_put_contents($log_file, "Database connection test failed - no result\n", FILE_APPEND);
        } else {
            $tableCount = $db->num_rows($testResult);
            file_put_contents($log_file, "Database connection successful. Found {$tableCount} tables.\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents($log_file, "Database connection test failed: " . sanitizeErrorMessage($e->getMessage()) . "\n", FILE_APPEND);
    }
    
    // Check if the table exists and has the last_refresh column
    try {
        $tableCheckSql = "SHOW TABLES LIKE 'node_hierarchy_paths'";
        logSqlExecution($tableCheckSql, $log_file, "Checking if table exists");
        try {
            $result = $db->exec_query($tableCheckSql);
        } catch (Exception $e) {
            file_put_contents($log_file, "Error checking table existence: " . sanitizeErrorMessage($e->getMessage()) . "\n", FILE_APPEND);
            throw $e;
        }
        
        // Check if we got a valid result
        if (!$result) {
            file_put_contents($log_file, "=== ERROR CHECKING TABLE [" . date('Y-m-d H:i:s') . "] ===\nInvalid result from table check query\n", FILE_APPEND);
            return false;
        }
        
        // Check if table exists
        if ($db->num_rows($result) == 0) {
            // Table doesn't exist, create it first
            file_put_contents($log_file, "=== MATERIALIZED PATH TABLE NOT FOUND [" . date('Y-m-d H:i:s') . "] ===\nCreating table...\n", FILE_APPEND);
            return createMaterializedPathTable($db);
        }
        
        // Check if last_refresh column exists
        $columnCheckSql = "SHOW COLUMNS FROM node_hierarchy_paths LIKE 'last_refresh'";
        $result = $db->exec_query($columnCheckSql);
        
        if ($result && $db->num_rows($result) == 0) {
            // Add the column
            $db->exec_query("ALTER TABLE node_hierarchy_paths ADD COLUMN last_refresh DATETIME DEFAULT CURRENT_TIMESTAMP");
        }
    } catch (Exception $e) {
        file_put_contents($log_file, "=== ERROR CHECKING TABLE [" . date('Y-m-d H:i:s') . "] ===\n" . $e->getMessage() . "\n", FILE_APPEND);
        return false;
    }
    
    // Check when the table was last refreshed
    try {
        $lastRefreshQuery = "SELECT MAX(last_refresh) as last_refresh_time FROM node_hierarchy_paths";
        $result = $db->exec_query($lastRefreshQuery);
        $row = $db->fetch_array($result);
        $lastRefreshTime = isset($row['last_refresh_time']) ? strtotime($row['last_refresh_time']) : 0;
        
        // Calculate if refresh is needed (if table is empty or older than refresh interval)
        $currentTime = time();
        $hoursSinceRefresh = ($lastRefreshTime > 0) ? ($currentTime - $lastRefreshTime) / 3600 : $refreshInterval + 1;
        
        // If refresh is needed or forced
        if ($forceRefresh || $hoursSinceRefresh >= $refreshInterval) {
            file_put_contents($log_file, "=== REFRESHING MATERIALIZED PATH TABLE [" . date('Y-m-d H:i:s') . "] ===\nLast refresh was " . round($hoursSinceRefresh, 2) . " hours ago\n", FILE_APPEND);
            
            // Start transaction
            // Make sure autocommit is off and start a transaction
            $db->exec_query("SET autocommit=0");
            $db->exec_query("START TRANSACTION");
            
            try {
                // Step 1: Clear the existing data
                $db->exec_query("DELETE FROM node_hierarchy_paths");
                
                // Step 2: Insert root nodes (those with no parent or parent_id=0)
                $insertRootSql = "INSERT INTO node_hierarchy_paths 
                    (node_id, level1_id, level1_name, full_path, last_refresh)
                    SELECT 
                        id as node_id,
                        id as level1_id,
                        name as level1_name,
                        name AS full_path,
                        NOW()
                    FROM nodes_hierarchy 
                    WHERE parent_id IS NULL OR parent_id = 0";
                
                $db->exec_query($insertRootSql);
                
                // Step 3: Insert level 2 nodes
                $insertLevel2Sql = "INSERT INTO node_hierarchy_paths 
                    (node_id, level1_id, level1_name, level2_id, level2_name, full_path, last_refresh)
                    SELECT 
                        nh.id AS node_id,
                        nhp.level1_id,
                        nhp.level1_name,
                        nh.id AS level2_id,
                        nh.name AS level2_name,
                        CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                        NOW()
                    FROM 
                        nodes_hierarchy nh
                        JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
                    WHERE 
                        nhp.level2_id IS NULL";
                
                $db->exec_query($insertLevel2Sql);
                file_put_contents($log_file, "Level 2 nodes inserted successfully\n", FILE_APPEND);
                
                // Step 4: Insert level 3 nodes
                $insertLevel3Sql = "INSERT INTO node_hierarchy_paths 
                    (node_id, level1_id, level1_name, level2_id, level2_name, level3_id, level3_name, full_path, last_refresh)
                    SELECT 
                        nh.id AS node_id,
                        nhp.level1_id,
                        nhp.level1_name,
                        nhp.level2_id,
                        nhp.level2_name,
                        nh.id AS level3_id,
                        nh.name AS level3_name,
                        CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                        NOW()
                    FROM 
                        nodes_hierarchy nh
                        JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
                    WHERE 
                        nhp.level2_id IS NOT NULL
                        AND nhp.level3_id IS NULL";
                
                $db->exec_query($insertLevel3Sql);
                file_put_contents($log_file, "Level 3 nodes inserted successfully\n", FILE_APPEND);
                
                // Step 5: Insert level 4 nodes
                $insertLevel4Sql = "INSERT INTO node_hierarchy_paths 
                    (node_id, level1_id, level1_name, level2_id, level2_name, level3_id, level3_name, level4_id, level4_name, full_path, last_refresh)
                    SELECT 
                        nh.id AS node_id,
                        nhp.level1_id,
                        nhp.level1_name,
                        nhp.level2_id,
                        nhp.level2_name,
                        nhp.level3_id,
                        nhp.level3_name,
                        nh.id AS level4_id,
                        nh.name AS level4_name,
                        CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                        NOW()
                    FROM 
                        nodes_hierarchy nh
                        JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
                    WHERE 
                        nhp.level3_id IS NOT NULL
                        AND nhp.level4_id IS NULL";
                
                $db->exec_query($insertLevel4Sql);
                file_put_contents($log_file, "Level 4 nodes inserted successfully\n", FILE_APPEND);
                
                // Step 6: Insert level 5 nodes
                $insertLevel5Sql = "INSERT INTO node_hierarchy_paths 
                    (node_id, level1_id, level1_name, level2_id, level2_name, level3_id, level3_name, level4_id, level4_name, level5_id, level5_name, full_path, last_refresh)
                    SELECT 
                        nh.id AS node_id,
                        nhp.level1_id,
                        nhp.level1_name,
                        nhp.level2_id,
                        nhp.level2_name,
                        nhp.level3_id,
                        nhp.level3_name,
                        nhp.level4_id,
                        nhp.level4_name,
                        nh.id AS level5_id,
                        nh.name AS level5_name,
                        CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                        NOW()
                    FROM 
                        nodes_hierarchy nh
                        JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
                    WHERE 
                        nhp.level4_id IS NOT NULL
                        AND nhp.level5_id IS NULL";
                
                $db->exec_query($insertLevel5Sql);
                file_put_contents($log_file, "Level 5 nodes inserted successfully\n", FILE_APPEND);
                
                // Commit the transaction
                $db->exec_query("COMMIT");
                file_put_contents($log_file, "Materialized path table refreshed successfully.\n\n", FILE_APPEND);
                return true;
                
            } catch (Exception $e) {
                file_put_contents($log_file, "Error during refresh: " . sanitizeErrorMessage($e->getMessage()) . "\n", FILE_APPEND);
                $db->exec_query("ROLLBACK");
                return false;
            }
        } else {
            // No refresh needed based on time
            file_put_contents($log_file, "=== MATERIALIZED PATH TABLE CHECK [" . date('Y-m-d H:i:s') . "] ===\nNo refresh needed. Last refresh was " . round($hoursSinceRefresh, 2) . " hours ago\n\n", FILE_APPEND);
            return false;
        }
    } catch (Exception $e) {
        file_put_contents($log_file, "=== ERROR DURING REFRESH CHECK [" . date('Y-m-d H:i:s') . "] ===\n" . sanitizeErrorMessage($e->getMessage()) . "\n", FILE_APPEND);
        return false;
    }
}

/**
 * Function to create the materialized path table if it doesn't exist
 * 
 * @param object $db Database connection object
 * @return bool True if table was created successfully, false otherwise
 */
function createMaterializedPathTable($db) {
    $log_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_execution_summary_query.log';

    try {
        // Create the table
        $createTableSql = "CREATE TABLE IF NOT EXISTS node_hierarchy_paths (
            node_id INT NOT NULL,
            level1_id INT NULL,
            level1_name VARCHAR(100) NULL,
            level2_id INT NULL,
            level2_name VARCHAR(100) NULL,
            level3_id INT NULL,
            level3_name VARCHAR(100) NULL,
            level4_id INT NULL,
            level4_name VARCHAR(100) NULL,
            level5_id INT NULL,
            level5_name VARCHAR(100) NULL,
            full_path VARCHAR(500) NULL,
            last_refresh DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (node_id),
            INDEX idx_l1 (level1_id),
            INDEX idx_l2 (level2_id),
            INDEX idx_l3 (level3_id),
            INDEX idx_l4 (level4_id),
            INDEX idx_l5 (level5_id),
            INDEX idx_hierarchy_path_fullpath (full_path(255))
        )";

        // Log the table creation SQL
        file_put_contents($log_file, "Executing table creation SQL: \n" . $createTableSql . "\n", FILE_APPEND);
        
        try {
            $db->exec_query($createTableSql);
        } catch (Exception $e) {
            file_put_contents($log_file, "Error creating table: " . sanitizeErrorMessage($e->getMessage()) . "\n", FILE_APPEND);
            throw $e; // Re-throw to be caught by outer try-catch
        }

        // Step 1: Insert root nodes (those with no parent or parent_id=0)
        $insertRootSql = "INSERT INTO node_hierarchy_paths 
            (node_id, level1_id, level1_name, full_path, last_refresh)
            SELECT 
                id as node_id,
                id as level1_id,
                name as level1_name,
                name AS full_path,
                NOW() as last_refresh
            FROM nodes_hierarchy 
            WHERE parent_id IS NULL OR parent_id = 0";
            
        $db->exec_query($insertRootSql);
        
        // Step 2: Insert level 2 nodes
        $insertLevel2Sql = "INSERT INTO node_hierarchy_paths 
            (node_id, level1_id, level1_name, level2_id, level2_name, full_path, last_refresh)
            SELECT 
                nh.id AS node_id,
                nhp.level1_id,
                nhp.level1_name,
                nh.id AS level2_id,
                nh.name AS level2_name,
                CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                NOW() as last_refresh
            FROM 
                nodes_hierarchy nh
                JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
            WHERE nhp.level2_id IS NULL";
            
        $db->exec_query($insertLevel2Sql);
        
        // Step 3: Insert level 3 nodes
        $insertLevel3Sql = "INSERT INTO node_hierarchy_paths 
            (node_id, level1_id, level1_name, level2_id, level2_name, level3_id, level3_name, full_path, last_refresh)
            SELECT 
                nh.id AS node_id,
                nhp.level1_id,
                nhp.level1_name,
                nhp.level2_id,
                nhp.level2_name,
                nh.id AS level3_id,
                nh.name AS level3_name,
                CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                NOW() as last_refresh
            FROM 
                nodes_hierarchy nh
                JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
            WHERE 
                nhp.level2_id IS NOT NULL
                AND nhp.level3_id IS NULL";
            
        $db->exec_query($insertLevel3Sql);
        
        // Step 4: Insert level 4 nodes
        $insertLevel4Sql = "INSERT INTO node_hierarchy_paths 
            (node_id, level1_id, level1_name, level2_id, level2_name, level3_id, level3_name, level4_id, level4_name, full_path, last_refresh)
            SELECT 
                nh.id AS node_id,
                nhp.level1_id,
                nhp.level1_name,
                nhp.level2_id,
                nhp.level2_name,
                nhp.level3_id,
                nhp.level3_name,
                nh.id AS level4_id,
                nh.name AS level4_name,
                CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                NOW() as last_refresh
            FROM 
                nodes_hierarchy nh
                JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
            WHERE 
                nhp.level3_id IS NOT NULL
                AND nhp.level4_id IS NULL";
            
        $db->exec_query($insertLevel4Sql);
        
        // Step 5: Insert level 5 nodes
        $insertLevel5Sql = "INSERT INTO node_hierarchy_paths 
            (node_id, level1_id, level1_name, level2_id, level2_name, level3_id, level3_name, level4_id, level4_name, level5_id, level5_name, full_path, last_refresh)
            SELECT 
                nh.id AS node_id,
                nhp.level1_id,
                nhp.level1_name,
                nhp.level2_id,
                nhp.level2_name,
                nhp.level3_id,
                nhp.level3_name,
                nhp.level4_id,
                nhp.level4_name,
                nh.id AS level5_id,
                nh.name AS level5_name,
                CONCAT(nhp.full_path, ' > ', nh.name) AS full_path,
                NOW() as last_refresh
            FROM 
                nodes_hierarchy nh
                JOIN node_hierarchy_paths nhp ON nh.parent_id = nhp.node_id
            WHERE 
                nhp.level4_id IS NOT NULL
                AND nhp.level5_id IS NULL";
            
        $db->exec_query($insertLevel5Sql);
        
        file_put_contents($log_file, "Created and populated materialized path table successfully.\n\n", FILE_APPEND);
        return true;
        
    } catch (Exception $e) {
        file_put_contents($log_file, "Error creating materialized path table: " . sanitizeErrorMessage($e->getMessage()) . "\n\n", FILE_APPEND);
        // Ensure any potential transaction is rolled back
        try {
            $db->exec_query("ROLLBACK");
            $db->exec_query("SET autocommit=1");
        } catch (Exception $ex) {
            // Silently continue if rollback fails
        }
        return false;
    }
}
?>