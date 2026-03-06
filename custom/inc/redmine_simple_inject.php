<?php
/**
 * Simple Redmine Integration Injector
 * 
 * This file directly injects Redmine bug details into TestLink pages
 * following the same approach used for the image display fix
 */

// Only run once
if (!defined('REDMINE_SIMPLE_INJECTOR_INCLUDED')) {
    define('REDMINE_SIMPLE_INJECTOR_INCLUDED', true);
    
    // Configuration - using the same settings from custom_config.inc.php
    global $tlCfg;
    $redmineUrl = isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['url']) ? 
                $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] : 'https://support.profinch.com';
    $redmineApiKey = isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['apikey']) ? 
                   $tlCfg->issueTracker->toolsDefaultValues['redmine']['apikey'] : 'a597e200f8923a85484e81ca81d731827b8dbf3d';
    
    // Get base URL
    $baseHref = '';
    if (isset($_SESSION['basehref'])) {
        $baseHref = $_SESSION['basehref'];
    } elseif (defined('TL_BASE_HREF')) {
        $baseHref = TL_BASE_HREF;
    } else {
        // Fallback - construct from server variables
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $uri = isset($_SERVER['SCRIPT_NAME']) ? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') : '';
        $baseHref = $protocol . $host . $uri . '/';
    }
    
    // Add the JavaScript to enhance bug display
    echo "<script type='text/javascript'>
    // Wait for page to load
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Redmine simple inject loaded');
        
        // Add custom styles
        var style = document.createElement('style');
        style.textContent = '\
            .redmine-bug-link {\n\
                color: #0066cc !important;\n\
                font-weight: bold !important;\n\
                text-decoration: underline !important;\n\
                display: inline-block !important;\n\
                margin: 2px 0 !important;\n\
            }\n\
        ';
        document.head.appendChild(style);
        
        // Function to find bug IDs in the page
        function findBugIds() {
            // Look for bug IDs in the page
            var bugPattern = /64\\d{3,5}/g;
            var pageText = document.body.innerHTML;
            var matches = pageText.match(bugPattern);
            
            if (matches) {
                // Get unique bug IDs
                var uniqueBugs = [];
                matches.forEach(function(bugId) {
                    if (uniqueBugs.indexOf(bugId) === -1) {
                        uniqueBugs.push(bugId);
                    }
                });
                
                console.log('Found bug IDs:', uniqueBugs);
                return uniqueBugs;
            }
            
            return [];
        }
        
        // Function to enhance bug display
        function enhanceBugDisplay() {
            var bugIds = findBugIds();
            
            bugIds.forEach(function(bugId) {
                // Find all table cells
                var cells = document.querySelectorAll('td');
                cells.forEach(function(cell) {
                    // If the cell contains just the bug ID
                    if (cell.textContent.trim() === bugId) {
                        // Create a link
                        var link = document.createElement('a');
                        link.href = '{$redmineUrl}/issues/' + bugId;
                        link.target = '_blank';
                        link.className = 'redmine-bug-link';
                        link.textContent = bugId + ': Redmine Issue';
                        
                        // Clear the cell and add the link
                        cell.innerHTML = '';
                        cell.appendChild(link);
                    }
                });
            });
        }
        
        // Run the bug display enhancement
        enhanceBugDisplay();
        
        // Also run periodically to catch any new bugs
        setInterval(enhanceBugDisplay, 5000);
    });
    </script>";
}
