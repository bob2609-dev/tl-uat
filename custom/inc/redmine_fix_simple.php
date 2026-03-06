<?php
/**
 * Simple Fix for Redmine Bug Display in TestLink
 * 
 * This file provides a direct solution to display Redmine bugs in TestLink
 * by modifying the execution page template.
 */

/**
 * Function to directly fix the bug display in TestLink
 */
function redmine_fix_simple() {
    // Add a simple script to fix the bug display
    echo '<script type="text/javascript">
    // Run when page is loaded
    document.addEventListener("DOMContentLoaded", function() {
        // Find all bug IDs in the page
        var bugPattern = /64\\d{3}/g; // Match 64 followed by 3 digits
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
            
            console.log("Found bug IDs:", uniqueBugs);
            
            // For each bug ID, create a link and insert it into the relevant bugs column
            uniqueBugs.forEach(function(bugId) {
                // Find all table cells
                var cells = document.querySelectorAll("td");
                cells.forEach(function(cell) {
                    // If the cell contains just the bug ID
                    if (cell.textContent.trim() === bugId) {
                        // Create a link
                        cell.innerHTML = \'<a href="https://support.profinch.com/issues/\' + bugId + \'" target="_blank" style="color:#0066cc;font-weight:bold;">\' + bugId + \': Redmine Issue</a>\';
                    }
                });
            });
        }
    });
    </script>';
    
    return true;
}
