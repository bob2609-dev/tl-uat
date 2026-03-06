{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource test_execution_summary_copy.tpl
*}

{include file="inc_head.tpl" openHead="yes"}
<script src="{$basehref}gui/javascript/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
{literal}
var currentPage = 1;
var currentFilters = {};

$(document).ready(function() {
    // Capture form submission and handle via AJAX
    $('#filter_form').on('submit', function(e) {
        e.preventDefault();
        currentPage = 1; // Reset to first page when filters change
        loadResultsViaAjax();
    });
    
    // Handle pagination clicks
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        if (page) {
            currentPage = page;
            loadResultsViaAjax();
            // Scroll to top of results
            $('html, body').animate({
                scrollTop: $('#results_container').offset().top - 100
            }, 200);
        }
    });
});

function loadResultsViaAjax() {
    var formData = $('#filter_form').serialize();
    formData += '&page=' + currentPage;
    formData += '&ajax=1'; // Flag to indicate AJAX request
    
    // Show loading indicator
    $('#results_container').html('<div class="loading">Loading results...</div>');
    
    $.ajax({
        url: window.location.pathname,
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#results_container').html(response);
        },
        error: function() {
            $('#results_container').html('<div class="error">Error loading results. Please try again.</div>');
        }
    });
}

function refreshPage() {
    loadResultsViaAjax();
}

function clearFilters() {
    document.getElementById('project_id').value = 0;
    document.getElementById('testplan_id').value = 0;
    document.getElementById('build_id').value = 0;
    document.getElementById('suite_id').value = 0;
    document.getElementById('status').value = '';
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value = '';
    loadResultsViaAjax();
}

function updatePlans() {
    document.getElementById('build_id').value = 0;
    loadResultsViaAjax();
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
    loadResultsViaAjax();
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

/* AJAX loading indicator */
.loading {
    text-align: center;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 5px;
    margin: 10px 0;
    font-weight: bold;
    color: #495057;
}

.loading:before {
    content: "";
    display: inline-block;
    width: 20px;
    height: 20px;
    margin-right: 10px;
    border: 3px solid rgba(0,0,0,0.2);
    border-radius: 50%;
    border-top-color: #007bff;
    animation: spin 1s ease-in-out infinite;
    vertical-align: middle;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.error {
    text-align: center;
    padding: 15px;
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    color: #721c24;
    margin: 10px 0;
}

/* Smooth transitions */
#results_container {
    transition: opacity 0.3s ease-in-out;
}

#results_container.loading-state {
    opacity: 0.6;
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
    background-color: #e9ecef;
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
    background-color: #edf2f7;
    border: 1px solid #d8e2ef;
    border-radius: 5px;
    margin-top: 5px;
}
.suite_header {
    background-color: #f5f7fa;
    border: 1px solid #e6ebf5;
    border-radius: 5px;
    margin-top: 5px;
    margin-left: 15px;
}

/* Add margin for nested suites */
.nested_suite_1 { margin-left: 15px; }
.nested_suite_2 { margin-left: 30px; }
.nested_suite_3 { margin-left: 45px; }
.nested_suite_4 { margin-left: 60px; }
.nested_suite_5 { margin-left: 75px; }
.nested_suite_6 { margin-left: 90px; }
.nested_suite_7 { margin-left: 105px; }
.nested_suite_8 { margin-left: 120px; }

.suite_name, .plan_name, .project_name {
    flex-grow: 1;
}
.suite_count {
    background-color: #f0f0f0;
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
    margin-left: 10px;
}

.executions_table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
.executions_table th, .executions_table td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.executions_table th {
    background-color: #f5f5f5;
    font-weight: bold;
}

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
    margin-right: 10px;
    transition: transform 0.3s;
}
.toggle-icon.open {
    transform: rotate(90deg);
}

/* Breadcrumb path for suites */
.suite_path {
    font-size: 12px;
    color: #666;
    margin-left: 24px;
    margin-bottom: 5px;
}
.path_separator {
    margin: 0 5px;
}

/* Status styles */
tr.status_p td {
    background-color: #e0f8e9;
}
tr.status_f td {
    background-color: #fde2e4;
}
tr.status_b td {
    background-color: #fff3cd;
}
tr.status_n td {
    background-color: #f8f9fa;
}

/* Pagination styling */
.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 20px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.pagination-controls {
    display: flex;
    gap: 5px;
}

.page-link {
    display: inline-block;
    padding: 6px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #2c5b8e;
    text-decoration: none;
    background-color: #fff;
    transition: all 0.2s;
}

.page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    text-decoration: none;
}

.page-link.active {
    background-color: #2c5b8e;
    border-color: #2c5b8e;
    color: white;
}

.page-ellipsis {
    display: inline-block;
    padding: 6px 12px;
}

.pagination-info {
    color: #6c757d;
    font-size: 14px;
}

/* Info message styling */
.info_message {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 15px;
    font-size: 14px;
}

.status_cell .passed {
    background-color: #28a745;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-block;
    font-size: 12px;
}
.status_cell .failed {
    background-color: #dc3545;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-block;
    font-size: 12px;
}
.status_cell .blocked {
    background-color: #fff3cd;
    color: #856404;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-block;
    font-size: 12px;
}
.status_cell .not_run {
    background-color: #e9ecef;
    color: #495057;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-block;
    font-size: 12px;
}
</style>
{include file="inc_head.tpl" openHead="no"}

<body>
<h1 class="title">{$gui->pageTitle|escape}</h1>

{if $gui->warning_msg != ''}
    <div class="warning_message">{$gui->warning_msg}</div>
{/if}

{if $gui->limit_warning != ''}
    <div class="warning_message">{$gui->limit_warning}</div>
{/if}

{if $gui->info_msg != ''}
    <div class="info_message">{$gui->info_msg}</div>
{/if}

<div class="workBack">
<!-- Filter Form -->
<form method="post" action="" id="filter_form">
    <div class="filter_panel">
        <div class="filter-row">
            <div class="filter_column">
                <label for="project_id">{$labels.project}:</label><br>
                <select name="project_id" id="project_id" onchange="updatePlans()">
                    <option value="0">{$labels.all}</option>
                    {foreach from=$gui->projects item=project}
                        <option value="{$project.id}" {if $gui->selectedProject == $project.id}selected{/if}>{$project.name|escape}</option>
                    {/foreach}
                </select>
            </div>
            <div class="filter_column">
                <label for="testplan_id">{$labels.testplan}:</label><br>
                <select name="testplan_id" id="testplan_id" onchange="document.getElementById('build_id').value=0;refreshPage();">
                    <option value="0">{$labels.all}</option>
                    {foreach from=$gui->testplans item=plan}
                        <option value="{$plan.id}" {if $gui->selectedPlan == $plan.id}selected{/if}>{$plan.name|escape}</option>
                    {/foreach}
                </select>
            </div>
            <div class="filter_column">
                <label for="build_id">{$labels.build}:</label><br>
                <select name="build_id" id="build_id" onchange="refreshPage()">
                    <option value="0">{$labels.all}</option>
                    {foreach from=$gui->builds item=build}
                        <option value="{$build.id}" {if $gui->selectedBuild == $build.id}selected{/if}>{$build.name|escape}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        
        <div class="filter-row">
            <div class="filter_column">
                <label for="suite_id">{$labels.testsuite}:</label><br>
                <select name="suite_id" id="suite_id" onchange="refreshPage()">
                    <option value="0">{$labels.all}</option>
                    {foreach from=$gui->suites item=suite}
                        {assign var="indent" value=""}
                        {foreach from=$suite.path_names item=path_segment name=path_segments}
                            {if !$smarty.foreach.path_segments.last}
                                {assign var="indent" value="$indent&nbsp;&nbsp;&nbsp;&nbsp;"}
                            {/if}
                        {/foreach}
                        <option value="{$suite.id}" {if $gui->selectedSuite == $suite.id}selected{/if}>{$indent}{$suite.name|escape}</option>
                    {/foreach}
                </select>
            </div>
            <div class="filter_column">
                <label for="status">{$labels.status}:</label><br>
                <select name="status" id="status" onchange="refreshPage()">
                    <option value="">{$labels.all}</option>
                    <option value="p" {if $gui->selectedStatus == 'p'}selected{/if}>{$labels.passed}</option>
                    <option value="f" {if $gui->selectedStatus == 'f'}selected{/if}>{$labels.failed}</option>
                    <option value="b" {if $gui->selectedStatus == 'b'}selected{/if}>{$labels.blocked}</option>
                    <option value="n" {if $gui->selectedStatus == 'n'}selected{/if}>{$labels.not_run}</option>
                </select>
            </div>
        </div>
        
        <div class="filter-row">
            <div class="filter_column">
                <label for="start_date">{$labels.start_date}:</label><br>
                <input type="date" name="start_date" id="start_date" value="{$gui->startDate}" onchange="refreshPage()">
            </div>
            <div class="filter_column">
                <label for="end_date">{$labels.end_date}:</label><br>
                <input type="date" name="end_date" id="end_date" value="{$gui->endDate}" onchange="refreshPage()">
            </div>
            <div class="filter_column buttons">
                <label>&nbsp;</label><br>
                <input type="button" value="{$labels.btn_apply}" onclick="refreshPage()">
                <input type="button" value="{$labels.btn_reset}" onclick="clearFilters()">
            </div>
        </div>
    </div>
    <input type="hidden" name="order_by" id="order_by" value="{$gui->order_by|escape}">
    <input type="hidden" name="order_dir" id="order_dir" value="{$gui->order_dir|escape}">
</form>

<!-- Dashboard with Summary Statistics -->
<div class="dashboard">
    <div class="dashboard_element execution_overview">
        <div class="dashboard_title">{$labels.execution_overview}</div>
        <div class="dashboard_content">
            <div class="stats_grid">
                <div class="stat_box total">
                    <div class="stat_icon">📊</div>
                    <div class="stat_number">{$gui->totalExecutions}</div>
                    <div class="stat_label">{$labels.total_executions}</div>
                </div>
                <div class="stat_box passed">
                    <div class="stat_icon">✅</div>
                    <div class="stat_number">{$gui->statusCounts.p}</div>
                    <div class="stat_label">{$labels.passed}</div>
                </div>
                <div class="stat_box failed">
                    <div class="stat_icon">❌</div>
                    <div class="stat_number">{$gui->statusCounts.f}</div>
                    <div class="stat_label">{$labels.failed}</div>
                </div>
            </div>
            <div class="progress-bar">
                {assign var="passedPercent" value=$gui->statusCounts.p/$gui->totalExecutions*100}
                {assign var="failedPercent" value=$gui->statusCounts.f/$gui->totalExecutions*100}
                {assign var="blockedPercent" value=$gui->statusCounts.b/$gui->totalExecutions*100}
                {assign var="notRunPercent" value=$gui->statusCounts.n/$gui->totalExecutions*100}
                <div class="bar passed-bar" style="width:{$passedPercent}%"></div>
                <div class="bar failed-bar" style="width:{$failedPercent}%"></div>
                <div class="bar blocked-bar" style="width:{$blockedPercent}%"></div>
                <div class="bar not-run-bar" style="width:{$notRunPercent}%"></div>
            </div>
        </div>
    </div>
    
    <div class="dashboard_element top_testers">
        <div class="dashboard_title">{$labels.top_testers}</div>
        <div class="dashboard_content">
            {if $gui->testerCounts|@count > 0}
                <table class="simple">
                    <tr>
                        <th>{$labels.tester}</th>
                        <th>{$labels.executions}</th>
                    </tr>
                    {foreach from=$gui->testerCounts key=testerId item=tester name=testers}
                        {if $smarty.foreach.testers.index < 5}
                        <tr>
                            <td>{$tester.name|escape}</td>
                            <td>{$tester.count}</td>
                        </tr>
                        {/if}
                    {/foreach}
                </table>
            {else}
                <p>{$labels.no_data}</p>
            {/if}
        </div>
    </div>
    
    <div class="dashboard_element test_suite_progress">
        <div class="dashboard_title">{$labels.test_suite_progress}</div>
        <div class="dashboard_content">
            {if $gui->suiteCounts|@count > 0}
                <table class="simple">
                    <tr>
                        <th>{$labels.testsuite}</th>
                        <th>{$labels.progress}</th>
                    </tr>
                    {foreach from=$gui->suiteCounts key=suiteId item=suite name=suites}
                        {if $smarty.foreach.suites.index < 5}
                        <tr>
                            <td>{$suite.name|escape}</td>
                            <td>
                                <div class="progress-bar">
                                    {assign var="passedPercent" value=$suite.statuses.p/$suite.count*100}
                                    {assign var="failedPercent" value=$suite.statuses.f/$suite.count*100}
                                    {assign var="blockedPercent" value=$suite.statuses.b/$suite.count*100}
                                    {assign var="notRunPercent" value=$suite.statuses.n/$suite.count*100}
                                    <div class="bar passed-bar" style="width:{$passedPercent}%"></div>
                                    <div class="bar failed-bar" style="width:{$failedPercent}%"></div>
                                    <div class="bar blocked-bar" style="width:{$blockedPercent}%"></div>
                                    <div class="bar not-run-bar" style="width:{$notRunPercent}%"></div>
                                </div>
                            </td>
                        </tr>
                        {/if}
                    {/foreach}
                </table>
            {else}
                <p>{$labels.no_data}</p>
            {/if}
        </div>
    </div>
</div>

<!-- Hierarchical Execution Results -->
<h2>{$labels.hierarchical_results}</h2>

{if $gui->data|@count > 0}
    {foreach from=$gui->data key=projectId item=project}
        <div class="project_container">
            <div class="project_header" onclick="toggleDetails('project_{$projectId}')">
                <span class="toggle-icon" id="icon_project_{$projectId}">▶</span>
                <span class="project_name">{$project.name|escape}</span>
            </div>
            <div id="project_{$projectId}" style="display:none;">
                {foreach from=$project.testplans key=testplanId item=testplan}
                    <div class="plan_container">
                        <div class="plan_header" onclick="toggleDetails('testplan_{$testplanId}')">
                            <span class="toggle-icon" id="icon_testplan_{$testplanId}">▶</span>
                            <span class="plan_name">{$testplan.name|escape}</span>
                        </div>
                        <div id="testplan_{$testplanId}" style="display:none;">
                            {foreach from=$testplan.suites key=suiteId item=suite}
                                <div class="suite_container nested_suite_{$suite.path|@count - 1}">
                                    <div class="suite_header" onclick="toggleDetails('suite_{$suiteId}_{$testplanId}')">
                                        <span class="toggle-icon" id="icon_suite_{$suiteId}_{$testplanId}">▶</span>
                                        <span class="suite_name">{$suite.name|escape}</span>
                                        <span class="suite_count">{$suite.executions|@count}</span>
                                    </div>
                                    
                                    <!-- Display the suite path from our path-based view -->
                                    <div class="path_display">
                                        {assign var="fullPath" value=$suite.path|escape}
                                        {assign var="pathSegments" value="|"|explode:$fullPath|replace:' > ':'|'}
                                        {foreach from=$pathSegments item=segment name=segments}
                                            {if $segment != ""}
                                                <span class="path_segment">{$segment}</span>
                                                {if !$smarty.foreach.segments.last}<span class="path_separator">&gt;</span>{/if}
                                            {/if}
                                        {/foreach}
                                    </div>
                                    
                                    <div id="suite_{$suiteId}_{$testplanId}" style="display:none;">
                                        <table class="executions_table">
                                            <tr>
                                                <th>{$labels.testcase}</th>
                                                <th>{$labels.version}</th>
                                                <th>{$labels.status}</th>
                                                <th>{$labels.build}</th>
                                                <th>{$labels.tester}</th>
                                                <th>{$labels.execution_ts}</th>
                                            </tr>
                                            {foreach from=$suite.executions item=execution}
                                                <tr class="status_{$execution.execution_status}">
                                                    <td>{$execution.tc_name|escape}</td>
                                                    <td>{$execution.tc_version}</td>
                                                    <td class="status_cell">
                                                        {if $execution.execution_status == 'p'}
                                                            <span class="passed">{$labels.passed}</span>
                                                        {elseif $execution.execution_status == 'f'}
                                                            <span class="failed">{$labels.failed}</span>
                                                        {elseif $execution.execution_status == 'b'}
                                                            <span class="blocked">{$labels.blocked}</span>
                                                        {elseif $execution.execution_status == 'n'}
                                                            <span class="not_run">{$labels.not_run}</span>
                                                        {/if}
                                                    </td>
                                                    <td>{$execution.build_name|escape}</td>
                                                    <td>
                                                        {if $execution.tester_firstname != '' && $execution.tester_lastname != ''}
                                                            {$execution.tester_firstname|escape} {$execution.tester_lastname|escape}
                                                        {elseif $execution.tester_login != ''}
                                                            {$execution.tester_login|escape}
                                                        {else}
                                                            -
                                                        {/if}
                                                    </td>
                                                    <td>{$execution.execution_timestamp|date_format:$smarty.const.TL_TIMESTAMP_FORMAT}</td>
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
{else}
    <p>{$labels.no_executions_found}</p>
{/if}

<!-- Pagination Controls -->
<div id="results_container">
{if isset($gui->data) && !empty($gui->data)}
    {if isset($gui->page) && isset($gui->totalPages) && $gui->totalPages > 1}
    <div class="pagination">
        <div class="pagination-controls">
            {if $gui->page > 1}
                <a href="javascript:void(0);" data-page="1" class="page-link">«</a>
                <a href="javascript:void(0);" data-page="{$gui->page-1}" class="page-link">‹</a>
            {/if}
        
            {* Show page numbers with a limit of 5 links *}
            {assign var=startPage value=max(1, $gui->page-2)}
            {assign var=endPage value=min($gui->totalPages, $startPage+4)}
            
            {if $startPage > 1}
                <span class="page-ellipsis">...</span>
            {/if}
            
            {for $i=$startPage to $endPage}
                {if $i == $gui->page}
                    <span class="page-link active">{$i}</span>
                {else}
                    <a href="javascript:void(0);" data-page="{$i}" class="page-link">{$i}</a>
                {/if}
            {/for}
            
            {if $endPage < $gui->totalPages}
                <span class="page-ellipsis">...</span>
            {/if}
            
            {if $gui->page < $gui->totalPages}
                <a href="javascript:void(0);" data-page="{$gui->page+1}" class="page-link">›</a>
                <a href="javascript:void(0);" data-page="{$gui->totalPages}" class="page-link">»</a>
            {/if}
        </div>
        
        <div class="pagination-info">
            {$labels.page|default:"Page"} {$gui->page} {$labels.of|default:"of"} {$gui->totalPages} ({$gui->totalExecutionsCount} {$labels.executions|default:"executions"})
        </div>
    </div>
    {/if}
{/if}
</div>
