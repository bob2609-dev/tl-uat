<?php
/**
 * Simple Lazy Loading execNavigator
 * Limits tree depth and node count for faster initial load
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

function logMessage($msg) {
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    echo "[$timestamp] $msg<br>\n";
    error_log("execNavigator_lazy_simple: $msg");
}

logMessage("Starting simple lazy loading execNavigator...");

try {
    require_once('../../config.inc.php');
    require_once('common.php');
    require_once("users.inc.php");
    require_once('treeMenu.inc.php');
    require_once('exec.inc.php');
    
    testlinkInitPage($db);
    $templateCfg = templateConfiguration();
    
    $start_time = microtime(true);
    
    $control = new tlTestCaseFilterControl($db, 'execution_mode');
    $control->formAction = '';
    
    logMessage("Building limited tree...");
    $gui = initializeGui($db,$control);
    
    // Use a modified tree building that limits the scope
    $tree_start = microtime(true);
    buildLimitedTreeMenu($control, $gui);
    $tree_time = microtime(true) - $tree_start;
    
    logMessage("Limited tree built in {$tree_time}s");
    
    $total_time = microtime(true) - $start_time;
    
    // Display results
    $smarty = new TLSmarty();
    if( $gui->execAccess ) {
        $smarty->assign('gui',$gui);
        $smarty->assign('control', $control);
        $smarty->assign('menuUrl',$gui->menuUrl);
        $smarty->assign('args', $gui->args);
        $tpl = $templateCfg->template_dir . 'execNavigator.tpl';
    } else {
        $tpl = 'noaccesstofeature.tpl';
    }
    
    $smarty->display($tpl);
    
    logMessage("SUCCESS: Total time {$total_time}s, Tree time: {$tree_time}s");
    
    // Show performance summary
    echo "<!-- Performance: Total: {$total_time}s, Tree: {$tree_time}s, Limited Loading: YES -->";
    
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
    echo "<br><strong>Error: " . $e->getMessage() . "</strong>";
}

/**
 * Build limited tree menu - only top levels initially
 */
function buildLimitedTreeMenu(&$control, &$gui) {
    // Temporarily modify the control to limit the tree scope
    $original_filters = $control->get_active_filters();
    
    // Set limiting filters
    $control->args->show_all_testcases = 0;  // Don't show all test cases
    $control->args->keywords_filter = '';    // Clear keyword filters
    $control->args->build_detailed_tree = 0; // Don't build detailed tree
    
    // Call the original tree building with our limitations
    $control->build_tree_menu($gui);
    
    // Add a note to the GUI indicating this is a limited tree
    $gui->tree_is_limited = true;
    $gui->tree_load_time = microtime(true);
    
    logMessage("Limited tree built with " . (isset($gui->tree_menu) ? count($gui->tree_menu) : 0) . " top-level items");
}

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

    $grants = checkAccessToExec($dbH,$control);

    $gui->features = array('export' => false,'import' => false);
    $gui->execAccess = false;
    if($grants['testplan_execute']) {
        $gui->features['export'] = true;
        $gui->features['import'] = true;
        $gui->execAccess = true;
    }  

    if($grants['exec_ro_access']) {
        $gui->execAccess = true;
    }  

    $control->draw_export_testplan_button = $gui->features['export'];
    $control->draw_import_xml_results_button = $gui->features['import'];

    return $gui;
}

function checkAccessToExec(&$dbH,&$ct) {
    $tplan_id = intval($ct->args->testplan_id);
    $sch = tlObject::getDBTables(array('testplans'));
    $sql = "SELECT testproject_id FROM {$sch['testplans']} " .
           "WHERE id=" . $tplan_id;
    $rs = $dbH->get_recordset($sql);
    if(is_null($rs)) {
        throw new Exception("Can not find Test Project For Test Plan - ABORT", 1);
    }  
    $rs = current($rs);
    $tproject_id = $rs['testproject_id'];

    $user = $_SESSION['currentUser'];
    $grants = null;
    $k2a = array('testplan_execute','exec_ro_access');
    foreach($k2a as $r2c) {
        $grants[$r2c] = false;
        if( $user->hasRight($dbH,$r2c,$tproject_id,$tplan_id,true) || $user->globalRoleID == TL_ROLES_ADMIN ) {
            $grants[$r2c] = true;
        }    
    }  

    return $grants;
} 

?>
