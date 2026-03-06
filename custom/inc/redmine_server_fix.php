<?php
/**
 * Server-side Redmine Bug Display Fix for TestLink
 * 
 * This file provides a direct server-side solution to make bug IDs clickable in TestLink
 */

/**
 * Function to make bug IDs clickable in TestLink
 */
function redmine_server_fix() {
    global $tlCfg;
    
    // Get the Redmine URL from configuration
    $redmineUrl = isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['url']) ? 
                 $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] : 'https://support.profinch.com';
    
    // Start output buffering to capture and modify the HTML
    ob_start(function($buffer) use ($redmineUrl) {
        // Pattern to match bug IDs (64 followed by 3-5 digits)
        $pattern = '/(64\d{3,5})/i';
        
        // Replace with links
        $replacement = '<a href="' . $redmineUrl . '/issues/$1" class="redmine-bug-link" target="_blank" style="color: #0066cc; font-weight: bold; text-decoration: underline;">$1</a>';
        
        // Perform the replacement
        $modified = preg_replace($pattern, $replacement, $buffer);
        
        return $modified;
    });
    
    // Return true to indicate success
    return true;
}

/**
 * Function to be called at the end of the page
 */
function redmine_server_fix_end() {
    // End output buffering and flush the modified content
    if (ob_get_level() > 0) {
        ob_end_flush();
    }
    
    return true;
}
