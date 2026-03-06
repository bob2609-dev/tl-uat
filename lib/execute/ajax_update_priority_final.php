<?php
/**
 * Final working AJAX endpoint to update test case priority
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// File logging function
function logToFile($message) {
    $logFile = __DIR__ . '/ajax_priority_debug.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Start logging
logToFile("=== AJAX Priority Update Started ===");
logToFile("Request method: " . $_SERVER['REQUEST_METHOD']);
logToFile("POST data: " . print_r($_POST, true));

header('Content-Type: application/json');

$response = array(
    'success' => false, 
    'message' => 'Starting test...',
    'debug' => array()
);

try {
    // Step 1: Check basic PHP
    $response['debug']['step1'] = 'PHP working';
    logToFile("Step 1: PHP working");
    
    // Step 2: Try to include config file
    try {
        require_once('../../config.inc.php');
        $response['debug']['step2'] = 'Config file loaded successfully';
        logToFile("Step 2: Config file loaded successfully");
    } catch (Exception $e) {
        $response['debug']['step2'] = 'Config file failed: ' . $e->getMessage();
        logToFile("Step 2 FAILED: " . $e->getMessage());
        throw new Exception('Config file error: ' . $e->getMessage());
    }
    
    // Step 3: Try to include common file
    try {
        require_once('common.php');
        $response['debug']['step3'] = 'Common file loaded successfully';
        logToFile("Step 3: Common file loaded successfully");
    } catch (Exception $e) {
        $response['debug']['step3'] = 'Common file failed: ' . $e->getMessage();
        logToFile("Step 3 FAILED: " . $e->getMessage());
        throw new Exception('Common file error: ' . $e->getMessage());
    }
    
    // Step 4: Check POST data and validate parameters
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tcvID = isset($_POST['tcvID']) ? intval($_POST['tcvID']) : 0;
        $priority = isset($_POST['priority']) ? trim($_POST['priority']) : '';
        $field_id = isset($_POST['field_id']) ? intval($_POST['field_id']) : 15;
        
        logToFile("Parameters: tcvID=$tcvID, priority=$priority, field_id=$field_id");
        
        if ($tcvID <= 0 || empty($priority)) {
            throw new Exception('Invalid parameters: tcvID=' . $tcvID . ', priority=' . $priority);
        }
        
        $valid_priorities = array('LOW', 'NORMAL', 'HIGH', 'CRITICAL');
        if (!in_array($priority, $valid_priorities)) {
            throw new Exception('Invalid priority value: ' . $priority);
        }
        
        if ($field_id <= 0) {
            throw new Exception('Invalid field_id: ' . $field_id);
        }
        
        $response['debug']['step4'] = 'POST data received and validated';
        logToFile("Step 4: POST data received and validated");
        $response['debug']['parameters'] = array(
            'tcvID' => $tcvID,
            'priority' => $priority,
            'field_id' => $field_id
        );
        
        // Step 5: Database operations
        try {
            logToFile("Step 5: Starting database operations");
            
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
            $response['debug']['step5'] = 'Database connected successfully';
            
            $node_id = $tcvID;
            logToFile("Using tcvID as node_id: $node_id");
            $response['debug']['node_id'] = $node_id;
            
            // Table existence check
            $table_check = $direct_db->query("SHOW TABLES LIKE 'cfield_design_values'");
            if (!$table_check || $table_check->num_rows == 0) {
                throw new Exception("Table cfield_design_values does not exist");
            }
            logToFile("Table cfield_design_values exists");
            
            // Check if record exists
            $check_sql = "SELECT COUNT(*) as cnt FROM cfield_design_values WHERE field_id = {$field_id} AND node_id = {$node_id}";
            $check_result = $direct_db->query($check_sql);
            if (!$check_result) {
                throw new Exception("Check query failed: " . $direct_db->error);
            }
            
            $check_row = $check_result->fetch_assoc();
            $exists = $check_row['cnt'] > 0;
            logToFile("Record exists: " . ($exists ? 'yes' : 'no'));
            $response['debug']['record_exists'] = $exists;
            
            if ($exists) {
                $update_sql = "UPDATE cfield_design_values SET value = '" . $direct_db->real_escape_string($priority) . "' WHERE field_id = {$field_id} AND node_id = {$node_id}";
                if (!$direct_db->query($update_sql)) {
                    throw new Exception("Update failed: " . $direct_db->error);
                }
                $response['debug']['action'] = 'Updated existing record';
                logToFile("Update successful");
            } else {
                $insert_sql = "INSERT INTO cfield_design_values (field_id, node_id, value) VALUES ({$field_id}, {$node_id}, '" . $direct_db->real_escape_string($priority) . "')";
                if (!$direct_db->query($insert_sql)) {
                    throw new Exception("Insert failed: " . $direct_db->error);
                }
                $response['debug']['action'] = 'Inserted new record';
                logToFile("Insert successful");
            }
            
            $direct_db->close();
            
            $response['success'] = true;
            $response['message'] = 'Priority updated successfully';
            logToFile("Priority update completed successfully");
            
        } catch (Exception $db_e) {
            $response['debug']['step5'] = 'Database operation failed: ' . $db_e->getMessage();
            logToFile("Database operation failed: " . $db_e->getMessage());
            $response['message'] = 'Database operation error: ' . $db_e->getMessage();
        }
        
    } else {
        $response['message'] = 'Not a POST request';
        logToFile("Not a POST request: " . $_SERVER['REQUEST_METHOD']);
    }
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Fatal error: ' . $e->getMessage();
    $response['debug']['fatal_error'] = $e->getTraceAsString();
    logToFile("FATAL ERROR: " . $e->getMessage());
}

// Log final response
logToFile("Final response: " . json_encode($response));
logToFile("=== AJAX Priority Update Ended ===\n");

echo json_encode($response);
?>
