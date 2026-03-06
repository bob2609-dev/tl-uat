{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource suite_execution_summary.tpl
*}

{include file="inc_head.tpl" openHead="yes"}
<script type="text/javascript">
{literal}
function refreshPage() {
    document.getElementById('filter_form').submit();
}

function clearFilters() {
    document.getElementById('project_id').value = 0;
    document.getElementById('testplan_id').value = 0;
    document.getElementById('build_id').value = 0;
    document.getElementById('status').value = '';
    document.getElementById('execution_path').value = '';
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('filter_form').submit();
}

function updatePlans() {
    document.getElementById('build_id').value = 0;
    document.getElementById('filter_form').submit();
}

function updateBuilds() {
    document.getElementById('filter_form').submit();
}

// Sort table by column
function sortTable(columnIndex) {
    var table = document.getElementById("suiteTable");
    var rows = Array.from(table.rows).slice(1); // Skip header row
    var ascending = table.getAttribute('data-sort-direction') !== 'asc';
    
    rows.sort(function(a, b) {
        var aVal = a.cells[columnIndex].textContent.trim();
        var bVal = b.cells[columnIndex].textContent.trim();
        
        // Check if values are numeric
        if (!isNaN(aVal) && !isNaN(bVal)) {
            return ascending ? aVal - bVal : bVal - aVal;
        }
        
        // String comparison
        return ascending ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
    });
    
    // Rebuild table
    rows.forEach(function(row) {
        table.appendChild(row);
    });
    
    table.setAttribute('data-sort-direction', ascending ? 'asc' : 'desc');
    
    // Update sort indicators
    var headers = table.querySelectorAll('th');
    headers.forEach(function(header, index) {
        header.classList.remove('sort-asc', 'sort-desc');
        if (index === columnIndex) {
            header.classList.add(ascending ? 'sort-asc' : 'sort-desc');
        }
    });
}

// Export to Excel functionality
function exportToExcel() {
    // Get current filter values
    var project = document.getElementById('project_id').value;
    var testplan = document.getElementById('testplan_id').value;
    var build = document.getElementById('build_id').value;
    var status = document.getElementById('status').value;
    var executionPath = document.getElementById('execution_path').value;
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;
    
    // Build URL with parameters
    var url = '../../../lib/execute/suite_execution_summary_export.php?';
    if (project) url += 'project_id=' + project + '&';
    if (testplan) url += 'testplan_id=' + testplan + '&';
    if (build) url += 'build_id=' + build + '&';
    if (status) url += 'status=' + status + '&';
    if (executionPath) url += 'execution_path=' + encodeURIComponent(executionPath) + '&';
    if (startDate) url += 'start_date=' + startDate + '&';
    if (endDate) url += 'end_date=' + endDate + '&';
    
    // Redirect to download the Excel file
    window.location.href = url;
}

// Export to Excel functionality
function exportToExcel2() {
    // Get current filter values
    var project = document.getElementById('project_id').value;
    var testplan = document.getElementById('testplan_id').value;
    var build = document.getElementById('build_id').value;
    var status = document.getElementById('status').value;
    var executionPath = document.getElementById('execution_path').value;
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;
    
    // Build URL with parameters
    var url = '../../../lib/execute/suite_execution_summary_export_updated.php?';
    if (project) url += 'project_id=' + project + '&';
    if (testplan) url += 'testplan_id=' + testplan + '&';
    if (build) url += 'build_id=' + build + '&';
    if (status) url += 'status=' + status + '&';
    if (executionPath) url += 'execution_path=' + encodeURIComponent(executionPath) + '&';
    if (startDate) url += 'start_date=' + startDate + '&';
    if (endDate) url += 'end_date=' + endDate + '&';
    
    // Redirect to download the Excel file
    window.location.href = url;
}
{/literal}
</script>

<style type="text/css">
{literal}
/* Filter panel styling */
.filter_panel {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filter_panel h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #333;
    font-size: 18px;
    border-bottom: 2px solid #007bff;
    padding-bottom: 8px;
}

/* Filter table styling */
.filter_table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 15px 10px;
}

.filter_cell {
    vertical-align: top;
    width: 33.33%;
}

.filter_cell label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #333;
    font-size: 14px;
}

.filter_select {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background-color: #fff;
    transition: border-color 0.3s ease;
}

.filter_select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.filter_input {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background-color: #fff;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.filter_input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.filter_input::placeholder {
    color: #999;
    font-style: italic;
}

/* Button styling */
.button_cell {
    text-align: center;
    padding-top: 15px;
}

.button_container {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.filter_button {
    padding: 12px 24px;
    border-radius: 6px;
    border: 2px solid;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    transition: all 0.3s ease;
    min-width: 140px;
}

.reset_button {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
}

.reset_button:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.export_button {
    background-color: #007bff;
    border-color: #007bff;
    color: #fff;
}

.export_button:hover {
    background-color: #0056b3;
    border-color: #004085;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Responsive design */
@media (max-width: 768px) {
    .filter_table {
        border-spacing: 10px 8px;
    }
    
    .filter_cell {
        width: 100%;
        display: block;
    }
    
    .filter_table tr {
        display: block;
        margin-bottom: 15px;
    }
    
    .filter_table td {
        display: block;
        width: 100% !important;
        margin-bottom: 10px;
    }
    
    .button_container {
        flex-direction: column;
        align-items: center;
    }
    
    .filter_button {
        width: 100%;
        max-width: 200px;
    }
}

/* Summary statistics styling */
.summary_stats {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

.stats_row {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 15px;
}

.stat_box {
    text-align: center;
    padding: 15px;
    border-radius: 5px;
    min-width: 120px;
    border: 1px solid #ddd;
}

.stat_number {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 5px;
}

.stat_label {
    font-size: 12px;
    text-transform: uppercase;
    color: #666;
}

.stat_box.passed { background-color: #d4edda; color: #155724; }
.stat_box.failed { background-color: #f8d7da; color: #721c24; }
.stat_box.blocked { background-color: #fff3cd; color: #856404; }
.stat_box.not_run { background-color: #e2e3e5; color: #383d41; }
.stat_box.total { background-color: #e7f3ff; color: #004085; }

/* Table styling */
.suite_table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-size: 13px;
}

.suite_table th {
    background-color: #f2f2f2;
    color: #333;
    font-weight: bold;
    text-align: left;
    padding: 12px 8px;
    border: 1px solid #ddd;
    cursor: pointer;
    position: relative;
}

.suite_table th:hover {
    background-color: #e9ecef;
}

.suite_table th.sort-asc::after {
    content: " ↑";
    color: #007bff;
}

.suite_table th.sort-desc::after {
    content: " ↓";
    color: #007bff;
}

.suite_table td {
    padding: 8px;
    border: 1px solid #ddd;
    vertical-align: middle;
}

.suite_table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.suite_table tr:hover {
    background-color: #f0f0f0;
}

/* Status-specific cell styling */
.passed { color: #155724; font-weight: bold; }
.failed { color: #721c24; font-weight: bold; }
.blocked { color: #856404; font-weight: bold; }
.not_run { color: #383d41; font-weight: bold; }

/* Path column styling */
.path_cell {
    font-family: monospace;
    font-size: 12px;
    max-width: 300px;
    word-break: break-word;
}

/* Rate column styling */
.rate_cell {
    text-align: right;
    font-weight: bold;
}
{/literal}
</style>

{include file="inc_head.tpl" openHead="no"}

<body>
<h1 class="title">{$gui->pageTitle|escape}</h1>

{if $gui->warning_msg != ''}
    <div class="warning_message">{$gui->warning_msg}</div>
{/if}

<div class="workBack">
    <!-- Filter Panel -->
    <div class="filter_panel">
        <h3>Filters</h3>
        <form method="post" id="filter_form" name="filter_form">
            <table class="filter_table">
                <tr>
                    <td class="filter_cell">
                        <label for="project_id">{lang_get s='testproject'}</label>
                        <select name="project_id" id="project_id" onchange="updatePlans()" class="filter_select">
                            <option value="0">{lang_get s='all_testprojects'}</option>
                            {foreach from=$gui->testprojects item=project}
                                <option value="{$project.id}" {if $gui->selectedProject == $project.id}selected{/if}>
                                    {$project.name|escape}
                                </option>
                            {/foreach}
                        </select>
                    </td>
                    <td class="filter_cell">
                        <label for="testplan_id">{lang_get s='testplan'}</label>
                        <select name="testplan_id" id="testplan_id" onchange="updateBuilds()" class="filter_select">
                            <option value="0">{lang_get s='all_testplans'}</option>
                            {foreach from=$gui->testplans item=plan}
                                <option value="{$plan.id}" {if $gui->selectedPlan == $plan.id}selected{/if}>
                                    {$plan.name|escape}
                                </option>
                            {/foreach}
                        </select>
                    </td>
                    <td class="filter_cell">
                        <label for="build_id">{lang_get s='build'}</label>
                        <select name="build_id" id="build_id" onchange="refreshPage()" class="filter_select">
                            <option value="0">{lang_get s='all_builds'}</option>
                            {foreach from=$gui->builds item=build}
                                <option value="{$build.id}" {if $gui->selectedBuild == $build.id}selected{/if}>
                                    {$build.name|escape}
                                </option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="filter_cell">
                        <label for="status">{lang_get s='status'}</label>
                        <select name="status" id="status" onchange="refreshPage()" class="filter_select">
                            <option value="">{lang_get s='all_statuses'}</option>
                            {foreach from=$gui->statuses item=opt key=key}
                                <option value="{$key}" {if $gui->selectedStatus == $key}selected{/if}>{$opt}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td class="filter_cell">
                        <label for="execution_path">Execution Path</label>
                        <input type="text" id="execution_path" name="execution_path" value="{$gui->selectedExecutionPath|escape}" placeholder="Filter by execution path..." onchange="refreshPage()" class="filter_input">
                    </td>
                    <td class="filter_cell">
                        <!-- Empty cell for alignment -->
                    </td>
                </tr>
                <tr>
                    <td class="filter_cell">
                        <label for="start_date">{lang_get s='start_date'}</label>
                        <input type="date" id="start_date" name="start_date" value="{$gui->startDate}" onchange="refreshPage()" class="filter_input">
                    </td>
                    <td class="filter_cell">
                        <label for="end_date">{lang_get s='end_date'}</label>
                        <input type="date" id="end_date" name="end_date" value="{$gui->endDate}" onchange="refreshPage()" class="filter_input">
                    </td>
                    <td class="filter_cell">
                        <!-- Empty cell for alignment -->
                    </td>
                </tr>
                <tr>
                    <td class="filter_cell button_cell" colspan="3">
                        <div class="button_container">
                            <input type="button" value="{lang_get s='reset_filters'}" onclick="clearFilters()" class="filter_button reset_button" >

                            {* make button green *}
                            <input type="button" value="Export to CSV" onclick="exportToExcel()" class="filter_button export_button"  style="background-color: #4aa0e6; border-color: #007bff; color: #dadada; font-weight: bold;background:#224a7a !important">
                        
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <!-- Summary Statistics -->
    {if $gui->suiteData|@count > 0}
        <div class="summary_stats">
            <h3>Overall Summary</h3>
            <div class="stats_row">
                <div class="stat_box total">
                    <div class="stat_number">{$gui->totalTestCases}</div>
                    <div class="stat_label">Total Test Cases</div>
                </div>
                <div class="stat_box passed">
                    <div class="stat_number">{$gui->totalPassed}</div>
                    <div class="stat_label">Passed ({$gui->overallPassRate}%)</div>
                </div>
                <div class="stat_box failed">
                    <div class="stat_number">{$gui->totalFailed}</div>
                    <div class="stat_label">Failed ({$gui->overallFailRate}%)</div>
                </div>
                <div class="stat_box blocked">
                    <div class="stat_number">{$gui->totalBlocked}</div>
                    <div class="stat_label">Blocked ({$gui->overallBlockRate}%)</div>
                </div>
                <div class="stat_box not_run">
                    <div class="stat_number">{$gui->totalNotRun}</div>
                    <div class="stat_label">Not Run ({$gui->overallPendingRate}%)</div>
                </div>
            </div>
        </div>

        <!-- Test Suite Execution Summary Table -->
        <h3>Test Suite Execution Summary</h3>
        <table class="suite_table" id="suiteTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Test Path</th>
                    <th onclick="sortTable(1)">Test Case Count</th>
                    <th onclick="sortTable(2)">Passed</th>
                    <th onclick="sortTable(3)">Failed</th>
                    <th onclick="sortTable(4)">Blocked</th>
                    <th onclick="sortTable(5)">Not Run</th>
                    <th onclick="sortTable(6)">Pass Rate</th>
                    <th onclick="sortTable(7)">Fail Rate</th>
                    <th onclick="sortTable(8)">Block Rate</th>
                    <th onclick="sortTable(9)">Pending Rate</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$gui->suiteData item=suite}
                    <tr>
                        <td class="path_cell">{$suite.test_path|escape}</td>
                        <td class="rate_cell">{$suite.testcase_count}</td>
                        <td class="rate_cell passed">{$suite.passed_count}</td>
                        <td class="rate_cell failed">{$suite.failed_count}</td>
                        <td class="rate_cell blocked">{$suite.blocked_count}</td>
                        <td class="rate_cell not_run">{$suite.not_run_count}</td>
                        <td class="rate_cell passed">{$suite.pass_rate}%</td>
                        <td class="rate_cell failed">{$suite.fail_rate}%</td>
                        <td class="rate_cell blocked">{$suite.block_rate}%</td>
                        <td class="rate_cell not_run">{$suite.pending_rate}%</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        <div class="info_message">
            {if $gui->selectedProject == 0}
                Please select a test project to view execution summary data.
            {else}
                No execution data found for the selected filters. Try adjusting your filter criteria.
            {/if}
        </div>
    {/if}
</div>

</body>
</html>
