<?php
/**
 * Custom Issue Integration for TestLink
 * Replaces the default issue tracker system with our custom multi-integration approach
 * 
 * @filesource  custom_issue_integration.php
 * @author      TestLink Custom Integration
 * @version     1.0
 * @created     2025-02-23
 */

require_once('../../config.inc.php');
require_once('../functions/common.php');

/**
 * Get custom integration for a specific project
 */
function getCustomIntegrationForProject($db, $tproject_id) {
    // Debug logging
    error_log("[CUSTOM_INTEGRATION] DEBUG: getCustomIntegrationForProject called with tproject_id: $tproject_id");
    
    $sql = "SELECT i.* FROM custom_bugtrack_integrations i
            JOIN custom_bugtrack_project_mapping m ON i.id = m.integration_id
            WHERE m.tproject_id = $tproject_id AND m.is_active = 1 AND i.is_active = 1
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

/**
 * Check if custom integration is available for project
 */
function hasCustomIntegration($db, $tproject_id) {
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    return !empty($integration);
}

/**
 * Create issue using custom integration
 */
function createCustomIssue($db, $tproject_id, $tplan_id, $tc_id, $execution_id, $summary, $description, $priority = 'Normal', $user = null) {
    // ENABLE REAL REDMINE API CALLS
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    
    if (!$integration) {
        error_log("[CUSTOM_INTEGRATION] No integration configured for project: $tproject_id");
        return array('success' => false, 'message' => 'No integration configured for this project');
    }
    
    // Log the issue creation attempt
    error_log("[CUSTOM_INTEGRATION] REAL ISSUE CREATION ATTEMPT:");
    error_log("[CUSTOM_INTEGRATION] Integration: " . $integration['name'] . " (ID: " . $integration['id'] . ")");
    error_log("[CUSTOM_INTEGRATION] Project ID: $tproject_id, Test Plan ID: $tplan_id");
    error_log("[CUSTOM_INTEGRATION] Test Case ID: $tc_id, Execution ID: $execution_id");
    error_log("[CUSTOM_INTEGRATION] Summary: " . substr($summary, 0, 100) . "...");
    error_log("[CUSTOM_INTEGRATION] Priority: $priority");
    
    // Prepare data for API call
    $data = array(
        'tproject_id' => $tproject_id,
        'tplan_id' => $tplan_id,
        'tc_id' => $tc_id,
        'execution_id' => $execution_id,
        'summary' => $summary,
        'description' => $description,
        'priority' => $priority
    );

    if ($user && isset($user->login)) {
        $data['tester'] = $user->login;
        error_log("[CUSTOM_INTEGRATION] Added tester to data: " . $user->login);
    }
    
    // Call the custom integrator API
    // Use web URL instead of file system path
    $baseUrl = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $url = $baseUrl . '/lib/execute/custom_bugtrack_integrator_simple.php?action=create_issue';
    
    error_log("[CUSTOM_INTEGRATION] Base URL: $baseUrl");
    error_log("[CUSTOM_INTEGRATION] TL_ABS_PATH: " . TL_ABS_PATH);
    error_log("[CUSTOM_INTEGRATION] Calling API: $url");
    error_log("[CUSTOM_INTEGRATION] Data: " . json_encode($data));
    
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
    
    error_log("[CUSTOM_INTEGRATION] Executing cURL request...");
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    $errno = curl_errno($ch);
    
    error_log("[CUSTOM_INTEGRATION] cURL Error: $error (errno: $errno)");
    error_log("[CUSTOM_INTEGRATION] HTTP Code: $httpCode");
    error_log("[CUSTOM_INTEGRATION] Raw Response: $response");
    
    curl_close($ch);
    
    if ($error) {
        error_log("[CUSTOM_INTEGRATION] CURL Error: $error");
        return array('success' => false, 'message' => 'CURL Error: ' . $error);
    }
    
    if ($httpCode !== 200) {
        error_log("[CUSTOM_INTEGRATION] HTTP Error: Code $httpCode, Response: $response");
        // For 500 errors, let's also check the simple API log
        if ($httpCode == 500) {
            error_log("[CUSTOM_INTEGRATION] 500 Error detected - check custom_bugtrack_integrator.log for details");
        }
        return array('success' => false, 'message' => "HTTP Error: $httpCode");
    }
    
    if (empty($response)) {
        error_log("[CUSTOM_INTEGRATION] Empty response from API");
        return array('success' => false, 'message' => 'Empty response from API');
    }
    
    $result = json_decode($response, true);
    
    if (!$result) {
        error_log("[CUSTOM_INTEGRATION] Invalid JSON response: $response");
        return array('success' => false, 'message' => 'Invalid JSON response');
    }
    
    error_log("[CUSTOM_INTEGRATION] REAL SUCCESS - Issue created: " . ($result['issue_id'] ?? 'unknown'));
    
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
        error_log("[CUSTOM_INTEGRATION] WARNING: Not inserting into execution_bugs - success=" . ($result['success'] ?? 'false') . ", issue_id=" . ($result['issue_id'] ?? 'null') . ", execution_id=" . ($execution_id ?? 'null'));
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
function getCustomIssueData($db, $tproject_id, $issue_id) {
    error_log("[CUSTOM_INTEGRATION] DEBUG: getCustomIssueData called with tproject_id: $tproject_id, issue_id: $issue_id");
    
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    
    if (!$integration) {
        error_log("[CUSTOM_INTEGRATION] No integration configured for project: $tproject_id");
        return null;
    }
    
    error_log("[CUSTOM_INTEGRATION] Using integration: " . $integration['name'] . " (Type: " . $integration['type'] . ")");
    
    // Based on integration type, call appropriate API
    if ($integration['type'] === 'redmine') {
        return getRedmineIssueData($integration, $issue_id);
    } else {
        error_log("[CUSTOM_INTEGRATION] Unsupported integration type: " . $integration['type']);
        return null;
    }
}

/**
 * Get Redmine issue data
 */
function getRedmineIssueData($integration, $issue_id) {
    $url = $integration['api_endpoint'] . '/issues/' . $issue_id . '.json';
    
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
?>
