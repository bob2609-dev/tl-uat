{include file="inc_head.tpl" openHead="yes"}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script type="text/javascript">
{literal}
// Optimized Suite Execution Summary with modern UX
(function() {
    'use strict';
    
    // Global variables
    let isLoading = false;
    let currentData = [];
    let sortColumn = -1;
    let sortDirection = 'asc';
    
    // Initialize on document ready
    $(document).ready(function() {
        initializeEventHandlers();
        initializeTooltips();
        loadCachedData();
    });
    
    function initializeEventHandlers() {
        // Form submission with loading state
        $('#runReportBtn').click(function(e) {
            e.preventDefault();
            if (!isLoading) {
                runReport();
            }
        });
        
        // Clear filters
        $('#clearFiltersBtn').click(function(e) {
            e.preventDefault();
            clearFilters();
        });
        
        // Export functionality
        $('#exportBtn').click(function(e) {
            e.preventDefault();
            exportToCSV();
        });
        
        // Dynamic dropdown updates
        $('#project_id').change(function() {
            if (!isLoading) {
                updatePlans();
            }
        });
        
        $('#testplan_id').change(function() {
            if (!isLoading) {
                updateBuilds();
            }
        });
        
        // Sortable table headers
        $('#suiteTable th.sortable').click(function() {
            if (!isLoading) {
                const columnIndex = $(this).index();
                sortTable(columnIndex);
            }
        });
        
        // Auto-save form state
        $('input, select').change(function() {
            saveFormState();
        });
    }
    
    function initializeTooltips() {
        $('[data-toggle="tooltip"]').tooltip({
            placement: 'top',
            trigger: 'hover'
        });
    }
    
    function setLoading(loading) {
        isLoading = loading;
        const $btn = $('#runReportBtn');
        const $spinner = $('#loadingSpinner');
        const $overlay = $('#loadingOverlay');
        
        if (loading) {
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            $spinner.show();
            $overlay.show();
            $('body').css('cursor', 'wait');
        } else {
            $btn.prop('disabled', false).html('<i class="fas fa-play"></i> Run Report');
            $spinner.hide();
            $overlay.hide();
            $('body').css('cursor', 'default');
        }
    }
    
    function showNotification(message, type = 'info') {
        const alertClass = type === 'error' ? 'alert-danger' : 
                         type === 'success' ? 'alert-success' : 'alert-info';
        
        const $alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `);
        
        $('#notificationArea').html($alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            $alert.alert('close');
        }, 5000);
    }
    
    function runReport() {
        const formData = $('#filterForm').serialize();
        
        // Validate form
        if (!$('#project_id').val()) {
            showNotification('Please select a project', 'error');
            return;
        }
        
        const hasAdditionalFilters = $('#testplan_id').val() || 
                                     $('#build_id').val() || 
                                     $('#status').val() || 
                                     $('#execution_path').val() || 
                                     $('#start_date').val() || 
                                     $('#end_date').val();
        
        if (!hasAdditionalFilters) {
            showNotification('Please add at least one additional filter', 'error');
            return;
        }
        
        setLoading(true);
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData + '&run=1&ajax=1',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentData = response.data;
                    updateTable(response.data);
                    updateSummary(response.summary);
                    showNotification('Report loaded successfully', 'success');
                    cacheData(response);
                } else {
                    showNotification(response.error || 'Error loading report', 'error');
                }
            },
            error: function(xhr, status, error) {
                showNotification('Network error: ' + error, 'error');
            },
            complete: function() {
                setLoading(false);
            }
        });
    }
    
    function updateTable(data) {
        const $tbody = $('#suiteTable tbody');
        $tbody.empty();
        
        if (!data || data.length === 0) {
            $tbody.append('<tr><td colspan="11" class="text-center">No data found</td></tr>');
            return;
        }
        
        data.forEach(function(row) {
            const $tr = $('<tr>').append(
                $('<td class="path-cell">').text(row.test_path || ''),
                $('<td class="text-center">').text(row.total_testcases || 0),
                $('<td class="text-center">').text(row.testcase_count || 0),
                $('<td class="text-center passed">').text(row.passed_count || 0),
                $('<td class="text-center failed">').text(row.failed_count || 0),
                $('<td class="text-center blocked">').text(row.blocked_count || 0),
                $('<td class="text-center not-run">').text(row.not_run_count || 0),
                $('<td class="text-center rate-cell">').text((row.pass_rate || 0) + '%'),
                $('<td class="text-center rate-cell">').text((row.fail_rate || 0) + '%'),
                $('<td class="text-center rate-cell">').text((row.block_rate || 0) + '%'),
                $('<td class="text-center rate-cell">').text((row.pending_rate || 0) + '%')
            );
            $tbody.append($tr);
        });
        
        $('#suiteTable').show();
    }
    
    function updateSummary(summary) {
        $('#totalTestCases').text(summary.totalTestCases || 0);
        $('#totalPassed').text(summary.totalPassed || 0);
        $('#totalFailed').text(summary.totalFailed || 0);
        $('#totalBlocked').text(summary.totalBlocked || 0);
        $('#totalNotRun').text(summary.totalNotRun || 0);
        $('#overallPassRate').text((summary.overallPassRate || 0) + '%');
        $('#overallFailRate').text((summary.overallFailRate || 0) + '%');
        $('#overallBlockRate').text((summary.overallBlockRate || 0) + '%');
        $('#overallPendingRate').text((summary.overallPendingRate || 0) + '%');
        
        $('#summarySection').show();
    }
    
    function sortTable(columnIndex) {
        if (sortColumn === columnIndex) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortColumn = columnIndex;
            sortDirection = 'asc';
        }
        
        currentData.sort(function(a, b) {
            let aVal, bVal;
            
            switch(columnIndex) {
                case 0: aVal = a.test_path; bVal = b.test_path; break;
                case 1: aVal = a.total_testcases; bVal = b.total_testcases; break;
                case 2: aVal = a.testcase_count; bVal = b.testcase_count; break;
                case 3: aVal = a.passed_count; bVal = b.passed_count; break;
                case 4: aVal = a.failed_count; bVal = b.failed_count; break;
                case 5: aVal = a.blocked_count; bVal = b.blocked_count; break;
                case 6: aVal = a.not_run_count; bVal = b.not_run_count; break;
                case 7: aVal = a.pass_rate; bVal = b.pass_rate; break;
                case 8: aVal = a.fail_rate; bVal = b.fail_rate; break;
                case 9: aVal = a.block_rate; bVal = b.block_rate; break;
                case 10: aVal = a.pending_rate; bVal = b.pending_rate; break;
            }
            
            if (typeof aVal === 'string') {
                return sortDirection === 'asc' ? 
                    aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
            } else {
                return sortDirection === 'asc' ? aVal - bVal : bVal - aVal;
            }
        });
        
        // Update sort indicators
        $('#suiteTable th').removeClass('sort-asc sort-desc');
        $('#suiteTable th').eq(columnIndex).addClass('sort-' + sortDirection);
        
        updateTable(currentData);
    }
    
    function clearFilters() {
        $('#filterForm')[0].reset();
        $('#testplan_id').html('<option value="">Select Test Plan</option>');
        $('#build_id').html('<option value="">Select Build</option>');
        localStorage.removeItem('suiteSummaryFormState');
        showNotification('Filters cleared', 'info');
    }
    
    function exportToCSV() {
        const formData = $('#filterForm').serialize();
        window.open('../../lib/execute/suite_execution_summary_export_proc.php?' + formData, '_blank');
    }
    
    function updatePlans() {
        const projectId = $('#project_id').val();
        if (!projectId) {
            $('#testplan_id').html('<option value="">Select Test Plan</option>');
            return;
        }
        
        $.ajax({
            url: '../../lib/execute/suite_execution_summary_proc.php',
            type: 'GET',
            data: { project_id: projectId, ajax: 1, get_plans: 1 },
            dataType: 'json',
            success: function(response) {
                const $select = $('#testplan_id');
                $select.html('<option value="">Select Test Plan</option>');
                
                if (response.plans && response.plans.length > 0) {
                    response.plans.forEach(function(plan) {
                        $select.append(`<option value="${plan.id}">${plan.name}</option>`);
                    });
                }
            }
        });
    }
    
    function updateBuilds() {
        const planId = $('#testplan_id').val();
        if (!planId) {
            $('#build_id').html('<option value="">Select Build</option>');
            return;
        }
        
        $.ajax({
            url: '../../lib/execute/suite_execution_summary_proc.php',
            type: 'GET',
            data: { testplan_id: planId, ajax: 1, get_builds: 1 },
            dataType: 'json',
            success: function(response) {
                const $select = $('#build_id');
                $select.html('<option value="">Select Build</option>');
                
                if (response.builds && response.builds.length > 0) {
                    response.builds.forEach(function(build) {
                        $select.append(`<option value="${build.id}">${build.name}</option>`);
                    });
                }
            }
        });
    }
    
    function saveFormState() {
        const formData = $('#filterForm').serializeArray();
        const formState = {};
        formData.forEach(function(item) {
            formState[item.name] = item.value;
        });
        localStorage.setItem('suiteSummaryFormState', JSON.stringify(formState));
    }
    
    function loadCachedData() {
        const cachedState = localStorage.getItem('suiteSummaryFormState');
        if (cachedState) {
            const formState = JSON.parse(cachedState);
            Object.keys(formState).forEach(function(key) {
                $(`[name="${key}"]`).val(formState[key]);
            });
            
            // Trigger dependent dropdown updates
            if (formState.project_id) {
                updatePlans();
            }
        }
    }
    
    function cacheData(response) {
        const cacheKey = 'suiteSummaryData_' + $('#project_id').val();
        const cacheData = {
            data: response.data,
            summary: response.summary,
            timestamp: Date.now()
        };
        localStorage.setItem(cacheKey, JSON.stringify(cacheData));
    }
})();
{/literal}
</script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style type="text/css">
{literal}
/* Modern CSS with animations and transitions */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}

.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0 !important;
    padding: 1.5rem;
}

.form-control, .custom-select {
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus, .custom-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.table {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    padding: 1rem;
}

.table th.sortable {
    cursor: pointer;
    position: relative;
    user-select: none;
}

.table th.sortable:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
}

.table th.sortable::after {
    content: '\f0dc';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    right: 10px;
    opacity: 0.3;
}

.table th.sort-asc::after {
    content: '\f0de';
    opacity: 1;
    color: #667eea;
}

.table th.sort-desc::after {
    content: '\f0dd';
    opacity: 1;
    color: #667eea;
}

.table td {
    border: none;
    padding: 0.875rem 1rem;
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}

.path-cell {
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    max-width: 300px;
    word-break: break-all;
}

.rate-cell {
    font-weight: 600;
}

.passed {
    color: #28a745;
    font-weight: 600;
}

.failed {
    color: #dc3545;
    font-weight: 600;
}

.blocked {
    color: #ffc107;
    font-weight: 600;
}

.not-run {
    color: #6c757d;
    font-weight: 600;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.875rem;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
}

.stat-card.total { border-top: 4px solid #007bff; }
.stat-card.passed { border-top: 4px solid #28a745; }
.stat-card.failed { border-top: 4px solid #dc3545; }
.stat-card.blocked { border-top: 4px solid #ffc107; }
.stat-card.not-run { border-top: 4px solid #6c757d; }

#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
}

#loadingSpinner {
    color: white;
    font-size: 3rem;
}

.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

/* Responsive design */
@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 1rem;
    }
    
    .table {
        font-size: 0.875rem;
    }
    
    .path-cell {
        max-width: 150px;
    }
}
{/literal}
</style>

{include file="inc_head.tpl" openHead="no"}
<body>
<div class="container-fluid mt-4">
    <h1 class="mb-4">{$gui->pageTitle|escape}</h1>
    
    <!-- Notification Area -->
    <div id="notificationArea" class="mb-3"></div>
    
    <!-- Filter Form Card -->
    <div class="card mb-4 fade-in">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Report Filters</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" name="filter_form">
                <div class="row">
                    <div class="col-md-3">
                        <label for="project_id" class="form-label">Project *</label>
                        <select id="project_id" name="project_id" class="custom-select">
                            <option value="">Select Project</option>
                            {html_options options=$gui->testprojects selected=$gui->selectedProject}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="testplan_id" class="form-label">Test Plan</label>
                        <select id="testplan_id" name="testplan_id" class="custom-select">
                            <option value="">Select Test Plan</option>
                            {html_options options=$gui->testplans selected=$gui->selectedPlan}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="build_id" class="form-label">Build</label>
                        <select id="build_id" name="build_id" class="custom-select">
                            <option value="">Select Build</option>
                            {html_options options=$gui->builds selected=$gui->selectedBuild}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="custom-select">
                            <option value="">All Statuses</option>
                            {html_options options=$gui->statuses selected=$gui->selectedStatus}
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-4">
                        <label for="execution_path" class="form-label">Execution Path</label>
                        <input type="text" id="execution_path" name="execution_path" class="form-control" 
                               value="{$gui->selectedExecutionPath|escape}" placeholder="Filter by execution path...">
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" 
                               value="{$gui->startDate|escape}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" 
                               value="{$gui->endDate|escape}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="runReportBtn" class="btn btn-primary mr-2">
                            <i class="fas fa-play"></i> Run Report
                        </button>
                        <button type="button" id="clearFiltersBtn" class="btn btn-secondary mr-2">
                            <i class="fas fa-redo"></i> Clear
                        </button>
                        <button type="button" id="exportBtn" class="btn btn-success">
                            <i class="fas fa-download"></i> Export
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Warning Message -->
    {if $gui->warning_msg}
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {$gui->warning_msg|escape}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    {/if}
    
    <!-- Summary Statistics -->
    <div id="summarySection" class="row mb-4" style="display: none;">
        <div class="col-md-2">
            <div class="stat-card total">
                <div class="stat-number" id="totalTestCases">0</div>
                <div class="stat-label">Total Test Cases</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card passed">
                <div class="stat-number" id="totalPassed">0</div>
                <div class="stat-label">Passed</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card failed">
                <div class="stat-number" id="totalFailed">0</div>
                <div class="stat-label">Failed</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card blocked">
                <div class="stat-number" id="totalBlocked">0</div>
                <div class="stat-label">Blocked</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card not-run">
                <div class="stat-number" id="totalNotRun">0</div>
                <div class="stat-label">Not Run</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card">
                <div class="stat-number" id="overallPassRate">0%</div>
                <div class="stat-label">Pass Rate</div>
            </div>
        </div>
    </div>
    
    <!-- Results Table -->
    <div id="tableSection" class="card" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table"></i> Suite Execution Results</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="suiteTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th class="sortable">Test Path</th>
                            <th class="sortable text-center">Overall Total</th>
                            <th class="sortable text-center">Test Case Count</th>
                            <th class="sortable text-center">Passed</th>
                            <th class="sortable text-center">Failed</th>
                            <th class="sortable text-center">Blocked</th>
                            <th class="sortable text-center">Not Run</th>
                            <th class="sortable text-center">Pass Rate (%)</th>
                            <th class="sortable text-center">Fail Rate (%)</th>
                            <th class="sortable text-center">Block Rate (%)</th>
                            <th class="sortable text-center">Pending Rate (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="11" class="text-center text-muted">
                                <i class="fas fa-info-circle"></i> Run the report to see results
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div id="loadingSpinner">
        <i class="fas fa-spinner fa-spin"></i>
    </div>
</div>

<!-- Hidden input for run flag -->
<input type="hidden" id="run" name="run" value="0">

</body>
</html>
