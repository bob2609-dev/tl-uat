<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *
 * Helper functions to load bug data for all executions
 * @filesource bug_loader_helper.php
 */

require_once('common.php');
require_once('exec.inc.php');

/**
 * Loads bugs for all executions of a test case, regardless of build
 * 
 * @param object &$db Database connection
 * @param object &$tcase_mgr Test case manager
 * @param integer $tcase_id Test case ID
 * @param integer $tplan_id Test plan ID
 * @param integer $tproject_id Test project ID
 * @param object &$issueTracker Issue tracker interface
 * @return array Bug data indexed by execution ID
 */
function load_all_bugs_for_testcase(&$db, &$tcase_mgr, $tcase_id, $tplan_id, $tproject_id, &$issueTracker) {
    $bugs = array();
    
    // Get all executions for this test case in all builds
    $sql = " SELECT E.id as execution_id 
             FROM {$tcase_mgr->tables['executions']} E
             JOIN {$tcase_mgr->tables['testplan_tcversions']} TPTCV ON TPTCV.id = E.testplan_tcversion_id
             WHERE TPTCV.tcversion_id IN 
                   (SELECT id FROM {$tcase_mgr->tables['tcversions']} WHERE tc_external_id = 
                       (SELECT MAX(tc_external_id) FROM {$tcase_mgr->tables['tcversions']} 
                        WHERE testcase_id = {$tcase_id}))
             AND TPTCV.testplan_id = {$tplan_id}
             ORDER BY E.id DESC";
    
    $executions = $db->get_recordset($sql);
    
    if (!empty($executions)) {
        foreach ($executions as $exec) {
            $exec_id = $exec['execution_id'];
            $bug_data = get_bugs_for_exec($db, $issueTracker, $exec_id);
            
            if (count($bug_data) > 0) {
                $bugs[$exec_id] = $bug_data;
            }
        }
    }
    
    return $bugs;
}
