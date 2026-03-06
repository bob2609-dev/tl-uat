<?php
/**
 * Custom header inclusion for Redmine integration
 * This file loads the necessary JavaScript files for the Redmine integration
 */

// Only include scripts once
if (!defined('REDMINE_FIX_INCLUDED')) {
    define('REDMINE_FIX_INCLUDED', true);
    
    // Get the base URL
    $baseHref = isset($_SESSION['basehref']) ? $_SESSION['basehref'] : './';
    
    // Add styles for Redmine integration
    echo "<style>
        .redmine-button {
            background-color: #f0ad4e;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            margin: 10px 0;
            display: inline-block;
            text-decoration: none;
            font-weight: bold;
        }
        .redmine-button:hover {
            background-color: #ec971f;
        }
        
        /* Styles for inline integration */
        .redmine-controls { 
            margin: 10px 0; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            background-color: #f9f9f9; 
        }
        .redmine-bug-item { 
            margin: 5px 0; 
            padding: 8px; 
            background-color: #fff; 
            border: 1px solid #ddd; 
            border-radius: 3px; 
        }
        .redmine-dialog {
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
        }
    </style>";
    
    // Include the direct buttons script for basic integration
    echo "<script type='text/javascript' src='{$baseHref}redmine_direct_buttons.js'></script>";
    
    // Include the inline integration script for advanced functionality
    echo "<script type='text/javascript' src='{$baseHref}redmine_inline_integration.js'></script>";
    
    // Include the template for direct controls if we're on an execution page
    if (strpos($_SERVER['SCRIPT_NAME'], 'execSetResults.php') !== false) {
        include_once('redmine_direct_controls.inc.tpl');
    }
    
    // Set flag to indicate we've included the files
    $redmineFixIncluded = true;
}
?>
