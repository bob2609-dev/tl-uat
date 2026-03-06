<?php
/**
 * Cached execNavigator - Solves the 6+ second tree building performance issue
 * Uses intelligent caching to avoid rebuilding the tree on every request
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$cache_ttl = 300; // 5 minutes cache
$cache_dir = __DIR__ . '/tree_cache';
if (!is_dir($cache_dir)) {
    mkdir($cache_dir, 0755, true);
}

function getCacheKey() {
    $user_id = $_SESSION['currentUser']->dbID;
    $tplan_id = intval($_REQUEST['testplan_id'] ?? 0);
    $filters = md5(serialize($_REQUEST));
    return "tree_cache_{$user_id}_{$tplan_id}_{$filters}";
}

function getCachedTree($cache_key) {
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

function setCachedTree($cache_key, $gui, $control) {
    global $cache_dir;
    $cache_file = $cache_dir . '/' . $cache_key . '.json';
    
    $cache_data = [
        'timestamp' => time(),
        'gui' => serialize($gui),
        'control' => serialize($control),
        'tree_menu' => isset($gui->tree_menu) ? $gui->tree_menu : null,
        'testcases_to_show' => isset($gui->testcases_to_show) ? $gui->testcases_to_show : null
    ];
    
    file_put_contents($cache_file, json_encode($cache_data));
}

function logPerformance($message, $time = 0) {
    $log_file = __DIR__ . '/cache_performance.log';
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    $log_entry = "[$timestamp] $message";
    if ($time > 0) {
        $log_entry .= " - {$time}s";
    }
    file_put_contents($log_file, $log_entry . "\n", FILE_APPEND);
}

// Start timing
$start_time = microtime(true);
logPerformance("Cached execNavigator started");

require_once('../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");
require_once('treeMenu.inc.php');
require_once('exec.inc.php');

testlinkInitPage($db);

$templateCfg = templateConfiguration();

$chronos[] = $tstart = microtime(true);
$control = new tlTestCaseFilterControl($db, 'execution_mode');
$control->formAction = '';

// Try to get cached tree
$cache_key = getCacheKey();
$cached_data = getCachedTree($cache_key);

if ($cached_data) {
    logPerformance("Cache HIT - using cached tree");
    
    // Restore from cache
    $gui = unserialize($cached_data['gui']);
    $control = unserialize($cached_data['control']);
    
    // Restore tree-specific data
    if (isset($cached_data['tree_menu'])) {
        $gui->tree_menu = $cached_data['tree_menu'];
    }
    if (isset($cached_data['testcases_to_show'])) {
        $gui->testcases_to_show = $cached_data['testcases_to_show'];
    }
    
    $cache_age = time() - $cached_data['timestamp'];
    logPerformance("Cache age: {$cache_age} seconds");
    
} else {
    logPerformance("Cache MISS - building new tree");
    
    // Build tree from scratch
    $gui = initializeGui($db,$control);
    
    // Time the tree building
    $tree_start = microtime(true);
    $control->build_tree_menu($gui);
    $tree_time = microtime(true) - $tree_start;
    
    logPerformance("Tree built from scratch", $tree_time);
    
    // Cache the results
    setCachedTree($cache_key, $gui, $control);
    logPerformance("Tree cached for future use");
}

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

$total_time = microtime(true) - $start_time;
logPerformance("Total execution time", $total_time);

// Show performance info on screen
echo "<!-- Performance Info: Total time: {$total_time}s, Cache: " . ($cached_data ? "HIT" : "MISS") . " -->";

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
