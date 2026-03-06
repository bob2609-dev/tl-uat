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
    logCustomIssueHandler("tproject_id: " . ($argsObj->tproject_id ?? 'NULL'));
    logCustomIssueHandler("tcversion_id: " . ($argsObj->tcversion_id ?? 'NULL'));
    
    // First, save the execution normally using TestLink's write_execution
    logCustomIssueHandler("Calling write_execution to save test execution");
    $result = write_execution($dbHandler, $argsObj, $requestObj, $issueTracker);
    
    // Check if we need to create an issue using custom integration
    if (isset($argsObj->createIssue) && $argsObj->createIssue) {
        logCustomIssueHandler("Issue creation requested - using custom integration");
        
        // Get integration details from the args object (populated from database)
        $integration = $argsObj->custom_integration ?? null;
        
        if (!$integration) {
            logCustomIssueHandler("ERROR: No integration configuration found");
            $result[1] = array( // addIssueOp
                'success' => false,
                'message' => 'No integration configuration found'
            );
        } else {
            // Get integration details from database
            $integrationType = $integration['type'] ?? 'REDMINE';
            $integrationUrl = $integration['url'] ?? '';
            $integrationName = $integration['name'] ?? 'Unknown';
            
            logCustomIssueHandler("Integration name: $integrationName");
            logCustomIssueHandler("Integration type: $integrationType");
            logCustomIssueHandler("Integration URL: $integrationUrl");
            
            // Get issue data
            $summary = $argsObj->bug_summary ?? '';
            $description = $argsObj->bug_notes ?? '';
            $priority = $argsObj->issuePriority ?? $integration['default_priority'] ?? 'Normal';
            
            // Get test case and execution info
            $tc_id = $argsObj->tc_id ?? 0;
            $execution_id = $argsObj->execution_id ?? 0;
            
            logCustomIssueHandler("Issue data - Summary: " . substr($summary, 0, 100) . "...");
            logCustomIssueHandler("Issue data - Priority: $priority");
            logCustomIssueHandler("Issue data - TC ID: $tc_id, Exec ID: $execution_id");
            
            if (empty($summary)) {
                logCustomIssueHandler("ERROR: Bug summary is required");
                $result[1] = array( // addIssueOp
                    'success' => false,
                    'message' => 'Bug summary is required'
                );
            } else {
                // Create mock response based on integration type (logging-only mode)
                $mockIssueId = generateMockIssueId($integrationType);
                $mockIssueUrl = generateMockIssueUrl($integrationType, $integrationUrl, $mockIssueId);
                
                logCustomIssueHandler("Mock issue created: $mockIssueId for $integrationType");
                
                $result[1] = array( // addIssueOp
                    'success' => true,
                    'issue_id' => $mockIssueId,
                    'issue_url' => $mockIssueUrl,
                    'message' => "Mock $integrationType issue created successfully (logging mode)"
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
