<?php
/**
 * AJAX endpoint to check if test case priority exists
 */

// Simple test - this should always appear in log
file_put_contents(__DIR__ . '/ajax_priority_debug.log', date('[Y-m-d H:i:s] ') . "SCRIPT EXECUTED\n", FILE_APPEND);

error_reporting(E_ALL);
ini_set('display_errors', 0); // Turn off display errors to prevent header issues

// File logging function
function logToFile($message) {
    $logFile = __DIR__ . '/ajax_priority_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Start logging
logToFile("=== AJAX Priority Check Started ===");
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'UNKNOWN';
logToFile("Request method: " . $request_method);
logToFile("GET data: " . print_r($_GET, true));

header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$response = array(
    'success' => false, 
    'has_priority' => false,
    'priority' => null,
    'message' => 'Starting test...',
    'debug' => array()
);

try {
    // Check basic PHP
    $response['debug']['step1'] = 'PHP working';
    logToFile("Step 1: PHP working");
    
    // Try to include config file
    try {
        require_once('../../config.inc.php');
        $response['debug']['step2'] = 'Config file loaded successfully';
        logToFile("Step 2: Config file loaded successfully");
    } catch (Exception $e) {
        $response['debug']['step2'] = 'Config file failed: ' . $e->getMessage();
        logToFile("Step 2 FAILED: " . $e->getMessage());
        throw new Exception('Config file error: ' . $e->getMessage());
    }
    
    // Check request method and parameters
    if ($request_method === 'GET') {
        $tcvID = isset($_GET['tcvID']) ? intval($_GET['tcvID']) : 0;
        $field_id = isset($_GET['field_id']) ? intval($_GET['field_id']) : 15;
        
        logToFile("Parameters: tcvID=$tcvID, field_id=$field_id");
        
        if ($tcvID <= 0) {
            throw new Exception('Invalid tcvID: ' . $tcvID);
        }
        
        if ($field_id <= 0) {
            throw new Exception('Invalid field_id: ' . $field_id);
        }
        
        $response['debug']['step3'] = 'GET data received and validated';
        logToFile("Step 3: GET data received and validated");
        
        // Database operations
        try {
            logToFile("Step 4: Starting database operations");
            
            $db_host = DB_HOST;
            $db_name = DB_NAME;
            $db_user = DB_USER;
            $db_pass = DB_PASS;
            
            logToFile("DB config - host=$db_host, name=$db_name, user=$db_user");
            
            $direct_db = new mysqli($db_host, $db_user, $db_pass, $db_name);
            
            if ($direct_db->connect_error) {
                throw new Exception("Database connection failed: " . $direct_db->connect_error);
            }
            
            logToFile("Database connected successfully");
            $response['debug']['step4'] = 'Database connected successfully';
            
            $node_id = $tcvID;
            logToFile("Using tcvID as node_id: $node_id");
            $response['debug']['node_id'] = $node_id;
            
            // Check if priority exists
            $sql = "SELECT value FROM cfield_design_values WHERE field_id = ? AND node_id = ?";
            logToFile("Executing SQL: " . $sql . " with field_id=$field_id, node_id=$node_id");
            
            $stmt = $direct_db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare query: " . $direct_db->error);
            }
            
            $stmt->bind_param("ii", $field_id, $node_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to execute query: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $row_count = $result->num_rows;
            logToFile("Query result: $row_count rows found");
            
            if ($row_count > 0) {
                $row = $result->fetch_assoc();
                $response['has_priority'] = true;
                $response['priority'] = $row['value'];
                logToFile("Priority found: " . $row['value']);
            } else {
                $response['has_priority'] = false;
                $response['priority'] = null;
                logToFile("No priority found");
            }
            
            $stmt->close();
            $direct_db->close();
            
            $response['success'] = true;
            $response['message'] = 'Priority check completed successfully';
            $response['debug']['action'] = 'Priority check completed';
            logToFile("Priority check completed successfully");
            
        } catch (Exception $db_e) {
            $response['debug']['step4'] = 'Database operation failed: ' . $db_e->getMessage();
            logToFile("Database operation failed: " . $db_e->getMessage());
            $response['message'] = 'Database operation error: ' . $db_e->getMessage();
        }
        
    } else {
        $response['message'] = 'Not a GET request';
        logToFile("Not a GET request: " . $request_method);
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Fatal error: ' . $e->getMessage();
    $response['debug']['fatal_error'] = $e->getTraceAsString();
    logToFile("FATAL ERROR: " . $e->getMessage());
}

// Log final response
logToFile("About to encode response - array size: " . count($response));
$json_response = json_encode($response);
if ($json_response === false) {
    logToFile("JSON ENCODE FAILED: " . json_last_error_msg());
    $response['json_error'] = json_last_error_msg();
    $json_response = json_encode($response);
}
logToFile("Final response: " . $json_response);
logToFile("=== AJAX Priority Check Ended ===\n");

echo $json_response;
?>
