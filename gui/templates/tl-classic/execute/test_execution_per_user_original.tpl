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
                        {foreach from=$gui->statuses key=key item=status}
                            <option value="{$key}" {if $gui->selectedStatus == $key}selected{/if}>{$status}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div class="filter_column">
                    <label for="user_id">{lang_get s='user'}</label>
                    <select name="user_id" id="user_id" onchange="refreshPage()">
                        <option value="0">{lang_get s='any'}</option>
                        {foreach from=$gui->users item=user}
                            <option value="{$user.id}" {if $gui->selectedUser == $user.id}selected{/if}>{$user.first|escape} {$user.last|escape} ({$user.login|escape})</option>
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
</div>
