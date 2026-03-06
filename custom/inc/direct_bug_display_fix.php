<?php
/**
 * Direct Bug Display Fix for TestLink
 * 
 * This file provides a direct fix for bug display issues by overriding the core functions
 * that handle bug display in TestLink.
 */

// Only run this code once
if (!defined('DIRECT_BUG_DISPLAY_FIX')) {
    define('DIRECT_BUG_DISPLAY_FIX', true);
    
    /**
     * Override the get_bugs_for_exec function to use our custom bug display format
     */
    function direct_get_bugs_for_exec($db, $bugInterface, $execution_id) {
        // Log that the function was called
        error_log('direct_get_bugs_for_exec called for execID: ' . $execution_id);
        file_put_contents(dirname(dirname(dirname(__FILE__))) . '/direct_bug_fix_debug.txt', 
                          date('Y-m-d H:i:s') . " - direct_get_bugs_for_exec called for execID: {$execution_id}\n", 
                          FILE_APPEND);
        
        $tables = tlObjectWithDB::getDBTables(array('execution_bugs','executions'));
        $bugsList = array();
        
        if ($bugInterface) {
            $sql = " SELECT execution_bugs.bug_id,executions.build_id,executions.testplan_id, " .
                   " execution_bugs.tcstep_id " .
                   " FROM {$tables['execution_bugs']} execution_bugs, {$tables['executions']} executions " .
                   " WHERE execution_bugs.execution_id = {$execution_id} " .
                   " AND execution_bugs.execution_id = executions.id ";
            
            $rs = $db->fetchRowsIntoMap($sql, 'bug_id');
            
            if ($rs) {
                foreach($rs as $bugID => $elem) {
                    // Get issue details from Redmine
                    $status = 'Unknown';
                    $isResolved = false;
                    
                    // Try to get the issue details
                    try {
                        if (method_exists($bugInterface, 'getIssue')) {
                            $issue = $bugInterface->getIssue($bugID);
                            if ($issue && isset($issue->statusVerbose)) {
                                $status = $issue->statusVerbose;
                                $isResolved = isset($issue->isResolved) ? $issue->isResolved : false;
                            }
                        }
                    } catch (Exception $e) {
                        error_log('Error getting issue details: ' . $e->getMessage());
                    }
                    
                    // Create a simple link that will display correctly
                    $url = "https://support.profinch.com/issues/{$bugID}";
                    $link = "<b>Issue #{$bugID}</b> [Status: {$status}] <a href=\"{$url}\" target=\"_blank\">View in Redmine</a>";
                    
                    // Add to the bug list
                    $bugsList[$bugID] = array(
                        'link_to_bts' => $link,
                        'isResolved' => $isResolved,
                        'build_name' => isset($elem['build_name']) ? $elem['build_name'] : '',
                        'tcstep_id' => $elem['tcstep_id']
                    );
                    
                    // Log the bug information
                    file_put_contents(dirname(dirname(dirname(__FILE__))) . '/direct_bug_fix_debug.txt', 
                                      date('Y-m-d H:i:s') . " - Created link for bug ID {$bugID}: {$link}\n", 
                                      FILE_APPEND);
                }
            }
        }
        
        return $bugsList;
    }
    
    /**
     * Function to inject our custom bug display fix
     */
    function direct_bug_display_fix() {
        // Log that the function was called
        error_log('direct_bug_display_fix() function was called at ' . date('Y-m-d H:i:s'));
        
        // Create a log file for debugging
        file_put_contents(dirname(dirname(dirname(__FILE__))) . '/direct_bug_fix_debug.txt', 
                          date('Y-m-d H:i:s') . " - direct_bug_display_fix() function was called\n", 
                          FILE_APPEND);
        
        // Override the get_bugs_for_exec function with our custom implementation
        global $tlCfg;
        $tlCfg->hooks['get_bugs_for_exec'] = 'direct_get_bugs_for_exec';
        
        // Add a visible notification div
        echo "<div id='bug-fix-notification' style='position:fixed;top:10px;right:10px;background:rgba(0,128,0,0.8);color:white;padding:10px;font-size:14px;z-index:9999;border:2px solid black;'>";
        echo "Direct Bug Fix Active - " . date('H:i:s');
        echo "</div>\n";
        
        return true;
    }
}
