/**
 * Redmine Bug Display Enhancement for TestLink
 * 
 * This script enhances the display of Redmine bugs in TestLink's test execution page
 * by adding the bug ID and status to the "Relevant bugs" column.
 */

// Wait for the page to load
document.addEventListener('DOMContentLoaded', function() {
    // Log that the script is running
    console.log('Redmine Bug Display Enhancement script running');
    
    // Find all delete confirmation links for bugs
    const deleteLinks = document.querySelectorAll('img.clickable[onclick*="delete_confirmation"]');
    
    // Process each delete link
    deleteLinks.forEach(function(link) {
        try {
            // Extract the bug ID from the onclick attribute
            const onclickAttr = link.getAttribute('onclick');
            const bugIdMatch = onclickAttr.match(/delete_confirmation\('\d+-\d+-(\d+)','(\d+)'/);
            
            if (bugIdMatch && bugIdMatch[1]) {
                const bugId = bugIdMatch[1];
                console.log('Found bug ID:', bugId);
                
                // Find the parent row
                const row = link.closest('tr');
                
                if (row) {
                    // Find the "Relevant bugs" cell (3rd cell in the row)
                    const bugCell = row.cells[2];
                    
                    if (bugCell) {
                        // Create a bug display element
                        const bugDisplay = document.createElement('div');
                        bugDisplay.style.fontWeight = 'bold';
                        bugDisplay.style.margin = '5px 0';
                        bugDisplay.innerHTML = `[ID:${bugId}] <span class="bug-status" data-bug-id="${bugId}">Loading...</span>`;
                        
                        // Add it to the cell
                        bugCell.appendChild(bugDisplay);
                        
                        // Fetch the bug status from Redmine
                        fetchBugStatus(bugId);
                    }
                }
            }
        } catch (error) {
            console.error('Error processing bug link:', error);
        }
    });
});

/**
 * Fetch the bug status from Redmine
 */
function fetchBugStatus(bugId) {
    // Create a URL for the bug JSON
    const url = '/redmine_bug_status.php?id=' + bugId;
    
    // Fetch the bug status
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Find all status elements for this bug
            const statusElements = document.querySelectorAll(`.bug-status[data-bug-id="${bugId}"]`);
            
            // Update each element
            statusElements.forEach(function(element) {
                if (data && data.status) {
                    // Set the status text
                    element.textContent = data.status;
                    
                    // Add color based on status
                    if (data.status.toLowerCase() === 'open') {
                        element.style.color = '#cc0000';
                    } else if (data.status.toLowerCase() === 'in progress') {
                        element.style.color = '#ff9900';
                    } else if (data.status.toLowerCase() === 'resolved' || data.status.toLowerCase() === 'closed') {
                        element.style.color = '#009900';
                    }
                } else {
                    element.textContent = 'Unknown';
                }
            });
        })
        .catch(error => {
            console.error('Error fetching bug status:', error);
            
            // Find all status elements for this bug
            const statusElements = document.querySelectorAll(`.bug-status[data-bug-id="${bugId}"]`);
            
            // Update each element
            statusElements.forEach(function(element) {
                element.textContent = 'Error';
            });
        });
}
