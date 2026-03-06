{include file="inc_head.tpl" openHead="yes"}
<script type="text/javascript">
{literal}
// Simple Suite Execution Summary with improved UX
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
        
        // Dropdown change handlers for dynamic updates
        $('#project_id').change(function() {
            updatePlans();
            $('#build_id').html('<option value="">Select Build</option>');
        });
        
        $('#testplan_id').change(function() {
            updateBuilds();
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
    
    function setLoading(loading) {
        isLoading = loading;
        const $btn = $('#runReportBtn');
        const $spinner = $('#loadingSpinner');
        
        if (loading) {
            $btn.prop('disabled', true).val('Loading...');
            $spinner.show();
            $('body').css('cursor', 'wait');
        } else {
            $btn.prop('disabled', false).val('Run Report');
            $spinner.hide();
            $('body').css('cursor', 'default');
        }
    }
    
    function showNotification(message, type) {
        const alertClass = type === 'error' ? 'alert_error' : 
                         type === 'success' ? 'alert_success' : 'alert_info';
        
        const $alert = $('<div class="' + alertClass + '" style="margin-bottom: 10px; padding: 10px; border-radius: 4px;">' + 
                        message + '</div>');
        
        $('#notificationArea').html($alert);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $alert.fadeOut();
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
        
        // Use the enhanced AJAX endpoint with tester data
        $.ajax({
            url: '../../lib/execute/test_ajax_report_with_testers.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentData = response.data;
                    updateTable(response.data);
                    updateSummary(response.summary);
                    updateTesterSummary(response.testers);
                    showNotification('Report loaded successfully', 'success');
                    cacheData(response);
                } else {
                    showNotification(response.error || 'Error loading report', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response Text:', xhr.responseText);
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
                $('<td class="path_cell">').text(row.test_path || ''),
                $('<td class="text-center">').text(row.total_testcases || 0),
                $('<td class="text-center">').text(row.testcase_count || 0),
                $('<td class="text-center passed">').text(row.passed_count || 0),
                $('<td class="text-center failed">').text(row.failed_count || 0),
                $('<td class="text-center blocked">').text(row.blocked_count || 0),
                $('<td class="text-center not_run">').text(row.not_run_count || 0),
                $('<td class="text-center rate_cell">').text((row.pass_rate || 0) + '%'),
                $('<td class="text-center rate_cell">').text((row.fail_rate || 0) + '%'),
                $('<td class="text-center rate_cell">').text((row.block_rate || 0) + '%'),
                $('<td class="text-center rate_cell">').text((row.pending_rate || 0) + '%')
            );
            $tbody.append($tr);
        });
        
        $('#suiteTable').show();
        $('#tableSection').show();
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
    
    function updateTesterSummary(testers) {
        const $tbody = $('#testerTable tbody');
        $tbody.empty();
        
        if (!testers || testers.length === 0) {
            $tbody.append('<tr><td colspan="7" class="text-center">No tester data available</td></tr>');
            $('#testerSection').hide();
            return;
        }
        
        testers.forEach(function(tester) {
            const $tr = $('<tr>').append(
                $('<td>').text(tester.tester_name || tester.tester_login),
                $('<td class="text-center">').text(tester.total_executions || 0),
                $('<td class="text-center passed">').text(tester.passed_count || 0),
                $('<td class="text-center failed">').text(tester.failed_count || 0),
                $('<td class="text-center blocked">').text(tester.blocked_count || 0),
                $('<td class="text-center not_run">').text(tester.not_run_count || 0),
                $('<td class="text-center rate_cell">').text((tester.pass_rate || 0) + '%')
            );
            $tbody.append($tr);
        });
        
        $('#testerSection').show();
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
        localStorage.removeItem('suiteSummaryFormState');
        showNotification('Filters cleared', 'info');
    }
    
    function exportToCSV() {
        const formData = $('#filterForm').serialize();
        window.open('../../lib/execute/suite_execution_summary_export_proc.php?' + formData, '_blank');
    }
    
    function updatePlans() {
        const projectId = $('#project_id').val();
        console.log('Updating plans for project ID:', projectId);
        
        if (!projectId) {
            $('#testplan_id').html('<option value="">Select Test Plan</option>');
            return;
        }
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { project_id: projectId, get_plans: 1, ajax: 1 },
            dataType: 'json',
            beforeSend: function() {
                console.log('Sending AJAX request for plans...');
            },
            success: function(response) {
                console.log('Received response for plans:', response);
                const $select = $('#testplan_id');
                $select.html('<option value="">Select Test Plan</option>');
                
                if (response.plans && response.plans.length > 0) {
                    response.plans.forEach(function(plan) {
                        console.log('Adding plan:', plan);
                        $select.append('<option value="' + plan.id + '">' + plan.name + '</option>');
                    });
                } else {
                    console.log('No plans found for project:', projectId);
                    showNotification('No test plans found for this project', 'info');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error loading plans:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                showNotification('Network error loading plans: ' + error, 'error');
            }
        });
    }
    
    function updateBuilds() {
        const planId = $('#testplan_id').val();
        console.log('Updating builds for plan ID:', planId);
        
        if (!planId) {
            $('#build_id').html('<option value="">Select Build</option>');
            return;
        }
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: { testplan_id: planId, get_builds: 1, ajax: 1 },
            dataType: 'json',
            beforeSend: function() {
                console.log('Sending AJAX request for builds...');
            },
            success: function(response) {
                console.log('Received response for builds:', response);
                const $select = $('#build_id');
                $select.html('<option value="">Select Build</option>');
                
                if (response.builds && response.builds.length > 0) {
                    response.builds.forEach(function(build) {
                        console.log('Adding build:', build);
                        $select.append('<option value="' + build.id + '">' + build.name + '</option>');
                    });
                } else {
                    console.log('No builds found for plan:', planId);
                    showNotification('No builds found for this test plan', 'info');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error loading builds:', {
                    status: status,
                    error: error,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                showNotification('Network error loading builds: ' + error, 'error');
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
                $('[name="' + key + '"]').val(formState[key]);
            });
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

<style type="text/css">
{literal}
/* TestLink-compatible CSS */
.filter_panel {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
}

.filter_table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 12px 8px;
}

.filter_select, .filter_input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 13px;
    box-sizing: border-box;
}

.button_row {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-top: 10px;
}

input.filter_button {
    padding: 12px 24px;
    border-radius: 6px;
    border: 2px solid;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: all 0.3s ease;
    min-width: 140px;
}

input.run_button {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: #fff !important;
}

input.run_button:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

input.reset_button {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
    color: #fff !important;
}

input.reset_button:hover {
    background-color: #5a6268 !important;
    border-color: #545b62 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

input.export_button {
    background-color: #007bff !important;
    border-color: #007bff !important;
    color: #fff !important;
}

input.export_button:hover {
    background-color: #0056b3 !important;
    border-color: #004085 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.suite_table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
    font-size: 13px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

.suite_table th {
    background: #f2f2f2;
    color: #333;
    font-weight: bold;
    text-align: left;
    padding: 10px 8px;
    border: 1px solid #ddd;
    cursor: pointer;
    user-select: none;
}

.suite_table th:hover {
    background: #e9ecef;
}

.suite_table td {
    padding: 8px;
    border: 1px solid #ddd;
    vertical-align: middle;
}

.suite_table tr:nth-child(even) {
    background: #fafafa;
}

.suite_table tr:hover {
    background: #f8f9fa;
}

.path_cell {
    font-family: monospace;
    font-size: 12px;
    max-width: 300px;
    word-break: break-word;
}

.rate_cell {
    text-align: right;
    font-weight: bold;
}

.passed {
    color: #28a745;
    font-weight: bold;
}

.failed {
    color: #dc3545;
    font-weight: bold;
}

.blocked {
    color: #ffc107;
    font-weight: bold;
}

.not_run {
    color: #6c757d;
    font-weight: bold;
}

.summary_stats {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
}

.summary_title {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 15px;
    color: #333;
}

.stats_container {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 15px;
}

.stat_box {
    flex: 1;
    min-width: 150px;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.stat_box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
}

.stat_number {
    font-size: 32px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat_label {
    font-size: 12px;
    text-transform: uppercase;
    color: #666;
}

.stat_box.total {
    background-color: #e7f3ff;
    color: #004085;
    border-top: 4px solid #007bff;
}

.stat_box.passed {
    background-color: #d4edda;
    color: #155724;
    border-top: 4px solid #28a745;
}

.stat_box.failed {
    background-color: #f8d7da;
    color: #721c24;
    border-top: 4px solid #dc3545;
}

.stat_box.blocked {
    background-color: #fff3cd;
    color: #856404;
    border-top: 4px solid #ffc107;
}

.stat_box.not_run {
    background-color: #e2e3e5;
    color: #383d41;
    border-top: 4px solid #6c757d;
}

#loadingSpinner {
    display: none;
    text-align: center;
    padding: 20px;
    font-size: 16px;
    color: #666;
}

.alert_success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert_error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.alert_info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.text-center {
    text-align: center;
}

.sort-asc::after {
    content: ' ↑';
    color: #007bff;
}

.sort-desc::after {
    content: ' ↓';
    color: #007bff;
}

/* Responsive design */
@media (max-width: 768px) {
    .stats_container {
        flex-direction: column;
    }
    
    .stat_box {
        margin-bottom: 10px;
    }
    
    .suite_table {
        font-size: 12px;
    }
    
    .path_cell {
        max-width: 150px;
    }
}
{/literal}
</style>

{include file="inc_head.tpl" openHead="no"}
<body>
<h1 class="title">{$gui->pageTitle|escape}</h1>

<!-- Notification Area -->
<div id="notificationArea"></div>

<!-- Filter Form -->
<div class="filter_panel">
    <form id="filterForm" name="filter_form">
        <table class="filter_table">
            <tr>
                <td>
                    <label for="project_id">Project *</label>
                    <select id="project_id" name="project_id" class="filter_select">
                        <option value="">Select Project</option>
                        {html_options options=$gui->testprojects selected=$gui->selectedProject}
                    </select>
                </td>
                <td>
                    <label for="testplan_id">Test Plan</label>
                    <select id="testplan_id" name="testplan_id" class="filter_select">
                        <option value="">Select Test Plan</option>
                        {html_options options=$gui->testplans selected=$gui->selectedPlan}
                    </select>
                </td>
                <td>
                    <label for="build_id">Build</label>
                    <select id="build_id" name="build_id" class="filter_select">
                        <option value="">Select Build</option>
                        {html_options options=$gui->builds selected=$gui->selectedBuild}
                    </select>
                </td>
                <td>
                    <label for="status">Status</label>
                    <select id="status" name="status" class="filter_select">
                        <option value="">All Statuses</option>
                        {html_options options=$gui->statuses selected=$gui->selectedStatus}
                    </select>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="execution_path">Execution Path</label>
                    <input type="text" id="execution_path" name="execution_path" class="filter_input" 
                           value="{$gui->selectedExecutionPath|escape}" placeholder="Filter by execution path...">
                </td>
                <td>
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="filter_input" 
                           value="{$gui->startDate|escape}">
                </td>
                <td>
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="filter_input" 
                           value="{$gui->endDate|escape}">
                </td>
                <td>
                    <div class="button_row">
                        <input type="button" id="runReportBtn" value="Run Report" class="filter_button run_button">
                        <input type="button" id="clearFiltersBtn" value="Clear" class="filter_button reset_button">
                        <input type="button" id="exportBtn" value="Export" class="filter_button export_button">
                    </div>
                </td>
            </tr>
        </table>
    </form>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner">
    <img src="{$tlImages.loading}" alt="Loading..." /> Loading report data...
</div>

<!-- Warning Message -->
{if $gui->warning_msg}
<div class="alert_error" style="margin-bottom: 16px; padding: 10px; border-radius: 4px;">
    {$gui->warning_msg|escape}
</div>
{/if}

<!-- Summary Statistics -->
<div id="summarySection" class="summary_stats" style="display: none;">
    <div class="summary_title">Execution Summary</div>
    <div class="stats_container">
        <div class="stat_box total">
            <div class="stat_number" id="totalTestCases">0</div>
            <div class="stat_label">Total Test Cases</div>
        </div>
        <div class="stat_box passed">
            <div class="stat_number" id="totalPassed">0</div>
            <div class="stat_label">Passed</div>
        </div>
        <div class="stat_box failed">
            <div class="stat_number" id="totalFailed">0</div>
            <div class="stat_label">Failed</div>
        </div>
        <div class="stat_box blocked">
            <div class="stat-number" id="totalBlocked">0</div>
            <div class="stat_label">Blocked</div>
        </div>
        <div class="stat_box not_run">
            <div class="stat-number" id="totalNotRun">0</div>
            <div class="stat_label">Not Run</div>
        </div>
        <div class="stat_box">
            <div class="stat-number" id="overallPassRate">0%</div>
            <div class="stat-label">Pass Rate</div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div id="tableSection" style="display: none;">
    <div class="summary_title">Suite Execution Results</div>
    <table id="suiteTable" class="suite_table" style="display: none;">
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
                    Run the report to see results
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Tester Summary Section -->
<div id="testerSection" class="summary_stats" style="display: none;">
    <div class="summary_title">Execution Summary by Tester</div>
    <table id="testerTable" class="suite_table">
        <thead>
            <tr>
                <th>Tester Name</th>
                <th class="text-center">Total Executions</th>
                <th class="text-center">Passed</th>
                <th class="text-center">Failed</th>
                <th class="text-center">Blocked</th>
                <th class="text-center">Not Run</th>
                <th class="text-center">Pass Rate (%)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="7" class="text-center text-muted">
                    Run the report to see tester summary
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Hidden input for run flag -->
<input type="hidden" id="run" name="run" value="0">

</body>
</html>
