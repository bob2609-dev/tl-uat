{include file="inc_head.tpl" openHead="yes"}
<script type="text/javascript">
{literal}
function runReport(){var f=document.getElementById('filter_form');document.getElementById('run').value=1;f.submit();}
function clearFilters(){var f=document.getElementById('filter_form');document.getElementById('project_id').value=0;document.getElementById('testplan_id').value=0;document.getElementById('build_id').value=0;document.getElementById('status').value='';document.getElementById('execution_path').value='';document.getElementById('start_date').value='';document.getElementById('end_date').value='';document.getElementById('run').value=0;f.submit();}
function updatePlans(){var f=document.getElementById('filter_form');document.getElementById('build_id').value=0;document.getElementById('run').value=0;f.submit();}
function updateBuilds(){var f=document.getElementById('filter_form');document.getElementById('run').value=0;f.submit();}
function sortTable(columnIndex){var table=document.getElementById('suiteTable');var rows=Array.from(table.rows).slice(1);var ascending=table.getAttribute('data-sort-direction')!=='asc';rows.sort(function(a,b){var aVal=a.cells[columnIndex].textContent.trim();var bVal=b.cells[columnIndex].textContent.trim();if(!isNaN(aVal)&&!isNaN(bVal)){return ascending?aVal-bVal:bVal-aVal;}return ascending?aVal.localeCompare(bVal):bVal.localeCompare(aVal);});rows.forEach(function(row){table.appendChild(row);});table.setAttribute('data-sort-direction',ascending?'asc':'desc');var headers=table.querySelectorAll('th');headers.forEach(function(header,index){header.classList.remove('sort-asc','sort-desc');if(index===columnIndex){header.classList.add(ascending?'sort-asc':'sort-desc');}});}
function exportToCSV(){var project=document.getElementById('project_id').value;var testplan=document.getElementById('testplan_id').value;var build=document.getElementById('build_id').value;var status=document.getElementById('status').value;var executionPath=document.getElementById('execution_path').value;var startDate=document.getElementById('start_date').value;var endDate=document.getElementById('end_date').value;var url='../../lib/execute/suite_execution_summary_export_proc.php?';if(project)url+='project_id='+project+'&';if(testplan)url+='testplan_id='+testplan+'&';if(build)url+='build_id='+build+'&';if(status)url+='status='+status+'&';if(executionPath)url+='execution_path='+encodeURIComponent(executionPath)+'&';if(startDate)url+='start_date='+startDate+'&';if(endDate)url+='end_date='+endDate+'&';window.location.href=url;}
{/literal}
</script>
<style type="text/css">
{literal}
.filter_panel{background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:16px;margin-bottom:16px}
.filter_table{width:100%;border-collapse:separate;border-spacing:12px 8px}
.filter_select,.filter_input{width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;font-size:13px;box-sizing:border-box}
.button_row{display:flex;gap:10px;justify-content:center;margin-top:10px}
.suite_table{width:100%;border-collapse:collapse;margin-top:16px;font-size:13px}
.suite_table th{background:#f2f2f2;color:#333;font-weight:bold;text-align:left;padding:10px 8px;border:1px solid #ddd;cursor:pointer}
.suite_table td{padding:8px;border:1px solid #ddd;vertical-align:middle}
.suite_table tr:nth-child(even){background:#fafafa}
.path_cell{font-family:monospace;font-size:12px;max-width:420px;word-break:break-word}
.rate_cell{text-align:right;font-weight:bold}
.passed{color:#155724;font-weight:bold}
.failed{color:#721c24;font-weight:bold}
.blocked{color:#856404;font-weight:bold}
.not_run{color:#383d41;font-weight:bold}
input.filter_button{padding:12px 24px;border-radius:6px;border:2px solid;cursor:pointer;font-weight:bold;font-size:14px;transition:all 0.3s ease;min-width:140px}
input.run_button{background-color:#28a745 !important;border-color:#28a745 !important;color:#fff !important}
input.run_button:hover{background-color:#218838 !important;border-color:#1e7e34 !important;transform:translateY(-1px);box-shadow:0 4px 8px rgba(0,0,0,0.2)}
input.reset_button{background-color:#6c757d !important;border-color:#6c757d !important;color:#fff !important}
input.reset_button:hover{background-color:#5a6268 !important;border-color:#545b62 !important;transform:translateY(-1px);box-shadow:0 4px 8px rgba(0,0,0,0.2)}
input.export_button{background-color:#007bff !important;border-color:#007bff !important;color:#fff !important}
input.export_button:hover{background-color:#0056b3 !important;border-color:#004085 !important;transform:translateY(-1px);box-shadow:0 4px 8px rgba(0,0,0,0.2)}
.summary_stats{background-color:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:20px;margin:20px 0}
.summary_title{font-size:16px;font-weight:bold;margin-bottom:15px;color:#333}
.stats_container{display:flex;justify-content:space-around;flex-wrap:wrap;gap:15px}
.stat_box{flex:1;min-width:150px;padding:20px;border-radius:8px;text-align:center;box-shadow:0 2px 4px rgba(0,0,0,0.1)}
.stat_number{font-size:32px;font-weight:bold;margin-bottom:5px}
.stat_label{font-size:12px;text-transform:uppercase;color:#666}
.stat_box.total{background-color:#e7f3ff;color:#004085}
.stat_box.passed{background-color:#d4edda;color:#155724}
.stat_box.failed{background-color:#f8d7da;color:#721c24}
.stat_box.blocked{background-color:#fff3cd;color:#856404}
.stat_box.not_run{background-color:#e2e3e5;color:#383d41}
{/literal}
</style>
{include file="inc_head.tpl" openHead="no"}
<body>
<h1 class="title">{$gui->pageTitle|escape}</h1>
{if $gui->warning_msg != ''}
    <div class="warning_message">{$gui->warning_msg}</div>
{/if}
<div class="workBack">
    <div class="filter_panel">
        <form method="post" id="filter_form" name="filter_form">
            <input type="hidden" id="run" name="run" value="{$gui->run|default:0}">
            <table class="filter_table">
                <tr>
                    <td>
                        <label for="project_id">{lang_get s='testproject'}</label>
                        <select name="project_id" id="project_id" onchange="updatePlans()" class="filter_select">
                            <option value="0">{lang_get s='all_testprojects'}</option>
                            {foreach from=$gui->testprojects item=project}
                                <option value="{$project.id}" {if $gui->selectedProject == $project.id}selected{/if}>{$project.name|escape}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td>
                        <label for="testplan_id">{lang_get s='testplan'}</label>
                        <select name="testplan_id" id="testplan_id" onchange="updateBuilds()" class="filter_select">
                            <option value="0">{lang_get s='all_testplans'}</option>
                            {foreach from=$gui->testplans item=plan}
                                <option value="{$plan.id}" {if $gui->selectedPlan == $plan.id}selected{/if}>{$plan.name|escape}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td>
                        <label for="build_id">{lang_get s='build'}</label>
                        <select name="build_id" id="build_id" class="filter_select">
                            <option value="0">{lang_get s='all_builds'}</option>
                            {foreach from=$gui->builds item=build}
                                <option value="{$build.id}" {if $gui->selectedBuild == $build.id}selected{/if}>{$build.name|escape}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="status">{lang_get s='status'}</label>
                        <select name="status" id="status" class="filter_select">
                            <option value="">{lang_get s='all_statuses'}</option>
                            {foreach from=$gui->statuses item=opt key=key}
                                <option value="{$key}" {if $gui->selectedStatus == $key}selected{/if}>{$opt}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td>
                        <label for="execution_path">Execution Path</label>
                        <input type="text" id="execution_path" name="execution_path" value="{$gui->selectedExecutionPath|escape}" placeholder="Filter by execution path..." class="filter_input"/>
                    </td>
                    <td>
                        <label for="start_date">{lang_get s='start_date'}</label>
                        <input type="date" id="start_date" name="start_date" value="{$gui->startDate}" class="filter_input"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="end_date">{lang_get s='end_date'}</label>
                        <input type="date" id="end_date" name="end_date" value="{$gui->endDate}" class="filter_input"/>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
            <div class="button_row">
                <input type="button" value="Run Report" onclick="runReport()" class="filter_button" style="background-color:#28a745;border-color:#28a745;color:#fff;font-weight:bold;background:#28a745 !important"/>
                <input type="button" value="{lang_get s='reset_filters'}" onclick="clearFilters()" class="filter_button" style="background-color:#6c757d;border-color:#6c757d;color:#fff;font-weight:bold;background:#6c757d !important"/>
                <input type="button" value="Export to CSV" onclick="exportToCSV()" class="filter_button" style="background-color:#007bff;border-color:#007bff;color:#fff;font-weight:bold;background:#007bff !important"/>
            </div>
        </form>
    </div>

    {if $gui->suiteData|@count > 0}
        <div class="summary_stats">
            <div class="summary_title">Overall Summary</div>
            <div class="stats_container">
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
        
        <table class="suite_table" id="suiteTable">
            <thead>
                <tr>
                    <th onclick="sortTable(0)">Test Path</th>
                    <th onclick="sortTable(1)">Overall Total</th>
                    <th onclick="sortTable(2)">Test Case Count</th>
                    <th onclick="sortTable(3)" class="passed">Passed</th>
                    <th onclick="sortTable(4)" class="failed">Failed</th>
                    <th onclick="sortTable(5)" class="blocked">Blocked</th>
                    <th onclick="sortTable(6)" class="not_run">Not Run</th>
                    <th onclick="sortTable(7)" class="passed">Pass Rate</th>
                    <th onclick="sortTable(8)" class="failed">Fail Rate</th>
                    <th onclick="sortTable(9)" class="blocked">Block Rate</th>
                    <th onclick="sortTable(10)" class="not_run">Pending Rate</th>
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
</body>
</html>
