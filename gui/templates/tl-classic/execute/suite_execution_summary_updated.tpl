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

// Function to refresh node hierarchy paths via AJAX
function refreshHierarchyPaths_SAMPLE() {
    // Show loading indicator
    var statusElement = document.getElementById('refresh_status');
    statusElement.innerHTML = '<span style="color:blue">Refreshing hierarchy paths...</span>';
    
    // Make the AJAX call
    var xhr = new XMLHttpRequest();
    // Use a relative path based on current URL context
    var ajaxUrl = '../../../lib/execute/refresh_hierarchy_paths.php';
    xhr.open('GET', ajaxUrl, true);
    // Log the URL for debugging
    console.log('AJAX URL:', ajaxUrl);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('Accept', 'application/json');
    
    // Handle response
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var response;
                try {
                    // Log the raw response for debugging
                    console.log('Raw response:', xhr.responseText);
                    
                    // Check if response is empty
                    if (!xhr.responseText) {
                        statusElement.innerHTML = '<span style="color:red">Error: Empty response</span>';
                        return;
                    }
                    
                    response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        statusElement.innerHTML = '<span style="color:green">Success: ' + response.message + '</span>';
                        // Refresh the page to show updated paths after a short delay
                        setTimeout(function() {
                            refreshPage();
                        }, 2000);
                    } else {
                        statusElement.innerHTML = '<span style="color:red">Error: ' + response.message + '</span>';
                    }
                } catch (e) {
                    statusElement.innerHTML = '<span style="color:red">Error parsing response: ' + e.message + '</span>';
                    console.error('JSON parse error:', e);
                }
            } else {
                statusElement.innerHTML = '<span style="color:red">HTTP Error: ' + xhr.status + ' ' + xhr.statusText + '</span>';
            }
        }
    };
    
    xhr.onerror = function() {
        statusElement.innerHTML = '<span style="color:red">Network error occurred</span>';
    };
    
    // Send the request
    xhr.send();
}


// Refresh hierarchy paths via AJAX on page load
function refreshHierarchyPaths() {
    // Show loading indicator
    var loadingDiv = document.getElementById('hierarchy-loading');
    var contentDiv = document.getElementById('main-content');
    
    if (loadingDiv) loadingDiv.style.display = 'block';
    if (contentDiv) contentDiv.style.display = 'none';
    
    // Create AJAX request
    var xhr = new XMLHttpRequest();
    // Use relative path based on current URL context (like in SAMPLE function)
    var ajaxUrl = '../../../lib/execute/refresh_hierarchy_paths.php';
    xhr.open('GET', ajaxUrl, true);
    // Log the URL for debugging
    console.log('AJAX URL:', ajaxUrl);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('Accept', 'application/json');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            // Hide loading indicator and show content
            if (loadingDiv) loadingDiv.style.display = 'none';
            if (contentDiv) contentDiv.style.display = 'block';
            
            if (xhr.status === 200) {
                try {
                    // Log the raw response for debugging
                    console.log('Raw response:', xhr.responseText);
                    
                    // Check if response is empty
                    if (!xhr.responseText) {
                        console.warn('Error: Empty response from hierarchy refresh');
                        return;
                    }
                    
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        console.log('Success: ' + response.message);
                        // Optionally refresh page data without full reload
                    } else {
                        console.warn('Hierarchy refresh failed:', response.message);
                        // Still show the page - refresh failure shouldn't block the UI
                    }
                } catch (e) {
                    console.warn('Error parsing hierarchy refresh response:', e.message);
                    console.error('JSON parse error:', e);
                    // Still show the page - parsing error shouldn't block the UI
                }
            } else {
                console.warn('HTTP Error: ' + xhr.status + ' ' + xhr.statusText);
                // Still show the page - network error shouldn't block the UI
            }
        }
    };
    
    xhr.onerror = function() {
        console.warn('Network error occurred during hierarchy refresh');
        if (loadingDiv) loadingDiv.style.display = 'none';
        if (contentDiv) contentDiv.style.display = 'block';
    };
    
    // Set timeout to prevent hanging
    xhr.timeout = 10000; // 10 seconds
    xhr.ontimeout = function() {
        console.warn('Hierarchy refresh timed out');
        if (loadingDiv) loadingDiv.style.display = 'none';
        if (contentDiv) contentDiv.style.display = 'block';
    };
    
    xhr.send();
}

// Run hierarchy refresh on page load
window.addEventListener('load', function() {
    refreshHierarchyPaths();
});

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
    refreshHierarchyPaths();
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

/* Loading indicator styling */
#hierarchy-loading {
    display: none;
    text-align: center;
    padding: 50px;
    font-size: 16px;
    color: #666;
}

.loading-spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

#main-content {
    display: none;
}
{/literal}
</style>

{include file="inc_head.tpl" openHead="no"}

<body>
<!-- Loading indicator -->
<div id="hierarchy-loading">
    <div class="loading-spinner"></div>
    <div>Refreshing hierarchy paths...</div>
    <div style="font-size: 14px; color: #999; margin-top: 10px;">This will only take a moment</div>
</div>

<!-- Main content (hidden initially) -->
<div id="main-content">
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

                        
                            {* make button red *}
                            <input type="button" value="Export to CSV" onclick="exportToExcel2()" class="filter_button export_button"  style="background-color: #e91a1a; border-color: #e91a1a; color: #dadada; font-weight: bold;background:#e91a1a !important">
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<div>
<span id="refresh_status"></span>
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
                    <th onclick="sortTable(1)">Overall Total</th>
                    <th onclick="sortTable(2)">Test Case Count</th>
                    <th onclick="sortTable(3)">Passed</th>
                    <th onclick="sortTable(4)">Failed</th>
                    <th onclick="sortTable(5)">Blocked</th>
                    <th onclick="sortTable(6)">Not Run</th>
                    <th onclick="sortTable(7)">Pass Rate</th>
                    <th onclick="sortTable(8)">Fail Rate</th>
                    <th onclick="sortTable(9)">Block Rate</th>
                    <th onclick="sortTable(10)">Pending Rate</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$gui->suiteData item=suite}
                    <tr>
                        <td class="path_cell">{$suite.test_path|escape}</td>
                        <td class="rate_cell">{$suite.total_testcases}</td>
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

</div> <!-- End main-content -->
</body>
</html>
