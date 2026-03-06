<?php
/**
 * Custom Bug Tracking Integration System for TestLink
 * Supports multiple Redmine, Jira, Bugzilla integrations per project
 * 
 * @filesource  custom_bugtrack_integrator.php
 * @author      TestLink Custom Integration
 * @version     1.0
 * @created     2025-02-23
 */

// -----------------------------------------------------------------------
// FIX: Start output buffering as the VERY FIRST THING.
// TestLink's database error handler calls die() after printing HTML,
// which bypasses all later ob_end_clean() calls and flushes the buffer
// to the client on script shutdown. By buffering from line 1 and
// registering a shutdown handler, we intercept that and emit clean JSON.
// -----------------------------------------------------------------------
ob_start();

// Suppress HTML error output globally - we handle errors ourselves
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Shutdown handler: if something calls die() or exit() unexpectedly
// (e.g. TestLink's DB error handler), discard any HTML that was
// buffered and replace it with a proper JSON error response.
register_shutdown_function(function() {
    // Only act if JSON hasn't been cleanly sent yet
    // (outputJson() sets this flag before echoing its payload)
    global $jsonAlreadySent, $logFile;
    if (!empty($jsonAlreadySent)) {
        return; // outputJson() already handled output cleanly
    }

    // Discard whatever HTML/text was buffered (e.g. DB error HTML)
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    // Check for a fatal PHP error
    $error = error_get_last();
    $message = 'Unexpected script termination (TestLink DB error or fatal PHP error).';
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = 'Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line'];
    }

    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    $payload = json_encode(['success' => false, 'message' => $message, 'data' => null, 'debug' => []]);
    if ($logFile) {
        file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] SHUTDOWN HANDLER: ' . $message . "\n", FILE_APPEND | LOCK_EX);
    }
    echo $payload;
});

// Set JSON header first
header('Content-Type: application/json');

// Enable comprehensive logging
$logFile = __DIR__ . '/custom_bugtrack_integrator.log';
function writeLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

writeLog("=== API Request Started ===");
writeLog("Request URI: " . $_SERVER['REQUEST_URI']);
writeLog("Request method: " . $_SERVER['REQUEST_METHOD']);
writeLog("POST data: " . json_encode($_POST));
writeLog("GET data: " . json_encode($_GET));

define('NOCRYPT', true);

// Get the correct base path for both web and CLI contexts
$basePath = dirname(__DIR__, 2); // Go up 2 levels from lib/execute to the root
if (!file_exists($basePath . '/config.inc.php')) {
    // Fallback for different directory structures
    $basePath = __DIR__ . '/../..';
}

require_once($basePath . '/config.inc.php');
require_once($basePath . '/lib/functions/common.php');

$db = new database(DB_TYPE);
doDBConnect($db, database::ONERROREXIT);

// Simple debug at very top to track execution
writeLog("DEBUG: Script started at " . date('Y-m-d H:i:s'));
writeLog("DEBUG: Database initialized manually for API call");

// Initialize response
$response = array(
    'success' => false,
    'message' => '',
    'data' => null,
    'debug' => array()
);

// Log function for debugging
function logDebug($message) {
    global $response;
    $response['debug'][] = $message;
    writeLog("DEBUG: $message");
    error_log("[CustomBugTracker] " . $message);
}

// Function to safely output JSON and exit
function outputJson($response) {
    global $logFile, $jsonAlreadySent;
    
    // Clear all output buffers and disable output buffering
    if (ob_get_level()) {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }
    
    // Turn off all error reporting and display
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    
    // Set clean JSON header
    header('Content-Type: application/json');
    
    // Output clean JSON
    $jsonOutput = json_encode($response);
    writeLog("JSON Response: " . $jsonOutput);

    // Signal the shutdown handler that we've cleanly handled output
    $jsonAlreadySent = true;

    echo $jsonOutput;
    exit;
}

// Get action from request
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// Check if database tables exist
function checkTablesExist($db) {
    $tables = array('custom_bugtrack_integrations', 'custom_bugtrack_project_mapping');
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '" . $table . "'";
        $result = $db->exec_query($sql);
        $row = $db->fetch_array($result);
        
        if (!$row) {
            logDebug("Table '$table' does NOT exist");
            return false;
        } else {
            logDebug("Table '$table' exists");
        }
    }
    return true;
}

// Temporarily bypass table check to isolate the issue
// if (!checkTablesExist($db)) {
//     $response['message'] = 'Database tables not found. Please install the custom bug tracking integration schema first.';
//     outputJson($response);
// }

try {
    error_log("DEBUG: About to enter switch statement with action: " . $action);
    writeLog("DEBUG: About to enter switch statement with action: " . $action);
    switch ($action) {
        case 'list_integrations':
            writeLog("DEBUG: Entering handleListIntegrations");
            handleListIntegrations($db);
            break;
        case 'list_integrations_for_project':
            writeLog("DEBUG: Entering handleListIntegrationsForProject");
            writeLog("DEBUG: About to include custom_issue_integration_safe.php");
            try {
                require_once(dirname(__FILE__) . '/custom_issue_integration_safe.php');
                writeLog("DEBUG: custom_issue_integration_safe.php included successfully");
                handleListIntegrationsForProject($db);
            } catch (Exception $e) {
                writeLog("FATAL: Failed to include custom_issue_integration_safe.php: " . $e->getMessage());
                $response['message'] = 'Failed to include integration functions: ' . $e->getMessage();
                outputJson($response);
            }
            break;
        case 'add_integration':
            handleAddIntegration($db);
            break;
        case 'update_integration':
            handleUpdateIntegration($db);
            break;
        case 'delete_integration':
            handleDeleteIntegration($db);
            break;
        case 'toggle_integration':
            handleToggleIntegration($db);
            break;
        case 'test_connection':
            handleTestConnection($db);
            break;
        case 'list_project_mappings':
            handleListProjectMappings($db);
            break;
        case 'add_project_mapping':
            handleAddProjectMapping($db);
            break;
        case 'remove_project_mapping':
            handleRemoveProjectMapping($db);
            break;
        case 'get_integration_for_project':
            handleGetIntegrationForProject($db);
            break;
        case 'list_projects':
            handleListProjects($db);
            break;
        case 'create_issue':
            handleCreateIssue($db);
            break;
        default:
            $response['message'] = 'Unknown action: ' . $action;
            outputJson($response);
            break;
    }
} catch (Exception $e) {
    $response['message'] = 'Fatal error: ' . $e->getMessage();
    logDebug('Exception: ' . $e->getMessage());
    logDebug('Stack trace: ' . $e->getTraceAsString());
    outputJson($response);
}

/**
 * List all integrations
 */
function handleListIntegrations($db) {
    global $response;
    
    $sql = "SELECT id, name, type, url, api_key, username, password, project_key, is_active, default_priority, 
                   created_on, updated_on 
            FROM custom_bugtrack_integrations 
            ORDER BY name";
    
    $result = $db->exec_query($sql);
    $integrations = array();
    
    while ($row = $db->fetch_array($result)) {
        $integrations[] = $row;
    }
    
    $response['success'] = true;
    $response['data'] = $integrations;
    outputJson($response);
}

/**
 * List integrations for a specific project (for picker UI)
 * Returns only credentials-stripped data for security
 */
function handleListIntegrationsForProject($db) {
    global $response;
    
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] handleListIntegrationsForProject called\n",
        FILE_APPEND
    );
    
    // Get tproject_id from GET parameter
    $tproject_id = intval($_GET['tproject_id'] ?? 0);
    
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] tproject_id from GET: $tproject_id\n",
        FILE_APPEND
    );
    
    if (!$tproject_id) {
        file_put_contents(
            'logs/multi_integration_debug.log',
            date('[Y-m-d H:i:s] ') . "[ERROR] tproject_id required but not provided\n",
            FILE_APPEND
        );
        $response['success'] = false;
        $response['message'] = 'tproject_id required';
        outputJson($response);
        return;
    }
    
    // Include the custom integration functions
    require_once(dirname(__FILE__) . '/custom_issue_integration_safe.php');
    
    // Get integrations for this project
    $integrations = getIntegrationsForProject($db, $tproject_id);
    
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] Found " . count($integrations) . " integrations for project $tproject_id\n",
        FILE_APPEND
    );
    
    // Return success response
    $response['success'] = true;
    $response['message'] = 'Integrations retrieved successfully';
    $response['status'] = 'ok';
    $response['tproject_id'] = $tproject_id;
    $response['integrations'] = $integrations; // credentials already stripped in function
    $response['count'] = count($integrations);
    $response['timestamp'] = date('Y-m-d H:i:s'); // Force cache refresh
    
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] Response: " . json_encode($response) . "\n",
        FILE_APPEND
    );
    
    outputJson($response);
}

/**
 * Add new integration
 */
function handleAddIntegration($db) {
    global $response;
    
    logDebug("handleAddIntegration called");
    
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? 'REDMINE';
    $url = $_POST['url'] ?? '';
    $api_key = $_POST['api_key'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $project_key = $_POST['project_key'] ?? '';
    $default_priority = $_POST['default_priority'] ?? 'Normal';
    
    logDebug("POST data received - name: $name, type: $type, url: $url");
    
    if (empty($name) || empty($url)) {
        $response['message'] = 'Name and URL are required';
        outputJson($response);
        return;
    }
    
    // Get current user ID safely
    $created_by = 1; // Default to admin user if session not available
    if (isset($_SESSION['currentUser']) && isset($_SESSION['currentUser']->dbID)) {
        $created_by = $_SESSION['currentUser']->dbID;
        logDebug("Using session user ID: $created_by");
    } else {
        logDebug("Session not available, using default user ID: $created_by");
    }
    
    // Check if name already exists
    $checkSql = "SELECT id FROM custom_bugtrack_integrations WHERE name = " . $db->db->quote($name);
    logDebug("Checking for existing integration with SQL: $checkSql");
    
    $existing = $db->fetchFirstRow($checkSql);
    
    if ($existing) {
        $response['message'] = 'Integration with this name already exists';
        outputJson($response);
        return;
    }
    
    $sql = "INSERT INTO custom_bugtrack_integrations 
            (name, type, url, api_key, username, password, project_key, default_priority, created_by) 
            VALUES (" . $db->db->quote($name) . ", " . $db->db->quote($type) . ", " . 
            $db->db->quote($url) . ", " . $db->db->quote($api_key) . ", " . 
            $db->db->quote($username) . ", " . $db->db->quote($password) . ", " . 
            $db->db->quote($project_key) . ", " . $db->db->quote($default_priority) . ", " . 
            $created_by . ")";
    
    logDebug("Insert SQL: $sql");
    
    try {
        $result = $db->exec_query($sql);
        
        if ($result) {
            $insert_id = $db->insert_id();
            logDebug("Integration added successfully with ID: $insert_id");
            $response['success'] = true;
            $response['message'] = 'Integration added successfully';
            $response['data'] = array('id' => $insert_id);
        } else {
            logDebug("Database query failed without exception");
            $response['message'] = 'Failed to add integration: Database query failed';
        }
    } catch (Exception $e) {
        logDebug("Exception in database query: " . $e->getMessage());
        $response['message'] = 'Failed to add integration: ' . $e->getMessage();
    }
    
    outputJson($response);
}

/**
 * Update existing integration
 */
function handleUpdateIntegration($db) {
    global $response;
    
    $id = intval($_POST['id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? 'REDMINE';
    $url = $_POST['url'] ?? '';
    $api_key = $_POST['api_key'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $project_key = $_POST['project_key'] ?? '';
    $default_priority = $_POST['default_priority'] ?? 'Normal';
    
    if ($id <= 0 || empty($name) || empty($url)) {
        $response['message'] = 'ID, Name and URL are required';
        outputJson($response);
        return;
    }
    
    // Check if name already exists for other integration
    $checkSql = "SELECT id FROM custom_bugtrack_integrations WHERE name = " . $db->db->quote($name) . " AND id != $id";
    $existing = $db->fetchFirstRow($checkSql);
    
    if ($existing) {
        $response['message'] = 'Integration with this name already exists';
        outputJson($response);
        return;
    }
    
    $sql = "UPDATE custom_bugtrack_integrations 
            SET name = " . $db->db->quote($name) . ", 
                type = " . $db->db->quote($type) . ", 
                url = " . $db->db->quote($url) . ", 
                api_key = " . $db->db->quote($api_key) . ", 
                username = " . $db->db->quote($username) . ", 
                password = " . $db->db->quote($password) . ", 
                project_key = " . $db->db->quote($project_key) . ", 
                default_priority = " . $db->db->quote($default_priority) . ", 
                updated_by = " . $_SESSION['currentUser']->dbID . ",
                updated_on = CURRENT_TIMESTAMP
            WHERE id = $id";
    
    $result = $db->exec_query($sql);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Integration updated successfully';
    } else {
        $response['message'] = 'Failed to update integration';
    }
    
    outputJson($response);
}

/**
 * Delete integration
 */
function handleDeleteIntegration($db) {
    global $response;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        $response['message'] = 'Valid ID is required';
        outputJson($response);
        return;
    }
    
    // Check if integration is mapped to any projects
    $checkSql = "SELECT COUNT(*) as count FROM custom_bugtrack_project_mapping WHERE integration_id = $id";
    $result = $db->fetchFirstRow($checkSql);
    
    if ($result['count'] > 0) {
        $response['message'] = 'Cannot delete integration that is mapped to projects. Remove mappings first.';
        outputJson($response);
        return;
    }
    
    $sql = "DELETE FROM custom_bugtrack_integrations WHERE id = $id";
    $result = $db->exec_query($sql);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Integration deleted successfully';
    } else {
        $response['message'] = 'Failed to delete integration';
    }
    
    outputJson($response);
}

/**
 * Toggle integration active status
 */
function handleToggleIntegration($db) {
    global $response;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        $response['message'] = 'Valid ID is required';
        outputJson($response);
        return;
    }
    
    $sql = "UPDATE custom_bugtrack_integrations 
            SET is_active = NOT is_active, updated_by = " . $_SESSION['currentUser']->dbID . ",
                updated_on = CURRENT_TIMESTAMP
            WHERE id = $id";
    
    $result = $db->exec_query($sql);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Integration status updated successfully';
    } else {
        $response['message'] = 'Failed to update integration status';
    }
    
    outputJson($response);
}

/**
 * Test connection to bug tracker
 */
function handleTestConnection($db) {
    global $response;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        $response['message'] = 'Valid ID is required';
        outputJson($response);
        return;
    }
    
    // Get integration details
    $sql = "SELECT * FROM custom_bugtrack_integrations WHERE id = $id";
    $integration = $db->fetchFirstRow($sql);
    
    if (!$integration) {
        $response['message'] = 'Integration not found';
        outputJson($response);
        return;
    }
    
    $startTime = microtime(true);
    
    try {
        if ($integration['type'] === 'REDMINE') {
            $result = testRedmineConnection($integration);
        } elseif ($integration['type'] === 'JIRA') {
            $result = testJiraConnection($integration);
        } elseif ($integration['type'] === 'BUGZILLA') {
            $result = testBugzillaConnection($integration);
        } else {
            $result = array('success' => false, 'message' => 'Unsupported integration type');
        }
        
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Log the test
        logIntegrationTest($db, $id, $result, $executionTime);
        
        $response['success'] = $result['success'];
        $response['message'] = $result['message'];
        $response['execution_time'] = $executionTime;
        
    } catch (Exception $e) {
        $response['message'] = 'Connection test failed: ' . $e->getMessage();
        logIntegrationTest($db, $id, array('success' => false, 'message' => $e->getMessage()), round((microtime(true) - $startTime) * 1000));
    }
    
    outputJson($response);
}

/**
 * Test Redmine connection
 */
function testRedmineConnection($integration) {
    $url = rtrim($integration['url'], '/') . '/issues.json?limit=1';
    $headers = array(
        'Content-Type: application/json',
        'X-Redmine-API-Key: ' . $integration['api_key']
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return array('success' => false, 'message' => 'CURL Error: ' . $error);
    }
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['issues'])) {
            return array('success' => true, 'message' => 'Connection successful');
        }
    }
    
    return array('success' => false, 'message' => 'HTTP ' . $httpCode . ': ' . $response);
}

/**
 * Test Jira connection
 */
function testJiraConnection($integration) {
    $url = rtrim($integration['url'], '/') . '/rest/api/2/myself';
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($integration['username'] . ':' . $integration['password'])
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return array('success' => false, 'message' => 'CURL Error: ' . $error);
    }
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['name'])) {
            return array('success' => true, 'message' => 'Connection successful for user: ' . $data['name']);
        }
    }
    
    return array('success' => false, 'message' => 'HTTP ' . $httpCode . ': ' . $response);
}

/**
 * Test Bugzilla connection
 */
function testBugzillaConnection($integration) {
    $url = rtrim($integration['url'], '/') . '/rest/version';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return array('success' => false, 'message' => 'CURL Error: ' . $error);
    }
    
    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if (isset($data['version'])) {
            return array('success' => true, 'message' => 'Connection successful - Bugzilla version: ' . $data['version']);
        }
    }
    
    return array('success' => false, 'message' => 'HTTP ' . $httpCode . ': ' . $response);
}

/**
 * Log integration test
 */
function logIntegrationTest($db, $integrationId, $result, $executionTime) {
    $sql = "INSERT INTO custom_bugtrack_integration_log 
            (integration_id, action, status, error_message, execution_time_ms, created_by) 
            VALUES ($integrationId, 'TEST_CONNECTION', '" . ($result['success'] ? 'SUCCESS' : 'ERROR') . "', 
                    " . $db->db->quote($result['message']) . ", $executionTime, " . $_SESSION['currentUser']->dbID . ")";
    
    $db->exec_query($sql);
}

/**
 * List project mappings
 */
function handleListProjectMappings($db) {
    global $response;
    
    // testprojects has no name column - name lives in nodes_hierarchy
    $sql = "SELECT m.id, m.tproject_id, m.is_active, 
                   nh.name as project_name, i.name as integration_name, i.type
            FROM custom_bugtrack_project_mapping m
            JOIN testprojects tp ON m.tproject_id = tp.id
            JOIN nodes_hierarchy nh ON nh.id = tp.id
            JOIN custom_bugtrack_integrations i ON m.integration_id = i.id
            ORDER BY nh.name, i.name";
    
    $result = $db->exec_query($sql);
    $mappings = array();
    
    while ($row = $db->fetch_array($result)) {
        $mappings[] = $row;
    }
    
    $response['success'] = true;
    $response['data'] = $mappings;
    outputJson($response);
}

/**
 * List TestLink projects
 */
function handleListProjects($db) {
    global $response;
    
    logDebug("handleListProjects function called");
    
    try {
        logDebug("Using direct query approach");
        
        // IMPORTANT: In TestLink, the project *name* is NOT stored in the
        // testprojects table. It lives in nodes_hierarchy (joined by id).
        // testprojects only holds metadata columns like notes, prefix,
        // active, tc_counter, is_public.
        //
        // We use INFORMATION_SCHEMA to validate which of the optional
        // metadata columns actually exist in this TestLink version before
        // building the SELECT, so we never reference a missing column.
        $optionalCols    = ['notes', 'prefix', 'active', 'tc_counter', 'is_public'];
        $availableTpCols = []; // qualified columns from testprojects

        $colCheckSql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'testprojects' 
                          AND COLUMN_NAME IN ('" . implode("','", $optionalCols) . "')";
        $colResult = $db->exec_query($colCheckSql);
        while ($col = $db->fetch_array($colResult)) {
            $colName           = $col['COLUMN_NAME'] ?? $col[0];
            $availableTpCols[] = 'tp.' . $colName;
        }

        $hasActive = in_array('tp.active', $availableTpCols);
        $whereSql  = $hasActive ? 'WHERE tp.active = 1' : '';
        $tpColsSql = empty($availableTpCols) ? '' : ', ' . implode(', ', $availableTpCols);

        // JOIN nodes_hierarchy to get the project name — standard TestLink
        // pattern; name is stored in nodes_hierarchy, never in testprojects.
        $sql = "SELECT tp.id, nh.name $tpColsSql
                FROM testprojects tp
                INNER JOIN nodes_hierarchy nh ON nh.id = tp.id
                $whereSql
                ORDER BY nh.name";
        
        logDebug("Safe SQL: " . $sql);
        
        $result = $db->exec_query($sql);
        
        if (!$result) {
            throw new Exception("Direct query failed - check testprojects table");
        }
        
        $projects = array();
        while ($row = $db->fetch_array($result)) {
            $projects[] = $row;
        }
        
        logDebug("Direct query found " . count($projects) . " projects");
        
        // Build the response object
        $projectsObject = array();
        foreach ($projects as $project) {
            if (is_object($project)) {
                $projectId     = intval($project->id);
                $projectName   = strip_tags($project->name   ?? '');
                $projectNotes  = strip_tags($project->notes  ?? '');
                $projectPrefix = $project->prefix   ?? '';
                $projectActive = $project->active   ?? '1';
                $projectCounter= intval($project->tc_counter ?? 0);
                $projectPublic = $project->is_public ?? '1';
            } else {
                $projectId     = intval($project['id']);
                $projectName   = strip_tags($project['name']   ?? '');
                $projectNotes  = strip_tags($project['notes']  ?? '');
                $projectPrefix = $project['prefix']   ?? '';
                $projectActive = $project['active']   ?? '1';
                $projectCounter= intval($project['tc_counter'] ?? 0);
                $projectPublic = $project['is_public'] ?? '1';
            }
            
            $key = ($projectId > 0) ? $projectId : 'project_' . count($projectsObject);
            
            $projectsObject[$key] = array(
                'id'         => $projectId,
                'name'       => $projectName,
                'notes'      => $projectNotes,
                'prefix'     => $projectPrefix,
                'active'     => $projectActive,
                'tc_counter' => $projectCounter,
                'is_public'  => $projectPublic
            );
            
            if (count($projectsObject) <= 3) {
                logDebug("Project: " . print_r($projectsObject[$key], true));
            }
        }
        
        logDebug("Final projects object has " . count($projectsObject) . " items");
        
        $response['success'] = true;
        $response['data']    = $projectsObject;
        $response['count']   = count($projectsObject);
        
        logDebug("About to output JSON response");
        outputJson($response);
        
    } catch (Exception $e) {
        logDebug("Error in handleListProjects: " . $e->getMessage());
        logDebug("Exception trace: " . $e->getTraceAsString());
        $response['success'] = false;
        $response['message'] = 'Error loading projects: ' . $e->getMessage();
        outputJson($response);
    }
}

/**
 * Add project mapping
 */
function handleAddProjectMapping($db) {
    global $response;
    
    logDebug("handleAddProjectMapping called");
    
    $tproject_id = intval($_POST['tproject_id'] ?? 0);
    $integration_id = intval($_POST['integration_id'] ?? 0);
    
    logDebug("POST data received - tproject_id: $tproject_id, integration_id: $integration_id");
    
    if ($tproject_id <= 0 || $integration_id <= 0) {
        $response['message'] = 'Valid project ID and integration ID are required';
        outputJson($response);
        return;
    }
    
    // Get current user ID safely
    $created_by = 1; // Default to admin user if session not available
    if (isset($_SESSION['currentUser']) && isset($_SESSION['currentUser']->dbID)) {
        $created_by = $_SESSION['currentUser']->dbID;
        logDebug("Using session user ID: $created_by");
    } else {
        logDebug("Session not available, using default user ID: $created_by");
    }
    
    // Check if mapping already exists
    $checkSql = "SELECT id FROM custom_bugtrack_project_mapping 
                 WHERE tproject_id = $tproject_id AND integration_id = $integration_id";
    logDebug("Checking for existing mapping with SQL: $checkSql");
    
    $existing = $db->fetchFirstRow($checkSql);
    
    if ($existing) {
        $response['message'] = 'This mapping already exists';
        outputJson($response);
        return;
    }
    
    $sql = "INSERT INTO custom_bugtrack_project_mapping 
            (tproject_id, integration_id, created_by) 
            VALUES ($tproject_id, $integration_id, $created_by)";
    
    logDebug("Insert SQL: $sql");
    
    try {
        $result = $db->exec_query($sql);
        
        if ($result) {
            $insert_id = $db->insert_id();
            logDebug("Project mapping added successfully with ID: $insert_id");
            $response['success'] = true;
            $response['message'] = 'Project mapping added successfully';
            $response['data'] = array('id' => $insert_id);
        } else {
            logDebug("Database query failed without exception");
            $response['message'] = 'Failed to add project mapping: Database query failed';
        }
    } catch (Exception $e) {
        logDebug("Exception in database query: " . $e->getMessage());
        $response['message'] = 'Failed to add project mapping: ' . $e->getMessage();
    }
    
    outputJson($response);
}

/**
 * Remove project mapping
 */
function handleRemoveProjectMapping($db) {
    global $response;
    
    $id = intval($_POST['id'] ?? 0);
    
    if ($id <= 0) {
        $response['message'] = 'Valid ID is required';
        outputJson($response);
        return;
    }
    
    $sql = "DELETE FROM custom_bugtrack_project_mapping WHERE id = $id";
    $result = $db->exec_query($sql);
    
    if ($result) {
        $response['success'] = true;
        $response['message'] = 'Project mapping removed successfully';
    } else {
        $response['message'] = 'Failed to remove project mapping';
    }
    
    outputJson($response);
}

/**
 * Get integration for a specific project
 */
function handleGetIntegrationForProject($db) {
    global $response;
    
    $tproject_id = intval($_GET['tproject_id'] ?? 0);
    
    if ($tproject_id <= 0) {
        $response['message'] = 'Valid project ID is required';
        outputJson($response);
        return;
    }
    
    $sql = "SELECT i.* FROM custom_bugtrack_integrations i
            JOIN custom_bugtrack_project_mapping m ON i.id = m.integration_id
            WHERE m.tproject_id = $tproject_id AND m.is_active = 1 AND i.is_active = 1
            ORDER BY m.created_on DESC
            LIMIT 1";
    
    $integration = $db->fetchFirstRow($sql);
    
    $response['success'] = true;
    $response['data'] = $integration;
    outputJson($response);
}

/**
 * Create issue in bug tracker
 */
function handleCreateIssue($db) {
    global $response;
    
    $tproject_id = intval($_POST['tproject_id'] ?? 0);
    $tplan_id = intval($_POST['tplan_id'] ?? 0);
    $tc_id = intval($_POST['tc_id'] ?? 0);
    $execution_id = intval($_POST['execution_id'] ?? 0);
    $summary = $_POST['summary'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'Normal';
    
    if ($tproject_id <= 0 || empty($summary)) {
        $response['message'] = 'Project ID and summary are required';
        outputJson($response);
        return;
    }
    
    // Get integration for this project
    $sql = "SELECT i.* FROM custom_bugtrack_integrations i
            JOIN custom_bugtrack_project_mapping m ON i.id = m.integration_id
            WHERE m.tproject_id = $tproject_id AND m.is_active = 1 AND i.is_active = 1
            ORDER BY m.created_on DESC
            LIMIT 1";
    
    $integration = $db->fetchFirstRow($sql);
    
    if (!$integration) {
        $response['message'] = 'No active integration found for this project';
        outputJson($response);
        return;
    }
    
    $startTime = microtime(true);
    
    try {
        if ($integration['type'] === 'REDMINE') {
            $result = createRedmineIssue($integration, $summary, $description, $priority);
        } elseif ($integration['type'] === 'JIRA') {
            $result = createJiraIssue($integration, $summary, $description, $priority);
        } elseif ($integration['type'] === 'BUGZILLA') {
            $result = createBugzillaIssue($integration, $summary, $description, $priority);
        } else {
            $result = array('success' => false, 'message' => 'Unsupported integration type');
        }
        
        $executionTime = round((microtime(true) - $startTime) * 1000);
        
        // Log the issue creation
        logIssueCreation($db, $integration['id'], $tproject_id, $tplan_id, $tc_id, $execution_id, $result, $executionTime);
        
        $response['success'] = $result['success'];
        $response['message'] = $result['message'];
        $response['issue_id'] = $result['issue_id'] ?? null;
        $response['issue_url'] = $result['issue_url'] ?? null;
        
    } catch (Exception $e) {
        $response['message'] = 'Issue creation failed: ' . $e->getMessage();
        logIssueCreation($db, $integration['id'], $tproject_id, $tplan_id, $tc_id, $execution_id, 
                         array('success' => false, 'message' => $e->getMessage()), round((microtime(true) - $startTime) * 1000));
    }
    
    outputJson($response);
}

/**
 * Create Redmine issue via REST API
 */
function createRedmineIssue($integration, $summary, $description, $priority) {
    $url = rtrim($integration['url'], '/') . '/issues.json';
    
    // Map priority to Redmine priority IDs
    $priorityMap = array(
        'Low' => 1,
        'Normal' => 2, 
        'High' => 3,
        'Urgent' => 4
    );
    
    $priorityId = $priorityMap[$priority] ?? 2;
    
    $data = array(
        'issue' => array(
            'project' => $integration['project_key'],
            'subject' => $summary,
            'description' => $description,
            'priority_id' => $priorityId,
            'tracker_id' => $integration['default_tracker_id'] ?? 1
        )
    );
    
    $headers = array(
        'Content-Type: application/json',
        'X-Redmine-API-Key: ' . $integration['api_key']
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return array(
            'success' => false,
            'message' => 'CURL error: ' . $error
        );
    }
    
    if ($httpCode !== 201) {
        return array(
            'success' => false,
            'message' => 'HTTP error ' . $httpCode . ': ' . $response
        );
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['issue']['id'])) {
        return array(
            'success' => false,
            'message' => 'Invalid response from Redmine API'
        );
    }
    
    $issueId = $result['issue']['id'];
    $issueUrl = rtrim($integration['url'], '/') . '/issues/' . $issueId;
    
    return array(
        'success' => true,
        'message' => 'Issue created successfully',
        'issue_id' => $issueId,
        'issue_url' => $issueUrl
    );
}

/**
 * Create Jira issue via REST API
 */
function createJiraIssue($integration, $summary, $description, $priority) {
    $url = rtrim($integration['url'], '/') . '/rest/api/2/issue';
    
    // Map priority to Jira priority names
    $priorityMap = array(
        'Low' => 'Low',
        'Normal' => 'Medium',
        'High' => 'High',
        'Urgent' => 'Highest'
    );
    
    $jiraPriority = $priorityMap[$priority] ?? 'Medium';
    
    $data = array(
        'fields' => array(
            'project' => array('key' => $integration['project_key']),
            'summary' => $summary,
            'description' => $description,
            'priority' => array('name' => $jiraPriority),
            'issuetype' => array('name' => 'Bug')
        )
    );
    
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($integration['username'] . ':' . $integration['password'])
    );
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return array(
            'success' => false,
            'message' => 'CURL error: ' . $error
        );
    }
    
    if ($httpCode !== 201) {
        return array(
            'success' => false,
            'message' => 'HTTP error ' . $httpCode . ': ' . $response
        );
    }
    
    $result = json_decode($response, true);
    
    if (!isset($result['key'])) {
        return array(
            'success' => false,
            'message' => 'Invalid response from Jira API'
        );
    }
    
    $issueId = $result['key'];
    $issueUrl = rtrim($integration['url'], '/') . '/browse/' . $issueId;
    
    return array(
        'success' => true,
        'message' => 'Issue created successfully',
        'issue_id' => $issueId,
        'issue_url' => $issueUrl
    );
}

/**
 * Create Bugzilla issue
 */
function createBugzillaIssue($integration, $summary, $description, $priority) {
    $url = rtrim($integration['url'], '/') . '/rest/bug';
    
    $data = array(
        'product' => $integration['project_key'],
        'summary' => $summary,
        'description' => $description,
        'priority' => $priority,
        'version' => 'unspecified'
    );
    
    $headers = array(
        'Content-Type: application/json'
    );
    
    if ($integration['api_key']) {
        $headers[] = 'X-BUGZILLA-API-KEY: ' . $integration['api_key'];
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return array('success' => false, 'message' => 'CURL Error: ' . $error);
    }
    
    if ($httpCode === 200) {
        $responseData = json_decode($response, true);
        $issueId = $responseData['id'];
        $issueUrl = rtrim($integration['url'], '/') . '/show_bug.cgi?id=' . $issueId;
        return array('success' => true, 'message' => 'Issue created successfully', 'issue_id' => $issueId, 'issue_url' => $issueUrl);
    }
    
    return array('success' => false, 'message' => 'HTTP ' . $httpCode . ': ' . $response);
}

/**
 * Log issue creation
 */
function logIssueCreation($db, $integrationId, $tprojectId, $tplanId, $tcId, $executionId, $result, $executionTime) {
    $sql = "INSERT INTO custom_bugtrack_integration_log 
            (integration_id, tproject_id, tplan_id, tc_id, execution_id, action, issue_id, status, error_message, execution_time_ms, created_by) 
            VALUES ($integrationId, $tprojectId, $tplanId, $tcId, $executionId, 'CREATE_ISSUE', 
                    " . $db->db->quote($result['issue_id'] ?? '') . ", '" . ($result['success'] ? 'SUCCESS' : 'ERROR') . "', 
                    " . $db->db->quote($result['message']) . ", $executionTime, " . $_SESSION['currentUser']->dbID . ")";
    
    $db->exec_query($sql);
}
?>