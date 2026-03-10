<?php
/**
 * Custom Issue Integration for TestLink
 * Replaces the default issue tracker system with our custom multi-integration approach
 * 
 * @filesource  custom_issue_integration.php
 * @author      TestLink Custom Integration
 * @version     1.1
 * @updated     2026-03-09 - Added assignee support, file logging
 */

require_once('../../config.inc.php');
require_once('../functions/common.php');

// Log file path
if (!defined('CUSTOM_INTEGRATION_LOG')) {
    define('CUSTOM_INTEGRATION_LOG', dirname(__FILE__) . '/custom_integration.log');
}

/**
 * Generate TestLink URLs for test case and execution
 */
function generateTestLinkUrls($tproject_id, $tplan_id, $tc_id, $execution_id) {
    $baseUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $urls = array();
    
    // Test Case URL
    $urls['test_case'] = $baseUrl . '/lib/testcases/archiveData.php?tcase_id=' . $tc_id;
    
    // Execution URL  
    $urls['execution'] = $baseUrl . '/lib/execute/execSetResults.php?level=testsuite&id=' . $tplan_id . '&tcase_id=' . $tc_id;
    
    // Test Plan URL
    $urls['test_plan'] = $baseUrl . '/lib/plan/planView.php?plan_id=' . $tplan_id;
    
    // Project URL
    $urls['project'] = $baseUrl . '/lib/projects/projectView.php?project_id=' . $tproject_id;
    
    return $urls;
}

/**
 * Log message to custom_integration.log file
 */
function logCustomIntegration($message, $level = 'DEBUG') {
    $timestamp = date('Y-m-d H:i:s');
    $logLine = "[$timestamp] [$level] $message" . PHP_EOL;
    
    // Ensure directory is writable
    $logDir = dirname(CUSTOM_INTEGRATION_LOG);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    error_log($logLine, 3, CUSTOM_INTEGRATION_LOG);
}

/**
 * Get custom integration for a specific project
 */
if (!function_exists('getCustomIntegrationForProject')) {
function getCustomIntegrationForProject($db, $tproject_id, $integration_id = null) {
    // Debug logging
    logCustomIntegration("DEBUG: getCustomIntegrationForProject called with tproject_id: $tproject_id, integration_id: " . ($integration_id ?? 'null'));

    $whereIntegration = '';
    if (!is_null($integration_id) && intval($integration_id) > 0) {
        $whereIntegration = " AND i.id = " . intval($integration_id);
    }

    $sql = "SELECT i.* FROM custom_bugtrack_integrations i
            JOIN custom_bugtrack_project_mapping m ON i.id = m.integration_id
            WHERE m.tproject_id = $tproject_id AND m.is_active = 1 AND i.is_active = 1" . $whereIntegration . "
            ORDER BY m.created_on DESC
            LIMIT 1";
    
    error_log("[CUSTOM_INTEGRATION] DEBUG: SQL: $sql");
    
    $result = $db->exec_query($sql);
    
    if ($result) {
        $row = $db->fetch_array($result);
        if ($row) {
            error_log("[CUSTOM_INTEGRATION] DEBUG: Found integration: " . json_encode($row));
            return $row;
        }
    }
    
    error_log("[CUSTOM_INTEGRATION] DEBUG: No integration found for project $tproject_id");
    return null;
}
}

/**
 * Check if custom integration is available for project
 */
if (!function_exists('hasCustomIntegration')) {
function hasCustomIntegration($db, $tproject_id) {
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    return !empty($integration);
}
}

/**
 * Create issue using custom integration
 */
function createCustomIssue($db, $tproject_id, $tplan_id, $tc_id, $execution_id, $summary, $description, $priority = 'Normal', $user = null, $selected_integration_id = null) {
    // ENABLE REAL REDMINE API CALLS
    $integration = getCustomIntegrationForProject($db, $tproject_id, $selected_integration_id);
    if (!$integration && !is_null($selected_integration_id)) {
        logCustomIntegration("DEBUG: Selected integration ID $selected_integration_id not found/active for project $tproject_id - falling back");
        $integration = getCustomIntegrationForProject($db, $tproject_id);
    }
    
    if (!$integration) {
        logCustomIntegration("[CUSTOM_INTEGRATION] No integration configured for project: $tproject_id");
        return array('success' => false, 'message' => 'No integration configured for this project');
    }
    
    // Log the issue creation attempt
    logCustomIntegration("REAL ISSUE CREATION ATTEMPT:");
    logCustomIntegration("Integration: " . $integration['name'] . " (ID: " . $integration['id'] . ")");
    logCustomIntegration("Project ID: $tproject_id, Test Plan ID: $tplan_id");
    logCustomIntegration("Test Case ID: $tc_id, Execution ID: $execution_id");
    logCustomIntegration("Summary: " . substr($summary, 0, 100) . "...");
    logCustomIntegration("Priority: $priority");
    
    // Prepare data for API call
    $data = array(
        'tproject_id' => $tproject_id,
        'tplan_id' => $tplan_id,
        'tc_id' => $tc_id,
        'execution_id' => $execution_id,
        'summary' => $summary,
        'description' => $description,
        'priority' => $priority,
        'assigned_to' => 2635, // Hardcoded for testing
        'testlink_urls' => generateTestLinkUrls($tproject_id, $tplan_id, $tc_id, $execution_id)
    );

    if (!is_null($selected_integration_id) && intval($selected_integration_id) > 0) {
        $data['integration_id'] = intval($selected_integration_id);
        logCustomIntegration("Passing selected integration_id to API: " . intval($selected_integration_id));
    }

    if ($user && isset($user->login)) {
        $data['tester'] = $user->login;
        logCustomIntegration("Added tester to data: " . $user->login);
    }
    
    // Call the custom integrator API
    // Use web URL instead of file system path
    $baseUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $url = $baseUrl . '/lib/execute/custom_bugtrack_integrator_simple.php?action=create_issue';
    
    logCustomIntegration("Base URL: $baseUrl");
    logCustomIntegration("TL_ABS_PATH: " . TL_ABS_PATH);
    logCustomIntegration("Calling API: $url");
    logCustomIntegration("Data: " . json_encode($data));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
    
    logCustomIntegration("Executing cURL request...");
    logCustomIntegration("cURL URL: $url");
    logCustomIntegration("cURL POST data: " . json_encode($data));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $errno = curl_errno($ch);
    
    logCustomIntegration("cURL Error: $error (errno: $errno)");
    logCustomIntegration("HTTP Code: $httpCode");
    logCustomIntegration("Raw Response: $response");
    
    curl_close($ch);
    
    if ($error) {
        logCustomIntegration("CURL Error: $error");
        return array('success' => false, 'message' => 'CURL Error: ' . $error);
    }
    
    if ($httpCode !== 200) {
        logCustomIntegration("HTTP Error: Code $httpCode, Response: $response");
        // For 500 errors, let's also check the simple API log
        if ($httpCode == 500) {
            logCustomIntegration("500 Error detected - check custom_bugtrack_integrator.log for details");
        }
        return array('success' => false, 'message' => "HTTP Error: $httpCode");
    }
    
    if (empty($response)) {
        logCustomIntegration("Empty response from API");
        return array('success' => false, 'message' => 'Empty response from API');
    }
    
    $result = json_decode($response, true);
    
    if (!$result) {
        logCustomIntegration("Invalid JSON response: $response");
        return array('success' => false, 'message' => 'Invalid JSON response');
    }
    
    logCustomIntegration("REAL SUCCESS - Issue created: " . ($result['issue_id'] ?? 'unknown'));
    
    // CRITICAL: Use TestLink's proper write_execution_bug function to link the bug
    if ($result['success'] && isset($result['issue_id'])) {
        $bug_id = $result['issue_id'];
        
        logCustomIntegration("LINKING BUG TO EXECUTION - Bug ID: $bug_id, Execution ID: $execution_id");
        
        // Use TestLink's proper write_execution_bug function
        require_once('../functions/exec.inc.php');
        
        $linkResult = write_execution_bug($db, $execution_id, $bug_id, 0); // tcstep_id = 0 for test case level
        
        if ($linkResult) {
            logCustomIntegration("SUCCESS: Bug ID $bug_id linked to execution $execution_id using write_execution_bug");
        } else {
            logCustomIntegration("ERROR: Failed to link bug ID $bug_id to execution $execution_id using write_execution_bug");
        }
    } else {
        logCustomIntegration("WARNING: Not inserting into execution_bugs - success=" . ($result['success'] ?? 'false') . ", issue_id=" . ($result['issue_id'] ?? 'null') . ", execution_id=" . ($execution_id ?? 'null'));
    }
    
    return $result;
}

/**
 * Get integration configuration for GUI
 */
function getCustomIntegrationConfig($db, $tproject_id) {
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    
    if (!$integration) {
        return null;
    }
    
    return array(
        'name' => $integration['name'],
        'type' => $integration['type'],
        'url' => $integration['url'],
        'project_key' => $integration['project_key'],
        'default_priority' => $integration['default_priority'],
        'enabled' => true
    );
}

/**
 * Override issue tracker check in execSetResults
 */
function overrideIssueTrackerCheck(&$args, &$gui, $db) {
    // Check if we have a custom integration for this project
    $customIntegration = getCustomIntegrationForProject($db, $args->tproject_id);
    
    if ($customIntegration) {
        // Override the default issue tracker settings
        $args->issue_tracker_enabled = true;
        $args->use_custom_integration = true;
        $args->custom_integration = $customIntegration;
        
        // Set GUI variables
        $gui->issueTrackerIntegrationOn = true;
        $gui->tlCanCreateIssue = true;
        $gui->tlCanAddIssueNote = false;
        $gui->customIntegrationEnabled = true;
        $gui->customIntegration = $customIntegration;
        
        // Set configuration
        $gui->issueTrackerCfg = new stdClass();
        $gui->issueTrackerCfg->bugSummaryMaxLength = 200;
        $gui->issueTrackerCfg->editIssueAttr = true;
        
        // Set access message
        $gui->accessToIssueTracker = lang_get('link_bts_create_bug') . 
                                     " ({$customIntegration['name']})";
        
        return true;
    }
    
    return false;
}

/**
 * Handle issue creation from execSetResults
 */
function handleCustomIssueCreation($db, $args, $gui) {
    if (!isset($args->createIssue) || !$args->createIssue) {
        return null;
    }
    
    if (!isset($args->use_custom_integration) || !$args->use_custom_integration) {
        return null;
    }
    
    // Get issue data
    $summary = $args->bug_summary ?? '';
    $description = $args->bug_notes ?? '';
    $priority = $args->issuePriority ?? $args->custom_integration['default_priority'] ?? 'Normal';
    
    // Get test case and execution info
    $tc_id = $args->tc_id ?? 0;
    $execution_id = $args->execution_id ?? 0;
    
    if (empty($summary)) {
        return array('success' => false, 'message' => 'Bug summary is required');
    }
    
    // Create the issue
    $result = createCustomIssue(
        $db,
        $args->tproject_id,
        $args->tplan_id,
        $tc_id,
        $execution_id,
        $summary,
        $description,
        $priority,
        $args->user
    );
    
    return $result;
}

/**
 * Get issue data using custom integration
 */
if (!function_exists('getCustomIssueData')) {
function getCustomIssueData($db, $tproject_id, $issue_id) {
    error_log("[CUSTOM_INTEGRATION] DEBUG: getCustomIssueData called with tproject_id: $tproject_id, issue_id: $issue_id");
    
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    
    if (!$integration) {
        error_log("[CUSTOM_INTEGRATION] No integration configured for project: $tproject_id");
        return null;
    }
    
    error_log("[CUSTOM_INTEGRATION] Using integration: " . $integration['name'] . " (Type: " . $integration['type'] . ")");
    
    // Based on integration type, call appropriate API
    if ($integration['type'] === 'REDMINE') {
        return getRedmineIssueData($integration, $issue_id);
    } else {
        error_log("[CUSTOM_INTEGRATION] Unsupported integration type: " . $integration['type']);
        return null;
    }
}
}

/**
 * Get Redmine issue data
 */
if (!function_exists('getRedmineIssueData')) {
function getRedmineIssueData($integration, $issue_id) {
    $url = $integration['url'] . '/issues/' . $issue_id . '.json';
    
    // Add cache-busting
    $url .= '?_t=' . time();
    
    error_log("[CUSTOM_INTEGRATION] Calling Redmine API: $url");
    
    try {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Redmine-API-Key: ' . $integration['api_key'],
            'Content-Type: application/json',
            'Cache-Control: no-cache, no-store, must-revalidate',
            'Pragma: no-cache',
            'Expires: 0'
        ));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("[CUSTOM_INTEGRATION] Redmine API response: HTTP $httpCode, Error: '$curlError'");
        
        if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            
            if (isset($data['issue'])) {
                $issue_data = array(
                    'bug_id' => $issue_id,
                    'status' => isset($data['issue']['status']['name']) ? $data['issue']['status']['name'] : 'Unknown',
                    'priority' => isset($data['issue']['priority']['name']) ? $data['issue']['priority']['name'] : 'N/A',
                    'assignee' => isset($data['issue']['assigned_to']['name']) ? $data['issue']['assigned_to']['name'] : 'N/A',
                    'updated_on' => isset($data['issue']['updated_on']) ? $data['issue']['updated_on'] : null
                );
                
                error_log("[CUSTOM_INTEGRATION] Successfully parsed issue $issue_id: " . json_encode($issue_data));
                return $issue_data;
            }
        } else {
            error_log("[CUSTOM_INTEGRATION] Redmine API response body: $response");
        }
    } catch (Exception $e) {
        error_log("[CUSTOM_INTEGRATION] Exception fetching issue $issue_id: " . $e->getMessage());
    }
    
    return null;
}
}
?>
