<?php
/**
 * Force Fix for Redmine Bug Display in TestLink
 * 
 * This file provides a direct JavaScript injection to make bug IDs clickable in TestLink
 * regardless of how TestLink's bug tracking integration is configured.
 */

/**
 * Function to inject JavaScript to make bug IDs clickable
 */
function redmine_force_fix() {
    // Get the JavaScript file path
    $jsPath = 'custom/inc/redmine_direct_fix.js';
    
    // Output the script tag to include the JavaScript
    echo '<script type="text/javascript" src="' . $jsPath . '?v=' . time() . '"></script>';
    
    return true;
}
