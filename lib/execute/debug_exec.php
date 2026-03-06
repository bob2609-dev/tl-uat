<?php
/**
 * Ultra-basic debugging script
 * Test each component individually to find the 500 error source
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$log_file = __DIR__ . '/debug_log.txt';
file_put_contents($log_file, "=== Debug Session Started: " . date('Y-m-d H:i:s') . " ===\n");

function debug_log($message) {
    global $log_file;
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    echo "[$timestamp] $message<br>\n";
}

debug_log("Starting debug process...");

try {
    debug_log("Step 1: Including config.inc.php");
    require_once('../../config.inc.php');
    debug_log("✓ config.inc.php loaded successfully");
    
    debug_log("Step 2: Including common.php");
    require_once('common.php');
    debug_log("✓ common.php loaded successfully");
    
    debug_log("Step 3: Including users.inc.php");
    require_once("users.inc.php");
    debug_log("✓ users.inc.php loaded successfully");
    
    debug_log("Step 4: Including treeMenu.inc.php");
    require_once('treeMenu.inc.php');
    debug_log("✓ treeMenu.inc.php loaded successfully");
    
    debug_log("Step 5: Including exec.inc.php");
    require_once('exec.inc.php');
    debug_log("✓ exec.inc.php loaded successfully");
    
    debug_log("Step 6: Calling testlinkInitPage()");
    testlinkInitPage($db);
    debug_log("✓ testlinkInitPage() completed successfully");
    
    debug_log("Step 7: Creating templateCfg");
    $templateCfg = templateConfiguration();
    debug_log("✓ templateCfg created successfully");
    
    debug_log("Step 8: Starting timer");
    $chronos[] = $tstart = microtime(true);
    debug_log("✓ Timer started");
    
    debug_log("Step 9: Creating tlTestCaseFilterControl");
    $control = new tlTestCaseFilterControl($db, 'execution_mode');
    debug_log("✓ tlTestCaseFilterControl created successfully");
    
    debug_log("Step 10: Setting formAction");
    $control->formAction = '';
    debug_log("✓ formAction set successfully");
    
    debug_log("Step 11: Calling initializeGui()");
    $gui = initializeGui($db,$control);
    debug_log("✓ initializeGui() completed successfully");
    
    debug_log("Step 12: Calling build_tree_menu()");
    $tree_start = microtime(true);
    $control->build_tree_menu($gui);
    $tree_time = microtime(true) - $tree_start;
    debug_log("✓ build_tree_menu() completed in {$tree_time}s");
    
    debug_log("Step 13: Creating smarty object");
    $smarty = new TLSmarty();
    debug_log("✓ smarty object created");
    
    debug_log("Step 14: Checking execAccess");
    if( $gui->execAccess ) {
        debug_log("✓ execAccess is true");
        $smarty->assign('gui',$gui);
        $smarty->assign('control', $control);
        $smarty->assign('menuUrl',$gui->menuUrl);
        $smarty->assign('args', $gui->args);
        $tpl = $templateCfg->template_dir . $templateCfg->default_template;
        debug_log("✓ Template assigned: $tpl");
    } else {
        debug_log("✓ execAccess is false, using noaccesstofeature.tpl");
        $tpl = 'noaccesstofeature.tpl';
    }
    
    debug_log("Step 15: Displaying template");
    $smarty->display($tpl);
    debug_log("✓ Template displayed successfully");
    
    $total_time = microtime(true) - $tstart;
    debug_log("✓ SUCCESS: Total execution time: {$total_time}s");
    
} catch (Exception $e) {
    debug_log("❌ ERROR: " . $e->getMessage());
    debug_log("❌ ERROR in file: " . $e->getFile() . " line " . $e->getLine());
    debug_log("❌ Stack trace: " . $e->getTraceAsString());
    echo "<br><strong>Error occurred: " . $e->getMessage() . "</strong>";
} catch (Error $e) {
    debug_log("❌ FATAL ERROR: " . $e->getMessage());
    debug_log("❌ FATAL ERROR in file: " . $e->getFile() . " line " . $e->getLine());
    debug_log("❌ Stack trace: " . $e->getTraceAsString());
    echo "<br><strong>Fatal error occurred: " . $e->getMessage() . "</strong>";
}

debug_log("=== Debug Session Ended ===");

// Include the functions that were in the original
function initializeGui(&$dbH,&$control) {
    debug_log("initializeGui() called");
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

    debug_log("initializeGui() completed successfully");
    return $gui;
}

function checkAccessToExec(&$dbH,&$ct) {
    debug_log("checkAccessToExec() called");
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

    debug_log("checkAccessToExec() completed successfully");
    return $grants;
} 

?>
