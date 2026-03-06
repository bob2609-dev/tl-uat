{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource test_execution_summary.tpl
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
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    document.getElementById('filter_form').submit();
}

function updatePlans() {
    document.getElementById('build_id').value = 0;
    document.getElementById('filter_form').submit();
}

function toggleDetails(id) {
    var element = document.getElementById(id);
    var icon = document.getElementById('icon_' + id);
    
    if (element.style.display === "none") {
        element.style.display = "block";
        if (icon) { icon.classList.add('open'); }
    } else {
        element.style.display = "none";
        if (icon) { icon.classList.remove('open'); }
    }
}

// Sort by column
function sortBy(column) {
    var by = document.getElementById('order_by');
    var dir = document.getElementById('order_dir');
    if (by.value === column) {
        dir.value = (dir.value === 'ASC') ? 'DESC' : 'ASC';
    } else {
        by.value = column;
        dir.value = 'ASC';
    }
    document.getElementById('filter_form').submit();
}

// Function to refresh node hierarchy paths via AJAX
function refreshHierarchyPaths() {
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

// Function to export execution data to Excel
function exportToExcel() {
    // Get current filter values
    var project = document.getElementById('project_id').value;
    var testplan = document.getElementById('testplan_id').value;
    var build = document.getElementById('build_id').value;
    var status = document.getElementById('status').value;
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;
    
    // Build URL with query parameters
    var url = '../../../lib/execute/excel_export_handler.php?';
    if (project !== '0') url += 'project=' + project + '&';
    if (testplan !== '0') url += 'testplan=' + testplan + '&';
    if (build !== '0') url += 'build=' + build + '&';
    if (status) url += 'status=' + status + '&';
    if (startDate) url += 'startdate=' + startDate + '&';
    if (endDate) url += 'enddate=' + endDate + '&';
    
    // Redirect to download the Excel file
    window.location.href = url;
    
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
                        statusElement.innerHTML = '<span style="color:green">' + response.message + '</span>';
                        // Refresh the page after a short delay to show the success message
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        statusElement.innerHTML = '<span style="color:red">Error: ' + (response.message || 'Database operation failed') + '</span>';
                    }
                } catch (e) {
                    statusElement.innerHTML = '<span style="color:red">Error parsing JSON response: ' + e.message + '</span>';
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
    
    xhr.send();
}
{/literal}
</script>
<link rel="stylesheet" href="{$basehref}css/dashboard.css" type="text/css" media="all" />
<!-- Custom styles for improved layout -->
<style type="text/css">
/* Modified Filter Panel */
.filter_panel {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    width: 100%;
}
.filter_column {
    flex: 1;
    min-width: 200px;
    max-width: 300px;
}
/* Enhanced Button Styling */
.filter_column.buttons input[type="button"] {
    background-color: #c0c0c0;
    color: #ffffff;
    border: 1px solid #a0a0a0;
    padding: 8px 20px;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}
.filter_column.buttons input[type="button"]:hover {
    background-color: #a8a8a8;
    border-color: #888888;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
/* Responsive adjustments */
@media (max-width: 1200px) {
    .filter_column {
        min-width: 180px;
    }
}

/* Dashboard layout */
.dashboard {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}
.dashboard_element {
    background: #fff;
    border-radius: 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    border: 1px solid #ccc;
    overflow: hidden;
}
.dashboard_element.execution_overview {
    grid-column: span 4;
}
.dashboard_element.top_testers {
    grid-column: span 4;
}
.dashboard_element.test_suite_progress {
    grid-column: span 4;
}
.dashboard_title {
    background-color: #2c5b8e;
    color: white;
    padding: 8px 15px;
    font-weight: bold;
    font-size: 14px;
    border-bottom: 1px solid #224a7a;
}
.dashboard_content {
    padding: 15px;
}

/* Stats boxes */
.stats_grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-gap: 15px;
    margin-bottom: 15px;
}
.stat_box {
    text-align: center;
    border: 1px solid #ddd;
    padding: 15px 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 120px;
}
.stat_icon {
    font-size: 20px;
    margin-bottom: 5px;
    color: #333333;

}
.stat_number {
    font-size: 24px;
    font-weight: bold;
    margin: 5px 0;
    color: #333333;
}
.stat_label {
    font-size: 12px;
    color: #333333;
}
.stat_box.total {
    background-color: #fff;
}
.stat_box.passed {
    background-color: #e0f8e9;
}
.stat_box.failed {
    background-color: #fde2e4;
}
.stat_box.blocked {
    background-color: #fff3cd;
}
.stat_box.not_run {
    background-color: #e4f1f9;
}
.stat_box.pass_rate {
    background-color: #f0f0f0;
}

/* Tables */
.simple {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}
.simple th {
    background-color: #f2f2f2;
    color: #333;
    font-weight: bold;
    text-align: left;
    padding: 8px;
    border: 1px solid #ddd;
}
.simple td {
    padding: 8px;
    border: 1px solid #ddd;
    vertical-align: middle;
}
.simple tr:nth-child(even) {
    background-color: #f9f9f9;
}
.simple tr:hover {
    background-color: #f0f0f0;
}

/* Execution details */
.status_cell span {
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    font-size: 12px;
}
.status_cell .passed {
    background-color: #d4edda;
    color: #155724;
}
.status_cell .failed {
    background-color: #f8d7da;
    color: #721c24;
}
.status_cell .blocked {
    background-color: #fff3cd;
    color: #856404;
}
.status_cell .not_run {
    background-color: #e9ecef;
    color: #495057;
}

/* Hierarchical view */
.project_container, .plan_container, .suite_container {
    margin-bottom: 10px;
}
.project_header, .plan_header, .suite_header {
    display: flex;
    align-items: center;
    padding: 10px;
    cursor: pointer;
}
.project_header {
    background-color: #e6e6e6;
    border: 1px solid #d0d0d0;
    border-radius: 5px;
}
.plan_header {
    background-color: #f0f0f0;
    border: 1px solid #e0e0e0;
    margin-left: 20px;
    border-radius: 3px;
}
.suite_header {
    background-color: #f8f8f8;
    border: 1px solid #eaeaea;
    margin-left: 40px;
    border-radius: 3px;
}
.project_header img, .plan_header img, .suite_header img {
    margin-right: 10px;
}
.project_name, .plan_name, .suite_name {
    font-weight: bold;
    flex: 1;
}
.project_content {
    margin-left: 10px;
    padding-top: 10px;
}
.plan_content {
    margin-left: 30px;
    padding-top: 10px;
}
.suite_content {
    margin-left: 50px;
    padding: 10px 0;
}

/* Status colors for rows */
tr.status_p { background-color: rgba(212, 237, 218, 0.3); }
tr.status_f { background-color: rgba(248, 215, 218, 0.3); }
tr.status_b { background-color: rgba(255, 243, 205, 0.3); }
tr.status_n { background-color: rgba(217, 237, 247, 0.3); }

/* Progress bars */
.progress-bar {
    height: 10px;
    background-color: #e9ecef;
    border-radius: 5px;
    margin-top: 3px;
    overflow: hidden;
}
.progress-bar .bar {
    height: 100%;
    float: left;
}
.progress-bar .passed-bar {
    background-color: #28a745;
}
.progress-bar .failed-bar {
    background-color: #dc3545;
}
.progress-bar .blocked-bar {
    background-color: #ffc107;
}
.progress-bar .not-run-bar {
    background-color: #17a2b8;
}

/* Inline folder icon styling */
.toggle-icon {
    font-size: 14px;
    cursor: pointer;
    color: #333333;
    margin-right: 5px;
}
.folder-icon::before { content: "\1F4C1"; } /* closed folder */
.folder-icon.open::before { content: "\1F4C2"; } /* open folder */

/* Scrollable sections */
.dashboard_element.top_testers .dashboard_content,
.dashboard_element.test_suite_progress .dashboard_content {
    max-height: 300px;
    overflow-y: auto;
}
/* End scrollable sections */

    /* Styles for Path Hierarchy Metrics */
    .full_width_container {
        width: 100%;
        margin-bottom: 20px;
        clear: both;
    }
    .scrollable_metrics {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
    }
    table.full_width {
        width: 100%;
    }
</style>
{include file="inc_head.tpl" openHead="no"}

<body>
<h1 class="title">{$gui->pageTitle|escape}</h1>

{if $gui->warning_msg != ''}
    <div class="warning_message">{$gui->warning_msg}</div>
{/if}

<div class="workBack">
    <form id="filter_form" method="post">
        <input type="hidden" name="order_by" id="order_by" value="{$gui->orderBy|default:''}" />
        <input type="hidden" name="order_dir" id="order_dir" value="{$gui->orderDir|default:'ASC'}" />
        <div class="filter_panel">
            <div class="filter-row">
                <div class="filter_column">
                    <label for="project_id">{lang_get s='test_project'}</label>
                    <select name="project_id" id="project_id" onchange="refreshPage()">
                        <option value="0">{lang_get s='select_project'}</option>
                        {foreach from=$gui->testprojects item=project}
                            <option value="{$project.id}" {if $gui->selectedProject == $project.id}selected{/if}>{$project.name|escape}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="filter_column">
                    <label for="testplan_id">{lang_get s='test_plan'}</label>
                    <select name="testplan_id" id="testplan_id" onchange="updatePlans()">
                        <option value="0">{lang_get s='all_testplans'}</option>
                        {foreach from=$gui->testplans item=plan}
                            <option value="{$plan.id}" {if $gui->selectedPlan == $plan.id}selected{/if}>{$plan.name|escape}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="filter_column">
                    <label for="build_id">{lang_get s='build'}</label>
                    <select name="build_id" id="build_id" onchange="refreshPage()">
                        <option value="0">{lang_get s='all_builds'}</option>
                        {foreach from=$gui->builds item=build}
                            <option value="{$build.id}" {if $gui->selectedBuild == $build.id}selected{/if}>{$build.name|escape}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter_column">
                    <label for="status">{lang_get s='status'}</label>
                    <select name="status" id="status" onchange="refreshPage()">
                        <option value="">{lang_get s='all_statuses'}</option>
                        {foreach from=$gui->statusOptions item=opt key=key}
                            <option value="{$key}" {if $gui->selectedStatus == $key}selected{/if}>{$opt}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="filter_column">
                    <label for="start_date">{lang_get s='start_date'}</label>
                    <input type="date" id="start_date" name="start_date" value="{$gui->startDate}" onchange="refreshPage()">
                </div>
                <div class="filter_column">
                    <label for="end_date">{lang_get s='end_date'}</label>
                    <input type="date" id="end_date" name="end_date" value="{$gui->endDate}" onchange="refreshPage()">
                </div>
                <div class="filter_column buttons">
                    <input type="button" value="{lang_get s='reset_filters'}" onclick="clearFilters()" style="background-color: #8ce5a1; border-color: #28a745; color: #000000; font-weight: bold;">
                </div>
                <div class="filter_column buttons">
                    <input type="button" value="Refresh Hierarchy Paths" onclick="refreshHierarchyPaths()" style="background-color: #8ce5a1; border-color: #28a745; color: #000000; font-weight: bold;">
                    <div id="refresh_status"></div>
                </div>
                <div class="filter_column buttons">
                    <input type="button" value="Export to Excel" onclick="exportToExcel()" style="background-color: #4aa0e6; border-color: #007bff; color: #dadada; font-weight: bold;background:#224a7a !important">
                </div>
            </div>
        </div>
    </form>

    <div class="dashboard">
        <!-- Execution Overview -->
        <div class="dashboard_element execution_overview">
            <div class="dashboard_title">{lang_get s='execution_overview'}</div>
            <div class="dashboard_content">
                <div class="stats_grid">
                    <div class="stat_box total">
                        <div class="stat_number">{$gui->totalExecutions}</div>
                        <div class="stat_label">Total Executions</div>
                    </div>
                    <div class="stat_box passed">
                     <!--   <div class="stat_icon">✓</div> -->
                        <div class="stat_number">{$gui->statusCounts.p}</div>
                        <div class="stat_label">Passed</div>
                    </div>
                    <div class="stat_box failed">
                     <!--   <div class="stat_icon">✗</div> -->
                        <div class="stat_number">{$gui->statusCounts.f}</div>
                        <div class="stat_label">Failed</div>
                    </div>
                    <div class="stat_box blocked">
                     <!--   <div class="stat_icon">⊘</div>-->
                        <div class="stat_number">{$gui->statusCounts.b}</div>
                        <div class="stat_label">Blocked</div>
                    </div>
                    <div class="stat_box not_run">
                        <div class="stat_number">{$gui->statusCounts.n}</div>
                        <div class="stat_label">Not Run</div>
                    </div>
                    <div class="stat_box pass_rate">
                        <div class="stat_number">{$gui->passRate}%</div>
                        <div class="stat_label">Pass Rate</div>
                    </div>
                </div>
                
                {if $gui->totalExecutions > 0}
                <div style="clear:both; margin-top: 15px;">
                    <div class="progress-bar">
                        {assign var="passedPercent" value=($gui->statusCounts.p / $gui->totalExecutions * 100)|round:2}
                        {assign var="failedPercent" value=($gui->statusCounts.f / $gui->totalExecutions * 100)|round:2}
                        {assign var="blockedPercent" value=($gui->statusCounts.b / $gui->totalExecutions * 100)|round:2}
                        
                        <div class="bar passed-bar" style="width:{$passedPercent}%"></div>
                        <div class="bar failed-bar" style="width:{$failedPercent}%"></div>
                        <div class="bar blocked-bar" style="width:{$blockedPercent}%"></div>
                    </div>
                </div>
                {/if}
            </div>
        </div>

        <!-- Top Testers -->
        <div class="dashboard_element top_testers">
            <div class="dashboard_title">{lang_get s='top_testers'}</div>
            <div class="dashboard_content">
                <table class="simple">
                    <tr>
                        <th onclick="sortBy('tester_name')" style="cursor:pointer">
                            {lang_get s='tester'}
                            {if $gui->orderBy=='tester_name'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                        </th>
                        <th onclick="sortBy('tester_count')" style="cursor:pointer">
                            {lang_get s='executions'}
                            {if $gui->orderBy=='tester_count'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                        </th>
                    </tr>
                    {foreach from=$gui->testerCounts key=id item=tester}
                        <tr>
                            <td>{$tester.name|escape}</td>
                            <td>{$tester.count}</td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>

        <!-- Test Suite Progress -->
        <div class="dashboard_element test_suite_progress">
            <div class="dashboard_title">{lang_get s='test_suite_progress'}</div>
            <div class="dashboard_content">
                <table class="simple">
                    <tr>
                        <th>{lang_get s='test_suite'}</th>
                        <th>{lang_get s='suite_path'}</th>
                        <th>{lang_get s='total'}</th>
                        <th>{lang_get s='passed'}</th>
                        <th>{lang_get s='failed'}</th>
                        <th>{lang_get s='blocked'}</th>
                        <th>{lang_get s='not_run'}</th>
                        <th>{lang_get s='pass_rate'}</th>
                    </tr>
                    {foreach from=$gui->suiteCounts key=id item=suite}
                        <tr>
                            <td>{$suite.name|escape}</td>
                            <td>{$suite.path|escape}</td>
                            <td>{$suite.count}</td>
                            <td class="passed">{$suite.statuses.p}</td>
                            <td class="failed">{$suite.statuses.f}</td>
                            <td class="blocked">{$suite.statuses.b}</td>
                            <td class="not_run">{$suite.statuses.n}</td>
                            <td>
                                {if $suite.count > 0}
                                    {math equation="(p/t)*100" p=$suite.statuses.p t=$suite.count format="%.2f"}%
                                {else}
                                    0%
                                {/if}
                            </td>
                          </tr>
                    {/foreach}
                </table>
            </div>
        </div>

    </div>
    
    <!-- Path Hierarchy Metrics -->
    <div class="full_width_container">
        <h2>{lang_get s='path_hierarchy_metrics'}</h2>
        <div class="scrollable_metrics">
            {if isset($gui->pathDetails) && $gui->pathDetails|@count > 0}
                <table class="simple full_width">
                        <tr>
                            <th>{lang_get s='test_path'}</th>
                            <th>{lang_get s='testcase_count'}</th>
                            <th>{lang_get s='passed'}</th>
                            <th>{lang_get s='failed'}</th>
                            <th>{lang_get s='blocked'}</th>
                            <th>{lang_get s='not_run'}</th>
                            <th title="Pass/(Pass+Failed)">{lang_get s='pass_rate'}</th>
                            <th title="Fail/(Pass+Failed)">{lang_get s='fail_rate'}</th>
                            <th title="Blocked/Total Test Cases">{lang_get s='block_rate'}</th>
                            <th title="Pending/(Total-Blocked)">{lang_get s='pending_rate'}</th>
                        </tr>
                        {foreach from=$gui->pathDetails item=path}
                            <tr>
                                <td>{$path.full_path|escape}</td>
                                <td>{$path.testcase_count}</td>
                                <td class="passed">{$path.passed_count}</td>
                                <td class="failed">{$path.failed_count}</td>
                                <td class="blocked">{$path.blocked_count}</td>
                                <td class="not_run">{$path.not_run_count}</td>
                                <td class="passed">{$path.pass_rate}%</td>
                                <td class="failed">{$path.fail_rate}%</td>
                                <td class="blocked">{$path.block_rate}%</td>
                                <td class="not_run">{$path.pending_rate}%</td>
                            </tr>
                        {/foreach}
                    </table>
                {else}
                    <div class="info_message">{lang_get s='no_path_data'}</div>
                {/if}
        </div>
    </div>
    </div>

    <!-- Hierarchical Execution Data -->
    <h2>{lang_get s='execution_details'}</h2>
    
    {if $gui->totalExecutions == 0}
        <div class="info_message">{lang_get s='no_data_available'}</div>
    {else}
        {foreach from=$gui->data key=projectId item=project}
            <div class="project_container">
                <div class="project_header" onclick="toggleDetails('project_{$projectId}')">
                    <span id="icon_project_{$projectId}" class="toggle-icon folder-icon" title="{lang_get s='expand_collapse'}"></span>
                    <span class="project_name">{$project.name}</span>
                </div>
                <div id="project_{$projectId}" class="project_content" style="display:none;">
                
                    {foreach from=$project.testplans key=planId item=plan}
                        <div class="plan_container">
                            <div class="plan_header" onclick="toggleDetails('plan_{$planId}')">
                                <span id="icon_plan_{$planId}" class="toggle-icon folder-icon" title="{lang_get s='expand_collapse'}"></span>
                                <span class="plan_name">{$plan.name}</span>
                            </div>
                            <div id="plan_{$planId}" class="plan_content" style="display:none;">
                            
                                {foreach from=$plan.suites key=suiteId item=suite}
                                    <div class="suite_container">
                                         <div class="suite_header" onclick="toggleDetails('suite_{$suiteId}_{$planId}')">
                                            <span id="icon_suite_{$suiteId}_{$planId}" class="toggle-icon folder-icon" title="{lang_get s='expand_collapse'}"></span>
                                            <span class="suite_name">{$suite.name|escape}</span>
                                            <span class="suite_path" style="margin-left: 10px; font-size: 0.9em; color: #666; font-style: italic;">{$suite.path|escape}</span>
                                        </div>
                                        <div id="suite_{$suiteId}_{$planId}" class="suite_content" style="display:none;">
                                            <table class="simple">
                                                <tr>
                                                    <th onclick="sortBy('tc_name')" style="cursor:pointer">
                                                        {lang_get s='test_case'}
                                                        {if $gui->orderBy=='tc_name'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                                                    </th>
                                                    <th onclick="sortBy('tc_version')" style="cursor:pointer">
                                                        {lang_get s='version'}
                                                        {if $gui->orderBy=='tc_version'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                                                    </th>
                                                    <th onclick="sortBy('build_name')" style="cursor:pointer">
                                                        {lang_get s='build'}
                                                        {if $gui->orderBy=='build_name'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                                                    </th>
                                                    <th onclick="sortBy('platform_name')" style="cursor:pointer">
                                                        {lang_get s='platform'}
                                                        {if $gui->orderBy=='platform_name'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                                                    </th>
                                                    <th onclick="sortBy('execution_status')" style="cursor:pointer">
                                                        {lang_get s='status'}
                                                        {if $gui->orderBy=='execution_status'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                                                    </th>
                                                    <th onclick="sortBy('tester_firstname')" style="cursor:pointer">
                                                        {lang_get s='tester'}
                                                        {if $gui->orderBy=='tester_firstname'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                                                    </th>
                                                    <th onclick="sortBy('execution_timestamp')" style="cursor:pointer">
                                                        {lang_get s='execution_ts'}
                                                        {if $gui->orderBy=='execution_timestamp'}{if $gui->orderDir=='ASC'} &#9650; {else} &#9660; {/if}{/if}
                                                    </th>
                                                </tr>
                                                
                                                {foreach from=$suite.executions item=execution}
                                                    <tr class="status_{$execution.execution_status}">
                                                        <td>{$execution.tc_name|escape}</td>
                                                        <td>{$execution.tc_version}</td>
                                                        <td>{$execution.build_notes|strip_tags|truncate:40}</td>
                                                        <td>{$execution.platform_notes|strip_tags|truncate:40}</td>
                                                        <td class="status_cell">
                                                            {if $execution.execution_status == 'p'}
                                                                <span class="passed">{lang_get s='test_status_passed'}</span>
                                                            {elseif $execution.execution_status == 'f'}
                                                                <span class="failed">{lang_get s='test_status_failed'}</span>
                                                            {elseif $execution.execution_status == 'b'}
                                                                <span class="blocked">{lang_get s='test_status_blocked'}</span>
                                                            {else}
                                                                <span class="not_run">{lang_get s='test_status_not_run'}</span>
                                                            {/if}
                                                        </td>
                                                        <td>{$execution.tester_firstname|escape} {$execution.tester_lastname|escape}</td>
                                                        <td>{$execution.execution_timestamp|escape}</td>
                                                    </tr>
                                                {/foreach}
                                            </table>
                                        </div>
                                    </div>
                                {/foreach}
                                
                            </div>
                        </div>
                    {/foreach}
                    
                </div>
            </div>
        {/foreach}
    {/if}
</div>

</body>
</html>