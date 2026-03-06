// TestLink Open Source Project - http://testlink.sourceforge.net/
// Shift-click functionality for test case selection

/**
 * Enables shift-click selection for checkboxes in test case assignment
 * This allows users to select a range of checkboxes by clicking the first checkbox,
 * then holding shift and clicking the last checkbox to select all in between.
 */

// Track the last checkbox that was clicked
var lastChecked = null;

// add alert message and console log message to show that script is loaded on window load
document.addEventListener('DOMContentLoaded', function() {
  console.log('Shift-click selection script loaded🤣🤣🤣');
  // alert('Shift-click selection script loaded');
});


// Function to initialize shift-click selection
function initShiftClickSelection() {
  // Get all checkboxes for test case assignment - handle both naming patterns
  var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="achecked_tc"], input[type="checkbox"][id^="achecked_tc"], input[type="checkbox"][name^="tcaseSet"], input[type="checkbox"][id^="tcaseSet_"]');
  
  // Add click event listener to each checkbox
  for (var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].addEventListener('click', function(e) {
      if (!lastChecked) {
        lastChecked = this;
        return;
      }
      
      // If shift key is pressed and this is not the same checkbox as last time
      if (e.shiftKey && this !== lastChecked) {
        // Get all checkboxes again to ensure we have the current state
        var allCheckboxes = document.querySelectorAll('input[type="checkbox"][name^="achecked_tc"], input[type="checkbox"][id^="achecked_tc"], input[type="checkbox"][name^="tcaseSet"], input[type="checkbox"][id^="tcaseSet_"]');
        var start = Array.prototype.indexOf.call(allCheckboxes, this);
        var end = Array.prototype.indexOf.call(allCheckboxes, lastChecked);
        
        // Swap if needed to ensure start is less than end
        if (start > end) {
          var temp = start;
          start = end;
          end = temp;
        }
        
        // Check all checkboxes in the range
        for (var i = start; i <= end; i++) {
          // Only check if the checkbox is not disabled
          if (!allCheckboxes[i].disabled) {
            allCheckboxes[i].checked = true;
            
            // If we're in the test case execution assignment page, also trigger the onchange event
            // to ensure the tester dropdown is properly marked for update
            if (allCheckboxes[i].onchange) {
              allCheckboxes[i].onchange();
            }
          }
        }
      }
      
      // Update the last checked checkbox
      lastChecked = this;
    });
  }
}

// Initialize when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
  initShiftClickSelection();
});

// Also handle cases where the page might be dynamically updated
function refreshShiftClickHandlers() {
  // Reset the last checked reference
  lastChecked = null;
  // Re-initialize the handlers
  initShiftClickSelection();
}
