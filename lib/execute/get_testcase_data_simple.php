<?php
// Disable all error reporting and output
error_reporting(0);
ini_set('display_errors', 0);

// Start output buffering to catch any unwanted output
ob_start();
@ini_set('max_execution_time', '300');
@set_time_limit(300);
@ini_set('default_socket_timeout', '300');

// TEMP: enable developer mode so detailed errors are returned to the client during debugging
if (!defined('DEVMODE')) {
    define('DEVMODE', false);
}

// Get tcversion_id parameter
$tcversion_id = isset($_REQUEST['tcversion_id']) ? intval($_REQUEST['tcversion_id']) : 0;

// Log the received parameter for debugging
error_log('Received tcversion_id: ' . $tcversion_id);

// Initialize response array
$response = array(
    'success' => false,
    'data' => array(),
    'message' => 'No data found'
);

// Determine if verbose logging is enabled (explicit only)
$debug_flag = isset($_REQUEST['_debug']) ? strtolower((string)$_REQUEST['_debug']) : '';
$log_enabled = in_array($debug_flag, array('1','true','yes'), true);
$log_enabled=true;

// Ensure logs directory exists next to this file
$logs_dir = __DIR__ . '/logs';
if (!is_dir($logs_dir)) {
    // Try to create directory with safe permissions
    @mkdir($logs_dir, 0755, true);
}
// If directory still does not exist or not writable, fallback to temp stream
$log_target_dir_ok = is_dir($logs_dir) && is_writable($logs_dir);

// Initialize log file path (use in-memory stream when logging disabled)
$log_filename = 'get_testcase_data_' . date('Y-m-d_His') . '.log';
$log_file = ($log_enabled && $log_target_dir_ok) ? ($logs_dir . '/' . $log_filename) : 'php://temp';

// Start logging - create file if it doesn't exist
$initial_log = "\n-------- SCRIPT START: " . date('Y-m-d H:i:s') . " --------\n";
$initial_log .= "Script: " . __FILE__ . "\n";
$initial_log .= "Log file: " . $log_file . "\n";
$initial_log .= "Received tcversion_id: " . var_export($tcversion_id, true) . "\n";
$initial_log .= "Request method: " . $_SERVER['REQUEST_METHOD'] . "\n";
$initial_log .= "Request parameters: " . print_r($_REQUEST, true) . "\n\n";
if ($log_enabled) {
    file_put_contents($log_file, $initial_log, FILE_APPEND);
}

// Log environment facts helpful for debugging
if ($log_enabled) {
    $env_log = '';
    $env_log .= 'Logs dir exists: ' . (is_dir($logs_dir) ? 'yes' : 'no') . "\n";
    $env_log .= 'Logs dir writable: ' . (is_writable($logs_dir) ? 'yes' : 'no') . "\n";
    $env_log .= 'Log file path in use: ' . $log_file . "\n";
    $env_log .= 'PHP version: ' . PHP_VERSION . "\n";
    $env_log .= 'mysqli client info: ' . (function_exists('mysqli_get_client_info') ? mysqli_get_client_info() : 'n/a') . "\n";
    $env_log .= 'mysqlnd loaded: ' . (extension_loaded('mysqlnd') ? 'yes' : 'no') . "\n\n";
    file_put_contents($log_file, $env_log, FILE_APPEND);
}

// Include main TestLink database configuration file
require_once(__DIR__ . '/../../config_db.inc.php');

try {
    // Log database connection attempt
    if ($log_enabled) {
        file_put_contents($log_file, "Attempting database connection to " . DB_HOST . "...\n", FILE_APPEND);
    }
    
    // Create database connection with explicit timeouts to avoid long hangs
    $db = mysqli_init();
    if ($db === false) {
        file_put_contents($log_file, "MySQL init FAILED\n", FILE_APPEND);
        throw new Exception('Database init failed');
    }
    // Set timeouts (seconds) - increased for large database and slow connections
    if (defined('MYSQLI_OPT_CONNECT_TIMEOUT')) { @mysqli_options($db, MYSQLI_OPT_CONNECT_TIMEOUT, 30); }
    if (defined('MYSQLI_OPT_READ_TIMEOUT'))    { @mysqli_options($db, MYSQLI_OPT_READ_TIMEOUT, 180); }
    // Ensure utf8mb4
    @mysqli_options($db, MYSQLI_INIT_COMMAND, "SET NAMES utf8mb4");
    // Real connect
    $connected = @mysqli_real_connect($db, DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if (!$connected || $db->connect_error) {
        $connErr = $db->connect_error ?: 'unknown error';
        file_put_contents($log_file, "Database connection FAILED: {$connErr}\n", FILE_APPEND);
        throw new Exception('Database connection failed: ' . $connErr);
    }
    
    // Log successful connection
    file_put_contents($log_file, "Database connection SUCCESSFUL\n", FILE_APPEND);
    // Set charset explicitly and tighten session timeouts - increased for large database
    @$db->set_charset('utf8mb4');
    @$db->query("SET SESSION wait_timeout=300, interactive_timeout=600");
    
    // Query to get test case data from the view
    $query = "
        SELECT 
            tcversion_id,
            testcase_name,
            Scenario_ID,
            Sub_Scenario,
            Primary_Module,
            Test_Type,
            Test_Script,
            Test_Execution_Path,
            Expected_Results,
            E_R_Process
        FROM vw_testcase_summary
        WHERE tcversion_id = ?
    ";
    
    // Log the complete query for debugging
    $debug_query = str_replace('?', $tcversion_id, $query);
    $log_message = "[DEBUG] Executing query:\n$debug_query\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
    
    // Prepare statement
    $stmt = $db->prepare($query);
    if (!$stmt) {
        $error_msg = 'Prepare failed: ' . $db->error;
        file_put_contents($log_file, "[ERROR] Query preparation FAILED: {$db->error}\n", FILE_APPEND);
        throw new Exception($error_msg);
    }
    
    file_put_contents($log_file, "[INFO] Query prepared successfully\n", FILE_APPEND);
    
    // Bind parameters and execute
    $stmt->bind_param('i', $tcversion_id);
    file_put_contents($log_file, "[DEBUG] Parameter bound: tcversion_id = $tcversion_id\n", FILE_APPEND);
    
    // Log the complete query with all parameters
    $complete_query = str_replace('?', "'$tcversion_id'", $query);
    file_put_contents($log_file, "[DEBUG] Complete query to execute:\n$complete_query\n", FILE_APPEND);
    
    // Execute the query with retry-once logic for transient disconnects/timeouts
    $execute_result = $stmt->execute();
    if (!$execute_result) {
        $errNo = $stmt->errno;
        $errMsg = $stmt->error;
        file_put_contents($log_file, "Query execution FAILED: [{$errNo}] {$errMsg}\n", FILE_APPEND);
        $shouldRetry = ($errNo == 2006 /* server gone away */ || $errNo == 2013 /* lost connection */ || stripos($errMsg, 'gone away') !== false || stripos($errMsg, 'timeout') !== false);
        if ($shouldRetry) {
            file_put_contents($log_file, "Retrying once: reconnecting with higher read timeout...\n", FILE_APPEND);
            // Cleanup
            @$stmt->close();
            @$db->close();
            // Reconnect with higher timeouts for large database
            $db = mysqli_init();
            if (defined('MYSQLI_OPT_CONNECT_TIMEOUT')) { @mysqli_options($db, MYSQLI_OPT_CONNECT_TIMEOUT, 30); }
            if (defined('MYSQLI_OPT_READ_TIMEOUT'))    { @mysqli_options($db, MYSQLI_OPT_READ_TIMEOUT, 240); }
            @mysqli_options($db, MYSQLI_INIT_COMMAND, "SET NAMES utf8mb4");
            $connected = @mysqli_real_connect($db, DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if (!$connected || $db->connect_error) {
                $connErr = $db->connect_error ?: 'unknown error';
                file_put_contents($log_file, "Reconnect FAILED: {$connErr}\n", FILE_APPEND);
                throw new Exception('Database reconnect failed: ' . $connErr);
            }
            file_put_contents($log_file, "Reconnect SUCCESSFUL\n", FILE_APPEND);
            @$db->set_charset('utf8mb4');
            @$db->query("SET SESSION wait_timeout=600, interactive_timeout=600");
            // Re-prepare and re-bind
            $stmt = $db->prepare($query);
            if (!$stmt) {
                file_put_contents($log_file, "[ERROR] Re-prepare FAILED: {$db->error}\n", FILE_APPEND);
                throw new Exception('Re-prepare failed: ' . $db->error);
            }
            $stmt->bind_param('i', $tcversion_id);
            file_put_contents($log_file, "[DEBUG] Retry: parameter bound tcversion_id={$tcversion_id}\n", FILE_APPEND);
            $execute_result = $stmt->execute();
            if (!$execute_result) {
                file_put_contents($log_file, "Retry execute FAILED: [{$stmt->errno}] {$stmt->error}\n", FILE_APPEND);
                throw new Exception('Execute failed after retry: ' . $stmt->error);
            }
            file_put_contents($log_file, "Retry execute SUCCESSFUL\n", FILE_APPEND);
        } else {
            throw new Exception('Execute failed: ' . $errMsg);
        }
    } else {
        file_put_contents($log_file, "Query executed successfully\n", FILE_APPEND);
    }
    
    // Try to get result via mysqlnd; if unavailable, fall back to bind_result
    $row = null;
    $num_rows = 0;
    $result = @$stmt->get_result();
    if ($result instanceof mysqli_result) {
        if ($log_enabled) { file_put_contents($log_file, "Got result set (mysqlnd)\n", FILE_APPEND); }
        $num_rows = $result->num_rows;
        if ($log_enabled) { file_put_contents($log_file, "Number of rows found: $num_rows\n", FILE_APPEND); }
        if ($num_rows > 0) {
            $row = $result->fetch_assoc();
        }
    } else {
        // mysqlnd not available: use bind_result
        if ($log_enabled) { file_put_contents($log_file, "get_result() unavailable, using bind_result fallback\n", FILE_APPEND); }
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        if ($log_enabled) { file_put_contents($log_file, "Number of rows found (store_result): $num_rows\n", FILE_APPEND); }
        if ($num_rows > 0) {
            $c_tcversion_id = $c_testcase_name = $c_Scenario_ID = $c_Sub_Scenario = $c_Primary_Module = '';
            $c_Test_Type = $c_Test_Script = $c_Test_Execution_Path = $c_Expected_Results = $c_E_R_Process = '';
            $stmt->bind_result(
                $c_tcversion_id,
                $c_testcase_name,
                $c_Scenario_ID,
                $c_Sub_Scenario,
                $c_Primary_Module,
                $c_Test_Type,
                $c_Test_Script,
                $c_Test_Execution_Path,
                $c_Expected_Results,
                $c_E_R_Process
            );
            if ($stmt->fetch()) {
                $row = array(
                    'tcversion_id' => $c_tcversion_id,
                    'testcase_name' => $c_testcase_name,
                    'Scenario_ID' => $c_Scenario_ID,
                    'Sub_Scenario' => $c_Sub_Scenario,
                    'Primary_Module' => $c_Primary_Module,
                    'Test_Type' => $c_Test_Type,
                    'Test_Script' => $c_Test_Script,
                    'Test_Execution_Path' => $c_Test_Execution_Path,
                    'Expected_Results' => $c_Expected_Results,
                    'E_R_Process' => $c_E_R_Process,
                );
            }
        }
    }

    if ($row) {
        // Set success response
        $response['success'] = true;
        $response['message'] = '';
        
        // Map database fields to response fields
        $response['data'] = array(
            'scenario_id' => $row['Scenario_ID'] ?: '',
            'sub_scenario' => $row['Sub_Scenario'] ?: '',
            'test_script' => $row['Test_Script'] ?: '',
            'expected_results' => $row['Expected_Results'] ?: '',
            'er_process' => $row['E_R_Process'] ?: '',
            'testcase_name' => $row['testcase_name'] ?: '',
            'primary_module' => $row['Primary_Module'] ?: '',
            'test_type' => $row['Test_Type'] ?: '',
            'test_execution_path' => $row['Test_Execution_Path'] ?: '',
            'test_data' => '', // This might come from another table
            'priority' => '', // This might come from another table
            'execution_status' => '', // This might come from another table
            'notes' => '' // This will be filled in by the JavaScript from the form
        );
        
        if ($log_enabled) {
            $log_data = "\nFormatted response data:\n";
            foreach ($response['data'] as $key => $value) {
                $log_data .= "$key: " . print_r($value, true) . "\n";
            }
            file_put_contents($log_file, $log_data, FILE_APPEND);
        }
    } else {
        // No data found for this ID
        $response['message'] = 'No test case data found for ID: ' . $tcversion_id;
    }
    
    // Close statement and connection
    $stmt->close();
    $db->close();
    file_put_contents($log_file, "Database connection closed\n", FILE_APPEND);
    
} catch (Exception $e) {
    // Log the error to both error_log and our custom log file
    $error_message = 'Error in get_testcase_data_simple.php: ' . $e->getMessage();
    error_log($error_message);
    
    // Log detailed error information to our log file
    file_put_contents($log_file, "\nEXCEPTION OCCURRED: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    file_put_contents($log_file, "Error message: {$e->getMessage()}\n", FILE_APPEND);
    file_put_contents($log_file, "Stack trace:\n{$e->getTraceAsString()}\n", FILE_APPEND);
    
    // Set error response
    $response['success'] = false;
    $response['message'] = 'Database error: ' . $e->getMessage();
    
    // For security, don't expose detailed error messages to the client in production
    if (!defined('DEVMODE') || !DEVMODE) {
        $response['message'] = 'An error occurred while fetching test case data';
    }
    
    // Log the response we're sending back
    file_put_contents($log_file, "Response being sent:\n" . print_r($response, true) . "\n", FILE_APPEND);
}

// Add debug info to the response for troubleshooting
$response['debug'] = array(
    'received_id' => $tcversion_id,
    'timestamp' => date('Y-m-d H:i:s')
);

// Clear any output that might have been generated
while (ob_get_level()) {
    ob_end_clean();
}

// Set headers to ensure proper JSON response
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Log the final response being sent back to the client
file_put_contents($log_file, "\n-------- FINAL RESPONSE: " . date('Y-m-d H:i:s') . " --------\n", FILE_APPEND);
file_put_contents($log_file, print_r($response, true) . "\n", FILE_APPEND);

// Generate JSON response
$json_response = json_encode($response);

// Log the actual JSON being sent
file_put_contents($log_file, "JSON Response: " . $json_response . "\n", FILE_APPEND);
file_put_contents($log_file, "JSON Response Length: " . strlen($json_response) . " bytes\n", FILE_APPEND);
file_put_contents($log_file, "-------- END OF REQUEST --------\n\n", FILE_APPEND);

// Return JSON response
echo $json_response;

// Flush output to ensure it's sent
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    flush();
}

// End script execution to prevent any additional output
exit;
