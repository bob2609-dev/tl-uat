<h3>Traceability Dump Report</h3>

{if !empty($gui->results)}
    <div class="table-responsive">
        <table class="simple">
            <thead>
                <tr>
                    <th>Test Case ID</th>
                    <th>Test Case Name</th>
                    <th>Status</th>
                    <th>Last Execution</th>
                    <th>Execution Notes</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$gui->results item=result}
                <tr>
                    <td>{$result.testcase_id|default:'N/A'}</td>
                    <td>{$result.testcase_name|default:'N/A'|escape}</td>
                    <td>{$result.status|default:'N/A'|escape}</td>
                    <td>{$result.last_execution|default:'N/A'|escape}</td>
                    <td>{$result.execution_notes|default:''|escape|nl2br}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    
    <div class="export-buttons" style="margin-top: 20px;">
        <button type="button" class="btn btn-primary" onclick="exportReport('csv')">
            <i class="fa fa-download"></i> Export to CSV
        </button>
        <button type="button" class="btn btn-success" onclick="exportReport('excel')" style="margin-left: 10px;">
            <i class="fa fa-file-excel-o"></i> Export to Excel
        </button>
    </div>
    
    <script type="text/javascript">
    function exportReport(format) {
        var testplanId = $('#testplan_id').val();
        if (!testplanId) {
            alert('Please select a test plan');
            return;
        }
        
        var url = 'lib/execute/other_custom_reports.php?doAction=export&reportId=traceability_dump&format=' + format + '&testplan_id=' + testplanId;
        window.open(url, '_blank');
    }
    </script>
{else}
    <div class="alert alert-info">
        No results found for the selected test plan.
    </div>
{/if}
