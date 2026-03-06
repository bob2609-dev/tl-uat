<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *
 * Helper functions to load bug data directly from database view
 * @filesource execution_bugs_helper.php
 */

/**
 * Get bugs directly from the database view for a specific execution ID
 * 
 * @param object &$db Database connection
 * @param integer $execution_id Execution ID to get bugs for
 * @return array Bug data array with bug_id as key
 */
function get_bugs_from_view($db, $execution_id) {
    $bugs = array();
    
    // Query the database view we created
    $sql = "SELECT bug_id FROM vw_execution_bugs WHERE execution_id = $execution_id";
    $result = $db->exec_query($sql);
    
    if ($result) {
        while($row = $db->fetch_array($result)) {
            $bug_id = $row['bug_id'];
            // Create a structure similar to what TestLink expects
            $bugs[$bug_id] = array(
                'bug_id' => $bug_id,
                'link_to_bts' => "https://support.profinch.com/issues/{$bug_id}"
            );
        }
    }
    
    return $bugs;
}

/**
 * Get bugs for a given test case (all executions)
 * 
 * @param object &$db Database connection
 * @param integer $testcase_id Test case ID to get bugs for
 * @return array Bug data array with bug_id as key
 */
function get_bugs_for_testcase($db, $testcase_id) {
    $bugs = array();
    
    // Query the database view we created to get all bugs for this test case
    $sql = "SELECT bug_id FROM vw_execution_bugs 
            JOIN nodes_hierarchy NH_TC ON vw_execution_bugs.testcase_name = NH_TC.name
            WHERE NH_TC.id = $testcase_id";
    
    $result = $db->exec_query($sql);
    
    if ($result) {
        while($row = $db->fetch_array($result)) {
            $bug_id = $row['bug_id'];
            // Create a structure similar to what TestLink expects
            $bugs[$bug_id] = array(
                'bug_id' => $bug_id,
                'link_to_bts' => "https://support.profinch.com/issues/{$bug_id}"
            );
        }
    }
    
    return $bugs;
}

/**
 * Smarty function to display bugs from DB view
 * This is registered in tlsmarty.inc.php
 */
function smarty_function_show_execution_bugs($params, $smarty) {
    global $db;
    
    $execution_id = isset($params['execution_id']) ? intval($params['execution_id']) : 0;
    $output = '';
    
    if ($execution_id > 0) {
        // Direct DB query to get bugs for this execution
        $sql = "SELECT bug_id FROM vw_execution_bugs WHERE execution_id = $execution_id";
        $result = $db->exec_query($sql);
        
        if ($result && $db->num_rows($result) > 0) {
            $output .= '<span style="margin-left:10px; background-color:#FFF; color:#000; padding:3px 8px; border-radius:4px; font-weight:bold;">\n';
            $output .= 'Bug #: ';
            
            $first = true;
            while ($row = $db->fetch_array($result)) {
                if (!$first) $output .= ', ';
                $output .= '<a href="https://support.profinch.com/issues/' . $row['bug_id'] . '" target="_blank">' . $row['bug_id'] . '</a>';
                $first = false;
            }
            
            $output .= '</span>';
        }
    }
    
    return $output;
}
