/**
 * Direct Redmine Bug Link Fix for TestLink
 * 
 * This script directly scans the page for bug IDs and replaces them with links
 * without relying on TestLink's bug tracking integration.
 */

(function() {
    // Configuration
    var redmineUrl = 'https://support.profinch.com';
    var bugPattern = /\b(64\d{3,5})\b/g;
    
    // Debug function to log with timestamp
    function debugLog(message, data) {
        var timestamp = new Date().toISOString();
        if (data) {
            console.log('[' + timestamp + '] REDMINE FIX: ' + message, data);
        } else {
            console.log('[' + timestamp + '] REDMINE FIX: ' + message);
        }
    }
    
    // Log script initialization
    debugLog('Script initialized with redmineUrl: ' + redmineUrl);
    
    // Function to scan the page and replace bug IDs with links
    function replaceBugIds() {
        debugLog('Starting page scan for bug IDs');
        var totalBugsFound = 0;
        var bugsReplaced = 0;
        
        // Dump all text content for debugging
        debugLog('Page text content (sample):', document.body.textContent.substring(0, 500));
        
        // Test if our regex works on sample data
        var testString = "Test with bug IDs: 64730, 64731, 64732";
        var testMatches = testString.match(bugPattern);
        debugLog('Regex test on sample data:', testMatches);
        
        // First, look for table cells with bug IDs
        var tables = document.getElementsByTagName('table');
        debugLog('Found ' + tables.length + ' tables to scan');
        
        for (var i = 0; i < tables.length; i++) {
            var cells = tables[i].getElementsByTagName('td');
            debugLog('Table ' + i + ' has ' + cells.length + ' cells');
            
            for (var j = 0; j < cells.length; j++) {
                var cell = cells[j];
                var html = cell.innerHTML;
                var text = cell.textContent;
                
                // Skip cells that already contain links
                if (html.indexOf('<a ') !== -1) continue;
                
                // Check if the text content contains a bug ID
                var matches = text.match(bugPattern);
                if (matches) {
                    totalBugsFound += matches.length;
                    debugLog('Found bug ID(s) in cell: ' + matches.join(', '), {
                        cellIndex: j,
                        tableIndex: i,
                        cellText: text,
                        cellHTML: html
                    });
                    
                    // Replace the bug IDs with links
                    cell.innerHTML = html.replace(bugPattern, 
                        '<a href="' + redmineUrl + '/issues/$1" class="redmine-bug-link" ' +
                        'style="color:#0066cc;font-weight:bold;text-decoration:underline;" ' +
                        'target="_blank">$1</a>');
                    bugsReplaced += matches.length;
                }
            }
        }
        
        // Also look for text nodes directly in the document
        debugLog('Scanning text nodes in the document');
        var textNodes = [];
        var walker = document.createTreeWalker(
            document.body, 
            NodeFilter.SHOW_TEXT, 
            null, 
            false
        );
        
        var node;
        while(node = walker.nextNode()) {
            var text = node.nodeValue;
            if (text && bugPattern.test(text)) {
                var matches = text.match(bugPattern);
                totalBugsFound += matches.length;
                debugLog('Found bug ID(s) in text node: ' + matches.join(', '), {
                    nodeText: text,
                    parentNode: node.parentNode.tagName
                });
                
                // Create a replacement span
                var span = document.createElement('span');
                span.innerHTML = text.replace(bugPattern, 
                    '<a href="' + redmineUrl + '/issues/$1" class="redmine-bug-link" ' +
                    'style="color:#0066cc;font-weight:bold;text-decoration:underline;" ' +
                    'target="_blank">$1</a>');
                
                // Replace the text node with our span
                if (node.parentNode) {
                    node.parentNode.replaceChild(span, node);
                    bugsReplaced += matches.length;
                }
            }
        }
        
        // Also look for bug IDs in delete confirmation handlers
        var scripts = document.getElementsByTagName('script');
        debugLog('Scanning ' + scripts.length + ' script tags');
        
        for (var s = 0; s < scripts.length; s++) {
            if (scripts[s].innerHTML && 
                scripts[s].innerHTML.indexOf('delete_confirmation') > -1) {
                
                var scriptContent = scripts[s].innerHTML;
                var matches = scriptContent.match(bugPattern);
                
                if (matches) {
                    totalBugsFound += matches.length;
                    debugLog('Found bug ID(s) in script: ' + matches.join(', '), {
                        scriptIndex: s,
                        scriptContent: scriptContent.substring(0, 100) + '...'
                    });
                }
            }
        }
        
        // Look for bug IDs in onclick attributes
        var elements = document.querySelectorAll('[onclick*="64"]');
        debugLog('Found ' + elements.length + ' elements with onclick containing 64');
        
        for (var e = 0; e < elements.length; e++) {
            var onclick = elements[e].getAttribute('onclick');
            if (onclick) {
                var matches = onclick.match(bugPattern);
                if (matches) {
                    totalBugsFound += matches.length;
                    debugLog('Found bug ID(s) in onclick: ' + matches.join(', '), {
                        elementIndex: e,
                        onclick: onclick,
                        element: elements[e].outerHTML.substring(0, 100) + '...'
                    });
                }
            }
        }
        
        // Summary of findings
        debugLog('Scan complete: Found ' + totalBugsFound + ' bug IDs, replaced ' + bugsReplaced);
    }
    
    // Run immediately
    debugLog('Running initial scan');
    replaceBugIds();
    
    // Also run when DOM is fully loaded
    if (document.readyState === 'loading') {
        debugLog('Registering DOMContentLoaded handler');
        document.addEventListener('DOMContentLoaded', function() {
            debugLog('DOM fully loaded, running scan again');
            replaceBugIds();
        });
    }
    
    // Run periodically to catch any dynamically added content
    debugLog('Setting up interval for periodic scanning');
    setInterval(function() {
        debugLog('Running periodic scan');
        replaceBugIds();
    }, 2000);
    
    // Also run on any XHR completion
    debugLog('Setting up XHR completion listener');
    var originalXHROpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function() {
        this.addEventListener('load', function() {
            debugLog('XHR completed, running scan');
            setTimeout(replaceBugIds, 500);
        });
        originalXHROpen.apply(this, arguments);
    };
})();
