/**
 * TestLink Bug Description Autofill - Simplified Version
 * Auto-fill bug description when creating an issue from test execution
 */

console.log('=== BUG DESCRIPTION AUTOFILL SCRIPT LOADED (SIMPLE VERSION) ===');

// Wait for document to be ready
jQuery(document).ready(function() {
    console.log('=== DOCUMENT READY ===');
    
    // Monitor for checkbox changes
    jQuery(document).on('change', 'input[type="checkbox"]', function() {
        console.log('Checkbox changed:', this.id, this.checked);
        
        // Check if this is the create issue checkbox
        if (this.id && (this.id.includes('create') || this.id.includes('issue')) && this.checked) {
            console.log('Create issue checkbox checked, triggering autofill');
            populateBugDescription();
        }
    });
});

/**
 * Main function to populate bug description
 */
function populateBugDescription() {
    console.log('=== POPULATING BUG DESCRIPTION ===');
    
    // Get execution ID from page
    var execId = getExecutionIdFromPage();
    if (!execId) {
        console.error('Could not find execution ID');
        return;
    }
    
    console.log('Found execution ID:', execId);
    
    // Fetch test case data
    fetchTestCaseData(execId);
}

/**
 * Get execution ID from page URL or form
 */
function getExecutionIdFromPage() {
    // Try to get from URL parameters
    var urlParams = new URLSearchParams(window.location.search);
    var execId = urlParams.get('exec_id');
    
    if (!execId) {
        // Try to get from form fields
        var execField = jQuery('input[name="exec_id"]');
        if (execField.length > 0) {
            execId = execField.val();
        }
    }
    
    return execId;
}

/**
 * Fetch test case data from server
 */
function fetchTestCaseData(execId) {
    console.log('Fetching test case data for exec_id:', execId);
    
    jQuery.ajax({
        url: 'get_testcase_data_simple.php',
        type: 'GET',
        data: { exec_id: execId },
        dataType: 'json',
        timeout: 300000, // 5 minutes timeout in milliseconds
        success: function(response) {
            console.log('Test case data received:', response);
            populateTemplateWithData(response);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching test case data:', error);
            console.log('Status:', status);
            console.log('XHR status:', xhr.status);
            
            if (status === 'timeout') {
                console.error('Request timed out - falling back to page-based population');
            } else {
                console.log('Falling back to page-based population');
            }
            populateTemplateFromPage();
        }
    });
}

/**
 * Populate the bug description template with the test case data
 */
function populateTemplateWithData(data) {
    console.log('=== POPULATE TEMPLATE =====');
    
    // Extract test case data
    var testCaseData = data;
    if (data && data.data && Array.isArray(data.data) && data.data.length > 0) {
        testCaseData = data.data[0];
    }
    
    // Build template
    var template = "";
    template += "Function ID: " + (testCaseData.Scenario_ID || testCaseData.testcase_number || "") + "\n";
    template += "Action: " + (testCaseData.testcase_name || "") + "\n";
    
    var testScript = testCaseData.Test_Script || "";
    testScript = testScript.replace(/<[^>]*>/g, '').trim().replace(/\s+/g, ' ');
    template += "Test scenario: " + testScript + "\n";
    
    // Get test data from custom field
    var testDataValue = "";
    var testDataElement = jQuery('[id^="custom_field_20_10_"]');
    if (testDataElement.length > 0) {
        testDataValue = testDataElement.val() || testDataElement.text() || "";
        testDataValue = testDataValue.trim();
    }
    
    template += "Test Data: [\n" + (testDataValue || "N/A") + "\n]\n";
    
    var expectedResult = testCaseData.Expected_Results || "";
    expectedResult = expectedResult.replace(/<[^>]*>/g, '').trim().replace(/\s+/g, ' ');
    template += "Expected result: " + expectedResult + "\n";
    
    // Get test result from notes
    var testResult = "";
    var notesElement = jQuery('textarea[name^="notes["]');
    if (notesElement.length > 0) {
        testResult = notesElement.val() || "";
        testResult = testResult.trim();
    }
    
    template += "Test result: [\n" + (testResult || "N/A") + "\n]\n";
    
    console.log('Generated template:', template);
    
    // Set the bug notes field
    setBugNotesValue(template);
}

/**
 * Set the bug notes field value
 */
function setBugNotesValue(template) {
    console.log('=== Setting bug notes value ===');
    
    // Try multiple selectors to find the bug notes field
    var bugNotesField = null;
    var selectors = [
        '#bug_notes',
        'textarea[name="bug_notes"]',
        'textarea[id*="bug_notes"]',
        'textarea[name*="notes"]',
        'textarea[id*="notes"]'
    ];
    
    for (var i = 0; i < selectors.length; i++) {
        var field = jQuery(selectors[i]);
        if (field.length > 0) {
            bugNotesField = field;
            console.log('Found field with selector:', selectors[i]);
            break;
        }
    }
    
    // If not found, use the first textarea as fallback
    if (!bugNotesField || bugNotesField.length === 0) {
        console.log('Bug notes field not found with selectors, trying fallback');
        var allTextareas = jQuery('textarea');
        if (allTextareas.length > 0) {
            bugNotesField = allTextareas.first();
            console.log('Using first textarea as fallback');
        }
    }
    
    if (bugNotesField && bugNotesField.length > 0) {
        console.log('Setting template to bug notes field');
        bugNotesField.val(template);
        
        // Trigger events to ensure UI updates
        bugNotesField.trigger('input').trigger('change');
        
        console.log('Template set successfully');
        return true;
    } else {
        console.error('Could not find bug notes field');
        return false;
    }
}

/**
 * Fallback method to populate template from page elements
 */
function populateTemplateFromPage() {
    console.log('=== Populating from page elements ===');
    
    var template = "";
    template += "Function ID: \n";
    template += "Action: \n";
    template += "Test scenario: \n";
    template += "Test Data: [\nN/A\n]\n";
    template += "Expected result: \n";
    template += "Test result: [\nN/A\n]\n";
    
    setBugNotesValue(template);
}

// Add debug function to window for manual testing
window.debugSetBugNotes = function(testTemplate) {
    var template = testTemplate || "Test content from debug function";
    console.log('=== DEBUG: Setting bug notes ===');
    setBugNotesValue(template);
};

console.log('=== BUG DESCRIPTION AUTOFILL SCRIPT READY ===');
