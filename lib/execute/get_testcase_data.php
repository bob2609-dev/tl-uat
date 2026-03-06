<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Fetches test case data from the database views for bug reporting
 * 
 * @package     TestLink
 * @copyright   2025 TestLink community
 */

// Disable all error reporting and output
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any unwanted output
ob_start();

// Initialize response array
$response = array(
    'success' => false,
    'data' => null,
    'message' => ''
);

try {
    // Include required files inside the try block to catch any errors
    require_once('../../config.inc.php');
    require_once('common.php');
    
    // Get parameters
    $exec_id = isset($_REQUEST['exec_id']) ? intval($_REQUEST['exec_id']) : 0;
    $tcversion_id = isset($_REQUEST['tcversion_id']) ? intval($_REQUEST['tcversion_id']) : 0;
    
    if (!$exec_id && !$tcversion_id) {
        throw new Exception('Missing required parameters: exec_id or tcversion_id');
    }
    
    // Connect to database
    $db = new database(DB_TYPE);
    $db->db->SetFetchMode(ADODB_FETCH_ASSOC);
    
    // Get execution details if exec_id is provided
    if ($exec_id) {
        $sql = "SELECT tcversion_id, notes FROM executions WHERE id = $exec_id";
        $result = $db->get_recordset($sql);
        
        if (!empty($result)) {
            $tcversion_id = $result[0]['tcversion_id'];
            $notes = $result[0]['notes'];
        } else {
            throw new Exception('Execution not found for ID: ' . $exec_id);
        }
    } else {
        $notes = '';
    }
    
    // Get test case data from the view - simplified query with error handling
    try {
        $sql = "SELECT 
                    tc.Scenario_ID, 
                    tc.Sub_Scenario, 
                    tc.Test_Script, 
                    tc.Expected_Results,
                    tc.E_R_Process,
                    tc.testcase_name,
                    tc.Primary_Module,
                    tc.Test_Type,
                    tc.Test_Execution_Path,
                    ex.Test_Data,
                    ex.Priority,
                    ex.Execution_Status_CF
                FROM 
                    vw_testcase_summary tc
                LEFT JOIN 
                    vw_latest_executions ex ON tc.tcversion_id = ex.tcversion_id
                WHERE 
                    tc.tcversion_id = $tcversion_id";
        
        $result = $db->get_recordset($sql);
        
        if (!empty($result)) {
            // Format the data with safe access
            $testCaseData = array(
                'scenario_id' => isset($result[0]['Scenario_ID']) ? $result[0]['Scenario_ID'] : '',
                'sub_scenario' => isset($result[0]['Sub_Scenario']) ? $result[0]['Sub_Scenario'] : '',
                'test_script' => isset($result[0]['Test_Script']) ? $result[0]['Test_Script'] : '',
                'expected_results' => isset($result[0]['Expected_Results']) ? $result[0]['Expected_Results'] : '',
                'er_process' => isset($result[0]['E_R_Process']) ? $result[0]['E_R_Process'] : 'TEMPLATE_EMPTY',
                'testcase_name' => isset($result[0]['testcase_name']) ? $result[0]['testcase_name'] : '',
                'primary_module' => isset($result[0]['Primary_Module']) ? $result[0]['Primary_Module'] : '',
                'test_type' => isset($result[0]['Test_Type']) ? $result[0]['Test_Type'] : '',
                'test_execution_path' => isset($result[0]['Test_Execution_Path']) ? $result[0]['Test_Execution_Path'] : '',
                'test_data' => isset($result[0]['Test_Data']) ? $result[0]['Test_Data'] : '',
                'priority' => isset($result[0]['Priority']) ? $result[0]['Priority'] : '',
                'execution_status' => isset($result[0]['Execution_Status_CF']) ? $result[0]['Execution_Status_CF'] : '',
                'notes' => $notes
            );
            
            $response['success'] = true;
            $response['data'] = $testCaseData;
        } else {
            throw new Exception('Test case version not found for ID: ' . $tcversion_id);
        }
    } catch (Exception $queryException) {
        // Specific handling for query errors
        throw new Exception('Database query error: ' . $queryException->getMessage());
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
    // Log the error for server-side debugging
    error_log('TestLink API Error: ' . $e->getMessage());
}

// Clear any output that might have been generated
ob_end_clean();

// Set content type to JSON
header('Content-Type: application/json');

// Return JSON response
echo json_encode($response);

// End script execution to prevent any additional output
exit;
