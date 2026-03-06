{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource test_execution_per_user.tpl
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
    document.getElementById('user_id').value = 0;
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

function exportToExcel() {
    var form = document.getElementById('filter_form');
    var exportInput = document.createElement('input');
    exportInput.type = 'hidden';
    exportInput.name = 'export_excel';
    exportInput.value = '1';
    form.appendChild(exportInput);
    form.submit();
    form.removeChild(exportInput);
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
.export_button {
    background-color: #28a745;
    color: #ffffff;
    border: 1px solid #1e7e34;
    padding: 8px 20px;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
}
.export_button:hover {
    background-color: #218838;
    border-color: #1e7e34;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* Dashboard Styles */
.dashboard {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin: 20px 0;
}

.dashboard_element {
    flex: 1;
    min-width: 300px;
    background-color: #ffffff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    overflow: hidden;
}

.dashboard_title {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 10px 15px;
    font-weight: bold;
}

.dashboard_content {
    padding: 15px;
}

.stats_grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.stat_box {
    flex: 1;
    min-width: 80px;
    text-align: center;
    padding: 10px;
    border-radius: 4px;
}

.stat_box.total {
    background-color: #e9ecef;
}

.stat_box.passed {
    background-color: #d4edda;
    color: #155724;
}

.stat_box.failed {
    background-color: #f8d7da;
    color: #721c24;
}

.stat_box.blocked {
    background-color: #fff3cd;
    color: #856404;
}

.stat_box.not_run {
    background-color: #e2e3e5;
    color: #383d41;
}

.stat_box.pass_rate {
    background-color: #cce5ff;
    color: #004085;
}

.stat_number {
    font-size: 24px;
    font-weight: bold;
}

.progress-bar {
    height: 20px;
    width: 100%;
    background-color: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
    display: flex;
}

.bar {
    height: 100%;
}

.passed-bar {
    background-color: #28a745;
}

.failed-bar {
    background-color: #dc3545;
}

.blocked-bar {
    background-color: #ffc107;
}

/* User execution results styling */
.user_execution_results {
    margin-top: 20px;
}

.user_section, .date_section {
    margin-bottom: 10px;
}

.user_header, .date_header {
    background-color: #f8f9fa;
    padding: 8px 12px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
}

.user_header:hover, .date_header:hover {
    background-color: #e9ecef;
}

.toggle_icon {
    display: inline-block;
    margin-right: 8px;
    transition: transform 0.2s;
}

.toggle_icon.open {
    transform: rotate(90deg);
}

.user_details, .date_details {
    padding: 0 10px;
    margin-left: 20px;
}

.execution_details {
    width: 100%;
    border-collapse: collapse;
}

.execution_details th {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 8px;
}

.execution_details td {
    border: 1px solid #dee2e6;
    padding: 8px;
}

tr.status_p {
    background-color: #d4edda;
}

tr.status_f {
    background-color: #f8d7da;
}

tr.status_b {
    background-color: #fff3cd;
}

tr.status_n {
    background-color: #e2e3e5;
}

.no_data {
    padding: 20px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
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
                        <option value="0">{lang_get s='select_testplan'}</option>
                        {foreach from=$gui->testplans item=plan}
                            <option value="{$plan.id}" {if $gui->selectedPlan == $plan.id}selected{/if}>{$plan.name|escape}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div class="filter_column">
                    <label for="build_id">{lang_get s='build'}</label>
                    <select name="build_id" id="build_id" onchange="refreshPage()">
                        <option value="0">{lang_get s='any'}</option>
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
                        <option value="">{lang_get s='all'}</option>
                        <option value="p" {if $gui->selectedStatus == 'p'}selected{/if}>{lang_get s='passed'}</option>
                        <option value="f" {if $gui->selectedStatus == 'f'}selected{/if}>{lang_get s='failed'}</option>
                        <option value="b" {if $gui->selectedStatus == 'b'}selected{/if}>{lang_get s='blocked'}</option>
                        <option value="n" {if $gui->selectedStatus == 'n'}selected{/if}>{lang_get s='not_run'}</option>
                    </select>
                </div>
                
                <div class="filter_column">
                    <label for="user_id">{lang_get s='user'}</label>
                    <select name="user_id" id="user_id" onchange="refreshPage()">
                        <option value="0">{lang_get s='all'}</option>
                        {foreach from=$gui->users key=id item=user}
                            <option value="{$id}" {if $gui->selectedUser == $id}selected{/if}>{$user->getDisplayName()|escape}</option>
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
            </div>
            <div class="filter-row">
                <div class="filter_column buttons">
                    <input type="button" value="{lang_get s='reset_filters'}" onclick="clearFilters()">
                </div>
                <div class="filter_column buttons">
                    <input type="button" class="export_button" value="{lang_get s='export_excel'}" onclick="exportToExcel()">
                </div>
            </div>
        </div>
    </form>

    <!-- Dashboard Overview -->
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
                        <div class="stat_number">{$gui->statusCounts.p}</div>
                        <div class="stat_label">Passed</div>
                    </div>
                    <div class="stat_box failed">
                        <div class="stat_number">{$gui->statusCounts.f}</div>
                        <div class="stat_label">Failed</div>
                    </div>
                    <div class="stat_box blocked">
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
                    {assign var="passedPercent" value=0}
                    {if $gui->totalExecutions > 0}
                        {assign var="passedPercent" value=$gui->statusCounts.p / $gui->totalExecutions * 100}
                    {/if}
                    
                    {assign var="failedPercent" value=0}
                    {if $gui->totalExecutions > 0}
                        {assign var="failedPercent" value=$gui->statusCounts.f / $gui->totalExecutions * 100}
                    {/if}
                    
                    {assign var="blockedPercent" value=0}
                    {if $gui->totalExecutions > 0}
                        {assign var="blockedPercent" value=$gui->statusCounts.b / $gui->totalExecutions * 100}
                    {/if}
                    
                    <div class="progress-bar">
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
                        <th>{lang_get s='user'}</th>
                        <th>{lang_get s='executions'}</th>
                        <th>{lang_get s='passed'}</th>
                        <th>{lang_get s='failed'}</th>
                    </tr>
                    {foreach from=$gui->userExecutions item=tester name=testers}
                        {if $smarty.foreach.testers.index < 5}
                            <tr>
                                <td>{$tester.name}</td>
                                <td>{$tester.total}</td>
                                <td>{$tester.statuses.p}</td>
                                <td>{$tester.statuses.f}</td>
                            </tr>
                        {/if}
                    {/foreach}
                </table>
            </div>
        </div>

        <!-- Daily Execution Summary -->
        <div class="dashboard_element test_suite_progress">
            <div class="dashboard_title">Daily Execution Summary</div>
            <div class="dashboard_content">
                <table class="simple">
                    <tr>
                        <th>{lang_get s='date'}</th>
                        <th>{lang_get s='total'}</th>
                        <th>{lang_get s='passed'}</th>
                        <th>{lang_get s='failed'}</th>
                        <th>{lang_get s='pass_rate'}</th>
                    </tr>
                    {foreach from=$gui->dailyExecutions key=date item=dayData name=days}
                        {if $smarty.foreach.days.index < 7}
                            {assign var="dayPassRate" value=0}
                            {if $dayData.total > 0}
                                {assign var="dayPassRate" value=$dayData.statuses.p / $dayData.total * 100}
                            {/if}
                            <tr>
                                <td>{$date}</td>
                                <td>{$dayData.total}</td>
                                <td>{$dayData.statuses.p}</td>
                                <td>{$dayData.statuses.f}</td>
                                <td>{$dayPassRate|string_format:"%.1f"}%</td>
                            </tr>
                        {/if}
                    {/foreach}
                </table>
            </div>
        </div>
    </div>

    {* Detailed Execution Results *}
    <h2>{lang_get s='detailed_execution_results'}</h2>

    <div class="user_execution_results">
    {if $gui->userExecutions|@count eq 0}
        <div class="no_data">{lang_get s='no_data_available'}</div>
    {else}
        {foreach from=$gui->userExecutions key=userId item=userData}
            <div class="user_section">
                <div class="user_header" onclick="toggleDetails('user_{$userId}')">
                    <span id="icon_user_{$userId}" class="toggle_icon">▶</span>
                    <span class="user_name">{$userData.name} ({$userData.total} executions)</span>
                </div>
                <div id="user_{$userId}" class="user_details" style="display:none;">
                    
                    {foreach from=$userData.days key=execDate item=dateData}
                        <div class="date_section">
                            <div class="date_header" onclick="toggleDetails('date_{$userId}_{$execDate|replace:'-':'_'}')">
                                <span id="icon_date_{$userId}_{$execDate|replace:'-':'_'}" class="toggle_icon">▶</span>
                                <span class="date_label">{$execDate} ({$dateData.total} executions)</span>
                            </div>
                            <div id="date_{$userId}_{$execDate|replace:'-':'_'}" class="date_details" style="display:none;">
                                <table class="simple execution_details">
                                    <tr>
                                        <th>{lang_get s='test_case'}</th>
                                        <th>{lang_get s='version'}</th>
                                        <th>{lang_get s='build'}</th>
                                        <th>{lang_get s='platform'}</th>
                                        <th>{lang_get s='execution_ts'}</th>
                                        <th>{lang_get s='status'}</th>
                                        <th>{lang_get s='notes'}</th>
                                    </tr>
                                    {foreach from=$dateData.executions item=execution}
                                        <tr class="status_{$execution.status}">
                                            <td>
                                                {if $execution.tcaseExternalId != ""}
                                                    {$execution.tcaseExternalId}:
                                                {/if}
                                                {$execution.tcaseName|escape}
                                            </td>
                                            <td>{$execution.tcaseVersionNumber}</td>
                                            <td>{$execution.buildName|escape}</td>
                                            <td>{$execution.platformName|escape}</td>
                                            <td>{$execution.execution_ts}</td>
                                            <td>{$gui->statuses[$execution.status]}</td>
                                            <td>
                                                {if $execution.notes|strip:"" != ""}
                                                    <a href="javascript:void(0);" onclick="alert('{$execution.notes|escape:'javascript'}')">View Notes</a>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </table>
                            </div>
                        </div>
                    {/foreach}
                    
                </div>
            </div>
        {/foreach}
    {/if}
    </div>
</div>
</body>
</html>
