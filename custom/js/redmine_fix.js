/**
 * Redmine Bug ID Fix for TestLink
 * 
 * This script scans the page for bug IDs (numbers starting with 64 followed by 3-5 digits)
 * and replaces them with clickable links to the Redmine issue tracker.
 */

// Self-executing function to avoid polluting the global namespace
(function() {
    console.log('Redmine bug fix script loaded');
    
    // Get the Redmine URL from a global variable or use a default
    var redmineUrl = (typeof window.redmineBaseUrl !== 'undefined') ? 
                     window.redmineBaseUrl : 'https://support.profinch.com';
    
    // Create a visual indicator to show the script is running
    var indicator = document.createElement('div');
    indicator.id = 'redmine-fix-indicator';
    indicator.style.position = 'fixed';
    indicator.style.bottom = '10px';
    indicator.style.right = '10px';
    indicator.style.background = 'rgba(0,0,0,0.7)';
    indicator.style.color = 'white';
    indicator.style.padding = '5px';
    indicator.style.fontSize = '10px';
    indicator.style.zIndex = '9999';
    indicator.innerHTML = 'Redmine Fix Active';
    document.body.appendChild(indicator);
    
    // Function to find and replace bug IDs with links
    function fixBugLinks() {
        console.log('Scanning for bug IDs');
        var pattern = /\b(64\d{3,5})\b/g;
        var found = 0;
        
        // Use TreeWalker to find all text nodes
        var walker = document.createTreeWalker(
            document.body,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
        
        // Collect nodes to replace
        var nodesToReplace = [];
        var node;
        while (node = walker.nextNode()) {
            // Skip script and style tags
            var parent = node.parentNode;
            if (parent && (parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE')) {
                continue;
            }
            
            // Check if the node contains a bug ID
            if (pattern.test(node.nodeValue)) {
                nodesToReplace.push(node);
            }
        }
        
        console.log('Found ' + nodesToReplace.length + ' nodes with bug IDs');
        
        // Replace the bug IDs with links
        for (var i = 0; i < nodesToReplace.length; i++) {
            var node = nodesToReplace[i];
            var text = node.nodeValue;
            
            // Create a span element with the replaced text
            var span = document.createElement('span');
            span.innerHTML = text.replace(pattern, '<a href="' + redmineUrl + '/issues/$1" target="_blank" style="color:#0066cc;font-weight:bold;">$1</a>');
            
            // Replace the text node with the span
            if (node.parentNode) {
                node.parentNode.replaceChild(span, node);
                found++;
            }
        }
        
        console.log('Replaced ' + found + ' bug IDs');
        
        // Update the indicator with the count
        if (found > 0) {
            indicator.innerHTML = 'Redmine Fix: ' + found + ' bug IDs linked';
        }
    }
    
    // Run the fix when the DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixBugLinks);
    } else {
        fixBugLinks();
    }
    
    // Also run periodically to catch any dynamically added content
    setInterval(fixBugLinks, 2000);
})();
