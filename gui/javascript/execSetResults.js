/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Functions JS for exeSetResult page
 *
 * @package     TestLink
 * @author      syji
 * @copyright   2010,2019 TestLink community
 * @filesource  execResult.js
 * @link        http://www.testlink.org
 * @used-by     execSetResult.tpl
 *
 */

// Log version for debugging purposes
console.log(
  "execSetResults.js: LOADING VERSION SUBMISSION_LOCK_FIX - 20260303-4",
);

// Add debugging to track when modal is shown
window.pendingExecutionAction = null;

window.setPendingExecutionAction = function (action) {
  window.pendingExecutionAction = action || null;
};

window.clearPendingExecutionAction = function () {
  window.pendingExecutionAction = null;
};

window.syncIssueSummaryVisibility = function () {
  var createIssueCheckbox = jQuery("#createIssue");
  if (createIssueCheckbox.length === 0) {
    return;
  }

  var shouldShow = createIssueCheckbox.is(":checked");
  var issueSummary = jQuery("#issue_summary");
  var bugSummary = jQuery("#bug_summary");

  if (issueSummary.length > 0) {
    if (shouldShow) {
      issueSummary.css("display", "table");
    } else {
      issueSummary.hide();
    }
  }

  if (bugSummary.length > 0) {
    if (shouldShow) {
      bugSummary.show();
    } else {
      bugSummary.hide();
    }
  }
};

window.syncSelectedIntegrationField = function () {
  var selectedField = jQuery("#selected_integration_id");
  if (selectedField.length > 0) {
    selectedField.val(window.selectedIntegrationId || "");
  }
};

window.resumePendingExecutionAction = function () {
  var action = window.pendingExecutionAction;
  window.pendingExecutionAction = null;

  if (!action) {
    return;
  }

  if (action.type === "saveExecStatus") {
    saveExecStatus(action.tcvID, action.status, action.msg, action.goNext);
    return;
  }

  if (action.type === "saveExecutionStatus") {
    saveExecutionStatus(action.tcvID, action.status, action.msg, action.goNext);
    return;
  }

  if (action.type === "buttonClick") {
    var btn = action.buttonId ? document.getElementById(action.buttonId) : null;
    if (btn) {
      btn.click();
    }
  }
};

window.debugDropdownState = function () {
  // Debug function - available for manual troubleshooting if needed
};

// Call debug on page load
jQuery(document).ready(function () {
  // Wait a bit longer for DOM to be fully loaded
  setTimeout(debugDropdownState, 2000);
  window.syncIssueSummaryVisibility();

  // Add click debugging to checkbox
  jQuery("#createIssue").on("click", function () {
    window.syncIssueSummaryVisibility();
  });

  jQuery(document).on("change", "#createIssue", function () {
    window.syncIssueSummaryVisibility();
  });

  // Add click debugging to execution buttons
  jQuery('[id^="save_results"], [id^="save_and_next"], [id^="move2next"]').on(
    "click",
    function (e) {
      // Check if we should show integration dropdown
      var createIssueChecked = jQuery("#createIssue").is(":checked");
      var hasSelectedIntegration =
        window.selectedIntegrationId && window.selectedIntegrationId !== "";

      if (createIssueChecked && !hasSelectedIntegration) {
        window.setPendingExecutionAction({
          type: "buttonClick",
          buttonId: this.id || null,
        });
        // Use timeout to show modal after any validation completes
        setTimeout(function () {
          window.showIntegrationModal();
        }, 100);
        // Prevent form submission until integration is selected
        e.preventDefault();
        e.stopPropagation();
        return false;
      } else if (createIssueChecked && hasSelectedIntegration) {
        // Show bug submission overlay
        if (typeof window.disableTestExecButtons === "function") {
          window.disableTestExecButtons(true);
        }
        // Allow form to proceed normally
        return true;
      } else {
        // Allow form to proceed normally
        return true;
      }
    },
  );
});

// Test function to manually trigger modal
window.testModal = function () {
  if (typeof showIntegrationModal === "function") {
    showIntegrationModal();
  }
};

// Simple test function
window.simpleTest = function () {
  // Test function - available for manual troubleshooting
};

// Debug function availability
window.debugFunctions = function () {
  // Debug function - available for manual troubleshooting if needed
};

// Test API for different projects
window.testProjectIntegrations = function (projectId) {
  var testProjectId = projectId || window.getCurrentTProjectId();
  if (!testProjectId) {
    console.error("No project ID available for integration test");
    return;
  }

  jQuery.ajax({
    url:
      window.location.origin +
      "/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=" +
      testProjectId,
    type: "GET",
    dataType: "json",
    success: function (data) {
      // Response handled silently in production
    },
    error: function (xhr, status, error) {
      console.error(
        "Error fetching integrations for project",
        testProjectId,
        ":",
        error,
      );
    },
  });
};

// Test API for different projects
window.testProjectIntegrations = function (projectId) {
  var testProjectId = projectId || window.getCurrentTProjectId();
  if (!testProjectId) {
    return;
  }

  jQuery.ajax({
    url:
      window.location.origin +
      "/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=" +
      testProjectId,
    type: "GET",
    dataType: "json",
    success: function (data) {
      // Response handled silently in production
    },
    error: function (xhr, status, error) {
      console.error(
        "Error fetching integrations for project",
        testProjectId,
        ":",
        error,
      );
    },
  });
};

window.myTest = function () {
  alert("MY TEST IS EXECUTED");
};

// Force function availability check
if (typeof testProjectIntegrations !== "function") {
  window.testProjectIntegrations = function (projectId) {
    var testProjectId = projectId || window.getCurrentTProjectId();
    if (!testProjectId) {
      return;
    }

    jQuery.ajax({
      url:
        window.location.origin +
        "/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=" +
        testProjectId,
      type: "GET",
      dataType: "json",
      success: function (data) {
        // Response handled silently in production
      },
      error: function (xhr, status, error) {
        console.error(
          "Error fetching integrations for project",
          testProjectId,
          ":",
          error,
        );
      },
    });
  };
}

// Integration Modal Functions
window.getTProjectIdFromFrameContext = function () {
  var frameUrls = [];

  try {
    frameUrls.push(window.location.href);
  } catch (e) {}
  try {
    if (window.parent && window.parent !== window) {
      frameUrls.push(window.parent.location.href);
    }
  } catch (e) {}
  try {
    if (window.top) {
      frameUrls.push(window.top.location.href);
    }
  } catch (e) {}

  for (var i = 0; i < frameUrls.length; i++) {
    try {
      var parsed = new URL(frameUrls[i], window.location.origin);
      if (parsed.searchParams.has("tproject_id")) {
        return String(parsed.searchParams.get("tproject_id"));
      }

      // In execution dashboard, project context is carried as ?id=<tproject_id>.
      if (
        parsed.pathname.indexOf("execDashboard.php") !== -1 &&
        parsed.searchParams.has("id")
      ) {
        return String(parsed.searchParams.get("id"));
      }
    } catch (e) {
      // ignore parse errors from inaccessible frame URLs
    }
  }

  return null;
};

window.getCurrentTProjectId = function () {
  var tprojectId = null;

  if (!tprojectId) {
    try {
      var tprojectField = jQuery('input[name="tproject_id"]');
      if (tprojectField.length > 0) {
        tprojectId = tprojectField.val();
      } else {
        var urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has("tproject_id")) {
          tprojectId = urlParams.get("tproject_id");
        }
      }
    } catch (e) {
      // Silent error handling
    }
  }

  if (!tprojectId) {
    tprojectId = window.getTProjectIdFromFrameContext();
  }

  if (!tprojectId && window.currentTProjectId) {
    // Server-rendered value from execSetResults.php ($gui->tproject_id),
    // which follows the same session-backed logic used in execHistory.php.
    tprojectId = window.currentTProjectId;
  }

  if (!tprojectId) {
    try {
      var form = jQuery("#execute_testcase_form");
      if (form.length > 0) {
        var formData = form.serializeArray();
        jQuery.each(formData, function (i, field) {
          if (field.name === "tproject_id") {
            tprojectId = field.value;
            return false;
          }
        });
      }
    } catch (e) {
      // Silent error handling
    }
  }

  return tprojectId ? String(tprojectId) : null;
};

window.showIntegrationModal = function () {
  var modal = jQuery("#integrationModal");
  if (modal.length === 0) {
    return;
  }

  // Populate with integrations
  populateIntegrationModalDropdown();

  // Show modal
  modal.show();
};

window.hideIntegrationModal = function () {
  jQuery("#integrationModal").hide();
};

window.confirmIntegrationSelection = function () {
  var selectedIntegration = jQuery("#integrationModalDropdown").val();
  if (selectedIntegration) {
    window.selectedIntegrationId = selectedIntegration;
    window.syncSelectedIntegrationField();
    hideIntegrationModal();
    window.resumePendingExecutionAction();
  } else {
    alert("Please select an integration.");
  }
};

window.cancelIntegrationSelection = function () {
  window.selectedIntegrationId = "";
  window.syncSelectedIntegrationField();
  window.clearPendingExecutionAction();
  hideIntegrationModal();
};

window.populateIntegrationModalDropdown = function () {
  var dropdown = jQuery("#integrationModalDropdown");
  if (dropdown.length === 0) {
    return;
  }

  // Clear existing options
  dropdown.empty();
  dropdown.append('<option value="">-- Select Integration --</option>');
  var tprojectId = window.getCurrentTProjectId();

  if (!tprojectId) {
    console.error(
      "Could not determine tproject_id - cannot fetch integrations",
    );
    dropdown.append('<option value="">No integrations available</option>');
    return;
  }

  // Fetch integrations from API
  jQuery.ajax({
    url:
      window.location.origin +
      "/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=" +
      tprojectId +
      "&_t=" +
      new Date().getTime(),
    type: "GET",
    dataType: "json",
    success: function (data) {
      var integrations = [];
      if (Array.isArray(data.integrations)) {
        integrations = data.integrations;
      } else if (Array.isArray(data.data)) {
        integrations = data.data;
      }

      if (data.success && integrations.length > 0) {
        jQuery.each(integrations, function (index, integration) {
          dropdown.append(
            '<option value="' +
              integration.id +
              '">' +
              integration.name +
              "</option>",
          );
        });

        // Auto-select if only one integration
        if (integrations.length === 1) {
          dropdown.val(integrations[0].id);
        }
      } else {
        dropdown.append(
          '<option value="">No integrations available for project ' +
            tprojectId +
            "</option>",
        );
      }
    },
    error: function (xhr, status, error) {
      console.error("Error fetching integrations:", error);
      dropdown.append('<option value="">Error loading integrations</option>');
    },
  });
};

// Integration Dropdown Functions (Legacy - now redirects to modal)
window.toggleIntegrationDropdown = function (show) {
  if (show) {
    // Redirect to modal instead of old dropdown
    if (typeof showIntegrationModal === "function") {
      showIntegrationModal();
    }
  } else {
    // Hide modal instead of old dropdown
    if (typeof hideIntegrationModal === "function") {
      hideIntegrationModal();
    }
  }
};

window.populateIntegrationDropdown = function () {
  var tprojectId = window.getCurrentTProjectId();

  if (!tprojectId) {
    console.error(
      "Could not determine tproject_id - cannot fetch integrations",
    );
    return;
  }

  // Use correct base URL construction
  var pathname = window.location.pathname;
  var baseUrl = window.location.origin;
  if (pathname.indexOf("/lib/execute/") !== -1) {
    baseUrl =
      window.location.origin +
      pathname.substring(0, pathname.lastIndexOf("/lib/execute/"));
  }

  var apiUrl =
    baseUrl +
    "/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=" +
    tprojectId +
    "&_t=" +
    new Date().getTime();

  // Use XMLHttpRequest for better compatibility
  var xhr = new XMLHttpRequest();
  xhr.open("GET", apiUrl, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);
          var integrations = [];
          if (Array.isArray(data.integrations)) {
            integrations = data.integrations;
          } else if (Array.isArray(data.data)) {
            integrations = data.data;
          }
          if (data.success && integrations.length > 0) {
            var dropdown = jQuery("#integration_dropdown");
            dropdown.empty();
            dropdown.append(
              '<option value="">-- Select Integration --</option>',
            );

            integrations.forEach(function (integration) {
              dropdown.append(
                '<option value="' +
                  integration.id +
                  '">' +
                  integration.name +
                  " (" +
                  integration.type +
                  ")</option>",
              );
            });

            // Auto-select if only one integration
            if (integrations.length === 1) {
              dropdown.val(integrations[0].id);
              handleIntegrationSelection(integrations[0].id);

              // Hide dropdown if only one integration (user doesn't need to choose)
              jQuery("#integration_dropdown_container").hide();
            }
          } else {
            var emptyDropdown = jQuery("#integration_dropdown");
            emptyDropdown.empty();
            emptyDropdown.append(
              '<option value="">No integrations available for project ' +
                tprojectId +
                "</option>",
            );
          }
        } catch (e) {
          console.error("Error parsing JSON response:", e);
        }
      } else {
        console.error(
          "Error loading integrations for dropdown. Status:",
          xhr.status,
        );
      }
    }
  };
  xhr.send();
};

window.handleIntegrationSelection = function (integrationId) {
  // Store selected integration globally for use during execution
  window.selectedIntegrationId = integrationId;
  window.syncSelectedIntegrationField();

  // Update bug summary with integration info
  if (integrationId) {
    // Add integration info to bug summary
    var integrationInfo =
      "\n\n--- Integration Selection ---\nSelected Integration ID: " +
      integrationId +
      "\n";
    var bugSummaryField = jQuery("#bug_summary");
    if (bugSummaryField.length > 0) {
      bugSummaryField.val(integrationInfo + bugSummaryField.val());
    }
  }
};

// Integration Picker Functions
window.showIntegrationPicker = function (tproject_id, context) {
  // Reset modal state
  resetIntegrationPickerModal();

  // Store context for callback
  window.integrationPickerContext = context;

  // Load integrations for this project
  loadIntegrationsForProject(tproject_id);
};

// Load integrations from API
window.loadIntegrationsForProject = function (tproject_id) {
  // Show loading state
  showIntegrationPickerLoading();

  // Use the correct base URL for API calls
  const baseUrl =
    window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, "");
  const timestamp = new Date().getTime(); // Cache buster
  const apiUrl =
    baseUrl +
    "/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=" +
    tproject_id +
    "&_t=" +
    timestamp;

  // Use XMLHttpRequest for better compatibility
  var xhr = new XMLHttpRequest();
  xhr.open("GET", apiUrl, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);

          if (data.success) {
            showIntegrationPickerList(data.integrations);
          } else {
            showIntegrationPickerError(
              data.message || "Failed to load integrations",
            );
          }
        } catch (e) {
          console.error("Error parsing JSON response:", e);
          showIntegrationPickerError("Error parsing server response");
        }
      } else {
        console.error("HTTP error:", xhr.status);
        showIntegrationPickerError("HTTP error! status: " + xhr.status);
      }
    }
  };
  xhr.onerror = function () {
    console.error("Network error occurred");
    showIntegrationPickerError("Network error occurred");
  };
  xhr.send();
};

// Show loading state
window.showIntegrationPickerLoading = function () {
  document.getElementById("integrationPickerContent").innerHTML =
    '<div class="text-center">' +
    '<div class="spinner-border" role="status">' +
    '<div class="spinner-border spinner-border-sm" role="status">' +
    '<span class="visually-hidden">Loading...</span>' +
    "</div>" +
    "</div>" +
    "</div>";

  hideIntegrationPickerError();
  hideIntegrationPickerEmpty();
  document.getElementById("confirmIntegrationSelection").disabled = true;
};

// Show integration list
window.showIntegrationPickerList = function (integrations) {
  if (!integrations || integrations.length === 0) {
    showIntegrationPickerEmpty();
    return;
  }

  let html = '<div class="list-group">';

  integrations.forEach((integration) => {
    html +=
      '<button type="button" class="list-group-item list-group-item-action" ' +
      'onclick="selectIntegration(' +
      integration.id +
      ", '" +
      integration.name +
      "', '" +
      integration.type +
      "')\"" +
      'data-integration-id="' +
      integration.id +
      '"' +
      'data-integration-name="' +
      integration.name +
      '"' +
      'data-integration-type="' +
      integration.type +
      '">' +
      "<div>" +
      "<strong>" +
      integration.name +
      "</strong>" +
      "<br>" +
      '<small class="text-muted">Type: ' +
      integration.type +
      "</small>" +
      "</div>" +
      "</button>";
  });

  html += "</div>";

  document.getElementById("integrationList").innerHTML = html;
  document.getElementById("integrationList").style.display = "block";

  hideIntegrationPickerLoading();
  hideIntegrationPickerError();
  document.getElementById("confirmIntegrationSelection").disabled = false;
};

// Show error state
window.showIntegrationPickerError = function (message) {
  document.getElementById("integrationPickerContent").innerHTML =
    '<div class="alert alert-danger">' +
    '<i class="fa fa-exclamation-triangle"></i> ' +
    message +
    "</div>";

  hideIntegrationPickerLoading();
  hideIntegrationPickerEmpty();
  document.getElementById("confirmIntegrationSelection").disabled = true;
};

// Show empty state
window.showIntegrationPickerEmpty = function () {
  document.getElementById("integrationPickerContent").innerHTML =
    '<div class="text-center text-muted">' +
    '<i class="fa fa-info-circle"></i>' +
    "<br>" +
    "<strong>No integrations found</strong>" +
    "<br>" +
    "<small>This project doesn't have any active bug tracking integrations configured.</small>" +
    "</div>";

  hideIntegrationPickerLoading();
  hideIntegrationPickerError();
  document.getElementById("confirmIntegrationSelection").disabled = true;
};

// Select integration
window.selectIntegration = function (
  integrationId,
  integrationName,
  integrationType,
) {
  // Store selected integration
  window.selectedIntegration = {
    id: integrationId,
    name: integrationName,
    type: integrationType,
  };

  // Update UI to show selection
  updateIntegrationSelectionUI();

  // Close modal
  const modal = bootstrap.Modal.getInstance(
    document.getElementById("integrationPickerModal"),
  );
  modal.hide();
};

// Update UI to show selected integration
window.updateIntegrationSelectionUI = function () {
  if (window.selectedIntegration) {
    // Update button text
    const confirmBtn = document.getElementById("confirmIntegrationSelection");
    confirmBtn.innerHTML =
      '<i class="fa fa-check"></i> ' +
      "Selected: " +
      window.selectedIntegration.name;

    // Enable confirm button
    confirmBtn.disabled = false;

    // If this is for bug creation, update the bug summary field
    if (window.integrationPickerContext === "bug_creation") {
      // Add integration info to bug summary field
      addIntegrationToBugSummary(window.selectedIntegration);
    }
  } else {
    // Reset button
    const confirmBtn = document.getElementById("confirmIntegrationSelection");
    confirmBtn.innerHTML =
      '<i class="fa fa-check"></i> ' + "Select Integration";
    confirmBtn.disabled = true;
  }
};

// Add integration info to bug summary field
window.addIntegrationToBugSummary = function (integration) {
  // Find the bug summary textarea
  const bugSummaryField = document.getElementById("bug_summary");
  if (!bugSummaryField) {
    console.error("Bug summary field not found");
    return;
  }

  // Add integration info to the beginning of the bug summary
  const integrationInfo =
    "\n\n--- Integration Selection ---\nSelected Integration: " +
    integration.name +
    " (Type: " +
    integration.type +
    ", ID: " +
    integration.id +
    ")\n";

  // Insert at the beginning
  bugSummaryField.value = integrationInfo + (bugSummaryField.value || "");

  // Make the field required
  toogleRequiredOnShowHide("bug_summary", "");
};

// Reset modal state
window.resetIntegrationPickerModal = function () {
  document.getElementById("integrationList").style.display = "none";
  document.getElementById("integrationError").style.display = "none";
  document.getElementById("integrationEmpty").style.display = "none";
  document.getElementById("confirmIntegrationSelection").disabled = true;
  window.selectedIntegration = null;
};

// Hide integration picker
window.hideIntegrationPicker = function () {
  const modal = bootstrap.Modal.getInstance(
    document.getElementById("integrationPickerModal"),
  );
  if (modal) {
    modal.hide();
  }
};

// Hide integration picker error
window.hideIntegrationPickerError = function () {
  document.getElementById("integrationError").style.display = "none";
};

// Hide integration picker empty
window.hideIntegrationPickerEmpty = function () {
  document.getElementById("integrationEmpty").style.display = "none";
};

// Hide integration picker loading
window.hideIntegrationPickerLoading = function () {
  const loadingElement = document.querySelector(
    "#integrationPickerContent .spinner-border",
  );
  if (loadingElement) {
    loadingElement.style.display = "none";
  }
};

console.log(
  "execSetResults.js loaded - integration picker functions available",
);

/**
 *
 */
function doSubmitForHTML5() {
  jQuery("#hidden-submit-button").click();
}

// Priority popup functions
window.showPriorityPopup = function (tcvID, status) {
  // Only show popup for Passed, Failed, or Blocked statuses
  if (status !== "p" && status !== "f" && status !== "b") {
    saveExecutionStatus(tcvID, status, undefined, undefined);
    return;
  }

  // First check if priority already exists in database
  jQuery.ajax({
    url: "lib/execute/ajax_check_priority.php",
    type: "GET",
    cache: false, // Disable caching
    data: {
      tcvID: tcvID,
      field_id: 15, // Case_Priority field ID
      _: new Date().getTime(), // Cache-busting parameter
    },
    success: function (response) {
      if (response.success) {
        if (response.has_priority) {
          // Priority exists, proceed directly with execution status save
          saveExecutionStatus(tcvID, status, undefined, undefined);
        } else {
          // No priority exists, show the popup
          showPriorityPopupInternal(tcvID, status);
        }
      } else {
        // If check fails, show popup as fallback
        showPriorityPopupInternal(tcvID, status);
      }
    },
    error: function (xhr, status, error) {
      // If AJAX fails, show popup as fallback
      showPriorityPopupInternal(tcvID, status);
    },
  });
};

// Internal function to show the actual popup
window.showPriorityPopupInternal = function (tcvID, status) {
  // Get current priority value
  var currentPriority = "";
  var priorityField = jQuery("#case_priority_" + tcvID);
  if (priorityField.length > 0) {
    currentPriority = priorityField.val();
  }

  var modalHtml =
    '<div class="modal fade" id="priorityModal" tabindex="-1" role="dialog" style="z-index: 10000;">' +
    '<div class="modal-dialog" role="document">' +
    '<div class="modal-content">' +
    '<div class="modal-header">' +
    '<h5 class="modal-title">Update Test Case Priority</h5>' +
    '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
    "</div>" +
    '<div class="modal-body">' +
    "<p>You are setting the execution status to <strong>" +
    getStatusText(status) +
    "</strong>. " +
    "Please select the test case priority (mandatory):</p>" +
    '<div class="form-group">' +
    '<div class="radio-group">' +
    '<label class="radio-inline" style="color: #28a745; font-weight: bold;">' +
    '<input type="radio" name="priorityRadio" value="LOW"' +
    (currentPriority === "LOW" ? " checked" : "") +
    "> LOW" +
    "</label>" +
    '<label class="radio-inline" style="color: #17a2b8; font-weight: bold;">' +
    '<input type="radio" name="priorityRadio" value="NORMAL"' +
    (currentPriority === "NORMAL" ? " checked" : "") +
    "> NORMAL" +
    "</label>" +
    '<label class="radio-inline" style="color: #ffc107; font-weight: bold;">' +
    '<input type="radio" name="priorityRadio" value="HIGH"' +
    (currentPriority === "HIGH" ? " checked" : "") +
    "> HIGH" +
    "</label>" +
    '<label class="radio-inline" style="color: #dc3545; font-weight: bold;">' +
    '<input type="radio" name="priorityRadio" value="CRITICAL"' +
    (currentPriority === "CRITICAL" ? " checked" : "") +
    "> CRITICAL" +
    "</label>" +
    "</div>" +
    "</div>" +
    "</div>" +
    '<div class="modal-footer">' +
    '<button type="button" class="btn btn-primary" onclick="savePriorityAndStatus(' +
    tcvID +
    ", '" +
    status +
    "')\">Save Priority & Continue</button>" +
    "</div>" +
    "</div>" +
    "</div>" +
    "</div>";

  // Remove existing modal if present
  jQuery("#priorityModal").remove();

  // Add modal to body and show it
  jQuery("body").append(modalHtml);
  jQuery("#priorityModal").modal("show");
};

window.getStatusText = function (status) {
  switch (status) {
    case "p":
      return "Passed";
    case "f":
      return "Failed";
    case "b":
      return "Blocked";
    default:
      return status;
  }
};

window.savePriorityAndStatus = function (tcvID, status) {
  var selectedPriority = jQuery('input[name="priorityRadio"]:checked').val();

  if (!selectedPriority) {
    alert("Please select a priority before continuing.");
    return;
  }

  // Save priority via AJAX - using main endpoint
  jQuery.ajax({
    url: "lib/execute/ajax_update_priority.php",
    type: "POST",
    data: {
      tcvID: tcvID,
      priority: selectedPriority,
      field_id: 15, // Case_Priority field ID
    },
    success: function (response) {
      if (response.success) {
        // Update the priority field on the page
        var priorityField = jQuery("#case_priority_" + tcvID);
        if (priorityField.length > 0) {
          priorityField.val(selectedPriority);
        }

        // Close modal and save execution status
        jQuery("#priorityModal").modal("hide");
        saveExecStatusDirect(tcvID, status);
      } else {
        alert("Error updating priority: " + response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", error);
      alert("AJAX Error: " + error + " (Status: " + xhr.status + ")");
    },
  });
};

window.saveExecStatusDirect = function (tcvID, status) {
  // Call the saveExecutionStatus function
  saveExecutionStatus(tcvID, status, undefined, undefined);
};

/**
 * Submit Executions status.
 * Used
 *
 */
function saveExecStatus(tcvID, status, msg, goNext) {
  // Check if bug submission is in progress
  if (window.bugSubmissionInProgress) {
    alert(
      "A bug submission is in progress. Please wait until it completes before changing test execution status.",
    );
    return false;
  }

  // Check if the createIssue checkbox is checked
  if (
    jQuery("#createIssue").length > 0 &&
    jQuery("#createIssue").is(":checked")
  ) {
    // If the checkbox is checked, check for integration selection for any status
    // Check if integration is selected
    var selectedIntegration = window.selectedIntegrationId;

    if (!selectedIntegration) {
      // No integration selected - show integration modal instead of overlay
      window.setPendingExecutionAction({
        type: "saveExecStatus",
        tcvID: tcvID,
        status: status,
        msg: msg,
        goNext: goNext,
      });
      if (typeof showIntegrationModal === "function") {
        showIntegrationModal();
        // Don't show alert here - let the modal handle it
        return false;
      } else {
        alert("Please select an integration before creating a bug.");
      }
      return false;
    }

    // Integration is selected - proceed with bug creation
  }

  // Check if we should show priority popup
  if (typeof showPriorityPopup === "function") {
    showPriorityPopup(tcvID, status);
    return false;
  }

  // Original logic continues if priority popup is not available
  saveExecutionStatus(tcvID, status, msg, goNext);
}

function saveExecutionStatus(tcvID, status, msg, goNext) {
  /* Init */
  jQuery("#save_and_next").val(0);
  jQuery("#save_results").val(0);
  jQuery("#save_partial_steps_exec").val(0);
  jQuery("#save_button_clicked").val(tcvID);
  jQuery("#statusSingle_" + tcvID).val(status);

  // Check if bug submission is in progress
  if (window.bugSubmissionInProgress) {
    alert(
      "A bug submission is in progress. Please wait until it completes before moving to the next test case.",
    );
    return false;
  }

  // Check if createIssue checkbox is checked
  var shouldCreateIssue =
    jQuery("#createIssue").length > 0 && jQuery("#createIssue").is(":checked");
  if (shouldCreateIssue) {
    // Check if integration is selected when createIssue is checked
    var selectedIntegration = window.selectedIntegrationId;

    if (!selectedIntegration || selectedIntegration === "") {
      // No integration selected - show integration modal instead of alert
      window.setPendingExecutionAction({
        type: "saveExecutionStatus",
        tcvID: tcvID,
        status: status,
        msg: msg,
        goNext: goNext,
      });
      if (typeof showIntegrationModal === "function") {
        showIntegrationModal();
        // Don't show alert here - let the modal handle it
        return false;
      } else {
        alert(
          "Please select an integration before moving to the next test case.",
        );
      }
      return false;
    }

    // Integration ID is carried by the hidden field #selected_integration_id
    // via syncSelectedIntegrationField() below - do NOT inject into bug_summary.
  }

  // Lock UI only at final submit stage to avoid blocking modal/priority flow.
  if (shouldCreateIssue) {
    window.syncSelectedIntegrationField();
    window.bugSubmissionInProgress = true;
    if (typeof disableTestExecButtons === "function") {
      disableTestExecButtons(true);
    }
  }

  if (goNext == undefined || goNext == 0) {
    jQuery("#save_results").val(1);
  } else {
    if (goNext == 1) {
      jQuery("#save_and_next").val(1);
    }
  }

  doSubmitForHTML5();
}

/**
 * Handle bug submission completion - reset overlay and flags
 */
window.handleBugSubmissionComplete = function() {
  console.log("Bug submission completed - resetting UI");
  window.bugSubmissionInProgress = false;
  if (typeof disableTestExecButtons === "function") {
    disableTestExecButtons(false);
  }
};

/**
 * Handle bug submission error - reset overlay and flags
 */
window.handleBugSubmissionError = function() {
  console.log("Bug submission failed - resetting UI");
  window.bugSubmissionInProgress = false;
  if (typeof disableTestExecButtons === "function") {
    disableTestExecButtons(false);
  }
  alert("Bug submission failed. Please try again or check the logs for details.");
};

/**
 * Check before save partial execution if notes or Status are not empty
 *
 * @returns true / false
 */
function checkStepsHaveContent(msg) {
  var notes = jQuery(".step_note_textarea");

  // https://www.tutorialrepublic.com/faq/
  //         how-to-check-if-an-element-exists-in-jquery.php
  if (notes.length == 0) {
    // there are no steps
    return true;
  }

  for (var idx = 0; idx < notes.length; idx++) {
    if (notes[idx].value) {
      return true;
    }
  }

  var status = jQuery(".step_status");
  for (var idx = 0; idx < status.length; idx++) {
    if (status[idx].value && status[idx].value !== "n") {
      return true;
    }
  }

  if (msg !== undefined) {
    alert(msg);
  }
  return false;
}

/**
 * Check if attachement is present
 *
 * @returns
 */
function checkStepsHaveAttachments() {
  var uploads = jQuery(".uploadedFile");
  for (var idx = 0; idx < uploads.length; idx++) {
    if (uploads[idx].value) {
      return true;
    }
  }
  return false;
}

/**
 * uses globals alert_box_title,warning_msg
 *
 *
 */
function checkCustomFields(theForm) {
  var cfields_inputs = "";
  var cfValidityChecks;
  var f = theForm;

  var cfield_container = jQuery("#save_button_clicked").val();
  var access_key = "cfields_exec_time_tcversionid_" + cfield_container;

  if (document.getElementById(access_key) != null) {
    cfields_inputs = document
      .getElementById(access_key)
      .getElementsByTagName("input");
    cfValidityChecks = validateCustomFields(cfields_inputs);
    if (!cfValidityChecks.status_ok) {
      var warning_msg = cfMessages[cfValidityChecks.msg_id];
      alert_message(
        alert_box_title,
        warning_msg.replace(/%s/, cfValidityChecks.cfield_label),
      );
      return false;
    }
  }
  return true;
}

/**
 * checkSubmitForStatusCombo
 * $statusCode has been checked, then false is returned to block form submit().
 *
 * Dev. Note - remember this:
 *  KO: onclick="foo();checkSubmitForStatus('n')"
 *  OK: onclick="foo();return checkSubmitForStatus('n')"
 *                            ^^^^^^
 */
function checkSubmitForStatusCombo(oid, statusCode2block) {
  if (jQuery("#" + oid).val() == statusCode2block) {
    alert_message(alert_box_title, warning_nothing_will_be_saved);
    return false;
  }
  return true;
}

/**
 *
 */
saveStepsPartialExecClicked = false;
$("#saveStepsPartialExec").click(function () {
  saveStepsPartialExecClicked = true;
});
