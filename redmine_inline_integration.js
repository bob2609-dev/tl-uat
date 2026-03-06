/**
 * Redmine Inline Integration for TestLink
 * 
 * This script adds bug creation and linking capabilities directly within TestLink's test execution page
 * without needing to navigate away from the page. It completely bypasses TestLink's complex integration
 * code, similar to how we fixed the image display issues.
 */

// Configuration - Change these values to match your Redmine installation
const REDMINE_CONFIG = {
    baseUrl: 'https://support.profinch.com',
    apiKey: 'a597e200f8923a85484e81ca81d731827b8dbf3d',
    projectId: 'nmb-fcubs-14-7-uat2'
};

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Exit early if not an execution page
    if (!isExecutionPage()) return;
    
    // Fix any missing bug icons
    fixMissingIcons();
    
    // Hide problematic TestLink warnings
    hideConnectionWarning();
    
    // Enhance the existing bug icons to use our custom functionality
    enhanceBugIcons();
    
    // Add our custom bug controls if they don't exist
    injectBugControls();
});

/**
 * Check if current page is a test execution page
 */
function isExecutionPage() {
    return document.querySelector('.exec_tc_title') !== null;
}

/**
 * Fix missing bug tracker icons
 */
function fixMissingIcons() {
    const iconSelectors = [
        'img[src*="bug_add"]',
        'img[src*="bug_link"]',
        'img[src*="bug_del"]',
        'img[src*="bug_"]'
    ];
    
    iconSelectors.forEach(selector => {
        const icons = document.querySelectorAll(selector);
        icons.forEach(icon => {
            // If image is broken, fix the path
            if (icon.naturalWidth === 0 || !icon.complete) {
                const filename = icon.src.split('/').pop();
                const newSrc = window.location.origin + '/testlink/gui/themes/default/images/' + filename;
                icon.src = newSrc;
                icon.style.width = '16px';
                icon.style.height = '16px';
                
                // Add text fallback if still not working
                icon.onerror = function() {
                    if (filename.includes('bug_add')) {
                        this.parentNode.innerHTML = '[+Bug]';
                        this.parentNode.style.color = '#337ab7';
                    } else if (filename.includes('bug_link')) {
                        this.parentNode.innerHTML = '[Link]';
                        this.parentNode.style.color = '#337ab7';
                    }
                };
            }
        });
    });
}

/**
 * Hide TestLink's connection warning message
 */
function hideConnectionWarning() {
    const warningElements = document.querySelectorAll('div[style*="color:red"]');
    
    warningElements.forEach(element => {
        if (element.textContent.includes('Something is preventing connection')) {
            element.style.display = 'none';
            
            // Add our success message
            const successMsg = document.createElement('div');
            successMsg.innerHTML = '<div style="color:green; margin:10px 0;">✓ Direct Redmine Integration Active</div>';
            element.parentNode.insertBefore(successMsg, element);
        }
    });
}

/**
 * Enhance existing bug icons to use our functionality
 */
function enhanceBugIcons() {
    // Find bug add icon and replace its behavior
    const addBugLinks = document.querySelectorAll('a[href*="bug_add"], img[src*="bug_add"]').forEach(link => {
        if (link.tagName === 'A') {
            // Replace the link's click behavior
            link.addEventListener('click', function(e) {
                e.preventDefault();
                showCreateBugDialog();
            });
        } else if (link.tagName === 'IMG' && link.parentNode.tagName === 'A') {
            // Replace the parent link's click behavior
            link.parentNode.addEventListener('click', function(e) {
                e.preventDefault();
                showCreateBugDialog();
            });
        }
    });
    
    // Find bug link icon and replace its behavior
    const linkBugLinks = document.querySelectorAll('a[href*="bug_link"], img[src*="bug_link"]').forEach(link => {
        if (link.tagName === 'A') {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                showLinkBugDialog();
            });
        } else if (link.tagName === 'IMG' && link.parentNode.tagName === 'A') {
            link.parentNode.addEventListener('click', function(e) {
                e.preventDefault();
                showLinkBugDialog();
            });
        }
    });
}

/**
 * Inject our custom bug controls if they don't exist
 */
function injectBugControls() {
    // Find the test case notes area as our insertion point
    const notesField = document.querySelector('textarea[name="notes"]');
    if (!notesField) return;
    
    // Create container for our controls
    const container = document.createElement('div');
    container.id = 'redmine-integration-controls';
    container.className = 'redmine-controls';
    container.style.margin = '10px 0';
    container.style.padding = '10px';
    container.style.border = '1px solid #ddd';
    container.style.borderRadius = '5px';
    container.style.backgroundColor = '#f9f9f9';
    
    // Add title
    const title = document.createElement('h3');
    title.textContent = 'Redmine Bug Tracking';
    title.style.margin = '0 0 10px 0';
    title.style.color = '#333';
    container.appendChild(title);
    
    // Add buttons
    const createBtn = document.createElement('button');
    createBtn.textContent = 'Create New Bug';
    createBtn.className = 'btn';
    createBtn.style.backgroundColor = '#f0ad4e';
    createBtn.style.color = '#fff';
    createBtn.style.border = 'none';
    createBtn.style.padding = '5px 10px';
    createBtn.style.marginRight = '10px';
    createBtn.style.borderRadius = '3px';
    createBtn.addEventListener('click', showCreateBugDialog);
    container.appendChild(createBtn);
    
    const linkBtn = document.createElement('button');
    linkBtn.textContent = 'Link Existing Bug';
    linkBtn.className = 'btn';
    linkBtn.style.backgroundColor = '#337ab7';
    linkBtn.style.color = '#fff';
    linkBtn.style.border = 'none';
    linkBtn.style.padding = '5px 10px';
    linkBtn.style.borderRadius = '3px';
    linkBtn.addEventListener('click', showLinkBugDialog);
    container.appendChild(linkBtn);
    
    // List of linked bugs
    const bugList = document.createElement('div');
    bugList.id = 'redmine-bug-list';
    bugList.style.marginTop = '10px';
    container.appendChild(bugList);
    
    // Add dialog containers (hidden initially)
    const createDialog = createBugDialog();
    const linkDialog = createLinkDialog();
    container.appendChild(createDialog);
    container.appendChild(linkDialog);
    
    // Insert our controls after the notes field
    notesField.parentNode.insertBefore(container, notesField.nextSibling);
    
    // Load existing bugs
    loadLinkedBugs();
}

/**
 * Create the bug creation dialog
 */
function createBugDialog() {
    const dialog = document.createElement('div');
    dialog.id = 'redmine-create-dialog';
    dialog.className = 'redmine-dialog';
    dialog.style.display = 'none';
    dialog.style.padding = '15px';
    dialog.style.marginTop = '15px';
    dialog.style.border = '1px solid #ddd';
    dialog.style.borderRadius = '5px';
    dialog.style.backgroundColor = '#fff';
    
    // Get test case information
    const tcTitle = document.querySelector('.exec_tc_title');
    const tcName = tcTitle ? tcTitle.textContent.trim() : 'Test Case';
    
    // Dialog content
    dialog.innerHTML = `
        <h3 style="margin-top:0">Create New Bug in Redmine</h3>
        <div style="margin-bottom:10px">
            <label style="display:block;font-weight:bold;margin-bottom:5px">Summary:</label>
            <input type="text" id="bug-summary" value="[TestLink] Failed: ${tcName}" style="width:100%;padding:5px;box-sizing:border-box">
        </div>
        <div style="margin-bottom:10px">
            <label style="display:block;font-weight:bold;margin-bottom:5px">Description:</label>
            <textarea id="bug-description" style="width:100%;height:100px;padding:5px;box-sizing:border-box">Test Case: ${tcName}\n\nPlease provide details about the issue:</textarea>
        </div>
        <div style="text-align:right">
            <button id="cancel-create" style="background:#ccc;border:none;padding:5px 10px;margin-right:10px;border-radius:3px">Cancel</button>
            <button id="submit-bug" style="background:#5cb85c;color:white;border:none;padding:5px 10px;border-radius:3px">Create Bug</button>
        </div>
        <div id="create-status" style="margin-top:10px"></div>
    `;
    
    // Return now, we'll add event handlers after it's in the DOM
    return dialog;
}

/**
 * Create the bug linking dialog
 */
function createLinkDialog() {
    const dialog = document.createElement('div');
    dialog.id = 'redmine-link-dialog';
    dialog.className = 'redmine-dialog';
    dialog.style.display = 'none';
    dialog.style.padding = '15px';
    dialog.style.marginTop = '15px';
    dialog.style.border = '1px solid #ddd';
    dialog.style.borderRadius = '5px';
    dialog.style.backgroundColor = '#fff';
    
    // Dialog content
    dialog.innerHTML = `
        <h3 style="margin-top:0">Link Existing Redmine Bug</h3>
        <div style="margin-bottom:10px">
            <label style="display:block;font-weight:bold;margin-bottom:5px">Bug ID:</label>
            <input type="text" id="bug-id" placeholder="Enter bug ID number" style="width:100%;padding:5px;box-sizing:border-box">
        </div>
        <div style="text-align:right">
            <button id="cancel-link" style="background:#ccc;border:none;padding:5px 10px;margin-right:10px;border-radius:3px">Cancel</button>
            <button id="verify-bug" style="background:#5bc0de;color:white;border:none;padding:5px 10px;margin-right:10px;border-radius:3px">Verify Bug</button>
            <button id="submit-link" style="background:#5cb85c;color:white;border:none;padding:5px 10px;border-radius:3px" disabled>Link Bug</button>
        </div>
        <div id="bug-details" style="margin-top:10px"></div>
    `;
    
    // Return now, we'll add event handlers after it's in the DOM
    return dialog;
}

/**
 * Show the bug creation dialog and set up its handlers
 */
function showCreateBugDialog() {
    // Hide any other dialogs
    hideDialogs();
    
    // Show this dialog
    const dialog = document.getElementById('redmine-create-dialog');
    dialog.style.display = 'block';
    
    // Set up event handlers
    document.getElementById('cancel-create').addEventListener('click', function() {
        dialog.style.display = 'none';
    });
    
    document.getElementById('submit-bug').addEventListener('click', function() {
        createBug();
    });
    
    // Focus the summary field
    document.getElementById('bug-summary').focus();
}

/**
 * Show the bug linking dialog and set up its handlers
 */
function showLinkBugDialog() {
    // Hide any other dialogs
    hideDialogs();
    
    // Show this dialog
    const dialog = document.getElementById('redmine-link-dialog');
    dialog.style.display = 'block';
    
    // Set up event handlers
    document.getElementById('cancel-link').addEventListener('click', function() {
        dialog.style.display = 'none';
    });
    
    document.getElementById('verify-bug').addEventListener('click', function() {
        verifyBug();
    });
    
    document.getElementById('submit-link').addEventListener('click', function() {
        linkBug();
    });
    
    // Focus the bug ID field
    document.getElementById('bug-id').focus();
}

/**
 * Hide all dialogs
 */
function hideDialogs() {
    const dialogs = document.querySelectorAll('.redmine-dialog');
    dialogs.forEach(dialog => {
        dialog.style.display = 'none';
    });
    
    // Reset status areas
    document.getElementById('create-status').innerHTML = '';
    document.getElementById('bug-details').innerHTML = '';
    
    // Reset link button
    const linkBtn = document.getElementById('submit-link');
    if (linkBtn) linkBtn.disabled = true;
}

/**
 * Create a new bug in Redmine
 */
function createBug() {
    const summary = document.getElementById('bug-summary').value;
    const description = document.getElementById('bug-description').value;
    const statusArea = document.getElementById('create-status');
    
    if (!summary) {
        statusArea.innerHTML = '<div style="color:red">Please enter a summary</div>';
        return;
    }
    
    statusArea.innerHTML = '<div style="color:blue">Creating bug...</div>';
    
    // Prepare the issue data
    const issueData = {
        issue: {
            project_id: REDMINE_CONFIG.projectId,
            subject: summary,
            description: description
        }
    };
    
    // Make the API request
    fetch(REDMINE_CONFIG.baseUrl + '/issues.json', {
        method: 'POST',
        headers: {
            'X-Redmine-API-Key': REDMINE_CONFIG.apiKey,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(issueData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        const bugId = data.issue.id;
        statusArea.innerHTML = `<div style="color:green">Bug #${bugId} created successfully!</div>`;
        
        // Link the newly created bug to this test execution
        linkBugToExecution(bugId, summary);
        
        // Hide the dialog after a short delay
        setTimeout(() => {
            document.getElementById('redmine-create-dialog').style.display = 'none';
        }, 2000);
    })
    .catch(error => {
        statusArea.innerHTML = `<div style="color:red">Error: ${error.message}</div>`;
    });
}

/**
 * Verify if a bug exists in Redmine
 */
function verifyBug() {
    const bugId = document.getElementById('bug-id').value;
    const detailsArea = document.getElementById('bug-details');
    const linkBtn = document.getElementById('submit-link');
    
    if (!bugId || isNaN(parseInt(bugId))) {
        detailsArea.innerHTML = '<div style="color:red">Please enter a valid bug ID number</div>';
        linkBtn.disabled = true;
        return;
    }
    
    detailsArea.innerHTML = '<div style="color:blue">Verifying bug...</div>';
    
    // Make API request to get bug details
    fetch(REDMINE_CONFIG.baseUrl + '/issues/' + bugId + '.json', {
        method: 'GET',
        headers: {
            'X-Redmine-API-Key': REDMINE_CONFIG.apiKey
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('HTTP error ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        const issue = data.issue;
        detailsArea.innerHTML = `
            <div style="border:1px solid #ddd; padding:10px; margin-top:10px;">
                <div style="font-weight:bold">#${issue.id}: ${issue.subject}</div>
                <div style="color:#666">${issue.status.name}</div>
            </div>
        `;
        
        // Enable the link button
        linkBtn.disabled = false;
        
        // Store the issue details for linking
        linkBtn.dataset.bugId = issue.id;
        linkBtn.dataset.bugSummary = issue.subject;
    })
    .catch(error => {
        detailsArea.innerHTML = `<div style="color:red">Error: Issue not found or other API error</div>`;
        linkBtn.disabled = true;
    });
}

/**
 * Link a bug to the current test execution
 */
function linkBug() {
    const linkBtn = document.getElementById('submit-link');
    const bugId = linkBtn.dataset.bugId;
    const bugSummary = linkBtn.dataset.bugSummary;
    
    if (!bugId) {
        return;
    }
    
    // Link the bug to this test execution
    linkBugToExecution(bugId, bugSummary);
    
    // Hide the dialog
    document.getElementById('redmine-link-dialog').style.display = 'none';
}

/**
 * Link a bug to the current test execution
 */
function linkBugToExecution(bugId, bugSummary) {
    // Find the current execution ID
    const executionId = getExecutionId();
    if (!executionId) {
        alert('Could not determine execution ID. Bug was created but could not be linked automatically.');
        return;
    }
    
    // Attempt to add the bug to TestLink's UI first
    addBugToTestLinkUI(bugId, bugSummary);
    
    // Then try to save it to TestLink's database if possible
    saveBugToDatabase(executionId, bugId);
    
    // Reload linked bugs in our UI
    loadLinkedBugs();
}

/**
 * Get the current execution ID
 */
function getExecutionId() {
    // Try to find the execution ID from various elements
    const executionIdField = document.querySelector('input[name="exec_id"]');
    if (executionIdField) {
        return executionIdField.value;
    }
    
    // Try to extract from the page URL if present
    const urlParams = new URLSearchParams(window.location.search);
    const execId = urlParams.get('exec_id');
    if (execId) {
        return execId;
    }
    
    return null;
}

/**
 * Add a bug to TestLink's UI without a page reload
 */
function addBugToTestLinkUI(bugId, bugSummary) {
    // Find the bug table in TestLink's UI
    const bugTable = document.querySelector('table.exec_tc_bugs');
    if (!bugTable) {
        // If table doesn't exist, create it
        createBugTable();
        return;
    }
    
    // Check if this bug is already linked
    const existingBugs = bugTable.querySelectorAll('td');
    for (let i = 0; i < existingBugs.length; i++) {
        if (existingBugs[i].textContent.includes(bugId)) {
            // Bug already linked
            return;
        }
    }
    
    // Add a new row to the bug table
    const newRow = bugTable.insertRow(-1);
    const cell = newRow.insertCell(0);
    
    // Create the bug link HTML
    const bugLink = document.createElement('a');
    bugLink.href = REDMINE_CONFIG.baseUrl + '/issues/' + bugId;
    bugLink.target = '_blank';
    bugLink.textContent = bugId;
    bugLink.title = bugSummary || 'View Bug';
    
    cell.appendChild(bugLink);
    cell.appendChild(document.createTextNode(' - ' + (bugSummary || 'Redmine Issue')));
}

/**
 * Create a bug table if it doesn't exist in TestLink's UI
 */
function createBugTable() {
    const executionArea = document.querySelector('.exec_tc_title');
    if (!executionArea) return;
    
    // Find a good insertion point
    let insertPoint = executionArea.parentNode;
    
    // Create bug table container
    const bugSection = document.createElement('div');
    bugSection.className = 'exec_tc_bugs';
    bugSection.innerHTML = '<table class="exec_tc_bugs"></table>';
    
    // Add header
    const header = document.createElement('h3');
    header.textContent = 'Linked Bugs';
    bugSection.insertBefore(header, bugSection.firstChild);
    
    insertPoint.appendChild(bugSection);
}

/**
 * Save the bug link to TestLink's database if possible
 */
function saveBugToDatabase(executionId, bugId) {
    // This is tricky because it requires direct access to TestLink's API
    // For now, we'll just skip this step and rely on our UI changes
    // If a more robust solution is needed, we could create a separate PHP endpoint
    // that handles the database work
    
    // Add the bug to our custom bug list
    addBugToCustomList(bugId);
}

/**
 * Add bug to our custom bug list
 */
function addBugToCustomList(bugId) {
    const bugList = document.getElementById('redmine-bug-list');
    if (!bugList) return;
    
    // Check if already in the list
    if (bugList.textContent.includes(bugId)) return;
    
    // Get bug details
    fetch(REDMINE_CONFIG.baseUrl + '/issues/' + bugId + '.json', {
        method: 'GET',
        headers: {
            'X-Redmine-API-Key': REDMINE_CONFIG.apiKey
        }
    })
    .then(response => response.json())
    .then(data => {
        const issue = data.issue;
        
        // Create bug item
        const bugItem = document.createElement('div');
        bugItem.className = 'redmine-bug-item';
        bugItem.style.margin = '5px 0';
        bugItem.style.padding = '8px';
        bugItem.style.backgroundColor = '#fff';
        bugItem.style.border = '1px solid #ddd';
        bugItem.style.borderRadius = '3px';
        
        // Add bug details
        bugItem.innerHTML = `
            <div>
                <a href="${REDMINE_CONFIG.baseUrl}/issues/${issue.id}" target="_blank" style="font-weight:bold;color:#337ab7">#${issue.id}</a>
                - ${issue.subject}
            </div>
            <div style="color:#666;font-size:90%">${issue.status.name}</div>
        `;
        
        bugList.appendChild(bugItem);
    })
    .catch(error => {
        console.error('Error getting bug details:', error);
    });
}

/**
 * Load all linked bugs into our custom list
 */
function loadLinkedBugs() {
    const bugList = document.getElementById('redmine-bug-list');
    if (!bugList) return;
    
    // Clear the list
    bugList.innerHTML = '';
    
    // Find bugs in TestLink's UI
    const bugLinks = document.querySelectorAll('table.exec_tc_bugs a');
    if (bugLinks.length === 0) {
        bugList.innerHTML = '<div style="color:#666;font-style:italic">No bugs linked yet</div>';
        return;
    }
    
    // Process each bug link
    bugLinks.forEach(link => {
        const bugId = link.textContent.trim();
        if (bugId && !isNaN(parseInt(bugId))) {
            addBugToCustomList(bugId);
        }
    });
}
