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
 * Refresh the materialized path table if needed
 * @param object $db - Database connection object
 * @param int $refreshInterval - Hours between refreshes
 * @param boolean $forceRefresh - If true, refresh regardless of last refresh time
 * @return boolean - True if refresh was done, false if not needed or error
 */
function refreshMaterializedPathTable($db, $refreshInterval = 24, $forceRefresh = false) {
    $log_file = dirname(__FILE__) . '/test_execution_summary_query.log';
    file_put_contents($log_file, "=== MATERIALIZED PATH TABLE CHECK [" . date('Y-m-d H:i:s') . "] ===\n", FILE_APPEND);
    
    // Check if table exists
    $tableCheckSql = "SHOW TABLES LIKE 'node_hierarchy_paths'";
    $result = $db->exec_query($tableCheckSql);
    
    // Create table if it doesn't exist
    if ($db->num_rows($result) == 0) {
        file_put_contents($log_file, "Table node_hierarchy_paths does not exist, creating it...\n", FILE_APPEND);
        if (!createMaterializedPathTable($db)) {
            return false;
        }
        return true; // Table was just created and populated with root nodes
    }
    
    // Check if the last_refresh column exists
    $columnCheckSql = "SHOW COLUMNS FROM node_hierarchy_paths LIKE 'last_refresh'";
    $result = $db->exec_query($columnCheckSql);
    
    if ($db->num_rows($result) == 0) {
        // Add the column if it doesn't exist
        $alterSql = "ALTER TABLE node_hierarchy_paths ADD COLUMN last_refresh DATETIME";
        $db->exec_query($alterSql);
        file_put_contents($log_file, "Added last_refresh column to node_hierarchy_paths table\n", FILE_APPEND);
    }
    
    // Check if we need to refresh based on time
    if (!$forceRefresh) {
        $checkSql = "SELECT MAX(last_refresh) as last_refresh FROM node_hierarchy_paths";
        $result = $db->exec_query($checkSql);
        
        if ($db->num_rows($result) > 0) {
            $row = $db->fetch_array($result);
            if (!empty($row['last_refresh'])) {
                $lastRefresh = strtotime($row['last_refresh']);
                $hoursSinceRefresh = (time() - $lastRefresh) / 3600;
                
                if ($hoursSinceRefresh < $refreshInterval) {
                    // No refresh needed
                    file_put_contents($log_file, "No refresh needed. Last refresh was " . round($hoursSinceRefresh, 2) . " hours ago\n\n", FILE_APPEND);
                    return false;
                }
            }
        }
    }
