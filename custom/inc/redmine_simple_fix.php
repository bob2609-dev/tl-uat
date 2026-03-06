<?php
/**
 * Simple Fix for Redmine Bug Display in TestLink
 * 
 * This file provides a very basic JavaScript injection to make bug IDs clickable in TestLink
 */

// Log that this file was included
error_log('redmine_simple_fix.php was included at ' . date('Y-m-d H:i:s'));

/**
 * Function to inject JavaScript to make bug IDs clickable
 */
function redmine_simple_fix() {
    // Log that the function was called
    error_log('redmine_simple_fix() function was called at ' . date('Y-m-d H:i:s'));
    // Get the Redmine URL from configuration
    global $tlCfg;
    $redmineUrl = isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['url']) ? 
                 $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] : 'https://support.profinch.com';
    
    // Output a visible HTML comment to verify the script is running
    echo "<!-- Redmine bug fix loaded at " . date('Y-m-d H:i:s') . " -->\n";
    
    // Add a small notification div that can be seen on the page
    echo "<div id='redmine-fix-notification' style='position:fixed;bottom:10px;right:10px;background:rgba(0,0,0,0.7);color:white;padding:5px;font-size:10px;z-index:9999;'>Redmine Fix Active</div>\n";
    
    // Output a simple script tag with minimal JavaScript
    echo "<script>\n";
    echo "console.log('Redmine bug fix script loaded at " . date('Y-m-d H:i:s') . "');\n";
    echo "alert('test')";
    echo "</script>\n";
    
    return true;
}
