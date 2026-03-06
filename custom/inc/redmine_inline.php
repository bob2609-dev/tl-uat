<?php
/**
 * Inline Fix for Redmine Bug Display in TestLink
 * 
 * This file provides a direct JavaScript injection to make bug IDs clickable in TestLink
 * regardless of how TestLink's bug tracking integration is configured.
 */

/**
 * Function to inject JavaScript to make bug IDs clickable
 */
function redmine_inline_fix() {
    // Output a simple alert to verify the script is running
    echo '<script>console.log("Redmine bug fix script loaded at " + new Date().toISOString());</script>';
    
    // Output the JavaScript directly
    echo '<script>
    // Redmine Bug Fix Script
    (function() {
        // Configuration
        var redmineUrl = "https://support.profinch.com";
        var bugPattern = /\\b(64\\d{3,5})\\b/g;
        
        // Debug function
        function log(msg, data) {
            console.log("REDMINE FIX: " + msg, data || "");
        }
        
        log("Script initialized");
        
        // Function to scan the page and replace bug IDs with links
        function replaceBugIds() {
            log("Scanning for bug IDs");
            var bugsFound = 0;
            
            // Test regex on sample data
            var testString = "Test with bug IDs: 64730, 64731, 64732";
            var testMatches = testString.match(bugPattern);
            log("Regex test:", testMatches);
            
            // Scan all text nodes
            var walker = document.createTreeWalker(
                document.body,
                NodeFilter.SHOW_TEXT,
                null,
                false
            );
            
            var nodesToReplace = [];
            var node;
            while (node = walker.nextNode()) {
                var text = node.nodeValue;
                if (text && bugPattern.test(text)) {
                    nodesToReplace.push(node);
                }
            }
            
            log("Found " + nodesToReplace.length + " text nodes with bug IDs");
            
            // Replace the nodes
            nodesToReplace.forEach(function(node) {
                var text = node.nodeValue;
                var matches = text.match(bugPattern);
                if (matches) {
                    bugsFound += matches.length;
                    log("Bug IDs found: " + matches.join(", "));
                    
                    var span = document.createElement("span");
                    span.innerHTML = text.replace(bugPattern, 
                        '<a href="' + redmineUrl + '/issues/$1" ' +
                        'style="color:#0066cc;font-weight:bold;text-decoration:underline;" ' +
                        'target="_blank">$1</a>');
                    
                    if (node.parentNode) {
                        node.parentNode.replaceChild(span, node);
                    }
                }
            });
            
            log("Replaced " + bugsFound + " bug IDs");
        }
        
        // Run immediately
        replaceBugIds();
        
        // Also run when DOM is fully loaded
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", replaceBugIds);
        }
        
        // Run periodically
        setInterval(replaceBugIds, 2000);
    })();
    </script>';
    
    return true;
}
