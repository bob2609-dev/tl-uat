<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *
 * @filesource  execBugsQuery.php
 * @package     TestLink
 * @copyright   2023 TestLink community
 * @link        http://www.testlink.org/
 *
 * Functions to query execution bugs from the vw_execution_bugs view
 *
 */

/**
 * Get execution bugs by execution ID or testcase ID
 * 
 * @param object &$db Database connection
 * @param mixed $id Either execution ID or testcase ID
 * @param string $type Type of ID provided ('execution' or 'testcase')
 * @param boolean $log Whether to log the query and results
 * @return array Array of bug information from vw_execution_bugs view
 */
function getExecutionBugs(&$db, $id, $type = 'execution', $log = false) {
    $bugs = array();
    $sql = "SELECT 
                EB.execution_id, 
                EB.bug_id,
                E.status as execution_status,
                E.execution_ts,
                E.build_id,
                E.testplan_id,
                NH_TC.id AS testcase_id,
                NH_TC.name AS testcase_name,
                B.name AS build_name,
                NH_TPL.name AS testplan_name,
                U.login AS tester_login,
                E.notes AS execution_notes
            FROM vw_execution_bugs EB
            WHERE ";
    
    // Add condition based on type
    if ($type == 'execution') {
        $id = intval($id);
        $sql .= " EB.execution_id = " . $id;
    } elseif ($type == 'testcase') {
        $id = intval($id);
        $sql .= " EB.testcase_id = " . $id;
    } else {
        return array(); // Invalid type
    }
    
    $sql .= " ORDER BY EB.execution_ts DESC";
    
    // Log the query if requested
    if ($log) {
        logExecutionBugsQuery($sql);
    }
    
    $result = $db->exec_query($sql);
    if ($result) {
        while ($row = $db->fetch_array($result)) {
            $bugs[] = $row;
        }
        
        // Log the results if requested
        if ($log) {
            logExecutionBugsResults($bugs);
        }
    } else {
        // Log the error if requested
        if ($log) {
            logExecutionBugsError($db->error_msg());
        }
    }
    
    return $bugs;
}

/**
 * Log execution bugs query to file
 * 
 * @param string $sql SQL query string
 */
function logExecutionBugsQuery($sql) {
    $logFile = dirname(__FILE__) . '/exec_bugs_query.log';
    
    // Ensure the log file exists
    if (!file_exists($logFile)) {
        createEmptyLogFile();
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[QUERY] [$timestamp] $sql\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Log execution bugs results to file
 * 
 * @param array $bugs Array of bug information
 */
function logExecutionBugsResults($bugs) {
    $logFile = dirname(__FILE__) . '/exec_bugs_query.log';
    
    // Ensure the log file exists
    if (!file_exists($logFile)) {
        createEmptyLogFile();
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $count = count($bugs);
    $logEntry = "[RESULTS] [$timestamp] Found $count bug(s):\n";
    
    foreach ($bugs as $bug) {
        $logEntry .= json_encode($bug, JSON_PRETTY_PRINT) . "\n";
    }
    
    $logEntry .= "----------------------------------------\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Log execution bugs query error to file
 * 
 * @param string $error Error message
 */
function logExecutionBugsError($error) {
    $logFile = dirname(__FILE__) . '/exec_bugs_query.log';
    
    // Ensure the log file exists
    if (!file_exists($logFile)) {
        createEmptyLogFile();
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[ERROR] [$timestamp] $error\n";
    $logEntry .= "----------------------------------------\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

/**
 * Create empty log file with header
 */
function createEmptyLogFile() {
    $logFile = dirname(__FILE__) . '/exec_bugs_query.log';
    $timestamp = date('Y-m-d H:i:s');
    $header = "=== TestLink Execution Bugs Query Log ===\n";
    $header .= "=== Created: $timestamp ===\n";
    $header .= "=== Format: [TYPE] [TIMESTAMP] DATA ===\n";
    $header .= "========================================\n\n";
    file_put_contents($logFile, $header);
}
?>
