<?php
/**
 * Working AJAX endpoint to update test case priority - clean version
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
    
    // Step 4: Check POST data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get and validate input parameters
        $tcvID = isset($_POST['tcvID']) ? intval($_POST['tcvID']) : 0;
        $priority = isset($_POST['priority']) ? trim($_POST['priority']) : '';
        $field_id = isset($_POST['field_id']) ? intval($_POST['field_id']) : 15; // Default to 15 if not provided
        
        logToFile("Parameters: tcvID=$tcvID, priority=$priority, field_id=$field_id");
        
        if ($tcvID <= 0 || empty($priority)) {
            throw new Exception('Invalid parameters: tcvID=' . $tcvID . ', priority=' . $priority);
        }
        
        // Validate priority value
        $valid_priorities = array('LOW', 'NORMAL', 'HIGH', 'CRITICAL');
        if (!in_array($priority, $valid_priorities)) {
            throw new Exception('Invalid priority value: ' . $priority);
        }
        
        // Validate field_id
        if ($field_id <= 0) {
            throw new Exception('Invalid field_id: ' . $field_id);
        }
        
        $response['debug']['step4'] = 'POST data received';
        logToFile("Step 4: POST data received - tcvID=$tcvID, priority=$priority, field_id=$field_id");
        $response['debug']['parameters'] = array(
            'tcvID' => $tcvID,
            'priority' => $priority,
            'field_id' => $field_id
        );
        
        // Step 5: Try database operations using direct connection
        try {
            logToFile("Step 5: Attempting direct database connection...");
            
            // Get database config from available constants
            $db_host = DB_HOST;
            $db_name = DB_NAME;
            $db_user = DB_USER;
            $db_pass = DB_PASS;
            
            logToFile("DB config - host=$db_host, name=$db_name, user=$db_user");
            
            // Direct MySQL connection
            $direct_db = new mysqli($db_host, $db_user, $db_pass, $db_name);
            
            if ($direct_db->connect_error) {
                logToFile("Direct MySQL connection failed: " . $direct_db->connect_error);
                throw new Exception("Direct MySQL connection failed: " . $direct_db->connect_error);
            }
            
            logToFile("Direct MySQL connection successful");
            $response['debug']['step5'] = 'Direct database connection successful';
            
            // Simple approach - use tcvID directly as node_id
            $node_id = $tcvID; // Use tcvID directly as node_id
            logToFile("Using tcvID as node_id: $node_id");
            $response['debug']['node_id'] = $node_id;
            
            // First, let's check if the table exists and get its structure
            $table_check_sql = "SHOW TABLES LIKE 'cfield_design_values'";
            logToFile("Table check SQL: " . $table_check_sql);
            
            $table_result = $direct_db->query($table_check_sql);
            if (!$table_result) {
                throw new Exception("Table check failed: " . $direct_db->error);
            }
            
            if ($table_result->num_rows == 0) {
                logToFile("Table cfield_design_values does not exist");
                throw new Exception("Table cfield_design_values does not exist");
            } else {
                logToFile("Table cfield_design_values exists");
            }
            
            // Now try the simple query
            $check_sql = "SELECT COUNT(*) as cnt FROM cfield_design_values WHERE field_id = {$field_id} AND node_id = {$node_id}";
            logToFile("Simple check query: " . $check_sql);
            
            $check_result = $direct_db->query($check_sql);
            if (!$check_result) {
                logToFile("Simple check query failed: " . $direct_db->error);
                throw new Exception("Check query failed: " . $direct_db->error);
            }
            
            $check_row = $check_result->fetch_assoc();
            $exists = $check_row['cnt'] > 0;
            logToFile("Record exists: " . ($exists ? 'yes' : 'no'));
            $response['debug']['record_exists'] = $exists;
            
            if ($exists) {
                // Update existing record
                $update_sql = "UPDATE cfield_design_values SET value = '" . $direct_db->real_escape_string($priority) . "' WHERE field_id = {$field_id} AND node_id = {$node_id}";
                logToFile("Update SQL: " . $update_sql);
                
                $update_result = $direct_db->query($update_sql);
                if (!$update_result) {
                    throw new Exception("Update failed: " . $direct_db->error);
                }
                
                $response['debug']['action'] = 'Updated existing record';
                logToFile("Update successful");
                
            } else {
                // Insert new record
                $insert_sql = "INSERT INTO cfield_design_values (field_id, node_id, value) VALUES ({$field_id}, {$node_id}, '" . $direct_db->real_escape_string($priority) . "')";
                logToFile("Insert SQL: " . $insert_sql);
                
                $insert_result = $direct_db->query($insert_sql);
                if (!$insert_result) {
                    throw new Exception("Insert failed: " . $direct_db->error);
                }
                
                $response['debug']['action'] = 'Inserted new record';
                logToFile("Insert successful");
            }
            
            $direct_db->close();
            
            $response['success'] = true;
            $response['message'] = 'Priority updated successfully using direct database connection';
            logToFile("Priority update completed successfully");
            
        } catch (Exception $db_e) {
            $response['debug']['step5'] = 'Database operation failed: ' . $db_e->getMessage();
            logToFile("Step 5 FAILED: " . $db_e->getMessage());
            logToFile("Database error trace: " . $db_e->getTraceAsString());
            $response['message'] = 'Database operation error: ' . $db_e->getMessage();
        }
        
    } else {
        $response['message'] = 'Invalid parameters';
        logToFile("Invalid parameters: tcvID=$tcvID, priority='$priority', field_id=$field_id");
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
    logToFile("Fatal error trace: " . $e->getTraceAsString());
}

// Log final response
logToFile("Final response: " . json_encode($response));
logToFile("=== AJAX Priority Update Ended ===\n");

echo json_encode($response);
?>
