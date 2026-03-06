<?php
/**
 * Custom Issue Handler for TestLink
 * Handles issue creation when using custom integrations (Redmine, Jira, Bugzilla)
 * 
 * @filesource  custom_issue_handler.php
 * @author      TestLink Custom Integration
 * @version     1.0
 * @created     2025-02-23
 */

require_once('../../config.inc.php');
require_once('../functions/common.php');

// Add comprehensive logging for custom issue handler
function logCustomIssueHandler($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s') . '.' . substr(microtime(true) * 1000, -3);
    $logMessage = "[{$timestamp}] [{$level}] [CUSTOM_ISSUE_HANDLER] {$message}\n";
    error_log($logMessage, 3, 'custom_integration_execution.log');
    
    // Also log to PHP error log for immediate visibility
    error_log($logMessage);
}

/**
 * Custom write_execution function that handles both default and custom integrations
 */
function custom_write_execution(&$dbHandler, &$argsObj, &$requestObj, &$issueTracker) {
    logCustomIssueHandler("=== CUSTOM WRITE EXECUTION START ===");
    logCustomIssueHandler("use_custom_integration: " . (isset($argsObj->use_custom_integration) ? ($argsObj->use_custom_integration ? 'true' : 'false') : 'not set'));
    logCustomIssueHandler("createIssue: " . (isset($argsObj->createIssue) ? ($argsObj->createIssue ? 'true' : 'false') : 'not set'));
    logCustomIssueHandler("tproject_id: " . (isset($argsObj->tproject_id) ? $argsObj->tproject_id : 'NULL'));
    
    // Get tcversion_id from request data (it's in tc_version array)
    $tcversion_id = null;
    if (isset($requestObj['tc_version']) && is_array($requestObj['tc_version'])) {
        $tcversion_id = reset($requestObj['tc_version']); // Get first value
    }
    logCustomIssueHandler("tcversion_id: " . (isset($tcversion_id) ? $tcversion_id : 'NULL'));
    
    // First, save execution normally using TestLink's write_execution
    logCustomIssueHandler("Calling write_execution to save test execution");
    $result = write_execution($dbHandler, $argsObj, $requestObj, $issueTracker);
    
    // CRITICAL: Get the execution ID from the write_execution result
    $execution_id = 0;
    // write_execution returns an array where the first element is a map of
    // tcversion_id => execution_id.
    if (!empty($result[0]) && is_array($result[0])) {
        // Get the first value from the map, which is our execution_id
        $execution_id = reset($result[0]);
        logCustomIssueHandler("Found execution ID from write_execution result: $execution_id");
    }

    if (empty($execution_id)) {
        logCustomIssueHandler("WARNING: Could not find execution ID in write_execution result. Result: " . json_encode($result));
    }
    
    // Check if we need to create an issue using custom integration
    if (isset($argsObj->createIssue) && $argsObj->createIssue) {
        logCustomIssueHandler("Issue creation requested - using custom integration");
        
        // Get integration details from args object (populated from database)
        $integration = isset($argsObj->custom_integration) ? $argsObj->custom_integration : null;
        
        if (!$integration) {
            logCustomIssueHandler("ERROR: No integration configuration found");
            $result[1] = array( // addIssueOp
                'success' => false,
                'message' => 'No integration configuration found'
            );
        } else {
            // Get integration details from database
            $integrationType = isset($integration['type']) ? $integration['type'] : 'REDMINE';
            $integrationUrl = isset($integration['url']) ? $integration['url'] : '';
            $integrationName = isset($integration['name']) ? $integration['name'] : 'Unknown';
            
            logCustomIssueHandler("Integration name: $integrationName");
            logCustomIssueHandler("Integration type: $integrationType");
            logCustomIssueHandler("Integration URL: $integrationUrl");
            
            // Get issue data
            $summary = isset($argsObj->bug_summary) ? $argsObj->bug_summary : '';
            $description = isset($argsObj->bug_notes) ? $argsObj->bug_notes : '';
            $priority = isset($argsObj->issuePriority) ? $argsObj->issuePriority : (isset($integration['default_priority']) ? $integration['default_priority'] : 'Normal');
            $selectedIntegrationID = intval(isset($argsObj->selected_integration_id) ? $argsObj->selected_integration_id : (isset($requestObj['selected_integration_id']) ? $requestObj['selected_integration_id'] : 0));
            
            logCustomIssueHandler("DEBUG: argsObj->bug_summary = '" . $argsObj->bug_summary . "'");
            logCustomIssueHandler("DEBUG: summary length = " . strlen($summary));
            logCustomIssueHandler("DEBUG: summary content = '" . $summary . "'");
            logCustomIssueHandler("DEBUG: description length = " . strlen($description));
            logCustomIssueHandler("DEBUG: description contains URLs = " . (strpos($description, 'TestLink URLs:') !== false ? 'YES' : 'NO'));
            logCustomIssueHandler("DEBUG: description preview = '" . substr($description, 0, 200) . "...'");
            
            // Get test case and execution info
            $tc_id = isset($argsObj->tc_id) ? $argsObj->tc_id : 0;

            // The execution_id has been retrieved above, after the call to write_execution.
            // We just need to check if it's valid before proceeding.
            logCustomIssueHandler("DEBUG: Re-checking execution ID before creating issue: $execution_id");
            
            logCustomIssueHandler("Issue data - Summary: " . substr($summary, 0, 100) . "...");
            logCustomIssueHandler("Issue data - Priority: $priority");
            logCustomIssueHandler("Issue data - TC ID: $tc_id, Exec ID: $execution_id");
            logCustomIssueHandler("Issue data - Selected Integration ID: " . ($selectedIntegrationID ?: 'none'));
            
            if (empty($summary)) {
                logCustomIssueHandler("ERROR: Bug summary is required");
                $result[1] = array( // addIssueOp
                    'success' => false,
                    'message' => 'Bug summary is required'
                );
            } else {
                // ENABLE REAL REDMINE API CALLS
                logCustomIssueHandler("Creating real issue in $integrationType");
                require_once('custom_issue_integration.php');
                $issueResult = createCustomIssue(
                    $dbHandler,
                    $argsObj->tproject_id,
                    $argsObj->tplan_id,
                    $tc_id,
                    $execution_id,
                    $summary,
                    $description,
                    $priority,
                    $argsObj->user,
                    $selectedIntegrationID > 0 ? $selectedIntegrationID : null
                );
                
                logCustomIssueHandler("createCustomIssue result: " . json_encode($issueResult));
                
                $result[1] = array( // addIssueOp
                    'success' => isset($issueResult['success']) ? $issueResult['success'] : false,
                    'issue_id' => isset($issueResult['issue_id']) ? $issueResult['issue_id'] : null,
                    'issue_url' => isset($issueResult['issue_url']) ? $issueResult['issue_url'] : null,
                    'message' => isset($issueResult['message']) ? $issueResult['message'] : 'Issue creation failed'
                );
            }
        }
    }
    
    logCustomIssueHandler("=== CUSTOM WRITE EXECUTION END ===");
    return $result;
}

/**
 * Generate mock issue ID based on integration type
 */
function generateMockIssueId($integrationType) {
    $timestamp = time();
    
    switch ($integrationType) {
        case 'REDMINE':
            return "MOCK-{$timestamp}";
        case 'JIRA':
            return "MOCK-{$timestamp}";
        case 'BUGZILLA':
            return $timestamp; // Bugzilla uses numeric IDs
        default:
            return "MOCK-{$timestamp}";
    }
}

/**
 * Generate mock issue URL based on integration type
 */
function generateMockIssueUrl($integrationType, $baseUrl, $issueId) {
    // Remove trailing slash from base URL
    $baseUrl = rtrim($baseUrl, '/');
    
    switch ($integrationType) {
        case 'REDMINE':
            return "{$baseUrl}/issues/{$issueId}";
        case 'JIRA':
            return "{$baseUrl}/browse/{$issueId}";
        case 'BUGZILLA':
            return "{$baseUrl}/show_bug.cgi?id={$issueId}";
        default:
            return "{$baseUrl}/issues/{$issueId}";
    }
}

?>
