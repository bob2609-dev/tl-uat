/**
 * Simple Redmine Bug ID Fix for TestLink
 * 
 * This script specifically targets bug IDs in TestLink's execution pages
 * and replaces them with clickable links to the Redmine issue tracker.
 */

try {
    // Set the Redmine base URL
    var redmineUrl = 'https://support.profinch.com';
    
    // Create visual indicator
    function createIndicator(message, isSuccess) {
        var indicator = document.createElement('div');
        indicator.id = 'redmine-fix-indicator';
        indicator.style.position = 'fixed';
        indicator.style.bottom = '10px';
        indicator.style.right = '10px';
        indicator.style.background = isSuccess ? 'rgba(0,128,0,0.7)' : 'rgba(0,0,0,0.7)';
        indicator.style.color = 'white';
        indicator.style.padding = '5px';
        indicator.style.fontSize = '12px';
        indicator.style.zIndex = '9999';
        indicator.style.borderRadius = '3px';
        indicator.innerHTML = message;
        document.body.appendChild(indicator);
        return indicator;
    }
    
    // Show initial indicator
    var indicator = createIndicator('Redmine Fix: Scanning...', false);
    
    // Function to convert bug IDs to links
    function convertBugIdsToLinks() {
        var pattern = /\b(64\d{3,5})\b/g;
        var totalFound = 0;
        
        // Method 1: Look for bug IDs in specific bug table cells
        console.log('Redmine fix: Looking for bug table');
        var bugTables = document.querySelectorAll('.exec_history_table');
        if (bugTables.length > 0) {
            console.log('Redmine fix: Found ' + bugTables.length + ' bug tables');
            
            bugTables.forEach(function(table) {
                var cells = table.querySelectorAll('td');
                cells.forEach(function(cell) {
                    var html = cell.innerHTML;
                    if (pattern.test(html)) {
                        console.log('Redmine fix: Found bug ID in cell: ' + html);
                        cell.innerHTML = html.replace(pattern, '<a href="' + redmineUrl + '/issues/$1" target="_blank" style="color:#0066cc;font-weight:bold;">$1</a>');
                        totalFound++;
                    }
                });
            });
        }
        
        // Method 2: Look for bug IDs in execution notes
        var notesFields = document.querySelectorAll('.exec_tc_title');
        if (notesFields.length > 0) {
            console.log('Redmine fix: Found ' + notesFields.length + ' notes fields');
            
            notesFields.forEach(function(field) {
                var html = field.innerHTML;
                if (pattern.test(html)) {
                    console.log('Redmine fix: Found bug ID in notes: ' + html);
                    field.innerHTML = html.replace(pattern, '<a href="' + redmineUrl + '/issues/$1" target="_blank" style="color:#0066cc;font-weight:bold;">$1</a>');
                    totalFound++;
                }
            });
        }
        
        // Method 3: Look for bug IDs in any table cell
        var allTables = document.querySelectorAll('table');
        console.log('Redmine fix: Found ' + allTables.length + ' total tables');
        
        allTables.forEach(function(table) {
            // Skip tables we've already processed
            if (table.classList.contains('exec_history_table')) {
                return;
            }
            
            var cells = table.querySelectorAll('td');
            cells.forEach(function(cell) {
                var html = cell.innerHTML;
                if (pattern.test(html) && !html.includes('href="' + redmineUrl)) {
                    console.log('Redmine fix: Found bug ID in general table cell: ' + html);
                    cell.innerHTML = html.replace(pattern, '<a href="' + redmineUrl + '/issues/$1" target="_blank" style="color:#0066cc;font-weight:bold;">$1</a>');
                    totalFound++;
                }
            });
        });
        
        // Update indicator with results
        if (totalFound > 0) {
            indicator.innerHTML = 'Redmine Fix: ' + totalFound + ' bug IDs linked';
            indicator.style.background = 'rgba(0,128,0,0.7)';
        } else {
            indicator.innerHTML = 'Redmine Fix: No bug IDs found';
        }
        
        console.log('Redmine fix: Replaced ' + totalFound + ' bug IDs');
        return totalFound;
    }
    
    // Run the conversion
    setTimeout(function() {
        convertBugIdsToLinks();
        
        // Run again after a delay to catch any dynamically loaded content
        setTimeout(convertBugIdsToLinks, 2000);
    }, 500);
    
} catch (error) {
    console.error('Redmine fix error:', error);
    createIndicator('Redmine Fix Error: ' + error.message, false);
}
