/**
 * Redmine Direct Integration Buttons
 *
 * This script adds bug creation and linking buttons directly to TestLink's execution page.
 * It injects the buttons after the user marks a test as failed.
 */

// Configuration
const REDMINE_CONFIG = {
    baseUrl: 'https://support.profinch.com',
    apiKey: 'a597e200f8923a85484e81ca81d731827b8dbf3d',
    projectId: 'nmb-fcubs-14-7-uat2'
};

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Redmine Direct Buttons loaded');
    injectRedmineButtons();
    
    // Watch for form changes (for when user sets status to "Failed")
    watchStatusChanges();
    
    // Watch for AJAX content changes (for when the page refreshes parts)
    watchForContentChanges();
});

/**
 * Inject Redmine buttons into the page
 */
function injectRedmineButtons() {
    // Look for all status selects
    const statusSelects = document.querySelectorAll('select[name^="statusSingle"]');
    
    statusSelects.forEach(function(select) {
        // Extract the test case ID from the select name
        const tcID = select.name.match(/\d+/);
        if (!tcID) return;
        
        // Create the container for our buttons if it doesn't exist
        let buttonsContainer = document.getElementById('redmine_buttons_' + tcID);
        if (!buttonsContainer) {
            buttonsContainer = document.createElement('div');
            buttonsContainer.id = 'redmine_buttons_' + tcID;
            buttonsContainer.className = 'redmine_buttons';
            buttonsContainer.style.marginTop = '15px';
            buttonsContainer.style.padding = '10px';
            buttonsContainer.style.border = '1px solid #f0ad4e';
            buttonsContainer.style.borderRadius = '5px';
            buttonsContainer.style.backgroundColor = '#fcf8e3';
            buttonsContainer.style.display = 'none'; // Initially hidden
            
            // Get test case information
            const tcNameElem = document.querySelector('.exec_tc_title');
            const tcName = tcNameElem ? tcNameElem.textContent.trim() : 'Test Case';
            
            // Create buttons content
            buttonsContainer.innerHTML = `
                <div style="font-weight:bold; margin-bottom:10px; color:#8a6d3b;">Redmine Bug Tracking</div>
                
                <div style="margin-bottom:10px;">
                    <a href="./redmine-integration.php?testcase=${encodeURIComponent(tcName)}&status=Failed&execution_id=${tcID}" 
                       target="_blank"
                       style="display:inline-block; background:#f0ad4e; color:white; padding:5px 10px; text-decoration:none; border-radius:3px;">
                      Create Bug in Redmine
                    </a>
                </div>
                
                <div style="margin-bottom:5px;">
                    <div style="margin-bottom:5px; font-weight:bold;">Link to Existing Bug:</div>
                    <div style="display:flex;">
                        <input type="text" id="redmine_bug_id_${tcID}" placeholder="Enter bug ID" 
                               style="flex:1; padding:5px; margin-right:5px; border:1px solid #ccc; border-radius:3px;">
                        <button type="button" onclick="window.open('./redmine-integration.php?testcase=${encodeURIComponent(tcName)}&status=Failed&operation=link&bug_id=' + document.getElementById('redmine_bug_id_${tcID}').value, '_blank')"
                                style="background:#337ab7; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">
                          Link Bug
                        </button>
                    </div>
                </div>
            `;
            
            // Insert after the status select container
            const parentDiv = select.closest('div');
            if (parentDiv && parentDiv.parentNode) {
                parentDiv.parentNode.insertBefore(buttonsContainer, parentDiv.nextSibling);
            }
        }
        
        // Show the buttons if status is "Failed"
        if (select.value === 'f') {
            buttonsContainer.style.display = 'block';
        }
        
        // Add change listener to the select
        select.addEventListener('change', function() {
            if (this.value === 'f') {
                buttonsContainer.style.display = 'block';
            } else {
                buttonsContainer.style.display = 'none';
            }
        });
    });
}

/**
 * Watch for status changes
 */
function watchStatusChanges() {
    // Watch for form changes
    document.addEventListener('change', function(e) {
        // If a status select was changed
        if (e.target && e.target.name && e.target.name.startsWith('statusSingle')) {
            // Re-inject the buttons after a short delay
            setTimeout(injectRedmineButtons, 100);
        }
    });
}

/**
 * Watch for content changes (AJAX)
 */
function watchForContentChanges() {
    // Use MutationObserver to watch for DOM changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                // Check if our elements got removed and re-add them
                setTimeout(injectRedmineButtons, 500);
            }
        });
    });
    
    // Start observing
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}
