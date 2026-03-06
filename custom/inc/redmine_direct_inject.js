/**
 * Redmine Direct Inject for TestLink
 * 
 * This script directly injects Redmine bug details into the TestLink execution page
 */

// Wait for the page to load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Redmine direct inject loaded');
    
    // Add custom styles
    var style = document.createElement('style');
    style.textContent = `
        .redmine-bug-link {
            color: #0066cc !important;
            font-weight: bold !important;
            text-decoration: underline !important;
            display: inline-block !important;
            margin: 2px 0 !important;
        }
        .redmine-bug-details {
            margin-top: 3px;
            padding: 5px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 12px;
        }
        .redmine-bug-status {
            display: inline-block;
            padding: 2px 5px;
            background-color: #5cb85c;
            color: white;
            border-radius: 3px;
            margin-right: 5px;
        }
        .redmine-bug-status.closed {
            background-color: #777;
        }
        .redmine-bug-status.in-progress {
            background-color: #f0ad4e;
        }
    `;
    document.head.appendChild(style);
    
    // Function to find bug IDs in the page
    function findBugIds() {
        // Look for bug IDs in the page (64 followed by 3-5 digits)
        var bugPattern = /64\d{3,5}/g;
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
    
    // Function to fetch bug details from Redmine
    function fetchBugDetails(bugId) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/testlink/custom/inc/redmine_proxy.php?id=' + bugId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            resolve(response);
                        } catch (e) {
                            reject('Error parsing response: ' + e.message);
                        }
                    } else {
                        reject('Error fetching bug details: ' + xhr.status);
                    }
                }
            };
            xhr.send();
        });
    }
    
    // Function to inject bug details into the page
    function injectBugDetails(bugId, details) {
        // Find all cells containing just the bug ID
        var cells = document.querySelectorAll('td');
        cells.forEach(function(cell) {
            if (cell.textContent.trim() === bugId) {
                // Create a link to the bug
                var link = document.createElement('a');
                link.href = 'https://support.profinch.com/issues/' + bugId;
                link.target = '_blank';
                link.className = 'redmine-bug-link';
                link.textContent = bugId;
                
                // Create a container for the bug details
                var detailsContainer = document.createElement('div');
                detailsContainer.className = 'redmine-bug-details';
                
                if (details.issue) {
                    var issue = details.issue;
                    var statusClass = 'open';
                    if (issue.status.name.toLowerCase().includes('closed')) {
                        statusClass = 'closed';
                    } else if (issue.status.name.toLowerCase().includes('progress')) {
                        statusClass = 'in-progress';
                    }
                    
                    detailsContainer.innerHTML = 
                        '<span class="redmine-bug-status ' + statusClass + '">' + issue.status.name + '</span>' +
                        '<strong>Priority:</strong> ' + issue.priority.name + '<br>' +
                        '<strong>Subject:</strong> ' + issue.subject + '<br>' +
                        '<strong>Description:</strong> ' + (issue.description ? issue.description.substring(0, 100) + '...' : 'No description');
                } else {
                    detailsContainer.innerHTML = 'Error loading bug details: No issue data';
                }
                
                // Clear the cell and add the link and details
                cell.innerHTML = '';
                cell.appendChild(link);
                cell.appendChild(detailsContainer);
            }
        });
    }
    
    // Main function to process all bugs
    function processBugs() {
        var bugIds = findBugIds();
        
        bugIds.forEach(function(bugId) {
            fetchBugDetails(bugId)
                .then(function(details) {
                    injectBugDetails(bugId, details);
                })
                .catch(function(error) {
                    console.error('Error processing bug ' + bugId + ':', error);
                });
        });
    }
    
    // Run the main function
    processBugs();
    
    // Also run periodically to catch any new bugs
    setInterval(processBugs, 5000);
});
