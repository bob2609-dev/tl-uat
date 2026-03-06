/**
 * Redmine Bug ID Fix for TestLink
 * 
 * This script scans the page for bug IDs (numbers starting with 64 followed by 3-5 digits)
 * and replaces them with clickable links to the Redmine issue tracker.
 */

// Self-executing function to avoid polluting the global namespace
try {
(function() {
    // Set the Redmine base URL
    var redmineUrl = 'https://support.profinch.com';
    
    // Function to create visual indicator
    function createIndicator() {
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
        return indicator;
    }
    
    // Function to find and replace bug IDs with links
    function fixBugLinks() {
        console.log('Redmine fix: Scanning for bug IDs');
        
        // Create indicator if it doesn't exist
        var indicator = document.getElementById('redmine-fix-indicator');
        if (!indicator) {
            indicator = createIndicator();
        }
        
        // Look for bug IDs in the entire document
        var pattern = /\b(64\d{3,5})\b/g;
        var found = 0;
        
        // First, try to find bug IDs in the BUG management column
        var bugCells = document.querySelectorAll('td:nth-child(5)');
        console.log('Redmine fix: Found ' + bugCells.length + ' potential bug cells to scan');
        
        bugCells.forEach(function(cell) {
            // Check if this cell contains bug IDs
            var cellText = cell.textContent || cell.innerText;
            if (pattern.test(cellText)) {
                console.log('Redmine fix: Found bug ID in cell: ' + cellText);
                
                // Use TreeWalker to find all text nodes in this cell
                var walker = document.createTreeWalker(
                    cell,
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
                    if (parent && (parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE' || parent.tagName === 'A')) {
                        continue;
                    }
                    
                    // Check if the node contains a bug ID
                    if (pattern.test(node.nodeValue)) {
                        console.log('Redmine fix: Found bug ID in text: ' + node.nodeValue);
                        nodesToReplace.push(node);
                    }
                }
                
                // Replace the bug IDs with links
                nodesToReplace.forEach(function(node) {
                    var text = node.nodeValue;
                    
                    // Create a span element with the replaced text
                    var span = document.createElement('span');
                    span.innerHTML = text.replace(pattern, '<a href="' + redmineUrl + '/issues/$1" target="_blank" style="color:#0066cc;font-weight:bold;">$1</a>');
                    
                    // Replace the text node with the span
                    if (node.parentNode) {
                        node.parentNode.replaceChild(span, node);
                        found++;
                    }
                });
            }
        });
        
        // If we didn't find any bug IDs in the cells, try scanning all tables
        if (found === 0) {
            // Target all tables where bug IDs might appear
            var tables = document.querySelectorAll('table');
            console.log('Redmine fix: Found ' + tables.length + ' tables to scan');
            
            tables.forEach(function(table) {
                // Use TreeWalker to find all text nodes in this table
                var walker = document.createTreeWalker(
                    table,
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
                    if (parent && (parent.tagName === 'SCRIPT' || parent.tagName === 'STYLE' || parent.tagName === 'A')) {
                        continue;
                    }
                    
                    // Check if the node contains a bug ID
                    if (pattern.test(node.nodeValue)) {
                        console.log('Redmine fix: Found bug ID in text: ' + node.nodeValue);
                        nodesToReplace.push(node);
                    }
                }
                
                // Replace the bug IDs with links
                nodesToReplace.forEach(function(node) {
                    var text = node.nodeValue;
                    
                    // Create a span element with the replaced text
                    var span = document.createElement('span');
                    span.innerHTML = text.replace(pattern, '<a href="' + redmineUrl + '/issues/$1" target="_blank" style="color:#0066cc;font-weight:bold;">$1</a>');
                    
                    // Replace the text node with the span
                    if (node.parentNode) {
                        node.parentNode.replaceChild(span, node);
                        found++;
                    }
                });
            });
        }
        
        console.log('Redmine fix: Replaced ' + found + ' bug IDs');
        
        // Update the indicator with the count
        if (found > 0) {
            indicator.innerHTML = 'Redmine Fix: ' + found + ' bug IDs linked';
            indicator.style.background = 'rgba(0,128,0,0.7)';  // Green background
        } else {
            indicator.innerHTML = 'Redmine Fix Active (0 bug IDs found)';
        }
        
        return found;
    }
    
    // Run when the page is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Redmine fix: DOM ready');
            setTimeout(function() {
                fixBugLinks();
                // Run periodically to catch any dynamically added content
                setInterval(fixBugLinks, 2000);
            }, 500);  // Small delay to ensure tables are rendered
        });
    } else {
        console.log('Redmine fix: DOM already loaded');
        setTimeout(function() {
            fixBugLinks();
            // Run periodically to catch any dynamically added content
            setInterval(fixBugLinks, 2000);
        }, 500);  // Small delay to ensure tables are rendered
    }
})();
} catch (error) {
    console.error('Redmine fix error:', error);
    // Create an error indicator
    var errorIndicator = document.createElement('div');
    errorIndicator.style.position = 'fixed';
    errorIndicator.style.bottom = '10px';
    errorIndicator.style.right = '10px';
    errorIndicator.style.background = 'rgba(255,0,0,0.7)';
    errorIndicator.style.color = 'white';
    errorIndicator.style.padding = '5px';
    errorIndicator.style.fontSize = '10px';
    errorIndicator.style.zIndex = '9999';
    errorIndicator.innerHTML = 'Redmine Fix Error: ' + error.message;
    document.body.appendChild(errorIndicator);
}
