<?php
/**
 * Working Custom Issue Handler for TestLink
 * Handles issue creation when using custom integrations (logging-only mode)
 */

function custom_write_execution(&$dbHandler, &$argsObj, &$requestObj, &$issueTracker) {
    error_log("[CUSTOM_HANDLER] custom_write_execution called");
    error_log("[CUSTOM_HANDLER] createIssue flag: " . (isset($argsObj->createIssue) ? ($argsObj->createIssue ? 'true' : 'false') : 'not set'));
    
    // First, save the execution normally
    $result = write_execution($dbHandler, $argsObj, $requestObj, $issueTracker);
    
    // Check if we need to create an issue
    if (isset($argsObj->createIssue) && $argsObj->createIssue) {
        error_log("[CUSTOM_HANDLER] Issue creation requested - using mock response");
        
        // Return mock success response (logging-only mode)
        $mockIssueId = "MOCK-" . time();
        $mockIssueUrl = "https://support.profinch.com/issues/" . $mockIssueId;
        
        $result[1] = array( // addIssueOp
            'success' => true,
            'issue_id' => $mockIssueId,
            'issue_url' => $mockIssueUrl,
            'message' => "Mock issue created successfully (logging mode)"
        );
        
        error_log("[CUSTOM_HANDLER] Mock issue created: $mockIssueId");
    }
    
    return $result;
}
?>
