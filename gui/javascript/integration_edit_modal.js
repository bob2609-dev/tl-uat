/**
 * Modern Modal for Editing Custom Integrations
 * Replaces the old window.open() approach with a proper modal
 */

// Create modal styles
var modalStyles = `
<style>
.integration-edit-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    z-index: 10000;
    display: none;
    justify-content: center;
    align-items: center;
    font-family: Arial, sans-serif;
}

.integration-edit-content {
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.integration-edit-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.integration-edit-title {
    font-size: 20px;
    font-weight: bold;
    color: #333;
    margin: 0;
}

.integration-edit-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 30px;
    height: 30px;
}

.integration-edit-close:hover {
    color: #333;
}

.integration-edit-form {
    margin-bottom: 20px;
}

.integration-edit-form-group {
    margin-bottom: 15px;
}

.integration-edit-form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #555;
}

.integration-edit-form-group input,
.integration-edit-form-group select,
.integration-edit-form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.integration-edit-form-group textarea {
    height: 80px;
    resize: vertical;
}

.integration-edit-buttons {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.integration-edit-btn {
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
}

.integration-edit-btn-primary {
    background-color: #007bff;
    color: white;
}

.integration-edit-btn-primary:hover {
    background-color: #0056b3;
}

.integration-edit-btn-secondary {
    background-color: #6c757d;
    color: white;
}

.integration-edit-btn-secondary:hover {
    background-color: #545b62;
}

.integration-edit-loading {
    text-align: center;
    padding: 20px;
    color: #666;
}
</style>
`;

// Add modal to page
document.head.insertAdjacentHTML('beforeend', modalStyles);

/**
 * Open integration edit modal
 */
function openIntegrationEditModal(integrationId) {
    // Create modal elements
    const modal = document.createElement('div');
    modal.className = 'integration-edit-modal';
    modal.innerHTML = `
        <div class="integration-edit-content">
            <div class="integration-edit-header">
                <h3 class="integration-edit-title">Edit Custom Integration</h3>
                <button class="integration-edit-close" onclick="closeIntegrationEditModal()">&times;</button>
            </div>
            
            <div class="integration-edit-form">
                <form id="integrationEditForm" onsubmit="saveIntegrationEdit(event, ${integrationId})">
                    <div class="integration-edit-form-group">
                        <label for="integrationName">Integration Name:</label>
                        <input type="text" id="integrationName" name="name" required>
                    </div>
                    
                    <div class="integration-edit-form-group">
                        <label for="integrationType">Type:</label>
                        <select id="integrationType" name="type" required>
                            <option value="redmine">Redmine</option>
                            <option value="jira">Jira</option>
                            <option value="bugzilla">Bugzilla</option>
                        </select>
                    </div>
                    
                    <div class="integration-edit-form-group">
                        <label for="integrationUrl">API Endpoint:</label>
                        <input type="url" id="integrationUrl" name="api_endpoint" required placeholder="https://example.com/redmine">
                    </div>
                    
                    <div class="integration-edit-form-group">
                        <label for="integrationApiKey">API Key:</label>
                        <input type="password" id="integrationApiKey" name="api_key" required placeholder="Your Redmine API key">
                    </div>
                    
                    <div class="integration-edit-form-group">
                        <label for="integrationUsername">Username (optional):</label>
                        <input type="text" id="integrationUsername" name="username" placeholder="redmine_username">
                    </div>
                    
                    <div class="integration-edit-form-group">
                        <label for="integrationPassword">Password (optional):</label>
                        <input type="password" id="integrationPassword" name="password" placeholder="redmine_password">
                    </div>
                    
                    <div class="integration-edit-form-group">
                        <label for="integrationProjectKey">Project Key (optional):</label>
                        <input type="text" id="integrationProjectKey" name="project_key" placeholder="project_identifier">
                    </div>
                    
                    <div class="integration-edit-buttons">
                        <button type="button" class="integration-edit-btn integration-edit-btn-secondary" onclick="closeIntegrationEditModal()">Cancel</button>
                        <button type="submit" class="integration-edit-btn integration-edit-btn-primary">Save Integration</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(modal);
    
    // Show modal
    modal.style.display = 'flex';
    
    // Load existing integration data
    loadIntegrationData(integrationId);
}

/**
 * Close integration edit modal
 */
function closeIntegrationEditModal() {
    const modal = document.querySelector('.integration-edit-modal');
    if (modal) {
        modal.style.display = 'none';
        document.body.removeChild(modal);
    }
}

/**
 * Load existing integration data into form
 */
function loadIntegrationData(integrationId) {
    // Show loading state
    const form = document.getElementById('integrationEditForm');
    const content = form.querySelector('.integration-edit-content');
    
    if (content) {
        content.innerHTML += '<div class="integration-edit-loading">Loading integration data...</div>';
    }
    
    // Fetch integration data
    fetch(`lib/execute/ajax_get_integration.php?id=${integrationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.integration) {
                // Populate form with existing data
                document.getElementById('integrationName').value = data.integration.name || '';
                document.getElementById('integrationType').value = data.integration.type || 'redmine';
                document.getElementById('integrationUrl').value = data.integration.api_endpoint || '';
                document.getElementById('integrationApiKey').value = data.integration.api_key || '';
                document.getElementById('integrationUsername').value = data.integration.username || '';
                document.getElementById('integrationPassword').value = data.integration.password || '';
                document.getElementById('integrationProjectKey').value = data.integration.project_key || '';
                
                // Remove loading state
                if (content) {
                    content.innerHTML = content.innerHTML.replace('<div class="integration-edit-loading">Loading integration data...</div>', '');
                }
            } else {
                // Handle error
                if (content) {
                    content.innerHTML = '<div style="color: red; padding: 20px;">Error loading integration data</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading integration data:', error);
            if (content) {
                content.innerHTML = '<div style="color: red; padding: 20px;">Error loading integration data</div>';
            }
        });
}

/**
 * Save integration edit
 */
function saveIntegrationEdit(event, integrationId) {
    event.preventDefault();
    
    const form = document.getElementById('integrationEditForm');
    const formData = new FormData(form);
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Saving...';
    submitBtn.disabled = true;
    
    fetch(`lib/execute/ajax_save_integration.php?id=${integrationId}`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Integration saved successfully!');
                closeIntegrationEditModal();
                // Refresh the page to show updated data
                window.location.reload();
            } else {
                alert('Error saving integration: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error saving integration:', error);
            alert('Error saving integration: ' + error.message);
        })
        .finally(() => {
            // Restore button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
}

/**
 * Close modal when clicking outside
 */
document.addEventListener('click', function(event) {
    const modal = document.querySelector('.integration-edit-modal');
    if (modal && event.target === modal) {
        closeIntegrationEditModal();
    }
});

/**
 * Add edit buttons to integration management page
 */
function addIntegrationEditButtons() {
    // Find integration rows and add edit buttons
    const integrationRows = document.querySelectorAll('tr');
    
    integrationRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 2) {
            const lastCell = cells[cells.length - 1];
            const integrationId = row.getAttribute('data-integration-id');
            
            if (integrationId && lastCell) {
                // Create edit button
                const editBtn = document.createElement('button');
                editBtn.textContent = '✏️ Edit';
                editBtn.className = 'integration-edit-btn';
                editBtn.style.cssText = 'margin-left: 10px; padding: 5px 10px; font-size: 12px;';
                editBtn.onclick = () => openIntegrationEditModal(integrationId);
                
                // Add button to cell
                lastCell.appendChild(editBtn);
            }
        }
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit for the page to fully load
    setTimeout(addIntegrationEditButtons, 1000);
});
