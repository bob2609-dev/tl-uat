<?php
/**
 * Hybrid execNavigator - Lazy Loading + Caching
 * Combines the best of both approaches for maximum performance
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$cache_ttl = 300; // 5 minutes cache
$cache_dir = __DIR__ . '/hybrid_cache';
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}

function logMessage($msg) {
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    echo "[$timestamp] $msg<br>\n";
    error_log("execNavigator_hybrid: $msg");
}

function getCacheKey() {
    $user_id = $_SESSION['currentUser']->dbID;
    $tplan_id = intval($_REQUEST['testplan_id'] ?? 0);
    $filters = md5(serialize($_REQUEST));
    return "hybrid_{$user_id}_{$tplan_id}_{$filters}";
}

function getCachedData($cache_key) {
    global $cache_dir, $cache_ttl;
    $cache_file = $cache_dir . '/' . $cache_key . '.json';
    
    if (file_exists($cache_file)) {
        $cache_data = json_decode(file_get_contents($cache_file), true);
        if ($cache_data && (time() - $cache_data['timestamp']) < $cache_ttl) {
            return $cache_data;
        }
    }
    return null;
}

function setCachedData($cache_key, $data) {
    global $cache_dir;
    $cache_file = $cache_dir . '/' . $cache_key . '.json';
    
    $cache_data = array(
        'timestamp' => time(),
        'data' => $data
    );
    
    file_put_contents($cache_file, json_encode($cache_data));
}

logMessage("Starting hybrid execNavigator (Lazy + Cache)...");

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
    
    $cache_key = getCacheKey();
    $cached_data = getCachedData($cache_key);
    
    if ($cached_data) {
        logMessage("Cache HIT - using cached lazy tree");
        $gui = $cached_data['data']['gui'];
        $tree_time = 0;
        $cache_age = time() - $cached_data['timestamp'];
        logMessage("Cache age: {$cache_age}s");
        
        // Re-check rights after cache retrieval
        $grants = checkAccessToExec($db,$control);
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
    } else {
        logMessage("Cache MISS - building new lazy tree");
        
        // Apply lazy loading limits
        $_REQUEST['show_all_testcases'] = '0';
        $_REQUEST['keywords_filter'] = '';
        $_REQUEST['build_detailed_tree'] = '0';
        
        $gui = initializeGui($db,$control);
        
        // Build limited tree
        $tree_start = microtime(true);
        buildHybridTree($control, $gui);
        $tree_time = microtime(true) - $tree_start;
        
        logMessage("Hybrid tree built in {$tree_time}s");
        
        // Cache the result
        setCachedData($cache_key, array('gui' => $gui));
        logMessage("Hybrid tree cached");
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
    
    logMessage("SUCCESS: Total time {$total_time}s, Tree time: {$tree_time}s, Cache: " . ($cached_data ? "HIT" : "MISS"));
    
    // Show performance summary
    echo "<!-- Performance: Total: {$total_time}s, Tree: {$tree_time}s, Hybrid Mode: " . ($cached_data ? "CACHED" : "BUILT") . " -->";
    
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
    echo "<br><strong>Error: " . $e->getMessage() . "</strong>";
}

/**
 * Build hybrid tree - limited scope with lazy loading capability
 */
function buildHybridTree(&$control, &$gui) {
    // Use the original tree building but with our limits in place
    $control->build_tree_menu($gui);
    
    // Mark this as a hybrid tree for potential AJAX loading
    $gui->hybrid_mode = true;
    $gui->tree_build_time = microtime(true);
    
    // Add lazy loading metadata
    if (isset($gui->tree_menu)) {
        $gui->tree_metadata = array(
            'total_nodes' => count($gui->tree_menu),
            'build_time' => microtime(true) - $gui->tree_build_time,
            'lazy_capable' => true,
            'cache_enabled' => true
        );
    }
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
