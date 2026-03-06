{* Template for Other Custom Reports *}
{config_load file="input_dimensions.conf" section="otherCustomReports"}
{include file="inc_head.tpl"}

<style type="text/css">
.custom-reports-container {
    display: flex;
    gap: 20px;
    margin: 15px;
}
.report-sidebar {
    width: 250px;
    padding: 10px;
    background: #f5f5f5;
    border-right: 1px solid #ddd;
}
.report-content {
    flex: 1;
    padding: 15px;
}
.report-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}
.report-menu li {
    margin-bottom: 5px;
}
.report-menu a {
    display: block;
    padding: 8px;
    color: #333;
    text-decoration: none;
}
.report-menu a:hover, .report-menu a.active {
    background: #e0e0e0;
    font-weight: bold;
}
.report-filters {
    margin-bottom: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
}
.report-results {
    min-height: 300px;
    border: 1px solid #ddd;
    padding: 15px;
    background: white;
}
.error {
    color: #d9534f;
}
</style>

<div class="workBack">
    <h1 class="title">{$gui->pageTitle}</h1>
    
    <div class="custom-reports-container">
        <div class="report-sidebar">
            <h2>Available Reports</h2>
            <ul class="report-menu">
                {foreach from=$gui->report_types key=id item=name}
                    <li>
                        <a href="javascript:void(0);" 
                           class="report-link {if $smarty.get.reportId == $id}active{/if}" 
                           data-report="{$id}">
                            {$name}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
        
        <div class="report-content">
            {if !empty($gui->testPlans)}
                <div class="report-filters">
                    <form method="get" id="report_filters">
                        <input type="hidden" name="reportId" id="report_id" value="">
                        <fieldset>
                            <legend>Report Filters</legend>
                            <div class="filter-row" style="margin-bottom: 15px;">
                                <label for="testplan_id">Test Plan:</label>
                                <select name="testplan_id" id="testplan_id" class="form-control" style="width: 70%;">
                                    {foreach from=$gui->testPlans key=planId item=planData}
                                        <option value="{$planId}" {if $planId == $gui->testplan_id}selected{/if}>
                                            {$planData.name|escape}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                        </fieldset>
                    </form>
                </div>
                
                <div class="report-results" id="report_results">
                    {if $gui->has_results}
                        {* Report content will be loaded here via AJAX *}
                        {if $gui->current_report == 'traceability_dump'}
                            {include file='other_custom_reports/traceability_dump.tpl'}
                        {/if}
                    {else}
                        <div class="alert alert-info">
                            Please select a report from the left menu.
                        </div>
                    {/if}
                </div>
            {else}
                <div class="alert alert-warning">
                    No active test plans found for the current project.
                </div>
            {/if}
        </div>
    </div>
</div>

{* Debug information - visible in page source *}
<!-- 
Debug Information:
Project ID: {$gui->tproject_id}
Test Plans: {$gui->testPlans|@count}
Selected Plan: {$gui->testplan_id}
-->

<script type="text/javascript">
$(document).ready(function() {
    // Debug info
    console.log('Page loaded');
    console.log('Test Plans:', {$gui->testPlans|@json_encode nofilter});
    console.log('Selected Plan ID:', {$gui->testplan_id});

    // Handle report link clicks
    $('.report-link').on('click', function(e) {
        e.preventDefault();
        var reportId = $(this).data('report');
        loadReport(reportId);
    });

    // Handle test plan changes
    $('#testplan_id').on('change', function() {
        var activeReport = $('.report-link.active');
        if (activeReport.length) {
            loadReport(activeReport.data('report'));
        }
    });

    function loadReport(reportId) {
        if (!reportId) return;
        
        var testplanId = $('#testplan_id').val();
        if (!testplanId) {
            alert('Please select a test plan');
            return;
        }

        // Show loading
        $('#report_results').html('<div class="alert alert-info">Loading report... <i class="fa fa-spinner fa-spin"></i></div>');
        
        // Update active state
        $('.report-link').removeClass('active');
        $('.report-link[data-report="' + reportId + '"]').addClass('active');
        
        // Update URL
        var url = new URL(window.location);
        url.searchParams.set('reportId', reportId);
        url.searchParams.set('testplan_id', testplanId);
        window.history.pushState({}, '', url);

        // Load report via AJAX
        $.ajax({
            url: 'lib/execute/other_custom_reports.php',
            type: 'POST',
            data: {
                doAction: 'loadReport',
                reportId: reportId,
                testplan_id: testplanId,
                tproject_id: {$gui->tproject_id}
            },
            success: function(response) {
                $('#report_results').html(response);
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Error loading report: ' + (xhr.responseText || status);
                $('#report_results').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                console.error('Report load error:', error);
            }
        });
    }

    // Load report from URL if specified
    var urlParams = new URLSearchParams(window.location.search);
    var reportId = urlParams.get('reportId');
    var testplanId = urlParams.get('testplan_id');
    
    if (reportId && testplanId) {
        // Set the selected test plan
        $('#testplan_id').val(testplanId);
        // Load the report
        loadReport(reportId);
    } else if ($('.report-link').length > 0) {
        // Load the first report by default if none specified
        $('.report-link').first().trigger('click');
    }
});
</script>
