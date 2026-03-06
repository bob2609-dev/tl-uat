<?php
/**
 * Performance Test Version - No Rights Checking
 * Bypasses all rights to test pure performance
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");
require_once('treeMenu.inc.php');
require_once('exec.inc.php');

testlinkInitPage($db);

$templateCfg = templateConfiguration();

$chronos[] = $tstart = microtime(true);

// Start with original control
$control = new tlTestCaseFilterControl($db, 'execution_mode');
$control->formAction = '';

echo "<!-- PERFORMANCE MODE: NO RIGHTS CHECKS - PURE PERFORMANCE TEST -->\n";

// Apply performance limits
$_REQUEST['show_all_testcases'] = '0';
$_REQUEST['keywords_filter'] = '';
$_REQUEST['build_detailed_tree'] = '0';

$gui = initializeGui($db,$control);

// Build limited tree
$tree_start = microtime(true);
$control->build_tree_menu($gui);
$tree_time = microtime(true) - $tree_start;

echo "<!-- Tree Building Time: {$tree_time}s -->\n";

$smarty = new TLSmarty();
$smarty->assign('gui',$gui);
$smarty->assign('control', $control);
$smarty->assign('menuUrl',$gui->menuUrl);
$smarty->assign('args', $gui->args);
$tpl = $templateCfg->template_dir . 'execNavigator.tpl';

$smarty->display($tpl);

$total_time = microtime(true) - $tstart;
echo "<!-- Performance: Total: {$total_time}s, Tree: {$tree_time}s, Rights: BYPASSED -->\n";

/**
 * Initialize GUI without rights restrictions
 */
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
    $control->draw_import_xml_results_button = $gui->features['import'];

    return $gui;
} 

?>
