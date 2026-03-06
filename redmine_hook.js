/**
 * Redmine Integration Hook for TestLink
 * This script adds direct Redmine integration buttons to TestLink's UI
 * following the same approach we used for the image display fix
 */

// Configuration
const REDMINE_CONFIG = {
    baseUrl: 'https://support.profinch.com',
    apiKey: 'a597e200f8923a85484e81ca81d731827b8dbf3d',
    projectId: 'nmb-fcubs-14-7-uat2'
};

// Track if a bug submission has been made in this session
let bugSubmitted = false;

// Add the hook after the page is fully loaded
window.addEventListener('DOMContentLoaded', function() {
    console.log('Redmine hook loaded');
    // Create and insert the Report Bug button in execution pages
    setTimeout(addRedmineButtons, 500); // Delay to ensure page is fully rendered
});

// Also add a window load event in case DOMContentLoaded already fired
window.addEventListener('load', function() {
    console.log('Window loaded - adding Redmine buttons');
    addRedmineButtons();
    
    // Also set up a mutation observer to watch for dynamic content changes
    setupMutationObserver();
});

// alert on page load add event listener
document.addEventListener('DOMContentLoaded', function() {
    console.log('Redmine hook loaded');
});


/**
 * Set up a mutation observer to watch for dynamic content changes
 */
function setupMutationObserver() {
    // Create an observer instance linked to a callback function
    const observer = new MutationObserver(function(mutations) {
        // Check if we need to re-add our buttons
        const hasRedmineButton = document.querySelector('.redmine-bug-button');
        if (!hasRedmineButton) {
            console.log('Content changed - re-adding Redmine buttons');
            addRedmineButtons();
        }
    });
    
    // Start observing the document with the configured parameters
    observer.observe(document.body, { childList: true, subtree: true });
}

/**
 * Add Redmine integration buttons to appropriate TestLink pages
 */
function addRedmineButtons() {
    console.log('Adding Redmine buttons');
    
    // Check if we're on an execution page by looking for various indicators
    const isExecutionPage = 
        document.querySelector('.exec_tc_title') !== null || 
        document.querySelector('input[name^="statusSingle"]') !== null ||
        document.querySelector('select[name^="statusSingle"]') !== null ||
        document.location.href.includes('execSetResults.php');
    
    if (isExecutionPage) {
        console.log('Execution page detected');
        
        // Find all status selects - these indicate individual test cases
        const statusSelects = document.querySelectorAll('select[name^="statusSingle"]');
        console.log('Found ' + statusSelects.length + ' status selects');
        
        if (statusSelects.length > 0) {
            // For each test case on the page, add buttons after the status select
            statusSelects.forEach(function(select) {
                // Check if this select already has our buttons
                const parentDiv = select.closest('div');
                if (parentDiv && !parentDiv.querySelector('.redmine-bug-button')) {
                    console.log('Adding buttons for a test case');
                    
                    // Create container for buttons
                    const buttonContainer = document.createElement('div');
                    buttonContainer.className = 'redmine-buttons-container';
                    buttonContainer.style.marginTop = '10px';
                    buttonContainer.style.padding = '5px';
                    buttonContainer.style.border = '1px solid #f0ad4e';
                    buttonContainer.style.borderRadius = '3px';
                    buttonContainer.style.backgroundColor = '#fcf8e3';
                    
                    // Get test case name
                    const tcTitle = document.querySelector('.exec_tc_title');
                    const tcName = tcTitle ? tcTitle.textContent.trim() : 'Test Case';
                    
                    // Create the Report Bug button
                    const reportBugBtn = document.createElement('button');
                    reportBugBtn.textContent = 'Create Bug in Redmine';
                    reportBugBtn.className = 'redmine-bug-button btn btn-warning';
                    reportBugBtn.style.backgroundColor = '#f0ad4e';
                    reportBugBtn.style.color = '#fff';
                    reportBugBtn.style.padding = '5px 10px';
                    reportBugBtn.style.border = 'none';
                    reportBugBtn.style.borderRadius = '3px';
                    reportBugBtn.style.cursor = 'pointer';
                    reportBugBtn.style.marginRight = '5px';
                    
                    // Add click handler with button disabling
                    reportBugBtn.addEventListener('click', function() {
                        handleBugSubmission(this, tcName, 'create');
                    });
                    
                    // Create the Link Bug button
                    const linkBugBtn = document.createElement('button');
                    linkBugBtn.textContent = 'Link Existing Bug';
                    linkBugBtn.className = 'redmine-link-button btn btn-primary';
                    linkBugBtn.style.backgroundColor = '#337ab7';
                    linkBugBtn.style.color = '#fff';
                    linkBugBtn.style.padding = '5px 10px';
                    linkBugBtn.style.border = 'none';
                    linkBugBtn.style.borderRadius = '3px';
                    linkBugBtn.style.cursor = 'pointer';
                    
                    // Add click handler with button disabling
                    linkBugBtn.addEventListener('click', function() {
                        handleBugSubmission(this, tcName, 'link');
                    });
                    
                    // Add title and buttons to container
                    const title = document.createElement('div');
                    title.textContent = 'Redmine Bug Tracking';
                    title.style.fontWeight = 'bold';
                    title.style.marginBottom = '5px';
                    title.style.color = '#8a6d3b';
                    
                    buttonContainer.appendChild(title);
                    buttonContainer.appendChild(reportBugBtn);
                    buttonContainer.appendChild(linkBugBtn);
                    
                    // Add container after the status select
                    parentDiv.appendChild(buttonContainer);
                    
                    // Initially hide the buttons - they'll be shown when status is 'Failed'
                    buttonContainer.style.display = 'none';
                    
                    // Show buttons if status is already 'Failed'
                    if (select.value === 'f') {
                        buttonContainer.style.display = 'block';
                    }
                    
                    // Add change listener to show/hide buttons based on status
                    select.addEventListener('change', function() {
                        if (this.value === 'f') {
                            buttonContainer.style.display = 'block';
                        } else {
                            buttonContainer.style.display = 'none';
                        }
                    });
                }
            });
        } else {
            // If no status selects found, try a more general approach
            console.log('No status selects found, trying general approach');
            
            // Get test case information
            const tcTitle = document.querySelector('.exec_tc_title');
            if (tcTitle && !tcTitle.nextElementSibling?.classList?.contains('redmine-bug-button')) {
                const tcName = tcTitle.textContent.trim();
                
                // Create the Report Bug button
                const reportBugBtn = document.createElement('button');
                reportBugBtn.textContent = 'Redmine Bug Tracker';
                reportBugBtn.className = 'redmine-bug-button btn btn-warning';
                reportBugBtn.style.backgroundColor = '#f0ad4e';
                reportBugBtn.style.color = '#fff';
                reportBugBtn.style.padding = '5px 10px';
                reportBugBtn.style.border = 'none';
                reportBugBtn.style.borderRadius = '3px';
                reportBugBtn.style.cursor = 'pointer';
                reportBugBtn.style.margin = '10px 0';
                // hide the button
                reportBugBtn.style.display = 'none';
                
                // Add click handler with button disabling
                reportBugBtn.addEventListener('click', function() {
                    handleBugSubmission(this, tcName, 'create');
                });
                
                // Insert after the title
                tcTitle.parentNode.insertBefore(reportBugBtn, tcTitle.nextSibling);
                
                // Add a visual indicator
                const statusIndicator = document.createElement('div');
                statusIndicator.textContent = 'Redmine Direct Integration Active';
                statusIndicator.style.color = 'green';
                statusIndicator.style.fontSize = '12px';
                statusIndicator.style.marginTop = '5px';
                // hide the indicator
                statusIndicator.style.display = 'none';
                
                tcTitle.parentNode.insertBefore(statusIndicator, reportBugBtn.nextSibling);
            }
        }
    }
  
    // Check if we're on a test case view page where bugs would be listed
    const bugSection = document.querySelector('div.exec_tc_bugs');
    if (bugSection) {
        // Add link to our custom Redmine interface
        const redmineLink = document.createElement('div');
        redmineLink.innerHTML = '<a href="./redmine-integration.php" target="_blank" style="color:#0066cc;font-weight:bold;">🐞 Open Redmine Bug Tracker</a>';
        redmineLink.style.margin = '10px 0';
        redmineLink.style.padding = '5px';
        redmineLink.style.backgroundColor = '#f5f5f5';
        redmineLink.style.border = '1px solid #ddd';
        redmineLink.style.borderRadius = '3px';
        
        bugSection.parentNode.insertBefore(redmineLink, bugSection);
    }
}

/**
 * Handle the bug submission process with button disabling and confirmation
 */
function handleBugSubmission(button, tcName, operation) {
    // Check if a bug has already been submitted in this session
    if (bugSubmitted) {
        // Ask for confirmation before submitting again
        if (!confirm('You have already submitted a bug in this session. Are you sure you want to submit again?')) {
            return; // User cancelled the submission
        }
    }
    
    // Disable the button to prevent double-clicks
    button.disabled = true;
    button.originalText = button.textContent;
    button.textContent = 'Processing...';
    button.style.backgroundColor = '#999999';
    button.style.cursor = 'not-allowed';
    button.style.opacity = '0.7';
    
    // Set the flag that a bug has been submitted
    bugSubmitted = true;
    
    // Open the Redmine integration page
    openRedmineIntegration(tcName, operation);
    
    // Re-enable the button after a delay (3 seconds)
    setTimeout(function() {
        button.disabled = false;
        button.textContent = button.originalText;
        button.style.backgroundColor = operation === 'create' ? '#f0ad4e' : '#337ab7';
        button.style.cursor = 'pointer';
        button.style.opacity = '1';
    }, 3000);
}

/**
 * Open the Redmine integration page with the appropriate context
 */
function openRedmineIntegration(tcName, operation) {
    // Get build and test plan information if available
    let testPlan = '';
    let buildName = '';
    let result = 'Failed';
    
    // Try to extract from breadcrumb or other UI elements
    const buildInfo = document.querySelector('.exec_tc_build') || document.querySelector('.light_toolbar span');
    if (buildInfo) {
        const buildText = buildInfo.textContent.trim();
        const buildMatch = buildText.match(/Build: (.+)/);
        if (buildMatch) buildName = buildMatch[1];
    }
    
    // Try to find test plan information
    const planInfo = document.querySelector('.exec_tc_tplan') || document.querySelector('select[name="testplan_id"] option[selected]');
    if (planInfo) {
        testPlan = planInfo.textContent.trim();
    }
    
    // Build URL with query parameters to pass context
    let redmineUrl = './redmine-integration.php' + 
        '?testcase=' + encodeURIComponent(tcName) + 
        '&testplan=' + encodeURIComponent(testPlan) + 
        '&build=' + encodeURIComponent(buildName) + 
        '&result=' + encodeURIComponent(result);
    
    // If operation is 'link', go to the link tab
    if (operation === 'link') {
        redmineUrl += '&operation=link';
    }
    
    // Open in a new window
    window.open(redmineUrl, '_blank', 'width=800,height=700');
}
