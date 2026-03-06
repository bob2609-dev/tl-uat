{* Integration Picker Modal for Bug Creation *}

<!-- Integration Picker Modal -->
<div id="integrationPickerModal" class="modal fade" tabindex="-1" role="dialog"
    aria-labelledby="integrationPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="integrationPickerModalLabel">
                    <i class="fa fa-bug"></i>
                    Select Bug Tracking Integration
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div id="integrationPickerContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div id="integrationList" class="list-group" style="display: none;">
                        <!-- Integrations will be loaded here -->
                    </div>

                    <div id="integrationError" class="alert alert-danger" style="display: none;">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span id="integrationErrorMessage">Error loading integrations</span>
                    </div>

                    <div id="integrationEmpty" class="text-center text-muted" style="display: none;">
                        <i class="fa fa-info-circle"></i>
                        <br>
                        <strong>No integrations found</strong>
                        <br>
                        <small>This project doesn't have any active bug tracking integrations configured.</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelIntegrationSelection" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa fa-times"></i>
                    Cancel
                </button>
                <button type="button" id="confirmIntegrationSelection" class="btn btn-primary" disabled>
                    <i class="fa fa-check"></i>
                    Select Integration
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Integration Picker JavaScript Functions -->
<script>
    {literal}
    // Global variable to track selected integration
    window.selectedIntegration = null;

    // Show integration picker
    function showIntegrationPicker(tproject_id, context) {
        console.log('showIntegrationPicker called with tproject_id:', tproject_id, 'context:', context);

        // Reset modal state
        resetIntegrationPickerModal();

        // Store context for callback
        window.integrationPickerContext = context;

        // Load integrations for this project
        loadIntegrationsForProject(tproject_id);
    }

    // Load integrations from API
    function loadIntegrationsForProject(tproject_id) {
        console.log('loadIntegrationsForProject called with tproject_id:', tproject_id);

        // Show loading state
        showIntegrationPickerLoading();

        // Use the correct base URL for API calls
        const baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
        const timestamp = new Date().getTime(); // Cache buster
        const apiUrl = `${baseUrl}/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=${tproject_id}&_t=${timestamp}`;

        console.log('API URL:', apiUrl);

        fetch(apiUrl)
            .then(response => {
                console.log('AJAX response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                return response.json();
            })
            .then(data => {
                console.log('AJAX response data:', data);

                if (data.success) {
                    showIntegrationPickerList(data.integrations);
                } else {
                    showIntegrationPickerError(data.message || 'Failed to load integrations');
                }
            })
            .catch(error => {
                console.error('Error loading integrations:', error);
                showIntegrationPickerError(error.message);
            });
    }

    // Show loading state
    function showIntegrationPickerLoading() {
        document.getElementById('integrationPickerContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    `;

        hideIntegrationPickerError();
        hideIntegrationPickerEmpty();
        document.getElementById('confirmIntegrationSelection').disabled = true;
    }

    // Show integration list
    function showIntegrationPickerList(integrations) {
        if (!integrations || integrations.length === 0) {
            showIntegrationPickerEmpty();
            return;
        }

        let html = '<div class="list-group">';

        integrations.forEach(integration => {
            html += `
            <button type="button" class="list-group-item list-group-item-action" 
onclick="selectIntegration(${integration.id}, '${integration.name}', '${integration.type}')"
data-integration-id="${integration.id}"
data-integration-name="${integration.name}"
data-integration-type="${integration.type}">
                <i class="fa fa-bug"></i>
<strong>${integration.name}</strong>
                <br>
<small class="text-muted">Type: ${integration.type}</small>
            </button>
        `;
        });

        html += '</div>';

        document.getElementById('integrationList').innerHTML = html;
        document.getElementById('integrationList').style.display = 'block';

        hideIntegrationPickerLoading();
        hideIntegrationPickerError();
        document.getElementById('confirmIntegrationSelection').disabled = false;
    }

    // Show error state
    function showIntegrationPickerError(message) {
        document.getElementById('integrationPickerContent').innerHTML = `
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i>
${message}
        </div>
    `;

        hideIntegrationPickerLoading();
        hideIntegrationPickerEmpty();
        document.getElementById('confirmIntegrationSelection').disabled = true;
    }

    // Show empty state
    function showIntegrationPickerEmpty() {
        document.getElementById('integrationPickerContent').innerHTML = `
        <div class="text-center text-muted">
            <i class="fa fa-info-circle"></i>
            <br>
            <strong>No integrations found</strong>
            <br>
            <small>This project doesn't have any active bug tracking integrations configured.</small>
            </div>
        `;

        hideIntegrationPickerLoading();
        hideIntegrationPickerError();
        document.getElementById('confirmIntegrationSelection').disabled = true;
    }

    // Select integration
    function selectIntegration(integrationId, integrationName, integrationType) {
        console.log('selectIntegration called with:', { integrationId, integrationName, integrationType });

        // Store selected integration
        window.selectedIntegration = {
            id: integrationId,
            name: integrationName,
            type: integrationType
        };

        // Update UI to show selection
        updateIntegrationSelectionUI();

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('integrationPickerModal'));
        modal.hide();
    }

    // Update UI to show selected integration
    function updateIntegrationSelectionUI() {
        if (window.selectedIntegration) {
            // Update button text
            const confirmBtn = document.getElementById('confirmIntegrationSelection');
            confirmBtn.innerHTML = `
                <i class="fa fa-check"></i>
    Selected: ${window.selectedIntegration.name}
            `;

            // Enable confirm button
            confirmBtn.disabled = false;

            // Log selection
            console.log('Integration selected:', window.selectedIntegration);

            // If this is for bug creation, update the bug summary
            if (window.integrationPickerContext === 'bug_creation') {
                // Add integration info to bug summary field
                addIntegrationToBugSummary(window.selectedIntegration);
            }
        } else {
            // Reset button
            const confirmBtn = document.getElementById('confirmIntegrationSelection');
            confirmBtn.innerHTML = `
                <i class="fa fa-check"></i>
                Select Integration
            `;
            confirmBtn.disabled = true;
        }
    }

    // Add integration info to bug summary field
    function addIntegrationToBugSummary(integration) {
        // Find the bug summary textarea
        const bugSummaryField = document.getElementById('bug_summary');
        if (!bugSummaryField) {
            console.error('Bug summary field not found');
            return;
        }

        // Add integration info to the beginning of the bug summary
        const integrationInfo = `\n\n--- Integration Selection ---\nSelected Integration: ${integration.name} (Type: ${integration.type}, ID: ${integration.id})\n`;

            // Insert at the beginning
            bugSummaryField.value = integrationInfo + (bugSummaryField.value || '');

            // Make the field required
            toogleRequiredOnShowHide('bug_summary', '');
        }

        // Reset modal state
        function resetIntegrationPickerModal() {
            document.getElementById('integrationList').style.display = 'none';
            document.getElementById('integrationError').style.display = 'none';
            document.getElementById('integrationEmpty').style.display = 'none';
            document.getElementById('confirmIntegrationSelection').disabled = true;
            window.selectedIntegration = null;
        }

        // Hide integration picker
        function hideIntegrationPicker() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('integrationPickerModal'));
            if (modal) {
                modal.hide();
            }
        }

        // Hide integration picker error
        function hideIntegrationPickerError() {
            document.getElementById('integrationError').style.display = 'none';
        }

        // Hide integration picker empty
        function hideIntegrationPickerEmpty() {
            document.getElementById('integrationEmpty').style.display = 'none';
        }

        // Hide integration picker loading
        function hideIntegrationPickerLoading() {
            const loadingElement = document.querySelector('#integrationPickerContent .spinner-border');
            if (loadingElement) {
                loadingElement.style.display = 'none';
            }
        }
    {/literal}
</script>