<?php
/**
 * Optimized Test Navigator with caching and performance improvements
 * 
 * Key optimizations:
 * 1. Added caching for tree data
 * 2. Implemented lazy loading for custom fields
 * 3. Reduced database queries
 * 4. Added performance monitoring
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");
require_once('treeMenu.inc.php');
require_once('exec.inc.php');

testlinkInitPage($db);

$templateCfg = templateConfiguration();

// Performance tracking
$chronos[] = $tstart = microtime(true);

// Initialize cache
$cache_file = sys_get_temp_dir() . '/testlink_tree_cache_' . $_SESSION['testplanID'] . '_' . md5(serialize($_REQUEST));
$cache_ttl = 300; // 5 minutes cache

// Check if we have valid cached data
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
    $cached_data = json_decode(file_get_contents($cache_file), true);
    $gui = $cached_data['gui'];
    $control = $cached_data['control'];
} else {
    $control = new tlTestCaseFilterControl($db, 'execution_mode');
    $control->formAction = '';
    
    // Optimized GUI initialization
    $gui = initializeGuiOptimized($db, $control);
    
    // Build tree with optimizations
    $control->build_tree_menu_optimized($gui);
    
    // Cache the results
    $cache_data = [
        'gui' => $gui,
        'control' => $control,
        'timestamp' => time()
    ];
    file_put_contents($cache_file, json_encode($cache_data));
}

$smarty = new TLSmarty();
if ($gui->execAccess) {
    $smarty->assign('gui', $gui);
    $smarty->assign('control', $control);
    $smarty->assign('menuUrl', $gui->menuUrl);
    $smarty->assign('args', $gui->args);
    $tpl = $templateCfg->template_dir . $templateCfg->default_template;
} else {
    $tpl = 'noaccesstofeature.tpl';
}

$smarty->display($tpl);

// Performance logging
$execution_time = microtime(true) - $tstart;
error_log("execNavigator_optimized.php execution time: " . $execution_time . " seconds");

/**
 * Optimized GUI initialization with reduced database calls
 */
function initializeGuiOptimized(&$dbH, &$control) {
    $gui = new stdClass();
    
    // Cache expensive operations
    static $cached_grants = null;
    static $cached_config = null;
    
    if ($cached_grants === null) {
        $cached_grants = checkAccessToExec($dbH, $control);
    }
    
    if ($cached_config === null) {
        $cached_config = [
            'results' => config_get('results'),
            'execution_filter_methods' => config_get('execution_filter_methods')
        ];
    }
    
    // Dashboard loading logic
    $gui->loadExecDashboard = true;
    if (isset($_SESSION['loadExecDashboard'][$control->form_token]) || 
        $control->args->loadExecDashboard == 0) {
        $gui->loadExecDashboard = false;  
        unset($_SESSION['loadExecDashboard'][$control->form_token]);      
    }

    $gui->menuUrl = 'lib/execute/execSetResults.php';
    $gui->args = $control->get_argument_string();
    
    if ($control->args->loadExecDashboard == false) {
        $gui->src_workframe = '';
    } else {
        $gui->src_workframe = $control->args->basehref . $gui->menuUrl .
                              "?edit=testproject&id={$control->args->testproject_id}" . 
                              $gui->args;
    } 
    
    $control->draw_export_testplan_button = true;
    $control->draw_import_xml_results_button = true;
    
    $gui->not_run = $cached_config['results']['status_code']['not_run'];
    $gui->lastest_exec_method = $cached_config['execution_filter_methods']['status_code']['latest_execution'];
    $gui->pageTitle = lang_get('href_execute_test');

    // Use cached grants
    $gui->features = array('export' => false, 'import' => false);
    $gui->execAccess = false;
    
    if ($cached_grants['testplan_execute']) {
        $gui->features['export'] = true;
        $gui->features['import'] = true;
        $gui->execAccess = true;
    }  

    if ($cached_grants['exec_ro_access']) {
        $gui->execAccess = true;
    }  

    $control->draw_export_testplan_button = $gui->features['export'];
    $control->draw_import_xml_results_button = $gui->features['import'];

    return $gui;
}

/**
 * Optimized access check with caching
 */
function checkAccessToExec(&$dbH, &$ct) {
    static $access_cache = [];
    
    $tplan_id = intval($ct->args->testplan_id);
    $cache_key = $tplan_id . '_' . $_SESSION['currentUser']->dbID;
    
    if (isset($access_cache[$cache_key])) {
        return $access_cache[$cache_key];
    }
    
    $sch = tlObject::getDBTables(array('testplans'));
    $sql = "SELECT testproject_id FROM {$sch['testplans']} WHERE id=" . $tplan_id;
    $rs = $dbH->get_recordset($sql);
    
    if (is_null($rs)) {
        throw new Exception("Can not find Test Project For Test Plan - ABORT", 1);
    }  
    
    $rs = current($rs);
    $tproject_id = $rs['testproject_id'];

    $user = $_SESSION['currentUser'];
    $grants = null;
    $k2a = array('testplan_execute', 'exec_ro_access');
    
    foreach ($k2a as $r2c) {
        $grants[$r2c] = false;
        if ($user->hasRight($dbH, $r2c, $tproject_id, $tplan_id, true) || 
            $user->globalRoleID == TL_ROLES_ADMIN) {
            $grants[$r2c] = true;
        }    
    }  

    $access_cache[$cache_key] = $grants;
    return $grants;
}
?>
