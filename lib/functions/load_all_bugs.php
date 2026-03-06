<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *
 * Helper function to load bug data from all executions for a test case
 * @filesource load_all_bugs.php
 */

/**
 * Load bugs from all executions of a test case across all builds
 * 
 * @param object &$db Database connection
 * @param object &$tcase_mgr Test case manager instance
 * @param integer $testcase_id Test case ID
 * @param integer $tplan_id Test plan ID
 * @param boolean $issue_tracker_enabled Whether issue tracker is enabled
 * @param object &$its Issue tracker instance
 * @return array Bug data array keyed by execution_id
 */
function load_all_bugs_for_testcase($db, $tcase_mgr, $testcase_id, $tplan_id, $issue_tracker_enabled, $its) {
    // Get all executions for this test case across all builds
    $sql = "SELECT E.id as execution_id, E.build_id, E.tcversion_id
            FROM executions E
            JOIN nodes_hierarchy NH_TCV ON E.tcversion_id = NH_TCV.id
            JOIN nodes_hierarchy NH_TC ON NH_TCV.parent_id = NH_TC.id
            WHERE NH_TC.id = {$testcase_id} 
            AND E.testplan_id = {$tplan_id}
            ORDER BY E.id DESC";
    
    $result = $db->exec_query($sql);
    $all_bugs = array();
    
    if ($result) {
        while ($row = $db->fetch_array($result)) {
            $execution_id = $row['execution_id'];
            
            // For each execution, get the bugs
            if ($issue_tracker_enabled) {
                $bugs = get_bugs_for_exec($db, $execution_id, $its);
                if (!empty($bugs)) {
                    $all_bugs[$execution_id] = $bugs;
                }
            }
        }
    }
    
    return $all_bugs;
}
