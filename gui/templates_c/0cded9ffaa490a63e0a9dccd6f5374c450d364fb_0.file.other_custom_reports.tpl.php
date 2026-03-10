<?php
/* Smarty version 3.1.33, created on 2026-03-07 11:45:13
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\other_custom_reports.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69ac01b90ad494_53316456',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0cded9ffaa490a63e0a9dccd6f5374c450d364fb' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\other_custom_reports.tpl',
      1 => 1764593230,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:other_custom_reports/traceability_dump.tpl' => 1,
  ),
),false)) {
function content_69ac01b90ad494_53316456 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", "otherCustomReports", 0);
?>

<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

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
    <h1 class="title"><?php echo $_smarty_tpl->tpl_vars['gui']->value->pageTitle;?>
</h1>
    
    <div class="custom-reports-container">
        <div class="report-sidebar">
            <h2>Available Reports</h2>
            <ul class="report-menu">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->report_types, 'name', false, 'id');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['id']->value => $_smarty_tpl->tpl_vars['name']->value) {
?>
                    <li>
                        <a href="javascript:void(0);" 
                           class="report-link <?php if ($_GET['reportId'] == $_smarty_tpl->tpl_vars['id']->value) {?>active<?php }?>" 
                           data-report="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
">
                            <?php echo $_smarty_tpl->tpl_vars['name']->value;?>

                        </a>
                    </li>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </ul>
        </div>
        
        <div class="report-content">
            <?php if (!empty($_smarty_tpl->tpl_vars['gui']->value->testPlans)) {?>
                <div class="report-filters">
                    <form method="get" id="report_filters">
                        <input type="hidden" name="reportId" id="report_id" value="">
                        <fieldset>
                            <legend>Report Filters</legend>
                            <div class="filter-row" style="margin-bottom: 15px;">
                                <label for="testplan_id">Test Plan:</label>
                                <select name="testplan_id" id="testplan_id" class="form-control" style="width: 70%;">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->testPlans, 'planData', false, 'planId');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['planId']->value => $_smarty_tpl->tpl_vars['planData']->value) {
?>
                                        <option value="<?php echo $_smarty_tpl->tpl_vars['planId']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['planId']->value == $_smarty_tpl->tpl_vars['gui']->value->testplan_id) {?>selected<?php }?>>
                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['planData']->value['name'], ENT_QUOTES, 'UTF-8', true);?>

                                        </option>
                                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </select>
                            </div>
                        </fieldset>
                    </form>
                </div>
                
                <div class="report-results" id="report_results">
                    <?php if ($_smarty_tpl->tpl_vars['gui']->value->has_results) {?>
                                                <?php if ($_smarty_tpl->tpl_vars['gui']->value->current_report == 'traceability_dump') {?>
                            <?php $_smarty_tpl->_subTemplateRender('file:other_custom_reports/traceability_dump.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                        <?php }?>
                    <?php } else { ?>
                        <div class="alert alert-info">
                            Please select a report from the left menu.
                        </div>
                    <?php }?>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning">
                    No active test plans found for the current project.
                </div>
            <?php }?>
        </div>
    </div>
</div>

<!-- 
Debug Information:
Project ID: <?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>

Test Plans: <?php echo count($_smarty_tpl->tpl_vars['gui']->value->testPlans);?>

Selected Plan: <?php echo $_smarty_tpl->tpl_vars['gui']->value->testplan_id;?>

-->

<?php echo '<script'; ?>
 type="text/javascript">
$(document).ready(function() {
    // Debug info
    console.log('Page loaded');
    console.log('Test Plans:', <?php echo json_encode($_smarty_tpl->tpl_vars['gui']->value->testPlans);?>
);
    console.log('Selected Plan ID:', <?php echo $_smarty_tpl->tpl_vars['gui']->value->testplan_id;?>
);

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
                tproject_id: <?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>

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
<?php echo '</script'; ?>
>
<?php }
}
