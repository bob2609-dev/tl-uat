{*
 * Optimized Execution Module Template
 * High-performance single-page execution interface
 *}

{include file="inc_head.tpl"}
{lang_get var="labels"
          s="title_test_execution,th_test_suite,th_test_case,th_status,
             th_build,th_platform,btn_save,btn_cancel,loading,error"}

<style>
/* Optimized Execution Module Styles */
.oem-container {
    display: flex;
    height: calc(100vh - 120px);
    margin: 10px;
    gap: 10px;
}

/* Left Pane - Tree Navigation */
.oem-tree-pane {
    width: 300px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow-y: auto;
    background: #f8f9fa;
}

.oem-tree-node {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.oem-tree-node:hover {
    background: #e9ecef;
}

.oem-tree-node.selected {
    background: #007bff;
    color: white;
}

.oem-tree-node.testsuite {
    font-weight: bold;
}

.oem-tree-node.testcase {
    font-weight: normal;
}

.oem-tree-expand {
    margin-right: 8px;
    font-family: monospace;
}

.oem-tree-status {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-left: 8px;
}

.oem-status-passed { background-color: #28a745; }
.oem-status-failed { background-color: #dc3545; }
.oem-status-blocked { background-color: #fd7e14; }
.oem-status-not_run { background-color: #6c757d; }

/* Center Pane - Test Case Details */
.oem-content-pane {
    flex: 1;
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.oem-header {
    padding: 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
}

.oem-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.oem-stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.oem-stat-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    color: white;
}

.oem-controls {
    display: flex;
    gap: 10px;
    align-items: center;
}

.oem-testcase-content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}

.oem-testcase-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #eee;
}

.oem-testcase-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.oem-testcase-meta {
    color: #6c757d;
    font-size: 14px;
}

.oem-section {
    margin-bottom: 25px;
}

.oem-section-title {
    font-weight: bold;
    margin-bottom: 10px;
    color: #495057;
}

.oem-step {
    margin-bottom: 15px;
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: #f8f9fa;
}

.oem-step-number {
    font-weight: bold;
    color: #007bff;
    margin-bottom: 8px;
}

.oem-step-actions, .oem-step-expected {
    margin-bottom: 8px;
}

/* Right Pane - Execution Controls */
.oem-execution-pane {
    width: 350px;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: flex;
    flex-direction: column;
}

.oem-execution-header {
    padding: 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    font-weight: bold;
}

.oem-execution-content {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
}

.oem-quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 20px;
}

.oem-quick-btn {
    padding: 15px;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
}

.oem-quick-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.oem-btn-pass {
    background: #28a745;
    color: white;
}

.oem-btn-fail {
    background: #dc3545;
    color: white;
}

.oem-btn-block {
    background: #fd7e14;
    color: white;
}

.oem-btn-reset {
    background: #6c757d;
    color: white;
}

.oem-notes-section {
    margin-top: 20px;
}

.oem-notes-textarea {
    width: 100%;
    height: 100px;
    margin-bottom: 10px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.oem-save-btn {
    width: 100%;
    padding: 10px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-weight: bold;
    cursor: pointer;
}

/* Loading indicator */
.oem-loading {
    text-align: center;
    padding: 20px;
    color: #6c757d;
}

.oem-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 1200px) {
    .oem-execution-pane {
        width: 300px;
    }
}

@media (max-width: 992px) {
    .oem-container {
        flex-direction: column;
        height: auto;
    }
    
    .oem-tree-pane, .oem-execution-pane {
        width: 100%;
        height: 300px;
    }
}
</style>

<div class="oem-container">
    <!-- Left Pane: Tree Navigation -->
    <div class="oem-tree-pane" id="treePane">
        <div class="oem-loading">
            <div class="oem-spinner"></div>
            Loading test suites...
        </div>
    </div>

    <!-- Center Pane: Test Case Content -->
    <div class="oem-content-pane">
        <div class="oem-header">
            <!-- Statistics Bar -->
            <div class="oem-stats">
                <div class="oem-stat-item">
                    <span>Passed:</span>
                    <span class="oem-stat-badge oem-status-passed" id="statPassed">0</span>
                </div>
                <div class="oem-stat-item">
                    <span>Failed:</span>
                    <span class="oem-stat-badge oem-status-failed" id="statFailed">0</span>
                </div>
                <div class="oem-stat-item">
                    <span>Blocked:</span>
                    <span class="oem-stat-badge oem-status-blocked" id="statBlocked">0</span>
                </div>
                <div class="oem-stat-item">
                    <span>Not Run:</span>
                    <span class="oem-stat-badge oem-status-not_run" id="statNotRun">0</span>
                </div>
            </div>

            <!-- Controls -->
            <div class="oem-controls">
                <select id="buildSelect" class="form-control" style="width: 200px;">
                    {html_options options=$gui.builds selected=$gui.current_build}
                </select>
                <select id="platformSelect" class="form-control" style="width: 200px;">
                    {html_options options=$gui.platforms selected=$gui.current_platform}
                </select>
                <button class="btn btn-secondary btn-sm" onclick="toggleLegacyMode()">
                    Legacy Mode
                </button>
            </div>
        </div>

        <div class="oem-testcase-content" id="testcaseContent">
            <div class="text-center text-muted" style="margin-top: 100px;">
                <h4>Select a test case from the tree to begin execution</h4>
                <p>Use the navigation tree on the left to browse test suites and test cases</p>
            </div>
        </div>
    </div>

    <!-- Right Pane: Execution Controls -->
    <div class="oem-execution-pane">
        <div class="oem-execution-header">
            Execution Controls
        </div>
        <div class="oem-execution-content" id="executionContent">
            <div class="text-center text-muted" style="margin-top: 50px;">
                <p>Select a test case to see execution options</p>
            </div>
        </div>
    </div>
</div>

<script>
// Optimized Execution Module JavaScript
var OEM = {
    currentTestCase: null,
    currentBuild: {$gui.current_build},
    currentPlatform: {$gui.current_platform},
    tplanId: {$gui.tplan_id},
    
    init: function() {
        this.loadTreeNodes(0);
        this.updateStats();
        this.bindEvents();
    },
    
    bindEvents: function() {
        $('#buildSelect').on('change', function() {
            OEM.currentBuild = $(this).val();
            OEM.refreshAll();
        });
        
        $('#platformSelect').on('change', function() {
            OEM.currentPlatform = $(this).val();
            OEM.refreshAll();
        });
    },
    
    loadTreeNodes: function(parentId) {
        var self = this;
        var treePane = $('#treePane');
        
        if (parentId === 0) {
            treePane.html('<div class="oem-loading"><div class="oem-spinner"></div>Loading test suites...</div>');
        }
        
        $.ajax({
            url: 'ajax_optimized_execution.php',
            method: 'POST',
            data: {
                action: 'get_tree_nodes',
                parent_id: parentId,
                tplan_id: this.tplanId,
                build_id: this.currentBuild,
                platform_id: this.currentPlatform
            },
            success: function(response) {
                if (response.success) {
                    self.renderTreeNodes(response.nodes, parentId);
                } else {
                    treePane.html('<div class="alert alert-danger">Error loading tree: ' + response.error + '</div>');
                }
            },
            error: function() {
                treePane.html('<div class="alert alert-danger">Network error loading tree</div>');
            }
        });
    },
    
    renderTreeNodes: function(nodes, parentId) {
        var self = this;
        var treePane = $('#treePane');
        var html = '';
        
        if (parentId === 0) {
            html = '';
        }
        
        nodes.forEach(function(node) {
            var statusClass = 'oem-status-' + self.getStatusClass(node.status);
            var expandIcon = node.has_children ? (node.expanded ? '▼' : '▶') : '•';
            
            html += '<div class="oem-tree-node ' + node.type + '" data-id="' + node.id + '" data-type="' + node.type + '">';
            html += '<div class="oem-node-content">';
            html += '<span class="oem-tree-expand">' + expandIcon + '</span>';
            html += '<span class="oem-node-name">' + node.name + '</span>';
            
            if (node.type === 'testcase') {
                html += '<span class="oem-tree-status ' + statusClass + '"></span>';
            } else if (node.testcase_count > 0) {
                html += '<span class="badge badge-secondary">' + node.testcase_count + '</span>';
            }
            
            html += '</div></div>';
        });
        
        if (parentId === 0) {
            treePane.html(html);
        } else {
            // Replace or append to existing parent node
            var parentNode = $('.oem-tree-node[data-id="' + parentId + '"]');
            if (parentNode.length > 0) {
                if (parentNode.data('expanded')) {
                    parentNode.after(html);
                } else {
                    parentNode.data('expanded', true);
                    parentNode.after(html);
                }
            }
        }
        
        // Bind click events
        $('.oem-tree-node').off('click').on('click', function() {
            self.onTreeNodeClick($(this));
        });
    },
    
    onTreeNodeClick: function(nodeElement) {
        var nodeId = nodeElement.data('id');
        var nodeType = nodeElement.data('type');
        
        // Remove previous selection
        $('.oem-tree-node').removeClass('selected');
        nodeElement.addClass('selected');
        
        if (nodeType === 'testsuite') {
            // Toggle expansion
            if (nodeElement.data('expanded')) {
                nodeElement.data('expanded', false);
                // Remove child nodes
                var nextElement = nodeElement.nextUntil('.oem-tree-node[data-type="testsuite"], .oem-tree-node[data-type="testcase"]').first();
                if (nextElement.length === 0) {
                    // Remove all following nodes until next sibling at same level
                    var level = this.getNodeLevel(nodeElement);
                    var next = nodeElement.next();
                    while (next.length > 0 && this.getNodeLevel(next) > level) {
                        var remove = next;
                        next = next.next();
                        remove.remove();
                    }
                }
            } else {
                nodeElement.data('expanded', true);
                this.loadTreeNodes(nodeId);
            }
        } else if (nodeType === 'testcase') {
            // Load test case details
            this.loadTestCase(nodeId);
        }
    },
    
    getNodeLevel: function(nodeElement) {
        var level = 0;
        nodeElement.prevAll('.oem-tree-node.testsuite').each(function() {
            level++;
        });
        return level;
    },
    
    loadTestCase: function(tcversionId) {
        var self = this;
        
        $('#testcaseContent').html('<div class="oem-loading"><div class="oem-spinner"></div>Loading test case...</div>');
        $('#executionContent').html('<div class="oem-loading"><div class="oem-spinner"></div>Loading execution controls...</div>');
        
        $.ajax({
            url: 'ajax_optimized_execution.php',
            method: 'POST',
            data: {
                action: 'get_testcase',
                tcversion_id: tcversionId,
                tplan_id: this.tplanId,
                build_id: this.currentBuild,
                platform_id: this.currentPlatform
            },
            success: function(response) {
                if (response.success) {
                    self.currentTestCase = response;
                    self.renderTestCase(response);
                    self.renderExecutionControls(response);
                } else {
                    $('#testcaseContent').html('<div class="alert alert-danger">Error loading test case: ' + response.error + '</div>');
                }
            },
            error: function() {
                $('#testcaseContent').html('<div class="alert alert-danger">Network error loading test case</div>');
            }
        });
    },
    
    renderTestCase: function(data) {
        var testcase = data.testcase;
        var steps = data.steps;
        var execution = data.execution;
        
        var html = '<div class="oem-testcase-header">';
        html += '<div class="oem-testcase-title">' + testcase.name + '</div>';
        html += '<div class="oem-testcase-meta">';
        html += 'Version: ' + testcase.version + ' | ';
        html += 'Author: ' + (testcase.author_login || 'Unknown') + ' | ';
        html += 'Created: ' + new Date(testcase.creation_ts).toLocaleDateString();
        html += '</div>';
        html += '</div>';
        
        if (testcase.summary) {
            html += '<div class="oem-section">';
            html += '<div class="oem-section-title">Summary</div>';
            html += '<div>' + testcase.summary + '</div>';
            html += '</div>';
        }
        
        if (testcase.preconditions) {
            html += '<div class="oem-section">';
            html += '<div class="oem-section-title">Preconditions</div>';
            html += '<div>' + testcase.preconditions + '</div>';
            html += '</div>';
        }
        
        if (steps && steps.length > 0) {
            html += '<div class="oem-section">';
            html += '<div class="oem-section-title">Test Steps</div>';
            
            steps.forEach(function(step) {
                html += '<div class="oem-step">';
                html += '<div class="oem-step-number">Step ' + step.step_number + '</div>';
                html += '<div class="oem-step-actions"><strong>Actions:</strong> ' + step.actions + '</div>';
                html += '<div class="oem-step-expected"><strong>Expected Results:</strong> ' + step.expected_results + '</div>';
                html += '</div>';
            });
            
            html += '</div>';
        }
        
        if (execution) {
            html += '<div class="oem-section">';
            html += '<div class="oem-section-title">Last Execution</div>';
            html += '<div><strong>Status:</strong> ' + this.getStatusText(execution.status) + '</div>';
            html += '<div><strong>Executed by:</strong> ' + (execution.tester_login || 'Unknown') + '</div>';
            html += '<div><strong>Date:</strong> ' + new Date(execution.execution_ts).toLocaleString() + '</div>';
            if (execution.notes) {
                html += '<div><strong>Notes:</strong> ' + execution.notes + '</div>';
            }
            html += '</div>';
        }
        
        $('#testcaseContent').html(html);
    },
    
    renderExecutionControls: function(data) {
        var testcase = data.testcase;
        var execution = data.execution;
        var currentStatus = execution ? execution.status : 'n';
        
        var html = '';
        
        // Quick action buttons
        html += '<div class="oem-quick-actions">';
        html += '<button class="oem-quick-btn oem-btn-pass" onclick="OEM.executeTest(\'p\')">';
        html += '✓ PASS</button>';
        html += '<button class="oem-quick-btn oem-btn-fail" onclick="OEM.executeTest(\'f\')">';
        html += '✗ FAIL</button>';
        html += '<button class="oem-quick-btn oem-btn-block" onclick="OEM.executeTest(\'b\')">';
        html += '⚠ BLOCK</button>';
        html += '<button class="oem-quick-btn oem-btn-reset" onclick="OEM.executeTest(\'n\')">';
        html += '↺ RESET</button>';
        html += '</div>';
        
        // Current status display
        html += '<div class="oem-section">';
        html += '<div class="oem-section-title">Current Status</div>';
        html += '<div style="font-size: 18px; font-weight: bold; color: ' + this.getStatusColor(currentStatus) + ';">';
        html += this.getStatusText(currentStatus);
        html += '</div>';
        html += '</div>';
        
        // Notes section
        html += '<div class="oem-notes-section">';
        html += '<div class="oem-section-title">Execution Notes</div>';
        html += '<textarea class="oem-notes-textarea" id="executionNotes" placeholder="Enter execution notes here...">';
        if (execution && execution.notes) {
            html += execution.notes;
        }
        html += '</textarea>';
        html += '<button class="oem-save-btn" onclick="OEM.saveExecution()">Save Execution</button>';
        html += '</div>';
        
        $('#executionContent').html(html);
    },
    
    executeTest: function(status) {
        this.currentStatus = status;
        this.saveExecution();
    },
    
    saveExecution: function() {
        if (!this.currentTestCase) {
            return;
        }
        
        var self = this;
        var notes = $('#executionNotes').val();
        var status = this.currentStatus || (this.currentTestCase.execution ? this.currentTestCase.execution.status : 'n');
        
        $.ajax({
            url: 'ajax_optimized_execution.php',
            method: 'POST',
            data: {
                action: 'update_execution',
                tcversion_id: this.currentTestCase.testcase.id,
                tplan_id: this.tplanId,
                build_id: this.currentBuild,
                platform_id: this.currentPlatform,
                status: status,
                notes: notes
            },
            success: function(response) {
                if (response.success) {
                    self.showSuccessMessage('Execution saved successfully');
                    self.updateStats();
                    // Refresh current test case to show updated status
                    self.loadTestCase(self.currentTestCase.testcase.id);
                } else {
                    self.showErrorMessage('Error saving execution: ' + response.error);
                }
            },
            error: function() {
                self.showErrorMessage('Network error saving execution');
            }
        });
    },
    
    updateStats: function() {
        var self = this;
        
        $.ajax({
            url: 'ajax_optimized_execution.php',
            method: 'POST',
            data: {
                action: 'get_stats',
                tplan_id: this.tplanId,
                build_id: this.currentBuild,
                platform_id: this.currentPlatform
            },
            success: function(response) {
                if (response.success) {
                    $('#statPassed').text(response.stats.passed);
                    $('#statFailed').text(response.stats.failed);
                    $('#statBlocked').text(response.stats.blocked);
                    $('#statNotRun').text(response.stats.not_run);
                }
            }
        });
    },
    
    refreshAll: function() {
        this.loadTreeNodes(0);
        this.updateStats();
        if (this.currentTestCase) {
            this.loadTestCase(this.currentTestCase.testcase.id);
        }
    },
    
    getStatusClass: function(status) {
        switch (status) {
            case 'p': return 'passed';
            case 'f': return 'failed';
            case 'b': return 'blocked';
            default: return 'not_run';
        }
    },
    
    getStatusText: function(status) {
        switch (status) {
            case 'p': return 'Passed';
            case 'f': return 'Failed';
            case 'b': return 'Blocked';
            case 'n': return 'Not Run';
            default: return 'Unknown';
        }
    },
    
    getStatusColor: function(status) {
        switch (status) {
            case 'p': return '#28a745';
            case 'f': return '#dc3545';
            case 'b': return '#fd7e14';
            case 'n': return '#6c757d';
            default: return '#6c757d';
        }
    },
    
    showSuccessMessage: function(message) {
        // Simple success notification
        var alert = $('<div class="alert alert-success" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">' + message + '</div>');
        $('body').append(alert);
        setTimeout(function() { alert.fadeOut(); }, 3000);
    },
    
    showErrorMessage: function(message) {
        // Simple error notification
        var alert = $('<div class="alert alert-danger" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">' + message + '</div>');
        $('body').append(alert);
        setTimeout(function() { alert.fadeOut(); }, 5000);
    }
};

// Initialize on page load
$(document).ready(function() {
    OEM.init();
});

function toggleLegacyMode() {
    window.location.href = '{$gui->menuUrl}?feature=executeTest';
}
</script>

{include file="inc_footer.tpl"}
