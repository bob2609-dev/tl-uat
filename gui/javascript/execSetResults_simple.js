console.log('toogleRequiredOnShowHide function exists:', typeof toogleRequiredOnShowHide);

// Add debugging to track when dropdown is shown
window.debugDropdownState = function() {
  console.log('Dropdown container visibility:', jQuery('#integration_dropdown_container').css('display'));
  console.log('Dropdown container exists:', jQuery('#integration_dropdown_container').length > 0);
  console.log('Create issue checked:', jQuery('#createIssue').is(':checked'));
  console.log('Selected integration:', window.selectedIntegrationId);
};

// Call debug on page load
jQuery(document).ready(function() {
  setTimeout(debugDropdownState, 1000);
  
  // Add click debugging to checkbox
  jQuery('#createIssue').on('click', function() {
    console.log('Checkbox clicked, current state:', jQuery(this).is(':checked'));
    console.log('Dropdown visibility after checkbox click:', jQuery('#integration_dropdown_container').css('display'));
  });
  
  // Add click debugging to execution buttons
  jQuery('[id^="save_results"], [id^="save_and_next"], [id^="move2next"]').on('click', function() {
    console.log('Execution button clicked');
    console.log('Create issue checked:', jQuery('#createIssue').is(':checked'));
    console.log('Dropdown visibility before execution:', jQuery('#integration_dropdown_container').css('display'));
  });
});

// Priority popup functions
window.showPriorityPopup = function(tcvID, status) {
  console.log('showPriorityPopup called with tcvID:', tcvID, 'status:', status);
  
  // Only show popup for Passed, Failed, or Blocked statuses
  if (status !== 'p' && status !== 'f' && status !== 'b') {
    console.log('Status not requiring priority popup:', status);
    return;
  }
  
  // Check if priority popup already exists for this test case
  var existingPopup = document.getElementById('priorityPopup_' + tcvID);
  if (existingPopup) {
    console.log('Priority popup already exists for tcvID:', tcvID);
    return;
  }
  
  // Show the priority popup
  showPriorityPopupInternal(tcvID, status);
};

// Internal function to show the actual popup
window.showPriorityPopupInternal = function(tcvID, status) {
  console.log('showPriorityPopupInternal called with tcvID:', tcvID, 'status:', status);
  
  // Get current priority value
  var currentPriority = jQuery('#priority_' + tcvID).val() || '';
  
  // Create popup HTML
  var popupHtml = '<div id="priorityPopup_' + tcvID + '" class="priority-popup" style="position: absolute; background: white; border: 1px solid #ccc; padding: 10px; z-index: 1000;">' +
    '<h4>Select Priority</h4>' +
    '<div style="margin: 10px 0;">' +
    '<label><input type="radio" name="priorityRadio" value="low" ' + (currentPriority === 'low' ? 'checked' : '') + '> Low</label><br>' +
    '<label><input type="radio" name="priorityRadio" value="medium" ' + (currentPriority === 'medium' ? 'checked' : '') + '> Medium</label><br>' +
    '<label><input type="radio" name="priorityRadio" value="high" ' + (currentPriority === 'high' ? 'checked' : '') + '> High</label><br>' +
    '</div>' +
    '<button onclick="savePriorityAndStatus(' + tcvID + ', \'' + status + '\')">Save</button> ' +
    '<button onclick="closePriorityPopup(' + tcvID + ')">Cancel</button>' +
    '</div>';
  
  // Add popup to the page
  jQuery('body').append(popupHtml);
  
  // Position the popup near the test case
  var testCaseElement = jQuery('#status_' + tcvID);
  if (testCaseElement.length > 0) {
    var position = testCaseElement.offset();
    jQuery('#priorityPopup_' + tcvID).css({
      top: position.top + testCaseElement.height() + 5,
      left: position.left
    });
  }
  
  // Show the modal backdrop
  jQuery('#priorityModal').modal('show');
};

window.getStatusText = function(status) {
  switch(status) {
    case 'p': return 'Passed';
    case 'f': return 'Failed';
    case 'b': return 'Blocked';
    case 'n': return 'Not Run';
    default: return status;
  }
};

window.savePriorityAndStatus = function(tcvID, status) {
  var selectedPriority = jQuery('input[name="priorityRadio"]:checked').val();
  
  if (!selectedPriority) {
    alert('Please select a priority');
    return;
  }
  
  console.log('Saving priority:', selectedPriority, 'for tcvID:', tcvID, 'status:', status);
  
  // Update the hidden priority field
  jQuery('#priority_' + tcvID).val(selectedPriority);
  
  // Close the popup
  closePriorityPopup(tcvID);
  
  // Save the execution status
  saveExecStatusDirect(tcvID, status);
};

window.closePriorityPopup = function(tcvID) {
  jQuery('#priorityPopup_' + tcvID).remove();
};

window.saveExecStatusDirect = function(tcvID, status) {
  // Call the saveExecutionStatus function
  saveExecutionStatus(tcvID, status, undefined, undefined);
};

/*
 * Function: saveExecStatus
 * 
 */
function saveExecStatus(tcvID, status, msg, goNext) {
  console.log('saveExecStatus called with tcvID:', tcvID, 'status:', status, 'msg:', msg, 'goNext:', goNext);
  
  // Check if bug submission is in progress
  if (window.bugSubmissionInProgress) {
    alert('A bug submission is in progress. Please wait until it completes before changing test execution status.');
    return false;
  }
  
  // Check if createIssue checkbox is checked
  var createIssueChecked = jQuery('#createIssue').is(':checked');
  var selectedIntegration = window.selectedIntegrationId;
  
  console.log('Create issue checked:', createIssueChecked, 'Selected integration:', selectedIntegration);
  
  if (createIssueChecked && !selectedIntegration) {
    // Show integration dropdown for user to select
    console.log('Showing integration dropdown for selection');
    toggleIntegrationDropdown(true);
    
    // Focus on the dropdown
    jQuery('#integration_dropdown').focus();
    
    // Show message to user
    alert('Please select an integration from the dropdown, then click the execution button again.');
    return false;
  }
  
  // If no message provided, use default based on status
  if (!msg) {
    switch(status) {
      case 'p':
        msg = 'Test case passed';
        break;
      case 'f':
        msg = 'Test case failed';
        break;
      case 'b':
        msg = 'Test case blocked';
        break;
      default:
        msg = 'Test case execution updated';
    }
  }
  
  // Set the flag to indicate bug submission is in progress
  window.bugSubmissionInProgress = true;
  
  // Disable all test execution buttons
  if (typeof disableTestExecButtons === 'function') {
    disableTestExecButtons(true);
  }
  
  // Prepare data for submission
  var submissionData = {
    save_results: tcvID,
    status: status,
    notes: msg,
    priority: jQuery('#priority_' + tcvID).val()
  };
  
  // Add integration data if bug creation is enabled
  if (createIssueChecked && selectedIntegration) {
    submissionData.createIssue = 1;
    submissionData.integration_id = selectedIntegration;
    console.log('Adding integration data to submission:', selectedIntegration);
  }
  
  // Update the status
  jQuery.ajax({
    url: 'lib/execute/execSetResults.php',
    type: 'POST',
    data: submissionData,
    success: function(response) {
      console.log('Execution status saved successfully');
      window.bugSubmissionInProgress = false;
      
      // Re-enable test execution buttons
      if (typeof disableTestExecButtons === 'function') {
        disableTestExecButtons(false);
      }
      
      // If goNext is true, move to next test case
      if (goNext) {
        moveToNextTC(tcvID);
      }
    },
    error: function(xhr, status, error) {
      console.error('Error saving execution status:', error);
      window.bugSubmissionInProgress = false;
      
      // Re-enable test execution buttons
      if (typeof disableTestExecButtons === 'function') {
        disableTestExecButtons(false);
      }
      
      alert('Error saving execution status: ' + error);
    }
  });
}

// Function to show/hide integration dropdown (make it globally available)
window.toggleIntegrationDropdown = function(show) {
  console.log('toggleIntegrationDropdown called with show:', show);
  var container = jQuery('#integration_dropdown_container');
  console.log('Container found:', container.length > 0);
  
  if (show) {
    console.log('Showing dropdown container');
    container.show();
    populateIntegrationDropdown();
  } else {
    console.log('Hiding dropdown container');
    container.hide();
  }
  
  // Log current state after change
  setTimeout(function() {
    console.log('Dropdown visibility after toggle:', container.css('display'));
  }, 100);
};

// Function to populate integration dropdown (make it globally available)
window.populateIntegrationDropdown = function() {
  console.log('populateIntegrationDropdown called');
  
  // Get current project integrations - use hardcoded value for testing
  var tprojectId = 242099; // Hardcoded for testing to avoid template errors
  
  // Use correct base URL construction
  var pathname = window.location.pathname;
  var baseUrl = window.location.origin;
  if (pathname.indexOf('/lib/execute/') !== -1) {
    baseUrl = window.location.origin + pathname.substring(0, pathname.lastIndexOf('/lib/execute/'));
  }
  
  var apiUrl = baseUrl + '/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=' + tprojectId + '&_t=' + new Date().getTime();
  
  // Use XMLHttpRequest for better compatibility
  var xhr = new XMLHttpRequest();
  xhr.open('GET', apiUrl, true);
  xhr.onreadystatechange = function() {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);
          if (data.success && data.integrations) {
            var dropdown = jQuery('#integration_dropdown');
            dropdown.empty();
            dropdown.append('<option value="">-- Select Integration --</option>');
            
            data.integrations.forEach(function(integration) {
              dropdown.append('<option value="' + integration.id + '">' + integration.name + ' (' + integration.type + ')</option>');
            });
            
            // Auto-select if only one integration
            if (data.integrations.length === 1) {
              console.log('Auto-selecting single integration:', data.integrations[0].id);
              dropdown.val(data.integrations[0].id);
              handleIntegrationSelection(data.integrations[0].id);
              
              // Hide dropdown if only one integration (user doesn't need to choose)
              jQuery('#integration_dropdown_container').hide();
            } else {
              // Show dropdown for multiple integrations
              console.log('Multiple integrations found, showing dropdown for user selection');
            }
          }
        } catch (e) {
          console.error('Error parsing JSON response:', e);
        }
      } else {
        console.error('Error loading integrations for dropdown. Status:', xhr.status);
      }
    }
  };
  xhr.send();
};

// Function to handle integration dropdown selection (make it globally available)
window.handleIntegrationSelection = function(integrationId) {
  console.log('Integration selected from dropdown:', integrationId);
  
  // Store selected integration globally for use during execution
  window.selectedIntegrationId = integrationId;
  
  // Update bug summary with integration info
  if (integrationId) {
    // Add integration info to bug summary
    var integrationInfo = '\n\n--- Integration Selection ---\nSelected Integration ID: ' + integrationId + '\n';
    var bugSummaryField = jQuery('#bug_summary');
    if (bugSummaryField.length > 0) {
      bugSummaryField.val(integrationInfo + bugSummaryField.val());
    }
  }
};

/*
 * Function: moveToNextTC
 */
function moveToNextTC(tcvID) {
  // Check if bug submission is in progress
  if (window.bugSubmissionInProgress) {
    alert('A bug submission is in progress. Please wait until it completes before moving to the next test case.');
    return false;
  }
  
  // Find the next test case
  var currentElement = jQuery('#status_' + tcvID);
  var parentRow = currentElement.closest('tr');
  var nextRow = parentRow.next();
  
  if (nextRow.length > 0) {
    // Find the next test case ID
    var nextTcvID = nextRow.find('[id^="status_"]').attr('id').replace('status_', '');
    console.log('Moving to next test case:', nextTcvID);
    
    // Scroll to the next test case
    jQuery('html, body').animate({
      scrollTop: nextRow.offset().top - 100
    }, 500);
    
    // Focus on the next test case
    nextRow.find('input, button').first().focus();
  } else {
    console.log('No next test case found');
    alert('You have reached the last test case in this test suite.');
  }
}
