<?php
/**
 * AJAX Endpoints for Optimized Execution Module
 * Handles all asynchronous operations
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once('exec.inc.php');

// Initialize database
testlinkInitPage($db);

// Set content type to JSON
header('Content-Type: application/json');

// Get action from request
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

try {
    switch ($action) {
        case 'get_tree_nodes':
            echo json_encode(getTreeNodes($db));
            break;
            
        case 'get_testcase':
            echo json_encode(getTestCase($db));
            break;
            
        case 'update_execution':
            echo json_encode(updateExecution($db));
            break;
            
        case 'get_stats':
            echo json_encode(getStats($db));
            break;
            
        default:
            echo json_encode(array('error' => 'Unknown action: ' . $action));
    }
} catch (Exception $e) {
    echo json_encode(array('error' => $e->getMessage()));
}

/**
 * Get tree nodes for lazy loading
 */
function getTreeNodes($db) {
    $parent_id = isset($_REQUEST['parent_id']) ? intval($_REQUEST['parent_id']) : 0;
    $tplan_id = isset($_REQUEST['tplan_id']) ? intval($_REQUEST['tplan_id']) : 0;
    $build_id = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
    $platform_id = isset($_REQUEST['platform_id']) ? intval($_REQUEST['platform_id']) : 0;
    
    $nodes = array();
    
    if ($parent_id == 0) {
        // Get root test suites
        $sql = "SELECT nh.id, nh.name, 'testsuite' as type,
                       COUNT(DISTINCT tc.id) as testcase_count
                FROM nodes_hierarchy nh
                LEFT JOIN nodes_hierarchy child ON nh.id = child.parent_id
                LEFT JOIN testplan_tcversions tptc ON child.id = tptc.tcversion_id
                WHERE nh.parent_id = 0 
                  AND nh.node_type_id = 1
                  AND EXISTS (
                      SELECT 1 FROM testproject_suites tps 
                      WHERE tps.testproject_id = ? AND tps.testsuite_id = nh.id
                  )
                GROUP BY nh.id, nh.name
                ORDER BY nh.name";
        
        $result = $db->GetAll($sql, array($_SESSION['testprojectID']));
    } else {
        // Get child nodes
        $sql = "SELECT nh.id, nh.name, 
                       CASE WHEN nh.node_type_id = 2 THEN 'testcase' ELSE 'testsuite' END as type,
                       CASE WHEN nh.node_type_id = 2 THEN 
                           (SELECT status FROM executions e 
                            WHERE e.tcversion_id = nh.id 
                              AND e.testplan_id = ? 
                              AND e.build_id = ? 
                              AND e.platform_id = ? 
                            ORDER BY e.id DESC LIMIT 1)
                       ELSE NULL END as status,
                       CASE WHEN nh.node_type_id = 2 THEN 0 ELSE 
                           (SELECT COUNT(DISTINCT tc.id) 
                            FROM nodes_hierarchy tc 
                            WHERE tc.parent_id = nh.id AND tc.node_type_id = 2) 
                       END as testcase_count
                FROM nodes_hierarchy nh
                WHERE nh.parent_id = ?
                ORDER BY nh.node_type_id, nh.name";
        
        $result = $db->GetAll($sql, array($tplan_id, $build_id, $platform_id, $parent_id));
    }
    
    if ($result) {
        foreach ($result as $row) {
            $nodes[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'type' => $row['type'],
                'status' => $row['status'] ?: 'n',
                'testcase_count' => intval($row['testcase_count']),
                'has_children' => ($row['type'] == 'testsuite' && $row['testcase_count'] > 0)
            );
        }
    }
    
    return array('success' => true, 'nodes' => $nodes);
}

/**
 * Get test case details
 */
function getTestCase($db) {
    $tcversion_id = isset($_REQUEST['tcversion_id']) ? intval($_REQUEST['tcversion_id']) : 0;
    $tplan_id = isset($_REQUEST['tplan_id']) ? intval($_REQUEST['tplan_id']) : 0;
    $build_id = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
    $platform_id = isset($_REQUEST['platform_id']) ? intval($_REQUEST['platform_id']) : 0;
    
    if (!$tcversion_id) {
        return array('success' => false, 'error' => 'Test case ID required');
    }
    
    // Get test case details
    $sql = "SELECT tc.id, tc.name, tc.summary, tc.preconditions, tc.version,
                   tc.author_id, tc.creation_ts, tc.updater_id, tc.modification_ts,
                   tc.active, tc重要性, tc.urgency,
                   u1.login as author_login, u2.login as updater_login
            FROM tcversions tc
            LEFT JOIN users u1 ON tc.author_id = u1.id
            LEFT JOIN users u2 ON tc.updater_id = u2.id
            WHERE tc.id = ?";
    
    $testcase = $db->GetRow($sql, array($tcversion_id));
    
    if (!$testcase) {
        return array('success' => false, 'error' => 'Test case not found');
    }
    
    // Get test steps
    $sql = "SELECT id, step_number, actions, expected_results, execution_type
            FROM tcsteps 
            WHERE tcversion_id = ?
            ORDER BY step_number";
    
    $steps = $db->GetAll($sql, array($tcversion_id));
    
    // Get latest execution status
    $sql = "SELECT e.id, e.status, e.execution_ts, e.notes, e.tester_id, e.duration,
                   u.login as tester_login
            FROM executions e
            LEFT JOIN users u ON e.tester_id = u.id
            WHERE e.tcversion_id = ? 
              AND e.testplan_id = ? 
              AND e.build_id = ? 
              AND e.platform_id = ?
            ORDER BY e.id DESC 
            LIMIT 1";
    
    $execution = $db->GetRow($sql, array($tcversion_id, $tplan_id, $build_id, $platform_id));
    
    return array(
        'success' => true,
        'testcase' => $testcase,
        'steps' => $steps ?: array(),
        'execution' => $execution
    );
}

/**
 * Update execution status
 */
function updateExecution($db) {
    $tcversion_id = isset($_REQUEST['tcversion_id']) ? intval($_REQUEST['tcversion_id']) : 0;
    $tplan_id = isset($_REQUEST['tplan_id']) ? intval($_REQUEST['tplan_id']) : 0;
    $build_id = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
    $platform_id = isset($_REQUEST['platform_id']) ? intval($_REQUEST['platform_id']) : 0;
    $status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
    $notes = isset($_REQUEST['notes']) ? $_REQUEST['notes'] : '';
    
    if (!$tcversion_id || !$status) {
        return array('success' => false, 'error' => 'Test case ID and status required');
    }
    
    // Validate status
    $valid_statuses = array('p', 'f', 'b', 'n');
    if (!in_array($status, $valid_statuses)) {
        return array('success' => false, 'error' => 'Invalid status');
    }
    
    try {
        // Insert new execution record
        $sql = "INSERT INTO executions (testplan_id, tcversion_id, build_id, platform_id, 
                                       status, notes, tester_id, execution_ts, duration) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 0)";
        
        $db->Execute($sql, array($tplan_id, $tcversion_id, $build_id, $platform_id, 
                                 $status, $notes, $_SESSION['userID']));
        
        return array('success' => true, 'message' => 'Execution updated successfully');
        
    } catch (Exception $e) {
        return array('success' => false, 'error' => 'Failed to update execution: ' . $e->getMessage());
    }
}

/**
 * Get updated statistics
 */
function getStats($db) {
    $tplan_id = isset($_REQUEST['tplan_id']) ? intval($_REQUEST['tplan_id']) : 0;
    $build_id = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
    $platform_id = isset($_REQUEST['platform_id']) ? intval($_REQUEST['platform_id']) : 0;
    
    $stats = array(
        'passed' => 0,
        'failed' => 0,
        'blocked' => 0,
        'not_run' => 0,
        'total' => 0
    );
    
    $sql = "SELECT e.status, COUNT(*) as count 
            FROM executions e
            JOIN testplan_tcversions tptc ON e.testplan_id = tptc.testplan_id 
                                           AND e.tcversion_id = tptc.tcversion_id
            WHERE e.testplan_id = ? 
              AND e.build_id = ? 
              AND e.platform_id = ?
            GROUP BY e.status";
    
    $result = $db->GetAll($sql, array($tplan_id, $build_id, $platform_id));
    
    if ($result) {
        foreach ($result as $row) {
            switch ($row['status']) {
                case 'p': $stats['passed'] = $row['count']; break;
                case 'f': $stats['failed'] = $row['count']; break;
                case 'b': $stats['blocked'] = $row['count']; break;
                case 'n': $stats['not_run'] = $row['count']; break;
            }
        }
    }
    
    // Get total test cases
    $sql = "SELECT COUNT(*) as total 
            FROM testplan_tcversions 
            WHERE testplan_id = ?";
    $result = $db->GetOne($sql, array($tplan_id));
    $stats['total'] = $result ? $result : 0;
    
    // Calculate not run from total minus executed
    $executed = $stats['passed'] + $stats['failed'] + $stats['blocked'];
    $stats['not_run'] = max(0, $stats['total'] - $executed);
    
    return array('success' => true, 'stats' => $stats);
}
?>
