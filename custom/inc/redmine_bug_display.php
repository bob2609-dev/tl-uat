<?php
/**
 * Redmine Bug Display for TestLink
 * 
 * This file provides a custom solution to display Redmine bugs in TestLink
 * following the same approach used for the image display fix.
 */

// Include our serialization fix
require_once(dirname(dirname(dirname(__FILE__))) . '/custom/inc/redmine_serialization_fix.php');

/**
 * Custom function to display Redmine bugs in TestLink
 */
function redmine_bug_display() {
    global $basehref;
    
    // Add the JavaScript to fix bug display
    $html = '<script type="text/javascript">
    // Wait for page to load
    window.addEventListener("DOMContentLoaded", function() {
        console.log("Redmine bug display loaded - Enhanced version");
        
        // Add custom styles for bug links
        var style = document.createElement("style");
        style.textContent = "\\n\
            .redmine-bug-link {\\n\
                color: #0066cc !important;\\n\
                font-weight: bold !important;\\n\
                text-decoration: underline !important;\\n\
                display: inline-block !important;\\n\
                margin: 2px 0 !important;\\n\
                padding: 2px 5px !important;\\n\
                background-color: #f5f5f5 !important;\\n\
                border-radius: 3px !important;\\n\
                border: 1px solid #ddd !important;\\n\
            }\\n\
        ";
        document.head.appendChild(style);
        
        // Function to fix bug display - more aggressive version
        function fixBugDisplay() {
            console.log("Running bug display fix");
            
            // Method 1: Fix by looking at tables with Relevant bugs header
            var allTables = document.querySelectorAll("table");
            allTables.forEach(function(table) {
                // Check if this is a bug table
                var headers = table.querySelectorAll("th");
                var isBugTable = false;
                var bugColumnIndex = -1;
                
                // Find the Relevant bugs column
                headers.forEach(function(header, index) {
                    if (header.textContent.includes("Relevant bugs")) {
                        isBugTable = true;
                        bugColumnIndex = index;
                        console.log("Found bug table with Relevant bugs at column " + bugColumnIndex);
                    }
                });
                
                if (isBugTable && bugColumnIndex >= 0) {
                    // Find all rows in the table
                    var rows = table.querySelectorAll("tr");
                    rows.forEach(function(row) {
                        var cells = row.querySelectorAll("td");
                        if (cells.length > bugColumnIndex) {
                            var bugCell = cells[bugColumnIndex];
                            
                            // If the cell is empty or just contains whitespace
                            if (bugCell && bugCell.textContent.trim() === "") {
                                // Look for delete buttons in the row
                                var deleteButtons = row.querySelectorAll("img[onclick*=\'deleteBug\']");
                                deleteButtons.forEach(function(button) {
                                    var onclickAttr = button.getAttribute("onclick") || "";
                                    var matches = onclickAttr.match(/\'(\\d+)-\\d+-\\d+\'/);
                                    
                                    if (matches && matches[1]) {
                                        var bugId = matches[1];
                                        console.log("Found bug ID from delete button: " + bugId);
                                        
                                        // Create a link to the bug
                                        var bugLink = document.createElement("a");
                                        bugLink.href = "[https://support.profinch.com/issues/"](https://support.profinch.com/issues/") + bugId;
                                        bugLink.target = "_blank";
                                        bugLink.className = "redmine-bug-link";
                                        bugLink.textContent = bugId + ": Redmine Issue";
                                        
                                        // Add the link to the cell
                                        bugCell.appendChild(bugLink);
                                        bugCell.appendChild(document.createElement("br"));
                                    }
                                });
                            }
                        }
                    });
                }
            });
            
            // Method 2: Look for any delete bug buttons anywhere on the page
            var allDeleteButtons = document.querySelectorAll("img[onclick*=\'deleteBug\']");
            allDeleteButtons.forEach(function(button) {
                var onclickAttr = button.getAttribute("onclick") || "";
                var matches = onclickAttr.match(/\'(\\d+)-\\d+-\\d+\'/);
                
                if (matches && matches[1]) {
                    var bugId = matches[1];
                    console.log("Found bug ID from global search: " + bugId);
                    
                    // Find the parent row
                    var row = button.closest("tr");
                    if (row) {
                        // Find all cells in the row
                        var cells = row.querySelectorAll("td");
                        cells.forEach(function(cell, index) {
                            // If this is likely the bug cell (typically before the delete button cell)
                            if (index < cells.length - 1 && cell.textContent.trim() === "") {
                                // Create a link to the bug
                                var bugLink = document.createElement("a");
                                bugLink.href = "[https://support.profinch.com/issues/"](https://support.profinch.com/issues/") + bugId;
                                bugLink.target = "_blank";
                                bugLink.className = "redmine-bug-link";
                                bugLink.textContent = bugId + ": Redmine Issue";
                                
                                // Add the link to the cell
                                cell.appendChild(bugLink);
                            }
                        });
                    }
                }
            });
        }
        
        // Run the fix immediately
        fixBugDisplay();
        
        // Also run periodically in case of AJAX updates
        setInterval(fixBugDisplay, 1000);
        
        // Add a mutation observer to detect DOM changes
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    // DOM changed, try to fix bug display
                    fixBugDisplay();
                }
            });
        });
        
        // Start observing the document body for changes
        observer.observe(document.body, { childList: true, subtree: true });
    });
    </script>';
    
    return $html;
}