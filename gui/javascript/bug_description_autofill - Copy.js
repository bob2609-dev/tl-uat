/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Auto-fill bug description when creating an issue from test execution
 *
 * @package     TestLink
 * @copyright   2025 TestLink community
 */

// Create loading overlay styles
var overlayStyles = `
<style>
    #bug_description_loading_overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }
    #bug_description_loading_message {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        text-align: center;
        max-width: 80%;
    }
    .loading-spinner {
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
        margin: 0 auto 15px auto;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
`;

// Add styles to the document
jQuery('head').append(overlayStyles);

// Function to show loading overlay
function showLoadingOverlay(message) {
    // alert("showing overlay")
    // Create overlay if it doesn't exist
    if (jQuery('#bug_description_loading_overlay').length === 0) {
        var overlay = `
        <div id="bug_description_loading_overlay">
            <div id="bug_description_loading_message">
                <div class="loading-spinner"></div>
                <div id="loading_text">${message || 'Loading...'}</div>
            </div>
        </div>
        `;
        jQuery('body').append(overlay);
    } else {
        // Update message if overlay exists
        jQuery('#loading_text').text(message || 'Loading...');
        jQuery('#bug_description_loading_overlay').show();
    }
}

// Function to hide loading overlay
function hideLoadingOverlay() {
    jQuery('#bug_description_loading_overlay').hide();
}

// Add test button to debug textarea updates
function addTestButton() {
    const testBtn = document.createElement('button');
    testBtn.textContent = 'TEST TEXTAREA';
    testBtn.style.position = 'fixed';
    testBtn.style.top = '10px';
    testBtn.style.right = '10px';
    testBtn.style.zIndex = '9999';
    testBtn.style.padding = '10px';
    testBtn.style.background = '#4CAF50';
    testBtn.style.color = 'white';
    testBtn.style.border = 'none';
    testBtn.style.borderRadius = '4px';
    testBtn.style.cursor = 'pointer';
    
    testBtn.onclick = function() {
        console.log('=== TEST BUTTON CLICKED ===');
        const testValue = 'TEST ' + new Date().toISOString();
        const field = document.getElementById('bug_notes');
        
        if (field) {
            console.log('Found bug_notes field, setting test value:', testValue);
            
            // Try different methods to set the value
            field.value = testValue;
            field.textContent = testValue;
            field.innerHTML = testValue;
            
            // Force a reflow to ensure the UI updates
            const event = document.createEvent('UIEvents');
            event.initUIEvent('input', true, true, window, 1);
            field.dispatchEvent(event);
            
            // Try dispatching events
            const events = ['input', 'change', 'blur', 'keyup'];
            events.forEach(eventType => {
                const event = new Event(eventType, { bubbles: true });
                field.dispatchEvent(event);
                console.log(`Dispatched ${eventType} event`);
            });
            
            // Try using jQuery if available
            if (typeof jQuery !== 'undefined') {
                jQuery(field).val(testValue).trigger('input').trigger('change');
                console.log('Used jQuery to set value');
            }
            
            console.log('Field value after setting:', field.value);
            
            // Try focusing and blurring
            field.focus();
            setTimeout(() => field.blur(), 100);
            
            return true;
        } else {
            console.error('Could not find bug_notes field');
            return false;
        }
    };
    
    // Add the button to the page
    document.body.appendChild(testBtn);
    console.log('Added test button to page');
}

// Debug function to check if our script is loaded
console.log('=== BUG DESCRIPTION AUTOFILL SCRIPT LOADED ===');

// Wait for the DOM to be fully loaded before adding the test button
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded, adding test button');
        addTestButton();
    });
} else {
    console.log('DOM already loaded, adding test button immediately');
    addTestButton();
}

// Function to monitor all checkbox changes
function monitorCheckboxChanges() {
    console.log('=== MONITORING ALL CHECKBOX CHANGES ===');
    
    // Monitor all current and future checkboxes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.matches('input[type="checkbox"]')) {
                            setupCheckboxDebugging(node);
                        }
                        // Check children too
                        node.querySelectorAll('input[type="checkbox"]').forEach(setupCheckboxDebugging);
                    }
                });
            }
        });
    });

    // Start observing the document with the configured parameters
    observer.observe(document.body, { 
        childList: true, 
        subtree: true,
        attributes: true,
        characterData: true
    });
    
    // Also set up debugging for existing checkboxes
    document.querySelectorAll('input[type="checkbox"]').forEach(setupCheckboxDebugging);
    
    console.log('Checkbox monitoring active');
}

function setupCheckboxDebugging(checkbox) {
    if (checkbox._debugSetup) return; // Don't set up the same checkbox twice
    checkbox._debugSetup = true;
    
    // Store original click handler
    const originalClick = checkbox.onclick;
    checkbox.onclick = function(e) {
        console.log('=== CHECKBOX CLICKED ===');
        console.log('Checkbox:', {
            id: this.id,
            name: this.name,
            className: this.className,
            checked: this.checked,
            parent: this.parentElement ? this.parentElement.outerHTML : 'No parent'
        });
        
        // Call original click handler if it exists
        if (originalClick) {
            console.log('Calling original click handler');
            return originalClick.apply(this, arguments);
        }
    };
    
    // Also monitor change events
    checkbox.addEventListener('change', function(e) {
        console.log('=== CHECKBOX CHANGE EVENT ===');
        console.log('Checkbox changed:', {
            id: this.id,
            name: this.name,
            checked: this.checked,
            value: this.value
        });
        console.trace('Change event stack trace');
    }, true); // Use capture phase to catch all events
}

// Function to manually trigger the population
globalThis.manualTrigger = function() {
    console.log('=== MANUAL TRIGGER ===');
    console.log('Calling populateBugDescription()...');
    try {
        populateBugDescription();
        return 'Manual trigger completed. Check console for details.';
    } catch (e) {
        console.error('Error in manualTrigger:', e);
        return 'Error in manualTrigger: ' + e.message;
    }
};

// Function to manually set the bug notes field
globalThis.setBugNotesManually = function() {
    console.log('=== MANUAL BUG NOTES SET ===');
    try {
        const template = '=== TEST BUG DESCRIPTION ===\nThis is a test description.\nIf you see this, the manual set worked!';
        
        // Try different selectors
        const selectors = [
            '#bug_notes',
            'textarea[name="bug_notes"]',
            'textarea',
            '.bug_notes',
            'div.bug_notes textarea',
            '.bug-notes',
            'div.bug-notes textarea'
        ];
        
        let found = false;
        
        for (const selector of selectors) {
            const elements = document.querySelectorAll(selector);
            console.log(`Found ${elements.length} elements with selector: ${selector}`);
            
            elements.forEach((el, index) => {
                console.log(`  Element ${index}:`, {
                    id: el.id,
                    name: el.name,
                    className: el.className,
                    tagName: el.tagName,
                    parent: el.parentElement ? el.parentElement.outerHTML.substring(0, 200) + '...' : 'No parent'
                });
                
                try {
                    el.value = template;
                    
                    // Trigger events
                    const events = ['input', 'change', 'keyup', 'keydown', 'blur'];
                    events.forEach(eventType => {
                        const event = new Event(eventType, { bubbles: true });
                        el.dispatchEvent(event);
                    });
                    
                    console.log(`  Set value on element with selector: ${selector}`);
                    found = true;
                } catch (e) {
                    console.error(`  Error setting value on element with selector ${selector}:`, e);
                }
            });
        }
        
        if (!found) {
            console.error('Could not find bug_notes field with any selector');
            return 'Could not find bug_notes field. Check console for details.';
        }
        
        return 'Bug notes set manually. Check the form to see if it worked.';
    } catch (e) {
        console.error('Error in setBugNotesManually:', e);
        return 'Error in setBugNotesManually: ' + e.message;
    }
};

// Function to attach event handlers
function attachEventHandlers() {
    console.log('=== ATTACHING EVENT HANDLERS ===');
    
    // Remove any existing handlers to prevent duplicates
    jQuery(document).off('change', 'input[type="checkbox"]');
    
    // Use event delegation for dynamically added elements
    jQuery(document).on('change', 'input[type="checkbox"]', function(e) {
        console.log('Checkbox changed:', this);
        console.log('Checkbox ID:', this.id);
        console.log('Checkbox name:', this.name);
        console.log('Checkbox checked:', this.checked);
        
        // If this is our target checkbox or any checkbox if we can't find the specific one
        if (this.id === 'createIssue' || this.name === 'createIssue' || 
            this.id.includes('create') || this.name.includes('create') ||
            this.id.includes('issue') || this.name.includes('issue')) {
            
            console.log('=== CREATE ISSUE CHECKBOX DETECTED ===');
            console.log('Checkbox state:', this.checked ? 'CHECKED' : 'UNCHECKED');
            
            if (this.checked) {
                console.log('Checkbox checked, populating description...');
                populateBugDescription();
            }
        }
    });
    
    console.log('Event handlers attached');
}

// Wait for document to be ready
jQuery(document).ready(function() {
    // Start monitoring all checkbox changes
    monitorCheckboxChanges();
    console.log('=== DOCUMENT READY ===');
    
    // Initial attachment of event handlers
    attachEventHandlers();
    
    // Also try to attach handlers after a short delay in case elements are added dynamically
    setTimeout(attachEventHandlers, 1000);
    
    // Try to attach on window load as well
    jQuery(window).on('load', attachEventHandlers);
    
    // Try to attach on any AJAX complete
    jQuery(document).ajaxComplete(function() {
        console.log('AJAX complete, reattaching handlers...');
        setTimeout(attachEventHandlers, 500);
    });
    console.log('=== DOCUMENT READY ===');
    
    // Log all checkboxes on the page for debugging
    console.log('=== PAGE ELEMENTS ===');
    console.log('All checkboxes:', jQuery('input[type="checkbox"]'));
    console.log('All inputs:', jQuery('input'));
    
    // Try to find and log the form that might contain our checkbox
    console.log('All forms:', jQuery('form'));
    
    // Try to find the checkbox again after a delay in case it's added dynamically
    setTimeout(function() {
        console.log('=== DELAYED CHECK ===');
        console.log('Checkboxes after delay:', jQuery('input[type="checkbox"]'));
        
        // Try to manually find and click the checkbox
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach((cb, index) => {
            console.log(`Checkbox ${index}:`, {
                id: cb.id,
                name: cb.name,
                className: cb.className,
                parentHtml: cb.parentElement ? cb.parentElement.outerHTML : 'No parent'
            });
        });
    }, 2000);
    
    // Function to fetch test case data from the server
    window.fetchTestCaseData = function(execId) {
        // Get tcversion_id from the page
        var tcversionId = getTcversionIdFromPage();
        var requestUrl = 'lib/execute/get_testcase_data_simple.php?tcversion_id=' + tcversionId;
        
        // Show loading overlay
        showLoadingOverlay('Fetching test case data...');
        
        // Log the request details
        console.log('===== FETCH TEST CASE DATA =====');
        console.log('Request URL:', requestUrl);
        console.log('tcversion_id:', tcversionId);
        console.log('Exec ID:', execId);
        
        // Log current URL and form data for debugging
        console.log('Current URL:', window.location.href);
        console.log('Form data:', jQuery('form').serialize());
        
        // Create a timestamp for this request
        var requestTimestamp = new Date().toISOString();
        
        // Make the AJAX call with error handling
        return new Promise((resolve, reject) => {
            const AJAX_TIMEOUT = 10000; // 10 seconds
            
            $.ajax({
                url: 'lib/execute/get_testcase_data_simple.php',
                type: 'GET',
                data: { 
                    tcversion_id: tcversionId,
                    _debug: true, // Add debug flag
                    _timestamp: requestTimestamp // Add timestamp for tracking
                },
                dataType: 'json',
                timeout: AJAX_TIMEOUT,
                success: function(response, status, xhr) {
                    console.log('===== API RESPONSE =====');
                    console.log('Status:', status);
                    console.log('Response:', response);
                    
                    try {
                        console.log('Response headers:', xhr.getAllResponseHeaders());
                    } catch (e) {
                        console.log('Could not get response headers:', e.message);
                    }
                    
                    // Log the raw response text if parsing failed
                    if (!response) {
                        console.error('Empty or invalid JSON response. Raw response:', xhr.responseText);
                        // Try to parse the response manually
                        try {
                            const parsed = JSON.parse(xhr.responseText);
                            console.log('Manually parsed response:', parsed);
                            response = parsed;
                        } catch (e) {
                            console.error('Could not parse response as JSON:', e);
                        }
                    }
                    
                    // Log the structure of the response
                    if (response) {
                        console.log('Response type:', typeof response);
                        console.log('Response keys:', Object.keys(response));
                        if (response.data) {
                            console.log('Response data type:', typeof response.data);
                            console.log('Response data keys:', Object.keys(response.data));
                        }
                    }
                    
                    hideLoadingOverlay();
                    
                    // If we still don't have a valid response, try to get more info
                    if (!response || typeof response !== 'object') {
                        console.error('Invalid response format. Response:', response);
                        console.error('Response text:', xhr.responseText);
                        console.error('Status code:', xhr.status, xhr.statusText);
                        
                        // Try to get more error details
                        let errorDetails = {
                            status: xhr.status,
                            statusText: xhr.statusText,
                            responseText: xhr.responseText,
                            readyState: xhr.readyState
                        };
                        
                        console.error('Error details:', errorDetails);
                        
                        resolve({
                            success: false,
                            message: `Invalid response format (${xhr.status} ${xhr.statusText})`,
                            details: errorDetails
                        });
                        return;
                    }
                    
                    resolve(response);
                },
                error: function(xhr, status, error) {
                    console.error('===== API ERROR =====');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Ready state:', xhr.readyState);
                    console.error('Status code:', xhr.status, xhr.statusText);
                    
                    // Get more details about the error
                    let errorDetails = {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        readyState: xhr.readyState
                    };
                    
                    console.error('Error details:', errorDetails);
                    
                    // Try to parse the response if it exists
                    let parsedResponse = null;
                    try {
                        if (xhr.responseText) {
                            parsedResponse = JSON.parse(xhr.responseText);
                            console.error('Parsed error response:', parsedResponse);
                        }
                    } catch (e) {
                        console.error('Could not parse error response:', e);
                    }
                    
                    hideLoadingOverlay();
                    resolve({
                        success: false,
                        message: `AJAX request failed: ${status} - ${error}`,
                        status: status,
                        error: error,
                        details: errorDetails,
                        parsedResponse: parsedResponse
                    });
                }
            });
        });
    };
});

// Enhanced page load functionality with vanilla JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    // More informative alert with timestamp
    const loadTime = new Date().toLocaleTimeString();
    console.log(`Page successfully loaded at ${loadTime}`);
});

// Alternative approach using window.onload (waits for all resources)
window.onload = function() {
    console.log('All page resources loaded (images, styles, scripts, etc.)');
    
    // Add some visual feedback to the page
    const loadMessage = document.createElement('div');
    // loadMessage.textContent = 'Page fully loaded!';
    // loadMessage.style.cssText = 'position: fixed; top: 10px; right: 10px; background: #4CAF50; color: white; padding: 10px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);';
    document.body.appendChild(loadMessage);
    
    // Auto-hide the message after 3 seconds
    setTimeout(function() {
        loadMessage.style.opacity = '0';
        loadMessage.style.transition = 'opacity 0.5s ease';
        
        // Remove from DOM after fade out
        setTimeout(function() {
            document.body.removeChild(loadMessage);
        }, 500);
    }, 3000);
    
    // Performance metrics
    if(window.performance) {
        const perfData = window.performance.timing;
        const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
        console.log(`Page load time: ${pageLoadTime}ms`);
    }
};

/**
 * Populate bug description with test case details
 */
function populateBugDescription() {
    console.log('populateBugDescription function called');
    
    // Only populate if checkbox is checked
    if (jQuery('#createIssue').is(':checked')) {
        console.log('Create Issue checkbox is checked');
        
        // Get tcversion_id from the page
        var tcversionId = getTcversionIdFromPage();
        console.log('Test Case Version ID:', tcversionId);
        
        if (!tcversionId) {
            console.error('Could not find tcversion_id');
            // Fall back to page scraping even without a tcversion_id
            populateTemplateFromPage();
            return;
        }
        
        // Make sure we have a valid ID before proceeding
        if (tcversionId.trim() === '') {
            console.error('Empty tcversion_id, falling back to page scraping');
            populateTemplateFromPage();
            return;
        }
        
        // Fetch test case data from the server
        window.fetchTestCaseData().then(function(response) {
            if (response.success && response.data) {
                console.log('Successfully fetched test case data:', response.data);
                populateTemplateWithData(response.data);
            } else {
                console.error('Failed to fetch test case data:', response.message);
                if (response.rawResponse) {
                    console.log('Raw API response excerpt:', response.rawResponse);
                }
                // Fall back to the old method if API call fails
                populateTemplateFromPage();
            }
        }).catch(function(error) {
            clearTimeout(apiTimeout); // Clear the timeout since we got a response (error)
            console.error('Error fetching test case data:', error);
            
            if (error.responseText) {
                console.log('Error response text excerpt:', error.responseText);
            }
            
            // Fall back to the old method if API call fails
            populateTemplateFromPage();
        });
    } else {
        console.log('Create Issue checkbox is NOT checked');
    }
}

/**
 * Get the execution ID from the page URL or form
 */
function getExecutionIdFromPage() {
    // Try to get it from the URL first
    var urlParams = new URLSearchParams(window.location.search);
    var execId = urlParams.get('exec_id') || urlParams.get('id');
    
    // If not in URL, try to get it from a hidden field
    if (!execId) {
        var execIdField = jQuery('input[name="exec_id"]');
        if (execIdField.length > 0) {
            execId = execIdField.val();
        }
    }
    
    return execId;
}

/**
 * Get the tcversion_id from the page URL or form
 */
function getTcversionIdFromPage() {
    // Look for the hidden tc_version field which contains the correct ID
    // Format is: <input type='hidden' name='tc_version[58348]' value='58347' />
    // Where 58348 is the ID we need
    
    // First try to get the ID from the form field name pattern tc_version[ID]
    var tcIdMatch = null;
    var tcVersionField = jQuery('input[name^="tc_version["]');
    if (tcVersionField.length > 0) {
        var nameAttr = tcVersionField.attr('name');
        tcIdMatch = nameAttr.match(/tc_version\[(\d+)\]/);
        if (tcIdMatch && tcIdMatch[1]) {
            console.log('Found TC ID from hidden field:', tcIdMatch[1]);
            return tcIdMatch[1];
        }
    }
    
    // If not found in hidden field, try to get it from the URL
    var urlParams = new URLSearchParams(window.location.search);
    var tcversionId = urlParams.get('tcversion_id');
    if (tcversionId) {
        console.log('Found TC ID from URL:', tcversionId);
        return tcversionId;
    }
    
    // If still not found, try to find it in other common fields
    var tcversionMatch = jQuery('.exec_tc_title').text().match(/version\s*[:-]\s*(\d+)/i);
    if (tcversionMatch && tcversionMatch[1]) {
        console.log('Found TC ID from title text:', tcversionMatch[1]);
        return tcversionMatch[1];
    }
    
    // If still not found, log an error but don't use a default value
    console.error('ERROR: Could not find a valid test case ID');
    return ''; // Return empty string instead of a default value
}

/**
 * Populate the bug description template with the test case data in the required format
 */
function populateTemplateWithData(data) {
    console.log('===== POPULATE TEMPLATE =====');
    console.log('Raw data received:', data);
    
    // Log the complete structure of the data
    if (data) {
        console.log('Data type:', typeof data);
        console.log('Data keys:', Object.keys(data));
        
        // If data has a data property, log its structure too
        if (data.data) {
            console.log('Data.data type:', typeof data.data);
            console.log('Data.data keys:', Object.keys(data.data));
            
            // If data.data is an array, log its length and first item
            if (Array.isArray(data.data)) {
                console.log('Data.data is an array with length:', data.data.length);
                if (data.data.length > 0) {
                    console.log('First item in data.data:', data.data[0]);
                }
            }
        }
    } else {
        console.error('No data received to populate template');
    }
    
    // Extract the first item from the data array if it exists
    var testCaseData = data;
    if (data.data && Array.isArray(data.data) && data.data.length > 0) {
        testCaseData = data.data[0];
        console.log('Using test case data from response.data[0]:', testCaseData);
    }
    
    // Initialize template
    var template = "";
    
    // 1. Function ID - Get from test case name or ID
    var functionId = testCaseData.Scenario_ID || testCaseData.testcase_number || "";
    
    // If no function ID found, try to extract from test case name
    if (!functionId && testCaseData.testcase_name) {
        var match = testCaseData.testcase_name.match(/([A-Z]+-\d+)/);
        if (match) {
            functionId = match[1];
        }
    }
    template += "Function ID: " + functionId + "\n";
    
    // 2. Action - Get from test case name or steps
    var action = testCaseData.testcase_name || "";
    template += "Action: " + action + "\n";
    
    // 3. Test scenario - Get from test case steps or description
    var testScript = testCaseData.Test_Script || "";
    // Clean up HTML tags and line breaks
    testScript = testScript.replace(/<[^>]*>/g, '').trim();
    testScript = testScript.replace(/\s+/g, ' '); // Normalize whitespace
    template += "Test scenario: " + testScript + "\n";
    
    // 4. Test Data - Get from custom fields or steps
    var testDataValue = "";
    
    // First try to get test data from the API response if available
    if (testCaseData.Test_Data) {
        testDataValue = testCaseData.Test_Data;
        console.log('Using test data from API response:', testDataValue);
    } 
    // Fall back to form field if not in API response
    else {
        var testDataElement = jQuery('[id^="custom_field_20_10_"]');
        
        if (testDataElement.length > 0) {
            console.log('Found test data element with ID:', testDataElement.attr('id'));
            
            // Get the value based on element type
            if (testDataElement.is('textarea') || testDataElement.is('input:text')) {
                testDataValue = testDataElement.val() || "";
            } else if (testDataElement.is('select')) {
                testDataValue = testDataElement.find('option:selected').text() || "";
            } else {
                testDataValue = testDataElement.text() || "";
            }
            
            // Clean up the test data
            testDataValue = testDataValue.trim()
                .replace(/<[^>]*>/g, '') // Remove HTML tags
                .replace(/\s+/g, ' ')    // Normalize whitespace
                .trim();
                
            console.log('Test data value from form field:', testDataValue);
        }
    }
    
    // Format test data with brackets if it's not empty
    template += "Test Data: [\n";
    if (testDataValue) {
        // Split by commas or newlines and format each item
        var testDataItems = testDataValue.split(/[,\n]/)
            .map(item => item.trim())
            .filter(item => item.length > 0);
            
        if (testDataItems.length > 0) {
            template += testDataItems.join("\n");
        } else {
            template += "N/A";
        }
    } else {
        template += "N/A";
    }
    template += "\n]\n";
    
    // 5. Expected result - Get from test case expected results
    var expectedResult = testCaseData.Expected_Results || "";
    // Clean up HTML tags and line breaks
    expectedResult = expectedResult.replace(/<[^>]*>/g, '').trim();
    expectedResult = expectedResult.replace(/\s+/g, ' '); // Normalize whitespace
    template += "Expected result: " + expectedResult + "\n";
    
    // 6. Test result - Get from execution notes or status
    var testResult = "";
    
    // First try to get test result from the API response if available
    if (testCaseData.execution_notes || testCaseData.Execution_Notes) {
        testResult = testCaseData.execution_notes || testCaseData.Execution_Notes;
        console.log('Using test result from API response:', testResult);
    }
    // Fall back to form field if not in API response
    else {
        var notesElement = jQuery('textarea[name^="notes["]');
        if (notesElement.length > 0) {
            testResult = notesElement.val() || "";
            // Clean up the notes
            testResult = testResult.trim()
                .replace(/<[^>]*>/g, '') // Remove HTML tags
                .replace(/\s+/g, ' ')    // Normalize whitespace
                .trim();
            console.log('Using test result from form field:', testResult);
        }
    }
    
    // Format test result with brackets if it's not empty
    template += "Test result: [\n";
    if (testResult) {
        // If test result is multi-line, ensure proper indentation
        var resultLines = testResult.split('\n');
        template += resultLines.map(line => line.trim()).filter(line => line.length > 0).join("\n");
    } else {
        template += "N/A";
    }
    template += "\n]\n";
    
    // 7. Add TestLink URLs
    var testCaseId = getTcversionIdFromPage();
    if (testCaseId) {
        var baseUrl = window.location.origin + window.location.pathname;
        baseUrl = baseUrl.substring(0, baseUrl.indexOf('/lib/execute/'));
        
        template += "\nTestLink URLs:\n";
        template += "- View Test Case: " + baseUrl + "/lib/execute/execSetResults.php?tcversion_id=" + testCaseId + "\n";
        template += "- Test Case Print View: " + baseUrl + "/lib/execute/execSetResultsPrint.php?tcversion_id=" + testCaseId + "\n";
    }
    
    console.log('Generated template:', template);
    
    // Function to log all event listeners on an element
    function logAllEventListeners(element) {
        if (!element) return;
        
        const events = [
            'input', 'change', 'keyup', 'keydown', 'keypress', 'click', 'focus', 'blur',
            'mousedown', 'mouseup', 'mousemove', 'mouseover', 'mouseout', 'mouseenter', 'mouseleave',
            'propertychange', 'DOMAttrModified', 'DOMCharacterDataModified', 'DOMNodeInserted', 'DOMNodeRemoved'
        ];
        
        console.log('Event listeners on element:', element);
        
        events.forEach(eventType => {
            try {
                if (typeof getEventListeners === 'function') {
                    const listeners = getEventListeners(element)[eventType];
                    if (listeners && listeners.length > 0) {
                        console.group('Event:', eventType);
                        listeners.forEach((listener, i) => {
                            console.log(`Listener ${i + 1}:`, {
                                type: eventType,
                                handler: typeof listener.listener === 'function' ? listener.listener.toString() : 'Not a function',
                                useCapture: listener.useCapture,
                                passive: listener.passive,
                                once: listener.once,
                                source: typeof listener.listener === 'function' ? 
                                    listener.listener.toString().substring(0, 200) + '...' : 'N/A'
                            });
                        });
                        console.groupEnd();
                    }
                } else {
                    console.log('getEventListeners is not available in this context');
                    return; // Exit the forEach loop
                }
            } catch (e) {
                console.log(`Could not get listeners for ${eventType}:`, e.message);
            }
        });
    }

    // Simple test function to set a hardcoded value
    function setTestValue() {
        console.log('=== TEST: Setting hardcoded value ===');
        const testValue = 'TEST 123 - This is a test string';
        const field = document.getElementById('bug_notes');
        
        if (field) {
            console.log('Found bug_notes field, setting test value');
            field.value = testValue;
            console.log('Value set. Field value is now:', field.value);
            
            // Try different events to trigger updates
            const events = ['input', 'change', 'blur', 'keyup'];
            events.forEach(eventType => {
                const event = new Event(eventType, { bubbles: true });
                field.dispatchEvent(event);
                console.log(`Dispatched ${eventType} event`);
            });
            
            return true;
        } else {
            console.error('Could not find bug_notes field');
            return false;
        }
    }
    
    // Set the bug description field value
    function setBugNotesValue(template) {
        console.log('=== Starting setBugNotesValue ===');
        
        // First, try setting a hardcoded value
        setTestValue();
        
        // Then proceed with the original functionality
        console.log('Attempting to find bug_notes field...');
        var bugNotesField = jQuery('#bug_notes, textarea[name="bug_notes"], textarea[id*="bug_notes"]');
        
        // If not found, try to find it near the createIssue checkbox
        if (bugNotesField.length === 0) {
            console.log('Field not found with standard selectors, trying context-based search...');
            var createIssueCheckbox = document.getElementById('createIssue');
            if (createIssueCheckbox) {
                // Look for the bug notes field near the checkbox
                var container = createIssueCheckbox.closest('tr, div, p, form');
                if (container) {
                    bugNotesField = jQuery('textarea', container);
                    console.log('Found', bugNotesField.length, 'textareas near createIssue checkbox');
                }
                
                // If still not found, try to find it in the form
                if (bugNotesField.length === 0) {
                    var form = jQuery(createIssueCheckbox).closest('form, .form-container, .execControls, .workBack');
                    if (form.length > 0) {
                        // Look for textareas in the same form/container
                        var textareas = form.find('textarea');
                        console.log('Found', textareas.length, 'textareas in the same container as createIssue checkbox');
                        
                        // If there's only one textarea, it's probably the one we want
                        if (textareas.length === 1) {
                            bugNotesField = textareas;
                            console.log('Using the only textarea found in the container');
                        } else if (textareas.length > 1) {
                            // Try to find the most likely candidate
                            textareas.each(function() {
                                var $ta = jQuery(this);
                                var id = $ta.attr('id') || '';
                                var name = $ta.attr('name') || '';
                                var className = $ta.attr('class') || '';
                                
                                // Check for common patterns in ID, name, or class
                                if (id.toLowerCase().includes('bug') || 
                                    name.toLowerCase().includes('bug') || 
                                    className.toLowerCase().includes('bug') ||
                                    id.toLowerCase().includes('note') || 
                                    name.toLowerCase().includes('note') || 
                                    className.toLowerCase().includes('note')) {
                                    
                                    console.log('Found likely candidate textarea:', {id: id, name: name, class: className});
                                    bugNotesField = $ta;
                                    return false; // Break the each loop
                                }
                            });
                            
                            // If still not found, try the first textarea after the createIssue checkbox
                            if (bugNotesField.length === 0) {
                                bugNotesField = jQuery(createIssueCheckbox).closest('tr, div').nextAll().find('textarea').first();
                                if (bugNotesField.length > 0) {
                                    console.log('Using first textarea after createIssue checkbox');
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // If we found a field, use it
        if (bugNotesField && bugNotesField.length > 0) {
            // Log the template we're about to set with detailed information
            console.group('===== TEMPLATE DATA =====');
            console.log('Template type:', typeof template);
            console.log('Template length:', template.length);
            console.log('Template content:');
            console.log(template);
            
            // Log the first 500 characters to see the actual content
            console.log('First 500 chars:', template.substring(0, 500));
            
            // Log line breaks and special characters
            console.log('Line breaks (\\n):', (template.match(/\n/g) || []).length);
            console.log('Carriage returns (\\r):', (template.match(/\r/g) || []).length);
            console.log('Tabs (\\t):', (template.match(/\t/g) || []).length);
            
            // Log the raw character codes for the first 100 characters
            const codes = [];
            for (let i = 0; i < Math.min(100, template.length); i++) {
                codes.push(template.charCodeAt(i));
            }
            console.log('First 100 char codes:', codes.join(','));
            
            // Log the template lines
            const lines = template.split('\n');
            console.log('Template lines:', lines.length);
            console.log('First 10 lines:', lines.slice(0, 10));
            
            console.groupEnd();
            
            // Log the current value before setting
            console.log('Current field value BEFORE setting:', {
                id: bugNotesField.attr('id'),
                name: bugNotesField.attr('name'),
                value: bugNotesField.val(),
                isVisible: bugNotesField.is(':visible'),
                isDisabled: bugNotesField.is(':disabled'),
                isReadonly: bugNotesField.is('[readonly]')
            });
            
            // First, try the direct approach
            try {
                console.log('Attempting direct value setting...');
                
                // Get the raw DOM element
                const element = bugNotesField[0];
                
                // 1. Try setting value directly
                element.value = template;
                
                // 2. Create and dispatch input event
                const inputEvent = new Event('input', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                
                // 3. Create and dispatch change event
                const changeEvent = new Event('change', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                
                // 4. Create a custom event for frameworks
                const customEvent = new CustomEvent('update:modelValue', {
                    detail: template,
                    bubbles: true,
                    cancelable: true
                });
                
                // Dispatch all events
                element.dispatchEvent(inputEvent);
                element.dispatchEvent(changeEvent);
                element.dispatchEvent(customEvent);
                
                // 5. Try using the native value setter
                const nativeInputValueSetter = Object.getOwnPropertyDescriptor(
                    window.HTMLTextAreaElement.prototype, 
                    'value'
                ).set;
                nativeInputValueSetter.call(element, template);
                
                // 6. Trigger a blur event to force update
                const blurEvent = new Event('blur', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                element.dispatchEvent(blurEvent);
                
                console.log('Direct value setting complete');
                
                // 7. Check if the value was set
                console.log('Value after setting (direct):', {
                    value: element.value,
                    valueLength: element.value.length,
                    isEqual: element.value === template
                });
                
                // 8. Try one more time with a small delay to ensure any framework has processed the change
                setTimeout(() => {
                    console.log('Delayed value check:', {
                        value: element.value,
                        valueLength: element.value.length,
                        isEqual: element.value === template
                    });
                    
                    // If still not set, try one more time
                    if (element.value !== template) {
                        console.log('Value still not set, trying one more time...');
                        element.value = template;
                        element.dispatchEvent(new Event('input', { bubbles: true }));
                        element.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }, 100);
                
                return true;
                
            } catch (e) {
                console.error('Error with direct approach:', e);
            }
            
            // Fallback to direct DOM manipulation if React approach fails
            try {
                console.log('Using direct DOM manipulation');
                const element = bugNotesField[0];
                
                // Create a new input event
                const inputEvent = new Event('input', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                
                // Create a new change event
                const changeEvent = new Event('change', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                
                // Set the value directly
                element.value = template;
                
                // Dispatch the events
                element.dispatchEvent(inputEvent);
                element.dispatchEvent(changeEvent);
                
                // Try to force a UI update
                element.dispatchEvent(new Event('keydown', { bubbles: true }));
                element.dispatchEvent(new Event('keyup', { bubbles: true }));
                element.dispatchEvent(new Event('blur', { bubbles: true }));
                
                console.log('Direct DOM manipulation complete');
            } catch (e) {
                console.error('Error with direct DOM manipulation:', e);
            }
            
            // Log the value after setting
            console.log('Current field value AFTER setting:', {
                value: bugNotesField.val(),
                'jQuery.val()': bugNotesField.val(),
                'element.value': bugNotesField[0] ? bugNotesField[0].value : 'N/A',
                'element.getAttribute(value)': bugNotesField[0] ? bugNotesField[0].getAttribute('value') : 'N/A'
            });
            
            // Add a debug function to the window for manual testing
            window.debugSetBugNotes = function() {
                console.log('=== DEBUG: Manually setting bug_notes ===');
                const field = document.querySelector('#bug_notes, textarea[name="bug_notes"], textarea[id*="bug_notes"]');
                if (field) {
                    console.log('Found field:', field);
                    field.value = template;
                    console.log('Value set directly');
                    
                    // Trigger events
                    const event = new Event('input', { bubbles: true });
                    field.dispatchEvent(event);
                    
                    const changeEvent = new Event('change', { bubbles: true });
                    field.dispatchEvent(changeEvent);
                    
                    console.log('Events dispatched');
                } else {
                    console.error('Field not found');
                }
            };
            
            // Try one more approach - find the field by its position relative to the createIssue checkbox
            try {
                const createIssueCheckbox = document.getElementById('createIssue');
                if (createIssueCheckbox) {
                    // Find the next textarea after the createIssue checkbox
                    const allElements = document.querySelectorAll('*');
                    let foundTextarea = false;
                    
                    for (let i = 0; i < allElements.length; i++) {
                        if (allElements[i] === createIssueCheckbox) {
                            // Look for the next textarea
                            for (let j = i + 1; j < allElements.length; j++) {
                                if (allElements[j].tagName === 'TEXTAREA') {
                                    allElements[j].value = template;
                                    console.log('Set value using DOM traversal');
                                    foundTextarea = true;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            } catch (e) {
                console.error('Error in DOM traversal approach:', e);
            }
            
            // Add a debug function to the window for manual testing
            window.debugSetBugNotes = function() {
                console.log('=== DEBUG: Manually setting bug_notes ===');
                console.log('Template:', template);
                
                // Try to find the field again
                const field = document.querySelector('#bug_notes, textarea[name="bug_notes"], textarea[id*="bug_notes"]');
                if (field) {
                    console.log('Found field:', field);
                    field.value = template;
                    console.log('Value set directly');
                    
                    // Trigger events
                    const event = new Event('input', { bubbles: true });
                    field.dispatchEvent(event);
                    
                    const changeEvent = new Event('change', { bubbles: true });
                    field.dispatchEvent(changeEvent);
                    
                    console.log('Events dispatched');
                } else {
                    console.error('Field not found');
                }
            };
            
            // Try to find the field by its position relative to the createIssue checkbox
            try {
                const createIssueCheckbox = document.getElementById('createIssue');
                if (createIssueCheckbox) {
                    // Find the next textarea after the createIssue checkbox
                    const allElements = document.querySelectorAll('*');
                    let foundTextarea = false;
                    
                    for (let i = 0; i < allElements.length; i++) {
                        if (allElements[i] === createIssueCheckbox) {
                            // Look for the next textarea
                            for (let j = i + 1; j < allElements.length; j++) {
                                if (allElements[j].tagName === 'TEXTAREA') {
                                    allElements[j].value = template;
                                    console.log('Set value using DOM traversal');
                                    foundTextarea = true;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
            } catch (e) {
                console.error('Error in DOM traversal approach:', e);
            }
            
            return true;
        } else {
            // Log all textareas on the page for debugging
            const textareas = Array.from(document.querySelectorAll('textarea'));
            console.error('Could not find bug_notes field. Available textareas:', 
                textareas.map(t => ({
                    id: t.id,
                    name: t.name,
                    className: t.className,
                    value: t.value.substring(0, 50) + (t.value.length > 50 ? '...' : '')
                }))
            );
            
            // Try to find any textarea that might be the bug notes
            const likelyCandidates = textareas.filter(t => 
                (t.id && (t.id.includes('bug') || t.id.includes('note'))) ||
                (t.name && (t.name.includes('bug') || t.name.includes('note'))) ||
                (t.className && (t.className.includes('bug') || t.className.includes('note')))
            );
            
            if (likelyCandidates.length > 0) {
                console.log('Likely candidate textareas found:', likelyCandidates);
            }
            
            return false;
        }
    }
    
    // Try to set the value immediately
    var success = setBugNotesValue();
    
    // If not successful, try again after a short delay in case the field isn't rendered yet
    if (!success) {
        console.log('Bug notes field not found, retrying in 500ms...');
        setTimeout(setBugNotesValue, 500);
    }
    
    return success;
}

/**
 * Fall back method to populate the template from the page if API fails
 */
function populateTemplateFromPage() {
    console.log('Falling back to populating template from page elements');
    
    // Get test case details from the page
    var testCaseDetails = {};
    
    // Get test case ID and name
    console.log('Attempting to get test case title');
    var tcTitleElement = jQuery('.exec_tc_title').last();
    console.log('tcTitleElement found:', tcTitleElement.length > 0);
    var tcTitleText = tcTitleElement.text();
    console.log('tcTitleText:', tcTitleText);
    testCaseDetails.title = jQuery.trim(tcTitleText);
    
    // Get test case description - use notes field as requested
    console.log('Attempting to get test case description from notes field');
    var notesElement = jQuery('#notes');
    console.log('notesElement found:', notesElement.length > 0);
    testCaseDetails.description = notesElement.val() || '';
    
    // Get expected results
    console.log('Attempting to get expected results');
    var expectedResultsElement = jQuery('.exec_test_spec_title:contains("Expected Results")').next();
    console.log('expectedResultsElement found:', expectedResultsElement.length > 0);
    
    // Get expected results from the page without hardcoded values
    var cleanExpectedResults = "";
    if (expectedResultsElement.length > 0) {
        var rawText = expectedResultsElement.text();
        if (rawText && rawText.trim()) {
            cleanExpectedResults = jQuery.trim(rawText);
        }
    }
    testCaseDetails.expected_results = cleanExpectedResults;
    
    // Get actual results/notes - using the dynamic ID pattern notes[ID]
    console.log('Attempting to get notes with dynamic ID pattern');
    
    // Get the test case ID using the same approach as in getTcversionIdFromPage
    var tcId = getTcversionIdFromPage();
    console.log('Looking for notes element with ID pattern: notes[' + tcId + ']');
    
    // Try to find the notes field with the exact dynamic ID pattern
    var notesElement = jQuery('textarea[id="notes[' + tcId + ']"]');
    console.log('Notes element with exact dynamic ID found:', notesElement.length > 0);
    
    // If not found, try with a more flexible selector
    if (notesElement.length === 0) {
        notesElement = jQuery('textarea[id^="notes["]');
        console.log('Notes element with flexible ID pattern found:', notesElement.length > 0);
    }
    
    // If still not found, fall back to the simple #notes selector
    if (notesElement.length === 0) {
        console.log('Falling back to simple #notes selector');
        notesElement = jQuery('#notes');
        console.log('Simple notes element found:', notesElement.length > 0);
    }
    
    // Make sure we actually get the value from the notes field
    var notesValue = "";
    if (notesElement.length > 0) {
        // Log the actual ID to help with debugging
        console.log('Found notes element with ID:', notesElement.attr('id'));
        
        notesValue = notesElement.val() || "";
        console.log('Raw notes value:', notesValue);
        
        // If the notes field is empty, try getting the text content instead
        if (!notesValue || !notesValue.trim()) {
            notesValue = notesElement.text() || "";
            console.log('Notes text content:', notesValue);
        }
        
        // Trim the value to remove any leading/trailing whitespace
        notesValue = notesValue.trim();
    } else {
        // If we still can't find it, try a more general approach
        console.log('Trying more general textarea selector');
        var allTextareas = jQuery('textarea');
        console.log('Found', allTextareas.length, 'textareas on the page');
        
        // Log all textareas for debugging
        allTextareas.each(function(index) {
            console.log('Textarea', index, 'ID:', jQuery(this).attr('id'), 
                        'Name:', jQuery(this).attr('name'),
                        'Value length:', (jQuery(this).val() || '').length);
        });
        
        console.log('Notes element not found with specific selectors, using empty string');
    }
    
    testCaseDetails.notes = notesValue;
    
    // Get execution status from custom field as requested
    console.log('Attempting to get execution status from custom field');
    var statusField = jQuery('select[id^="custom_field_6_13_"]');
    console.log('statusField found:', statusField.length > 0);
    var statusText = "";
    
    if (statusField.length > 0) {
        statusText = statusField.find('option:selected').text();
        // If status is empty or just whitespace, use DEFERRED as fallback
        if (!statusText || !statusText.trim()) {
            statusText = "DEFERRED";
        }
    } else {
        // Fallback to the original status select if custom field not found
        var statusSelect = jQuery('select[id^="statusSingle_"]');
        console.log('Fallback statusSelect found:', statusSelect.length > 0);
        
        if (statusSelect.length > 0) {
            statusText = statusSelect.find('option:selected').text();
            if (!statusText || !statusText.trim()) {
                statusText = "DEFERRED";
            }
        } else {
            statusText = "DEFERRED";
        }
    }
    
    console.log('statusText:', statusText);
    testCaseDetails.execution_status = statusText;
    
    // Get test execution path
    console.log('Attempting to get test execution path');
    var testSuiteTitle = jQuery('.exec_additional_info .exec_testsuite_details').text();
    console.log('testSuiteTitle:', testSuiteTitle);
    
    // Clean up the test execution path
    var cleanPath = "Customers > Operations> Customer Input";
    if (testSuiteTitle && testSuiteTitle.trim()) {
        // Extract just the path part, removing 'details' label if present
        var pathMatch = testSuiteTitle.match(/details\s*(.+)/i);
        if (pathMatch && pathMatch[1]) {
            cleanPath = jQuery.trim(pathMatch[1]);
        } else {
            cleanPath = jQuery.trim(testSuiteTitle);
        }
    }
    testCaseDetails.test_execution_path = cleanPath;
    
    // Create formatted template matching bugAdd.php format
    // Only proceed if the createIssue checkbox is checked
    const createIssueCheckbox = document.querySelector('input[type="checkbox"][id*="create"], input[type="checkbox"][id*="issue"]');
    if (!createIssueCheckbox || !createIssueCheckbox.checked) {
        console.log('Create issue checkbox not checked, skipping template generation');
        return '';
    }
    
    console.log('Creating template with gathered data');
    var template = "\n";
    
    // Using the full test case title as is
    var testCaseName = testCaseDetails.title || '';
    
    // template += "Test Case: " + testCaseId + "\n\n";
    
    // Get priority from custom field as requested
    console.log('Attempting to get priority from custom field');
    var priorityField = jQuery('select[id^="custom_field_6_11_"]');
    console.log('priorityField found:', priorityField.length > 0);
    var priorityValue = "P0";
    
    if (priorityField.length > 0) {
        var selectedPriority = priorityField.find('option:selected').text();
        if (selectedPriority && selectedPriority.trim()) {
            priorityValue = selectedPriority.trim();
        }
    }
    testCaseDetails.priority = priorityValue;
    console.log('Priority value:', priorityValue);
    
    // Add standard sections with empty strings if data not available
    // New template format without section titles
    template += "Function ID: " + (testCaseDetails.scenario_id || "") + "\n";
    template += "Action: " + (testCaseDetails.sub_scenario || "") + "\n";
    
    // Use test script as the test scenario
    template += "Test scenario: " + (testCaseDetails.test_script || "") + "\n";
    
    // Get test data from custom field with ID pattern custom_field_20_10_
    console.log('Attempting to get test data from custom field');
    var testDataElement = jQuery('[id^="custom_field_20_10_"]');
    console.log('Test data element found:', testDataElement.length > 0);
    
    var testDataValue = "";  // Empty string as default, no hardcoded values
    if (testDataElement.length > 0) {
        // Log the actual ID to help with debugging
        console.log('Found test data element with ID:', testDataElement.attr('id'));
        
        // Check if it's a textarea, input, or select
        if (testDataElement.is('textarea') || testDataElement.is('input:text')) {
            testDataValue = testDataElement.val() || "";
        } else if (testDataElement.is('select')) {
            testDataValue = testDataElement.find('option:selected').text() || "";
        } else {
            testDataValue = testDataElement.text() || "";
        }
        
        console.log('Raw test data value:', testDataValue);
        
        // Trim the value and use default if empty
        testDataValue = testDataValue.trim();
        if (!testDataValue) {
            testDataValue = " ";
        }
    }
    
    // Format test data with brackets
    var formattedTestData = "[\n" + testDataValue + "\n]";
    template += "Test Data: " + formattedTestData + "\n";
    
    // Expected results without section title
    template += "Expected result: " + (testCaseDetails.expected_results || "") + "\n";
    
    // Test result (from notes) in square brackets
    var formattedNotes = "[\n" + notesValue.trim() + "\n]";
    template += "Test result: " + formattedNotes + "\n";
    
    console.log('Template created:', template);
    
    // Set the bug description field value
    console.log('Attempting to set bug_notes field');
    var bugNotesField = jQuery('#bug_notes');
    console.log('bugNotesField found:', bugNotesField.length > 0);
    bugNotesField.val(template);
    console.log('Template set to bug_notes field');
}
