/**
 * Redmine Integration Fix for TestLink
 * This script fixes bug tracker icons and provides direct integration with Redmine
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Fix missing icons
    fixMissingIcons();
    
    // Hide problematic connection message
    hideConnectionWarning();
    
    // Add direct Redmine integration button
    addRedmineButton();
});

/**
 * Fix missing bug tracker icons in TestLink
 */
function fixMissingIcons() {
    // Find all bug related icons and fix their paths
    const iconSelectors = [
        'img[src*="bug_add"]',
        'img[src*="bug_link"]',
        'img[src*="bug_del"]',
        'img[src*="bug_"]'
    ];
    
    iconSelectors.forEach(selector => {
        const icons = document.querySelectorAll(selector);
        icons.forEach(icon => {
            // If image is missing or has wrong path
            if (icon.naturalWidth === 0 || !icon.complete) {
                // Try to fix the path
                const filename = icon.src.split('/').pop();
                const newSrc = window.location.origin + '/testlink/gui/themes/default/images/' + filename;
                icon.src = newSrc;
                icon.style.width = '16px';
                icon.style.height = '16px';
                
                // Add fallback icons if still not loading
                icon.onerror = function() {
                    if (filename.includes('bug_add')) {
                        // Use a simple text fallback for bug add
                        const parent = this.parentNode;
                        if (parent) {
                            parent.innerHTML = '[+Bug]';
                            parent.style.color = '#337ab7';
                            parent.style.fontWeight = 'bold';
                        }
                    } else if (filename.includes('bug_link')) {
                        // Use a simple text fallback for bug link
                        const parent = this.parentNode;
                        if (parent) {
                            parent.innerHTML = '[Link]';
                            parent.style.color = '#337ab7';
                            parent.style.fontWeight = 'bold';
                        }
                    }
                };
            }
        });
    });
}

/**
 * Hide the annoying connection warning
 */
function hideConnectionWarning() {
    const warningElements = document.querySelectorAll('div[style*="color:red"]');
    
    warningElements.forEach(element => {
        if (element.textContent.includes('Something is preventing connection to Bug Tracking System')) {
            // Hide the warning
            element.style.display = 'none';
            
            // Add our custom message
            const customMsg = document.createElement('div');
            customMsg.innerHTML = '<div style="color:green; margin:10px 0;">' +
                                 '✓ Using Direct Redmine Integration</div>';
            element.parentNode.insertBefore(customMsg, element);
        }
    });
}

/**
 * Add a direct Redmine button to test execution pages
 */
function addRedmineButton() {
    // Check if we're on an execution page
    const isExecutionPage = document.querySelector('.exec_tc_title') !== null;
    
    if (isExecutionPage) {
        // Get test case information
        const tcTitle = document.querySelector('.exec_tc_title');
        const tcText = tcTitle ? tcTitle.textContent.trim() : 'Test Case';
        
        // Create the Report Bug button
        const reportBugBtn = document.createElement('button');
        reportBugBtn.textContent = 'Report Bug in Redmine';
        reportBugBtn.className = 'btn btn-warning';
        reportBugBtn.style.backgroundColor = '#f0ad4e';
        reportBugBtn.style.color = '#fff';
        reportBugBtn.style.padding = '5px 10px';
        reportBugBtn.style.border = 'none';
        reportBugBtn.style.borderRadius = '3px';
        reportBugBtn.style.cursor = 'pointer';
        reportBugBtn.style.margin = '10px 0';
        
        // Add click handler
        reportBugBtn.addEventListener('click', function() {
            // Get test case information from the page
            const tcTitleElement = document.querySelector('.exec_tc_title');
            const tcName = tcTitleElement ? tcTitleElement.textContent.trim() : '';
            
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
            const redmineUrl = './redmine-integration.php' + 
                '?testcase=' + encodeURIComponent(tcName) + 
                '&testplan=' + encodeURIComponent(testPlan) + 
                '&build=' + encodeURIComponent(buildName) + 
                '&result=' + encodeURIComponent(result);
            
            // Open in a new window
            window.open(redmineUrl, '_blank', 'width=800,height=700');
        });
        
        // Find a good place to insert the button
        const insertPoint = document.querySelector('.exec_tc_title');
        if (insertPoint && insertPoint.parentNode) {
            insertPoint.parentNode.insertBefore(reportBugBtn, insertPoint.nextSibling);
        }
    }
}
