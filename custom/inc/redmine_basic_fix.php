<?php
/**
 * Basic Redmine Bug Display Fix for TestLink
 * 
 * This file provides a very simple solution to make bug IDs clickable in TestLink
 */

/**
 * Function to make bug IDs clickable in TestLink
 */
function redmine_basic_fix() {
    global $tlCfg;
    
    // Get the Redmine URL from configuration
    $redmineUrl = isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['url']) ? 
                 $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] : 'https://support.profinch.com';
    
    // Output a simple CSS and JavaScript to enhance bug display
    echo '<style>
        .redmine-bug-link {
            color: #0066cc !important;
            font-weight: bold !important;
            text-decoration: underline !important;
        }
    </style>';
    
    echo '<script type="text/javascript">
    // Execute immediately and also after DOM is loaded
    (function() {
        function injectBugLinks() {
            // Direct approach - find table cells with bug IDs
            var tables = document.getElementsByTagName("table");
            for (var i = 0; i < tables.length; i++) {
                var rows = tables[i].getElementsByTagName("tr");
                for (var j = 0; j < rows.length; j++) {
                    var cells = rows[j].getElementsByTagName("td");
                    for (var k = 0; k < cells.length; k++) {
                        var cell = cells[k];
                        // Look for bug IDs in the cell text
                        var text = cell.innerHTML;
                        if (text && text.match(/64\d{3,5}/)) {
                            // Replace bug IDs with links
                            cell.innerHTML = text.replace(/(64\d{3,5})/g, 
                                "<a href=\"' . $redmineUrl . '/issues/$1\" class=\"redmine-bug-link\" target=\"_blank\">$1</a>");
                        }
                    }
                }
            }
            
            // Also look for bug IDs in delete confirmation handlers
            var scripts = document.getElementsByTagName("script");
            for (var s = 0; s < scripts.length; s++) {
                if (scripts[s].innerHTML && scripts[s].innerHTML.indexOf("delete_confirmation") > -1 && 
                    scripts[s].innerHTML.match(/64\d{3,5}/)) {
                    console.log("Found script with bug ID");
                }
            }
        }
        
        // Run immediately
        injectBugLinks();
        
        // Also run when DOM is loaded
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", injectBugLinks);
        }
        
        // Run periodically to catch any dynamically added content
        setInterval(injectBugLinks, 1000);
    })();
    </script>';
    
    return true;
}