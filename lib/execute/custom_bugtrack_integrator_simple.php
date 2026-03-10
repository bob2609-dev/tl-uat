<?php
/**
 * Simple Custom Bug Tracking Integration API
 * Bypasses TestLink initialization for direct API calls
 */

ob_start();
error_reporting(0);
ini_set('display_errors', 0);

register_shutdown_function(function() {
    if (!headers_sent()) {
        $error = error_get_last();
        if ($error && $error['type'] === E_ERROR) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode(array(
                'success' => false,
                'message' => 'Fatal error: ' . $error['message'],
                'debug' => array(
                    'file' => $error['file'],
                    'line' => $error['line']
                )
            ));
        }
    }
});

function writeLog($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    $logFile = dirname(__FILE__) . '/custom_integration.log';
    error_log($logMessage, 3, $logFile);
}

function outputJson($data) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Simple debug at very top
writeLog("=== API Request Started ===");
writeLog("Request URI: " . $_SERVER['REQUEST_URI']);
writeLog("Request method: " . $_SERVER['REQUEST_METHOD']);

// Handle JSON input
$jsonInput = file_get_contents('php://input');
if (!empty($jsonInput)) {
    $_POST = json_decode($jsonInput, true) ?: array();
    writeLog("JSON input data: " . $jsonInput);
} else {
    writeLog("POST data: " . json_encode($_POST));
}

writeLog("GET data: " . json_encode($_GET));

/**
 * Extract TestLink URLs from input data (supports both old and new formats)
 */
function extractTestLinkUrls() {
    $urls = array();
    
    // New format: testlink_urls array
    if (isset($_POST['testlink_urls']) && is_array($_POST['testlink_urls'])) {
        $urls = $_POST['testlink_urls'];
    } 
    // Old format: individual URL fields
    else {
        $urls['test_case'] = isset($_POST['testlink_url']) ? $_POST['testlink_url'] : 'N/A';
        $urls['test_plan'] = isset($_POST['testplan_url']) ? $_POST['testplan_url'] : 'N/A';
        $urls['execution'] = 'N/A';
        $urls['project'] = 'N/A';
    }
    
    return $urls;
}

/**
 * Format TestLink URLs for description
 */
function formatTestLinkUrlsForDescription($urls) {
    $testlinkUrls = "\r\n\r\n--- TestLink URLs ---\r\n";
    $testlinkUrls .= "Test Case: " . (isset($urls['test_case']) ? $urls['test_case'] : 'N/A') . "\r\n";
    $testlinkUrls .= "Execution: " . (isset($urls['execution']) ? $urls['execution'] : 'N/A') . "\r\n";
    $testlinkUrls .= "Test Plan: " . (isset($urls['test_plan']) ? $urls['test_plan'] : 'N/A') . "\r\n";
    $testlinkUrls .= "Project: " . (isset($urls['project']) ? $urls['project'] : 'N/A') . "\r\n";
    
    return $testlinkUrls;
}

// Initialize response
$response = array(
    'success' => false,
    'message' => '',
    'data' => null,
    'debug' => array()
);

try {
    // Simple database connection without full TestLink init
    writeLog("Loading config.inc.php...");
    define('NOCRYPT', true);
    require_once('../../config.inc.php');
    writeLog("Config loaded successfully");
    
    // Connect to database directly using MySQLi
    writeLog("Attempting database connection to " . DB_NAME . " on " . DB_HOST);
    
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_error) {
        throw new Exception('Database connection failed: ' . $db->connect_error);
    }
    
    writeLog("Database connection successful");
    
    // Get action
    $action = $_GET['action'] ?? '';
    writeLog("Action: $action");
    
    switch ($action) {
        case 'create_issue':
            handleCreateIssue($db);
            break;
        default:
            $response['message'] = 'Unknown action: ' . $action;
            outputJson($response);
    }
    
} catch (Exception $e) {
    writeLog("Exception: " . $e->getMessage());
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    outputJson($response);
}

function handleCreateIssue($db) {
    global $response;
    
    $tproject_id = intval($_POST['tproject_id'] ?? 0);
    $tplan_id = intval($_POST['tplan_id'] ?? 0);
    $tc_id = intval($_POST['tc_id'] ?? 0);
    $execution_id = intval($_POST['execution_id'] ?? 0);
    $integration_id = intval($_POST['integration_id'] ?? 0);
    $summary = $_POST['summary'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'Normal';
    $tester = $_POST['tester'] ?? null;
    $assigned_to = intval($_POST['assigned_to'] ?? 0); // Assignee ID
    
    writeLog("Creating issue for project: $tproject_id");
    writeLog("Requested integration_id: $integration_id");
    writeLog("Original summary: '$summary'");
    writeLog("Original description length: " . strlen($description));
    writeLog("Assigned to ID: $assigned_to");
    
    if ($tproject_id <= 0 || empty($summary)) {
        $response['message'] = 'Project ID and summary are required';
        outputJson($response);
    }
    
    // Get integration for this project using MySQLi
    if ($integration_id > 0) {
        $sql = "SELECT i.* FROM custom_bugtrack_integrations i
                JOIN custom_bugtrack_project_mapping m ON i.id = m.integration_id
                WHERE m.tproject_id = $tproject_id AND m.integration_id = $integration_id
                AND m.is_active = 1 AND i.is_active = 1
                LIMIT 1";
    } else {
        $sql = "SELECT i.* FROM custom_bugtrack_integrations i
                JOIN custom_bugtrack_project_mapping m ON i.id = m.integration_id
                WHERE m.tproject_id = $tproject_id AND m.is_active = 1 AND i.is_active = 1
                ORDER BY m.created_on DESC
                LIMIT 1";
    }
    
    writeLog("Executing SQL: $sql");
    
    $result = $db->query($sql);
    if (!$result) {
        throw new Exception('SQL Error: ' . $db->error);
    }
    
    $integration = $result->fetch_assoc();
    
    if (!$integration) {
        $response['message'] = 'No active integration found for this project';
        outputJson($response);
    }
    
    writeLog("Found integration: " . $integration['name']);
    writeLog("Integration type: " . $integration['type']);
    writeLog("Integration URL: " . $integration['url']);
    
    // Create real issue based on integration type
    if ($integration['type'] === 'REDMINE') {
        $result = createRedmineIssue($integration, $summary, $description, $priority, $tester, $assigned_to);
    } elseif ($integration['type'] === 'JIRA') {
        $result = createJiraIssue($integration, $summary, $description, $priority, $tester, $assigned_to);
    } elseif ($integration['type'] === 'BUGZILLA') {
        $result = createBugzillaIssue($integration, $summary, $description, $priority, $tester);
    } else {
        $result = array('success' => false, 'message' => 'Unsupported integration type: ' . $integration['type']);
    }
    
    if ($result['success']) {
        $response['success'] = true;
        $response['message'] = 'Issue created successfully in ' . $integration['type'];
        $response['issue_id'] = $result['issue_id'];
        $response['issue_url'] = $result['issue_url'];
        
        writeLog("Real " . $integration['type'] . " issue created: " . $result['issue_id']);
    } else {
        $response['success'] = false;
        $response['message'] = $result['message'];
        
        writeLog($integration['type'] . " issue creation failed: " . $result['message']);
    }
    
    outputJson($response);
}

function createRedmineIssue($integration, $summary, $description, $priority, $tester = null, $assigned_to = 0) {
    writeLog("Creating real Redmine issue...");
    writeLog("Assigned to ID: $assigned_to");
    
    // Map TestLink priority to Redmine priority
    $priorityMap = array(
        'Low' => 1,
        'Normal' => 2,
        'High' => 3,
        'Urgent' => 4
    );
    $redminePriority = $priorityMap[$priority] ?? 2;
    
    // Extract and format TestLink URLs
    $testlinkUrlsArray = extractTestLinkUrls();
    $testlinkUrls = formatTestLinkUrlsForDescription($testlinkUrlsArray);
    
    // Ensure TestLink URLs are included in description
    if (strpos($description, 'TestLink URLs') === false) {
        $description .= $testlinkUrls;
    }

    if ($tester) {
        $description .= "\r\nTester: " . $tester;
        writeLog("Added tester to description: " . $tester);
    }
    
    // Prepare Redmine API data
    $issueData = array(
        'issue' => array(
            'project_id' => $integration['project_key'],
            'subject' => $summary, // Use the full summary
            'description' => $description,
            'priority_id' => $redminePriority
        )
    );
    
    // Add assignee if provided
    if ($assigned_to > 0) {
        $issueData['issue']['assigned_to_id'] = $assigned_to;
        writeLog("Adding assignee_id: $assigned_to to Redmine issue");
    }
    
    $jsonData = json_encode($issueData);
    writeLog("Redmine API data: " . $jsonData);
    
    // Call Redmine API
    $redmineUrl = rtrim($integration['url'], '/') . '/issues.json';
    writeLog("Calling Redmine API: $redmineUrl");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $redmineUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-Redmine-API-Key: ' . $integration['api_key']
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        writeLog("Redmine API cURL Error: $error");
        return array('success' => false, 'message' => 'cURL Error: ' . $error);
    }
    
    writeLog("Redmine API HTTP Code: $httpCode");
    writeLog("Redmine API Response: $response");
    
    if ($httpCode !== 201) { // Redmine returns 201 for created
        writeLog("Redmine API Error: HTTP $httpCode - $response");
        return array('success' => false, 'message' => "Redmine API Error: HTTP $httpCode");
    }
    
    $result = json_decode($response, true);
    if (!$result || !isset($result['issue']['id'])) {
        writeLog("Invalid Redmine API response: $response");
        return array('success' => false, 'message' => 'Invalid Redmine API response');
    }
    
    $issueId = $result['issue']['id'];
    $issueUrl = rtrim($integration['url'], '/') . '/issues/' . $issueId;
    
    writeLog("Redmine issue created successfully: ID $issueId");
    
    return array(
        'success' => true,
        'issue_id' => $issueId,
        'issue_url' => $issueUrl,
        'message' => "Redmine issue #$issueId created successfully"
    );
}

function createJiraIssue($integration, $summary, $description, $priority, $tester = null, $assigned_to = '') {
    writeLog("Creating real Jira issue...");
    writeLog("Assigned to: $assigned_to");
    
    // Map TestLink priority to Jira priority
    $priorityMap = array(
        'Low' => 'Low',
        'Normal' => 'Medium',
        'High' => 'High',
        'Urgent' => 'Highest'
    );
    $jiraPriority = $priorityMap[$priority] ?? 'Medium';
    
    // Extract and format TestLink URLs
    $testlinkUrlsArray = extractTestLinkUrls();
    $testlinkUrls = formatTestLinkUrlsForDescription($testlinkUrlsArray);
    
    // Ensure TestLink URLs are included in description
    if (strpos($description, 'TestLink URLs') === false) {
        $description .= $testlinkUrls;
    }

    if ($tester) {
        $description .= "\r\nTester: " . $tester;
        writeLog("Added tester to description: " . $tester);
    }
    
    // Prepare Jira API data
    $issueData = array(
        'fields' => array(
            'project' => array('key' => $integration['project_key']),
            'summary' => $summary,
            'description' => $description,
            'priority' => array('name' => $jiraPriority),
            'issuetype' => array('name' => 'Bug')
        )
    );
    
    // Add assignee if provided (Jira uses username or accountId)
    if (!empty($assigned_to)) {
        $issueData['fields']['assignee'] = array('name' => $assigned_to);
        writeLog("Adding assignee: $assigned_to to Jira issue");
    }
    
    $jsonData = json_encode($issueData);
    writeLog("Jira API data: " . $jsonData);
    
    // Call Jira API
    $jiraUrl = rtrim($integration['url'], '/') . '/rest/api/2/issue';
    writeLog("Calling Jira API: $jiraUrl");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $jiraUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($integration['username'] . ':' . $integration['password'])
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        writeLog("Jira API cURL Error: $error");
        return array('success' => false, 'message' => 'cURL Error: ' . $error);
    }
    
    writeLog("Jira API HTTP Code: $httpCode");
    writeLog("Jira API Response: $response");
    
    if ($httpCode !== 201) { // Jira returns 201 for created
        writeLog("Jira API Error: HTTP $httpCode - $response");
        return array('success' => false, 'message' => "Jira API Error: HTTP $httpCode");
    }
    
    $result = json_decode($response, true);
    if (!$result || !isset($result['id'])) {
        writeLog("Invalid Jira API response: $response");
        return array('success' => false, 'message' => 'Invalid Jira API response');
    }
    
    $issueId = $result['key']; // Jira uses issue key like "PROJ-123"
    $issueUrl = rtrim($integration['url'], '/') . '/browse/' . $issueId;
    
    writeLog("Jira issue created successfully: $issueId");
    
    return array(
        'success' => true,
        'issue_id' => $issueId,
        'issue_url' => $issueUrl,
        'message' => "Jira issue $issueId created successfully"
    );
}

function createBugzillaIssue($integration, $summary, $description, $priority, $tester = null) {
    writeLog("Creating real Bugzilla issue...");
    
    // Map TestLink priority to Bugzilla priority
    $priorityMap = array(
        'Low' => 'Low',
        'Normal' => 'Normal',
        'High' => 'High',
        'Urgent' => 'Highest'
    );
    $bugzillaPriority = $priorityMap[$priority] ?? 'Normal';
    
    // Extract and format TestLink URLs
    $testlinkUrlsArray = extractTestLinkUrls();
    $testlinkUrls = formatTestLinkUrlsForDescription($testlinkUrlsArray);
    
    // Ensure TestLink URLs are included in description
    if (strpos($description, 'TestLink URLs') === false) {
        $description .= $testlinkUrls;
    }

    if ($tester) {
        $description .= "\r\nTester: " . $tester;
        writeLog("Added tester to description: " . $tester);
    }
    
    // Prepare Bugzilla API data
    $bugData = array(
        'product' => $integration['project_key'],
        'summary' => $summary,
        'description' => $description,
        'priority' => $bugzillaPriority,
        'version' => $integration['default_version'] ?? 'unspecified',
        'op_sys' => 'All',
        'platform' => 'All',
        'status' => 'NEW'
    );
    
    $jsonData = json_encode($bugData);
    writeLog("Bugzilla API data: " . $jsonData);
    
    // Call Bugzilla API
    $bugzillaUrl = rtrim($integration['url'], '/') . '/rest/bug';
    writeLog("Calling Bugzilla API: $bugzillaUrl");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bugzillaUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'X-BUGZILLA-API-KEY: ' . $integration['api_key']
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        writeLog("Bugzilla API cURL Error: $error");
        return array('success' => false, 'message' => 'cURL Error: ' . $error);
    }
    
    writeLog("Bugzilla API HTTP Code: $httpCode");
    writeLog("Bugzilla API Response: $response");
    
    if ($httpCode !== 201) { // Bugzilla returns 201 for created
        writeLog("Bugzilla API Error: HTTP $httpCode - $response");
        return array('success' => false, 'message' => "Bugzilla API Error: HTTP $httpCode");
    }
    
    $result = json_decode($response, true);
    if (!$result || !isset($result['id'])) {
        writeLog("Invalid Bugzilla API response: $response");
        return array('success' => false, 'message' => 'Invalid Bugzilla API response');
    }
    
    $issueId = $result['id'];
    $issueUrl = rtrim($integration['url'], '/') . '/show_bug.cgi?id=' . $issueId;
    
    writeLog("Bugzilla issue created successfully: ID $issueId");
    
    return array(
        'success' => true,
        'issue_id' => $issueId,
        'issue_url' => $issueUrl,
        'message' => "Bugzilla issue #$issueId created successfully"
    );
}

?>
