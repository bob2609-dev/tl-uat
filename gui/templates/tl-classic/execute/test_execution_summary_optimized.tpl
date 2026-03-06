<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optimized Test Execution Summary</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<style>
/* Modern CSS for Optimized Execution Summary */
.exec_summary_container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.filter_section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    color: white;
}

.filter_title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.filter_grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.filter_group {
    display: flex;
    flex-direction: column;
}

.filter_group label {
    font-weight: 500;
    margin-bottom: 5px;
    font-size: 14px;
    opacity: 0.9;
}

.filter_group select,
.filter_group input {
    padding: 10px 12px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 8px;
    background: rgba(255,255,255,0.95);
    color: #333;
    font-size: 14px;
    transition: all 0.3s ease;
}

.filter_group select:focus,
.filter_group input:focus {
    outline: none;
    border-color: rgba(255,255,255,0.6);
    background: white;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.2);
}

.action_buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn_run {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(40,167,69,0.3);
}

.btn_run:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40,167,69,0.4);
}

.btn_clear {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(108,117,125,0.3);
}

.btn_clear:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(108,117,125,0.4);
}

.btn_export {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(23,162,184,0.3);
}

.btn_export:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(23,162,184,0.4);
}

.summary_stats {
    background: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: none;
}

.summary_title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
}

.stats_grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.stat_item {
    text-align: center;
    padding: 15px;
    border-radius: 8px;
    background: #f8f9fa;
    transition: transform 0.3s ease;
}

.stat_item:hover {
    transform: translateY(-2px);
}

.stat_value {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat_label {
    font-size: 12px;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.5px;
}

.stat_total { background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); }
.stat_passed { background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%); }
.stat_failed { background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%); }
.stat_blocked { background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%); }
.stat_not_run { background: linear-gradient(135deg, #f3e5f5 0%, #e1bee7 100%); }

.results_section {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    display: none;
}

.results_title {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 10px;
}

.exec_table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.exec_table th {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    color: white;
    padding: 12px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.exec_table td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
    font-size: 14px;
}

.exec_table tr:hover {
    background-color: #f8f9fa;
}

.status_passed { color: #28a745; font-weight: 600; }
.status_failed { color: #dc3545; font-weight: 600; }
.status_blocked { color: #fd7e14; font-weight: 600; }
.status_not_run { color: #6c757d; font-weight: 600; }

.loading {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #007bff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 1000;
    max-width: 300px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.3s ease;
}

.notification.show {
    opacity: 1;
    transform: translateX(0);
}

.notification.success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
.notification.error { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
.notification.info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }

@media (max-width: 768px) {
    .filter_grid {
        grid-template-columns: 1fr;
    }
    
    .stats_grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .exec_table {
        font-size: 12px;
    }
    
    .exec_table th,
    .exec_table td {
        padding: 8px;
    }
}
</style>

<div class="exec_summary_container">
    <!-- Filter Section -->
    <div class="filter_section">
        <div class="filter_title">Test Execution Summary</div>
        
        <div class="filter_grid">
            <div class="filter_group">
                <label for="project_id">Project</label>
                <select id="project_id" name="project_id">
                    <option value="">Select Project</option>
                </select>
            </div>
            
            <div class="filter_group">
                <label for="testplan_id">Test Plan</label>
                <select id="testplan_id" name="testplan_id">
                    <option value="">Select Test Plan</option>
                </select>
            </div>
            
            <div class="filter_group">
                <label for="build_id">Build</label>
                <select id="build_id" name="build_id">
                    <option value="">Select Build</option>
                </select>
            </div>
            
            <div class="filter_group">
                <label for="tester_id">Tester</label>
                <select id="tester_id" name="tester_id">
                    <option value="">All Testers</option>
                </select>
            </div>
            
            <div class="filter_group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Any</option>
                    <option value="p">Passed</option>
                    <option value="f">Failed</option>
                    <option value="b">Blocked</option>
                    <option value="n">Not Run</option>
                </select>
            </div>
            
            <div class="filter_group">
                <label for="execution_path">Execution Path</label>
                <input type="text" id="execution_path" name="execution_path" value="" placeholder="Filter by path...">
            </div>
            
            <div class="filter_group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="">
            </div>
            
            <div class="filter_group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="">
            </div>
        </div>
        
        <div class="action_buttons">
            <button type="button" class="btn btn_run" onclick="runReport()">
                🎯 Run Report
            </button>
            <button type="button" class="btn btn_clear" onclick="clearFilters()">
                🔄 Clear Filters
            </button>
            <button type="button" class="btn btn_export" onclick="exportToCSV()">
                📊 Export CSV
            </button>
        </div>
    </div>
    
    <!-- Summary Statistics -->
    <div id="summarySection" class="summary_stats">
        <div class="summary_title">📊 Execution Summary</div>
        <div class="stats_grid">
            <div class="stat_item stat_total">
                <div class="stat_value" id="totalExecutions">0</div>
                <div class="stat_label">Total Executions</div>
            </div>
            <div class="stat_item stat_passed">
                <div class="stat_value" id="passedCount">0</div>
                <div class="stat_label">Passed</div>
            </div>
            <div class="stat_item stat_failed">
                <div class="stat_value" id="failedCount">0</div>
                <div class="stat_label">Failed</div>
            </div>
            <div class="stat_item stat_blocked">
                <div class="stat_value" id="blockedCount">0</div>
                <div class="stat_label">Blocked</div>
            </div>
            <div class="stat_item stat_not_run">
                <div class="stat_value" id="notRunCount">0</div>
                <div class="stat_label">Not Run</div>
            </div>
        </div>
    </div>
    
    <!-- Results Table -->
    <div id="resultsSection" class="results_section">
        <div class="results_title">📋 Execution Details</div>
        <div id="tableContainer">
            <div class="loading">
                <div class="spinner"></div>
                <div>Loading execution data...</div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification" class="notification"></div>

<script>
let currentData = [];
let isLoading = false;

// Data from PHP
const testProjects = {$testprojects_json|default:'[]'};
const testPlans = {$testplans_json|default:'[]'};
const builds = {$builds_json|default:'[]'};

// Initialize event handlers
$(document).ready(function() {
    populateInitialData();
    initializeEventHandlers();
    
    // Set initial values if they exist
    {if $gui->selectedProject}$('#project_id').val({$gui->selectedProject});{/if}
    {if $gui->selectedPlan}$('#testplan_id').val({$gui->selectedPlan});{/if}
    {if $gui->selectedBuild}$('#build_id').val({$gui->selectedBuild});{/if}
    {if $gui->selectedStatus}$('#status').val('{$gui->selectedStatus}');{/if}
    {if $gui->selectedExecutionPath}$('#execution_path').val('{$gui->selectedExecutionPath}');{/if}
    {if $gui->startDate}$('#start_date').val('{$gui->startDate}');{/if}
    {if $gui->endDate}$('#end_date').val('{$gui->endDate}');{/if}
    
    // Load testers if project is selected
    if ($('#project_id').val()) {
        updateTesters();
    }
});

function populateInitialData() {
    // Populate projects
    const $projectSelect = $('#project_id');
    testProjects.forEach(function(project) {
        const name = project.name || project;
        const id = project.id || project;
        $projectSelect.append(`<option value="${id}">${name}</option>`);
    });
    
    // Populate test plans
    const $planSelect = $('#testplan_id');
    testPlans.forEach(function(plan) {
        $planSelect.append(`<option value="${plan.id}">${plan.name}</option>`);
    });
    
    // Populate builds
    const $buildSelect = $('#build_id');
    builds.forEach(function(build) {
        $buildSelect.append(`<option value="${build.id}">${build.name}</option>`);
    });
}

function initializeEventHandlers() {
    $('#project_id, #testplan_id, #build_id').on('change', function() {
        updateTesters();
    });
}

function updateTesters() {
    const projectId = $('#project_id').val();
    const planId = $('#testplan_id').val();
    const buildId = $('#build_id').val();
    
    if (!projectId) {
        $('#tester_id').html('<option value="">All Testers</option>');
        return;
    }
    
    $.ajax({
        url: 'test_execution_summary_optimized.php',
        type: 'POST',
        data: {
            ajax: 1,
            action: 'get_testers',
            project_id: projectId,
            testplan_id: planId,
            build_id: buildId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const $testerSelect = $('#tester_id');
                $testerSelect.html('<option value="">All Testers</option>');
                
                response.testers.forEach(function(tester) {
                    $testerSelect.append(`<option value="${tester.id}">${tester.name}</option>`);
                });
            }
        },
        error: function() {
            showNotification('Error loading testers', 'error');
        }
    });
}

function runReport() {
    if (isLoading) return;
    
    const formData = {
        ajax: 1,
        action: 'run_report',
        project_id: $('#project_id').val(),
        testplan_id: $('#testplan_id').val(),
        build_id: $('#build_id').val(),
        tester_id: $('#tester_id').val(),
        status: $('#status').val(),
        execution_path: $('#execution_path').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val()
    };
    
    if (!formData.project_id) {
        showNotification('Please select a project', 'error');
        return;
    }
    
    setLoading(true);
    
    $.ajax({
        url: 'test_execution_summary_optimized.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                currentData = response.data;
                updateTable(response.data);
                updateSummary(response.summary);
                showNotification('Report loaded successfully', 'success');
            } else {
                showNotification(response.error || 'Error loading report', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            showNotification('Network error: ' + error, 'error');
        },
        complete: function() {
            setLoading(false);
        }
    });
}

function updateTable(data) {
    const $container = $('#tableContainer');
    
    if (!data || data.length === 0) {
        $container.html('<div class="loading">No execution data found</div>');
        return;
    }
    
    let html = `
        <table class="exec_table">
            <thead>
                <tr>
                    <th>Project</th>
                    <th>Test Plan</th>
                    <th>Build</th>
                    <th>Test Suite</th>
                    <th>Test Case</th>
                    <th>Tester</th>
                    <th>Status</th>
                    <th>Execution Date</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    data.forEach(function(row) {
        const statusClass = `status_${row.status}`;
        const statusText = getStatusText(row.status);
        const formattedDate = new Date(row.execution_timestamp).toLocaleString();
        
        html += `
            <tr>
                <td>${row.project_notes || 'N/A'}</td>
                <td>${row.testplan_notes || 'N/A'}</td>
                <td>${row.build_name || 'N/A'}</td>
                <td>${row.parent_suite_name || 'N/A'}</td>
                <td>${row.tc_name || 'N/A'}</td>
                <td>${row.tester_name || 'N/A'}</td>
                <td class="${statusClass}">${statusText}</td>
                <td>${formattedDate}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table>';
    $container.html(html);
}

function updateSummary(summary) {
    $('#totalExecutions').text(summary.totalExecutions || 0);
    $('#passedCount').text(summary.statusCounts.p || 0);
    $('#failedCount').text(summary.statusCounts.f || 0);
    $('#blockedCount').text(summary.statusCounts.b || 0);
    $('#notRunCount').text(summary.statusCounts.n || 0);
    
    $('#summarySection').show();
    $('#resultsSection').show();
}

function getStatusText(status) {
    const statusMap = {
        'p': 'Passed',
        'f': 'Failed', 
        'b': 'Blocked',
        'n': 'Not Run'
    };
    return statusMap[status] || status;
}

function clearFilters() {
    $('#project_id, #testplan_id, #build_id, #tester_id, #status').val('');
    $('#execution_path, #start_date, #end_date').val('');
    $('#summarySection, #resultsSection').hide();
    showNotification('Filters cleared', 'info');
}

function exportToCSV() {
    if (!currentData || currentData.length === 0) {
        showNotification('No data to export', 'error');
        return;
    }
    
    let csv = 'Project,Test Plan,Build,Test Suite,Test Case,Tester,Status,Execution Date\n';
    
    currentData.forEach(function(row) {
        const statusText = getStatusText(row.status);
        const formattedDate = new Date(row.execution_timestamp).toLocaleString();
        
        csv += `"${row.project_notes || 'N/A'}","${row.testplan_notes || 'N/A'}","${row.build_name || 'N/A'}","${row.parent_suite_name || 'N/A'}","${row.tc_name || 'N/A'}","${row.tester_name || 'N/A'}","${statusText}","${formattedDate}"\n`;
    });
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'execution_summary_' + new Date().toISOString().slice(0, 10) + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('CSV exported successfully', 'success');
}

function setLoading(loading) {
    isLoading = loading;
    const $runBtn = $('.btn_run');
    
    if (loading) {
        $runBtn.html('⏳ Loading...').prop('disabled', true);
        $('#tableContainer').html('<div class="loading"><div class="spinner"></div><div>Loading execution data...</div></div>');
    } else {
        $runBtn.html('🎯 Run Report').prop('disabled', false);
    }
}

function showNotification(message, type) {
    const $notification = $('#notification');
    $notification.removeClass('success error info').addClass(type);
    $notification.text(message);
    $notification.addClass('show');
    
    setTimeout(function() {
        $notification.removeClass('show');
    }, 3000);
}
</script>
</body>
</html>
