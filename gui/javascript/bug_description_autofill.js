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

// Wait for document to be ready
jQuery(document).ready(function() {
    // Add event listener for the createIssue checkbox
    jQuery(document).on('change', '#createIssue', function() {
        populateBugDescription();
    });
    
    // Also check if the checkbox is already checked when the page loads
    var createIssueCheckbox = jQuery('#createIssue');
    if(createIssueCheckbox.length > 0 && createIssueCheckbox.is(':checked')) {
        populateBugDescription();
    }
    
    // Function to fetch test case data from the server
    window.fetchTestCaseData = function(execId) {
        // Get tcversion_id from the page
        var tcversionId = getTcversionIdFromPage();
        var requestUrl = 'lib/execute/get_testcase_data_simple.php?tcversion_id=' + tcversionId;
        
        // Show loading overlay
        showLoadingOverlay('Fetching test case data...');
        
        // Create a timestamp for this request
        var requestTimestamp = new Date().toISOString();
        
        return new Promise(function(resolve, reject) {
            jQuery.ajax({
                url: 'lib/execute/get_testcase_data_simple.php',
                type: 'GET',
                data: { 
                    tcversion_id: tcversionId,
                    _timestamp: requestTimestamp
                },
                dataType: 'text',
                timeout: 300000,
                success: function(responseText, status, xhr) {
                    hideLoadingOverlay();
                    
                    // Try to parse JSON manually
                    try {
                        var response = JSON.parse(responseText);
                        resolve(response);
                    } catch (e) {
                        // Fall back to page scraping
                        resolve({
                            success: false,
                            message: 'JSON parse failed, using page scraping fallback'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    hideLoadingOverlay();
                    
                    if (status === 'timeout') {
                        resolve({
                            success: false,
                            message: 'API request timed out, using page scraping fallback'
                        });
                    } else {
                        resolve({
                            success: false,
                            message: 'API request failed, using page scraping fallback'
                        });
                    }
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
        }, 5000);
    }, 30000);
    
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
    // Only populate if checkbox is checked
    if (jQuery('#createIssue').is(':checked')) {
        // Get tcversion_id from the page
        var tcversionId = getTcversionIdFromPage();
        
        if (!tcversionId) {
            // Fall back to page scraping even without a tcversion_id
            populateTemplateFromPage();
            return;
        }
        
        // Make sure we have a valid ID before proceeding
        if (tcversionId.trim() === '') {
            populateTemplateFromPage();
            return;
        }
        
        // Fetch test case data from the server
        window.fetchTestCaseData(tcversionId).then(function(response) {
            if (response.success && response.data) {
                populateTemplateWithData(response);
            } else {
                // Fall back to the old method if API call fails
                populateTemplateFromPage();
            }
        }).catch(function(error) {
            // Fall back to the old method if API call fails
            populateTemplateFromPage();
        });
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
            return tcIdMatch[1];
        }
    }
    
    // If not found in hidden field, try to get it from the URL
    var urlParams = new URLSearchParams(window.location.search);
    var tcversionId = urlParams.get('tcversion_id');
    if (tcversionId) {
        return tcversionId;
    }
    
    // If still not found, try to find it in other common fields
    var tcversionMatch = jQuery('.exec_tc_title').text().match(/version\s*[:-]\s*(\d+)/i);
    if (tcversionMatch && tcversionMatch[1]) {
        return tcversionMatch[1];
    }
    
    // Return empty string instead of a default value
    return '';
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
    
    // Extract the test case data from the response
    var testCaseData = data;
    if (data.data) {
        if (Array.isArray(data.data) && data.data.length > 0) {
            // If data.data is an array, use the first item
            testCaseData = data.data[0];
            console.log('Using test case data from response.data[0]:', testCaseData);
        } else if (typeof data.data === 'object') {
            // If data.data is an object, use it directly
            testCaseData = data.data;
            console.log('Using test case data from response.data:', testCaseData);
        }
    }
    
    // Initialize template
    var template = "";
    
    // 1. Function ID - Get from scenario_id field
    var functionId = testCaseData.scenario_id || "";
    console.log('Function ID from API:', functionId);
    
    // If no function ID found, try to extract from test case name
    if (!functionId && testCaseData.testcase_name) {
        var match = testCaseData.testcase_name.match(/([A-Z]+-\d+)/);
        if (match) {
            functionId = match[1];
        }
    }
    template += "Function ID: " + functionId + "\n";
    
    // 2. Action - Get from sub_scenario field
    var action = testCaseData.sub_scenario || testCaseData.testcase_name || "";
    console.log('Action from API:', action);
    template += "Action: " + action + "\n";
    
    // 3. Test scenario - Get from test_script field
    var testScript = testCaseData.test_script || "";
    console.log('Test Script from API:', testScript);
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
                .replace(/[ \t]+/g, ' ') // Normalize spaces and tabs only, preserve newlines
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
    
    // 5. Expected result - Get from expected_results field
    var expectedResult = testCaseData.expected_results || "";
    console.log('Expected Results from API:', expectedResult);
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
    console.log('Test case ID for URLs:', testCaseId);
    
    if (testCaseId) {
        var baseUrl = window.location.origin + window.location.pathname;
        baseUrl = baseUrl.substring(0, baseUrl.indexOf('/lib/execute/'));
        console.log('Base URL for TestLink:', baseUrl);
        
        template += "\nTestLink URLs:\n";
        // template += "- View Test Case: " + baseUrl + "/lib/execute/execSetResults.php?tcversion_id=" + testCaseId + "\n";
        // template += "- Test Case Print View: " + baseUrl + "/lib/execute/execSetResultsPrint.php?tcversion_id=" + testCaseId + "\n";
        console.log('TestLink URLs added to template');
    } else {
        console.log('No test case ID found for URLs');
    }
    
    console.log('Generated template:', template);
    
    // Set the bug description field value
    function setBugNotesValue() {
        console.log('Attempting to find bug_notes field...');
        
        // Try different selectors to find the bug_notes field
        var bugNotesField = null;
        var foundField = null;
        
        // Try different selectors in order of likelihood
        var selectors = [
            '#bug_notes',
            'textarea[name="bug_notes"]',
            'textarea[id*="bug_notes"]',
            'textarea[id^="bug_notes"]',
            'textarea[class*="bug_notes"]',
            'textarea[class^="bug_notes"]'
        ];
        
        // Try each selector
        for (var i = 0; i < selectors.length; i++) {
            bugNotesField = jQuery(selectors[i]);
            if (bugNotesField.length > 0) {
                console.log('Found bug_notes field using selector:', selectors[i]);
                foundField = bugNotesField;
                break;
            }
        }
        
        // If still not found, try to find any textarea that might be the bug notes
        if (!foundField) {
            console.log('Bug notes field not found with standard selectors, trying to find by context...');
            
            // Look for a textarea near the createIssue checkbox
            var createIssueCheckbox = jQuery('#createIssue');
            if (createIssueCheckbox.length > 0) {
                // Find the closest form or container
                var form = createIssueCheckbox.closest('form, .form-container, .execControls, .workBack');
                if (form.length > 0) {
                    // Look for textareas in the same form/container
                    var textareas = form.find('textarea');
                    console.log('Found', textareas.length, 'textareas in the same container as createIssue checkbox');
                    
                    // If there's only one textarea, it's probably the one we want
                    if (textareas.length === 1) {
                        foundField = textareas;
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
                                foundField = $ta;
                                return false; // Break the each loop
                            }
                        });
                        
                        // If still not found, try the first textarea after the createIssue checkbox
                        if (!foundField) {
                            foundField = createIssueCheckbox.closest('tr, div').nextAll().find('textarea').first();
                            if (foundField.length > 0) {
                                console.log('Using first textarea after createIssue checkbox');
                            }
                        }
                    }
                }
            }
        }
        
        // If we found a field, use it
        if (foundField && foundField.length > 0) {
            bugNotesField = foundField;
            
            // Log the template we're about to set
            console.log('===== TEMPLATE TO SET =====');
            console.log(template);
            console.log('===========================');
            
            // Log the current value before setting
            console.log('Current field value BEFORE setting:', {
                id: bugNotesField.attr('id'),
                name: bugNotesField.attr('name'),
                value: bugNotesField.val(),
                isVisible: bugNotesField.is(':visible'),
                isDisabled: bugNotesField.is(':disabled'),
                isReadonly: bugNotesField.is('[readonly]')
            });
            
            // Set the value using jQuery
            console.log('Setting field value using jQuery.val()...');
            bugNotesField.val(template);
            console.log('jQuery.val() completed');
            
            // Trigger change event in case the field is being watched
            console.log('Triggering change and input events...');
            bugNotesField.trigger('change');
            bugNotesField.trigger('input');
            
            // Also try to set the value using vanilla JS as a fallback
            try {
                // Get the native DOM element
                const element = bugNotesField[0];
                if (element) {
                    console.log('Setting value using vanilla JS...');
                    // Set the value directly
                    element.value = template;
                    
                    // Create and dispatch input event
                    const inputEvent = new Event('input', { 
                        bubbles: true,
                        cancelable: true
                    });
                    element.dispatchEvent(inputEvent);
                    
                    // Create and dispatch change event
                    const changeEvent = new Event('change', { 
                        bubbles: true,
                        cancelable: true 
                    });
                    element.dispatchEvent(changeEvent);
                    
                    console.log('Value set using vanilla JS events');
                    
                    // Try one more approach - set the value using element.setAttribute()
                    console.log('Trying setAttribute approach...');
                    element.setAttribute('value', template);
                    
                    // Try to force a UI update
                    const event = new Event('input', { bubbles: true });
                    const tracker = element._valueTracker;
                    if (tracker) {
                        tracker.setValue(template);
                    }
                    element.dispatchEvent(event);
                }
            } catch (e) {
                console.error('Error setting value with vanilla JS:', e);
            }
            
            // Log the value after setting
            console.log('Current field value AFTER setting:', {
                value: bugNotesField.val(),
                'jQuery.val()': bugNotesField.val(),
                'element.value': bugNotesField[0]?.value,
                'element.getAttribute(value)': bugNotesField[0]?.getAttribute('value')
            });
            
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
        setTimeout(setBugNotesValue, 5000);
    }
}

/**
 * Fall back method to populate the template from the page if API fails
 */
function populateTemplateFromPage() {
    // Get test case details from the page
    var testCaseDetails = {};
    
    // Get test case ID and name
    var tcTitleElement = jQuery('.exec_tc_title').last();
    var tcTitleText = tcTitleElement.text();
    testCaseDetails.title = jQuery.trim(tcTitleText);
    
    // Get test case description - use notes field as requested
    var notesElement = jQuery('#notes');
    testCaseDetails.description = notesElement.val() || '';
    
    // Get expected results
    var expectedResultsElement = jQuery('.exec_test_spec_title:contains("Expected Results")').next();
    
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
    // Get the test case ID using the same approach as in getTcversionIdFromPage
    var tcId = getTcversionIdFromPage();
    
    // Try to find the notes field with the exact dynamic ID pattern
    var notesElement = jQuery('textarea[id="notes[' + tcId + ']"]');
    
    // If not found, try with a more flexible selector
    if (notesElement.length === 0) {
        notesElement = jQuery('textarea[id^="notes["]');
    }
    
    // If still not found, fall back to the simple #notes selector
    if (notesElement.length === 0) {
        notesElement = jQuery('#notes');
    }
    
    // Make sure we actually get the value from the notes field
    var notesValue = "";
    if (notesElement.length > 0) {
        notesValue = notesElement.val() || "";
        
        // If the notes field is empty, try getting the text content instead
        if (!notesValue || !notesValue.trim()) {
            notesValue = notesElement.text() || "";
        }
        
        // Trim the value to remove any leading/trailing whitespace
        notesValue = notesValue.trim();
    }
    
    testCaseDetails.notes = notesValue;
    
    // Get execution status from custom field as requested
    var statusField = jQuery('select[id^="custom_field_6_13_"]');
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
        
        if (statusSelect.length > 0) {
            statusText = statusSelect.find('option:selected').text();
            if (!statusText || !statusText.trim()) {
                statusText = "DEFERRED";
            }
        } else {
            statusText = "DEFERRED";
        }
    }
    
    testCaseDetails.execution_status = statusText;
    
    // Get test execution path
    var testSuiteTitle = jQuery('.exec_additional_info .exec_testsuite_details').text();
    
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
    var template = "\n";
    
    // Extract test case ID from the title
    var testCaseId = '';
    var testCaseName = testCaseDetails.title;
    
    // Try to extract test case ID using regex - handle multiple formats
    var tcIdMatch = testCaseName.match(/([A-Z]+-TC\d+)/i) || testCaseName.match(/TC-\d+\.RP-TC\d+/i);
    if (tcIdMatch && tcIdMatch[1]) {
        testCaseId = tcIdMatch[1];
    } else {
        // Fallback to RP-TC1 if no match found
        testCaseId = "RP-TC1";
    }
    
    // Get priority from custom field as requested
    var priorityField = jQuery('select[id^="custom_field_6_11_"]');
    var priorityValue = "P0";
    
    if (priorityField.length > 0) {
        var selectedPriority = priorityField.find('option:selected').text();
        if (selectedPriority && selectedPriority.trim()) {
            priorityValue = selectedPriority.trim();
        }
    }
    testCaseDetails.priority = priorityValue;
    
    // Add standard sections with empty strings if data not available
    // New template format without section titles
    template += "Function ID: " + (testCaseDetails.scenario_id || "") + "\n";
    template += "Action: " + (testCaseDetails.sub_scenario || "") + "\n";
    
    // Use test script as the test scenario
    template += "Test scenario: " + (testCaseDetails.test_script || "") + "\n";
    
    // Get test data from custom field with ID pattern custom_field_20_10_
    var testDataElement = jQuery('[id^="custom_field_20_10_"]');
    
    var testDataValue = "";  // Empty string as default, no hardcoded values
    if (testDataElement.length > 0) {
        // Check if it's a textarea, input, or select
        if (testDataElement.is('textarea') || testDataElement.is('input:text')) {
            testDataValue = testDataElement.val() || "";
        } else if (testDataElement.is('select')) {
            testDataValue = testDataElement.find('option:selected').text() || "";
        } else {
            testDataValue = testDataElement.text() || "";
        }
        
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
    
    // Set the bug description field value
    var bugNotesField = jQuery('#bug_notes');
    bugNotesField.val(template);
}
