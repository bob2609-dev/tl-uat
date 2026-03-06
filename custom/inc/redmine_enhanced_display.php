<?php
/**
 * Enhanced Redmine Bug Display for TestLink
 * 
 * This file provides a comprehensive solution to display Redmine bugs with details in TestLink
 */

// Include our serialization fix
require_once(dirname(dirname(dirname(__FILE__))) . '/custom/inc/redmine_serialization_fix.php');

/**
 * Function to enhance the bug display in TestLink
 */
function redmine_enhanced_display() {
    global $basehref;
    
    // Get the Redmine configuration
    global $tlCfg;
    $redmineUrl = $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'];
    $redmineApiKey = $tlCfg->issueTracker->toolsDefaultValues['redmine']['apikey'];
    
    // Add the JavaScript to fix bug display
    echo '<script type="text/javascript">
    // Wait for page is loaded
    document.addEventListener("DOMContentLoaded", function() {
        console.log("Redmine enhanced display loaded");
        
        // Add custom styles for bug links
        var style = document.createElement("style");
        style.textContent = "\n\
            .redmine-bug-link {\n\
                color: #0066cc !important;\n\
                font-weight: bold !important;\n\
                text-decoration: underline !important;\n\
                display: inline-block !important;\n\
                margin: 2px 0 !important;\n\
            }\n\
            .redmine-bug-details {\n\
                margin-top: 3px;\n\
                padding: 5px;\n\
                background-color: #f5f5f5;\n\
                border: 1px solid #ddd;\n\
                border-radius: 3px;\n\
                font-size: 12px;\n\
            }\n\
            .redmine-bug-status {\n\
                display: inline-block;\n\
                padding: 2px 5px;\n\
                background-color: #5cb85c;\n\
                color: white;\n\
                border-radius: 3px;\n\
                margin-right: 5px;\n\
            }\n\
            .redmine-bug-status.closed {\n\
                background-color: #777;\n\
            }\n\
            .redmine-bug-status.in-progress {\n\
                background-color: #f0ad4e;\n\
            }\n\
        ";
        document.head.appendChild(style);
        
        // Function to fetch bug details from Redmine
        function fetchBugDetails(bugId, element) {
            // Create a container for the bug details
            var detailsContainer = document.createElement("div");
            detailsContainer.className = "redmine-bug-details";
            detailsContainer.innerHTML = "Loading bug details...";
            element.appendChild(detailsContainer);
            
            // Make an AJAX request to get bug details
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "' . $basehref . 'custom/inc/redmine_proxy.php?id=" + bugId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.issue) {
                                var issue = response.issue;
                                var statusClass = "open";
                                if (issue.status.name.toLowerCase().includes("closed")) {
                                    statusClass = "closed";
                                } else if (issue.status.name.toLowerCase().includes("progress")) {
                                    statusClass = "in-progress";
                                }
                                
                                detailsContainer.innerHTML = 
                                    "<span class=\"redmine-bug-status " + statusClass + "\">" + issue.status.name + "</span>" +
                                    "<strong>Priority:</strong> " + issue.priority.name + "<br>" +
                                    "<strong>Subject:</strong> " + issue.subject + "<br>" +
                                    "<strong>Description:</strong> " + (issue.description ? issue.description.substring(0, 100) + "..." : "No description");
                            } else {
                                detailsContainer.innerHTML = "Error loading bug details: No issue data";
                            }
                        } catch (e) {
                            detailsContainer.innerHTML = "Error parsing bug details: " + e.message;
                        }
                    } else {
                        detailsContainer.innerHTML = "Error loading bug details: " + xhr.status;
                    }
                }
            };
            xhr.send();
        }
        
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
                        var link = document.createElement("a");
                        link.href = "' . $redmineUrl . '/issues/" + bugId;
                        link.target = "_blank";
                        link.className = "redmine-bug-link";
                        link.textContent = bugId;
                        
                        // Clear the cell and add the link
                        cell.innerHTML = "";
                        cell.appendChild(link);
                        
                        // Fetch and display bug details
                        fetchBugDetails(bugId, cell);
                    }
                });
            });
        }
    });
    </script>';
    
    return true;
}
