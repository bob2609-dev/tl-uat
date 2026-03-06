<?php
/**
 * Fast execNavigator - Simple approach to reduce tree building time
 * Uses basic file caching without complex serialization
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simple cache configuration
$cache_file = __DIR__ . '/tree_menu_cache.txt';
$cache_ttl = 300; // 5 minutes

function logMessage($msg) {
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    echo "[$timestamp] $msg<br>\n";
    error_log("execNavigator_fast: $msg");
}

logMessage("Starting fast execNavigator...");

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
    
    // Check if we have a recent cache
    $use_cache = false;
    if (file_exists($cache_file)) {
        $cache_age = time() - filemtime($cache_file);
        if ($cache_age < $cache_ttl) {
            $use_cache = true;
            logMessage("Using cache (age: {$cache_age}s)");
        } else {
            logMessage("Cache expired (age: {$cache_age}s)");
        }
    } else {
        logMessage("No cache found");
    }
    
    if ($use_cache) {
        // Try to use cached tree data
        logMessage("Attempting to use cached tree...");
        $gui = initializeGui($db,$control);
        
        // Read cached tree menu
        $cached_data = file_get_contents($cache_file);
        if ($cached_data && strpos($cached_data, 'tree_menu=') !== false) {
            // Parse cached tree menu
            parse_str($cached_data, $cache_vars);
            if (isset($cache_vars['tree_menu'])) {
                $gui->tree_menu = unserialize(base64_decode($cache_vars['tree_menu']));
                logMessage("Successfully loaded cached tree");
                
                // Skip the expensive tree building
                $tree_time = 0;
            } else {
                logMessage("Cache corrupted, rebuilding...");
                $use_cache = false;
            }
        } else {
            logMessage("Invalid cache format, rebuilding...");
            $use_cache = false;
        }
    }
    
    if (!$use_cache) {
        // Build tree from scratch (this will be slow)
        logMessage("Building tree from scratch...");
        $gui = initializeGui($db,$control);
        
        $tree_start = microtime(true);
        $control->build_tree_menu($gui);
        $tree_time = microtime(true) - $tree_start;
        
        logMessage("Tree built in {$tree_time}s");
        
        // Cache the tree menu for next time
        if (isset($gui->tree_menu)) {
            $cache_data = 'tree_menu=' . base64_encode(serialize($gui->tree_menu));
            file_put_contents($cache_file, $cache_data);
            logMessage("Tree cached for future use");
        }
    }
    
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
    echo "<!-- Performance: Total: {$total_time}s, Tree: {$tree_time}s, Cache: " . ($use_cache ? "HIT" : "MISS") . " -->";
    
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
    echo "<br><strong>Error: " . $e->getMessage() . "</strong>";
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
