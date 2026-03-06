/**
 * Integration Picker JavaScript
 * Handles the integration selection modal for multi-integration bug creation
 */

// Global variables
let integrationPickerCallback = null;
let currentTprojectId = null;
let availableIntegrations = [];

/**
 * Show integration picker modal
 * @param {number} tproject_id - TestLink project ID
 * @param {function} callback - Function to call when integration is selected
 * @param {string} context - Context for logging (e.g., 'bug_creation', 'checkbox', 'execution')
 */
function showIntegrationPicker(tproject_id, callback, context = 'unknown') {
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] showIntegrationPicker called with tproject_id: $tproject_id, context: $context\n",
        FILE_APPEND
    );
    
    integrationPickerCallback = callback;
    currentTprojectId = tproject_id;
    
    // Reset modal state
    resetIntegrationPicker();
    
    // Show loading state
    showIntegrationPickerLoading();
    
    // Show the modal
    const modal = document.getElementById('integrationPickerModal');
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    } else {
        // Fallback for older Bootstrap versions
        modal.style.display = 'block';
        modal.classList.add('show');
        document.body.classList.add('modal-open');
        
        // Create backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'integration-picker-backdrop';
        document.body.appendChild(backdrop);
    }
    
    // Load integrations
    loadIntegrationsForProject(tproject_id);
}

/**
 * Load integrations for a specific project
 * @param {number} tproject_id - TestLink project ID
 */
function loadIntegrationsForProject(tproject_id) {
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] loadIntegrationsForProject called with tproject_id: $tproject_id\n",
        FILE_APPEND
    );
    
    fetch(`lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=${tproject_id}`)
        .then(response => {
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[DEBUG] AJAX response status: " . response.status . "\n",
                FILE_APPEND
            );
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[DEBUG] AJAX response data: " . json_encode(data) . "\n",
                FILE_APPEND
            );
            
            hideIntegrationPickerLoading();
            
            if (data.status === 'ok' && data.integrations) {
                availableIntegrations = data.integrations;
                displayIntegrations(data.integrations);
            } else {
                showIntegrationPickerError(data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[ERROR] AJAX error: " . error.message . "\n",
                FILE_APPEND
            );
            
            hideIntegrationPickerLoading();
            showIntegrationPickerError(error.message);
        });
}

/**
 * Display integrations in the modal
 * @param {Array} integrations - Array of integration objects
 */
function displayIntegrations(integrations) {
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] displayIntegrations called with " . count(integrations) . " integrations\n",
        FILE_APPEND
    );
    
    const container = document.getElementById('integrationPickerItems');
    container.innerHTML = '';
    
    if (integrations.length === 0) {
        showIntegrationPickerEmpty();
        return;
    }
    
    integrations.forEach(integration => {
        const item = createIntegrationItem(integration);
        container.appendChild(item);
    });
    
    showIntegrationPickerList();
}

/**
 * Create integration item element
 * @param {Object} integration - Integration object
 * @returns {HTMLElement} - Integration item element
 */
function createIntegrationItem(integration) {
    const div = document.createElement('div');
    div.className = 'integration-picker-item list-group-item list-group-item-action';
    div.dataset.integrationId = integration.id;
    
    // Type badge color based on integration type
    let badgeClass = 'bg-secondary';
    if (integration.type === 'REDMINE') {
        badgeClass = 'bg-danger';
    } else if (integration.type === 'JIRA') {
        badgeClass = 'bg-primary';
    } else if (integration.type === 'BUGZILLA') {
        badgeClass = 'bg-warning';
    }
    
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold">${escapeHtml(integration.name)}</h6>
                <div class="mb-2">
                    <span class="badge ${badgeClass} integration-type-badge">${integration.type}</span>
                </div>
                <div class="integration-url">
                    <small class="text-muted">
                        <i class="fas fa-link"></i> ${escapeHtml(integration.url)}
                    </small>
                </div>
            </div>
            <div class="ms-3">
                <button type="button" class="btn btn-primary btn-sm">
                    <i class="fas fa-check"></i> Select
                </button>
            </div>
        </div>
    `;
    
    // Add click handler
    div.addEventListener('click', () => selectIntegration(integration.id));
    
    return div;
}

/**
 * Handle integration selection
 * @param {number} integration_id - Selected integration ID
 */
function selectIntegration(integration_id) {
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] Integration selected: $integration_id\n",
        FILE_APPEND
    );
    
    const integration = availableIntegrations.find(i => i.id == integration_id);
    if (!integration) {
        file_put_contents(
            'logs/multi_integration_debug.log',
            date('[Y-m-d H:i:s] ') . "[ERROR] Integration not found: $integration_id\n",
            FILE_APPEND
        );
        return;
    }
    
    // Close modal
    closeIntegrationPicker();
    
    // Call callback with selected integration
    if (integrationPickerCallback && typeof integrationPickerCallback === 'function') {
        integrationPickerCallback(integration_id);
    } else {
        file_put_contents(
            'logs/multi_integration_debug.log',
            date('[Y-m-d H:i:s] ') . "[ERROR] No callback function defined\n",
            FILE_APPEND
        );
    }
}

/**
 * Close the integration picker modal
 */
function closeIntegrationPicker() {
    const modal = document.getElementById('integrationPickerModal');
    
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    } else {
        // Fallback for older Bootstrap versions
        modal.style.display = 'none';
        modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Remove backdrop
        const backdrop = document.getElementById('integration-picker-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
    }
    
    // Reset state
    integrationPickerCallback = null;
    currentTprojectId = null;
    availableIntegrations = [];
}

/**
 * Reset modal to initial state
 */
function resetIntegrationPicker() {
    document.getElementById('integrationPickerLoading').style.display = 'none';
    document.getElementById('integrationPickerError').style.display = 'none';
    document.getElementById('integrationPickerEmpty').style.display = 'none';
    document.getElementById('integrationPickerList').style.display = 'none';
    document.getElementById('integrationPickerItems').innerHTML = '';
}

/**
 * Show loading state
 */
function showIntegrationPickerLoading() {
    document.getElementById('integrationPickerLoading').style.display = 'block';
    document.getElementById('integrationPickerError').style.display = 'none';
    document.getElementById('integrationPickerEmpty').style.display = 'none';
    document.getElementById('integrationPickerList').style.display = 'none';
}

/**
 * Hide loading state
 */
function hideIntegrationPickerLoading() {
    document.getElementById('integrationPickerLoading').style.display = 'none';
}

/**
 * Show error state
 * @param {string} message - Error message
 */
function showIntegrationPickerError(message) {
    document.getElementById('integrationPickerErrorMessage').textContent = message;
    document.getElementById('integrationPickerError').style.display = 'block';
    document.getElementById('integrationPickerEmpty').style.display = 'none';
    document.getElementById('integrationPickerList').style.display = 'none';
}

/**
 * Show empty state
 */
function showIntegrationPickerEmpty() {
    document.getElementById('integrationPickerEmpty').style.display = 'block';
    document.getElementById('integrationPickerError').style.display = 'none';
    document.getElementById('integrationPickerList').style.display = 'none';
}

/**
 * Show integration list
 */
function showIntegrationPickerList() {
    document.getElementById('integrationPickerList').style.display = 'block';
    document.getElementById('integrationPickerError').style.display = 'none';
    document.getElementById('integrationPickerEmpty').style.display = 'none';
}

/**
 * Retry loading integrations
 */
function retryLoadIntegrations() {
    if (currentTprojectId) {
        showIntegrationPickerLoading();
        loadIntegrationsForProject(currentTprojectId);
    }
}

/**
 * Go to integration settings
 */
function goToIntegrationSettings() {
    window.location.href = 'lib/execute/custom_bugtrack_integration.html';
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} - Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Initialize integration picker when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] Integration picker JavaScript loaded\n",
        FILE_APPEND
    );
});
