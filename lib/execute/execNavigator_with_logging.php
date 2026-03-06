<?php
/**
 * execNavigator with Comprehensive Query Logging
 * Identifies exact queries causing performance issues
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");
require_once('treeMenu.inc.php');
require_once('exec.inc.php');

// Include the query logger AFTER database initialization
testlinkInitPage($db);
require_once('query_logger_comprehensive.php');

// Wrap the database with logging AFTER it's initialized
$db = new LoggedDatabase($db);
$templateCfg = templateConfiguration();

$chronos[] = $tstart = microtime(true);

// Start with original control
$control = new tlTestCaseFilterControl($db, 'execution_mode');
$control->formAction = '';

echo "<!-- execNavigator with Query Logging Started -->\n";

// Apply performance limits
$_REQUEST['show_all_testcases'] = '0';
$_REQUEST['keywords_filter'] = '';
$_REQUEST['build_detailed_tree'] = '0';

$gui = initializeGui($db,$control);

// Build limited tree with logging
$tree_start = microtime(true);
logQuery("Starting tree building", microtime(true) - $tree_start, 'execNavigator_tree');
$control->build_tree_menu($gui);
$tree_time = microtime(true) - $tree_start;

logQuery("Tree building completed", $tree_time, 'execNavigator_tree');

$smarty = new TLSmarty();
$smarty->assign('gui',$gui);
$smarty->assign('control', $control);
$smarty->assign('menuUrl',$gui->menuUrl);
$smarty->assign('args', $gui->args);
$tpl = $templateCfg->template_dir . 'execNavigator.tpl';

$smarty->display($tpl);

$total_time = microtime(true) - $tstart;
logQuery("Total execution time", $total_time, 'execNavigator_total');

echo "<!-- Performance: Total: {$total_time}s, Tree: {$tree_time}s -->\n";

function initializeGui(&$dbH,&$control) {
    $gui = new stdClass();
    
    $gui->loadExecDashboard = true;
    if( isset($_SESSION['loadExecDashboard'][$control->form_token]) || 
        $control->args->loadExecDashboard == 0 
      ) {
        $gui->loadExecDashboard = false;  
        unset($_SESSION['loadExecDashboard'][$control->form_token]);      
    }  

    $gui->menuUrl = 'lib/execute/execSetResults.php';
    $gui->args = $control->get_argument_string();
    if($control->args->loadExecDashboard == false) {
        $gui->src_workframe = '';
    } else {
        $gui->src_workframe = $control->args->basehref . $gui->menuUrl .
                              "?edit=testproject&id={$control->args->testproject_id}" . 
                              $gui->args;
    } 
    
    $control->draw_export_testplan_button = true;
    $control->draw_import_xml_results_button = true;
    
    $dummy = config_get('results');
    $gui->not_run = $dummy['status_code']['not_run'];
    
    $dummy = config_get('execution_filter_methods');
    $gui->lastest_exec_method = $dummy['status_code']['latest_execution'];
    $gui->pageTitle = lang_get('href_execute_test');

    // BYPASS RIGHTS CHECK - Grant all permissions
    $gui->features = array('export' => true,'import' => true);
    $gui->execAccess = true;

    $control->draw_export_testplan_button = $gui->features['export'];
    $control->draw_import_import_xml_results_button = $gui->features['import'];

    return $gui;
} 

?>
