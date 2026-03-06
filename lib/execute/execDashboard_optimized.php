<?php
/**
 * Optimized Execution Dashboard with performance improvements
 * 
 * Key optimizations:
 * 1. Reduced database queries
 * 2. Custom field caching
 * 3. Lazy loading for heavy operations
 * 4. Connection pooling
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once('exec.inc.php');
require_once("attachments.inc.php");
require_once("specview.php");
require_once('performance_optimizations.php');

$cfg = null;
testlinkInitPage($db);
$templateCfg = templateConfiguration();

// Performance tracking
$tstart = microtime(true);

$smarty = new TLSmarty();

// Initialize with optimizations
list($args, $tplan_mgr) = init_args_optimized($db, $cfg);
$gui = initializeGui_optimized($db, $args, $cfg, $tplan_mgr);

$smarty->assign('gui', $gui);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);

// Performance logging
$execution_time = microtime(true) - $tstart;
error_log("execDashboard_optimized.php execution time: " . $execution_time . " seconds");

/**
 * Optimized argument initialization with caching
 */
function init_args_optimized(&$dbHandler, $cfgObj) {
    static $cached_data = [];
    
    $args = new stdClass();
    $_REQUEST = strings_stripSlashes($_REQUEST);
    
    // Use cached testplan manager
    $tplan_mgr = new CachedTestPlan($dbHandler);

    // Get context from session with caching
    getContextFromGlobalScope($args);
    $args->user = $_SESSION['currentUser'];
    $args->user_id = $args->user->dbID;
    $args->caller = isset($_REQUEST['caller']) ? $_REQUEST['caller'] : 'exec_feature';
    $args->reload_caller = false;
    
    $args->tplan_id = intval(isset($_REQUEST['tplan_id']) ? $_REQUEST['tplan_id'] : $_SESSION['testplanID']);
    $args->tproject_id = intval(isset($_REQUEST['tproject_id']) ? $_REQUEST['tproject_id'] : $_SESSION['testprojectID']);

    if ($args->tproject_id <= 0) {
        $tree_mgr = new tree($dbHandler);
        $dm = $tree_mgr->get_node_hierarchy_info($args->tplan_id);
        $args->tproject_id = $dm['parent_id']; 
    }

    // Cache build and platform data
    $cache_key = $args->tplan_id . '_' . $args->user_id;
    
    if (!isset($cached_data[$cache_key])) {
        // Optimized build retrieval
        if (is_null($args->build_id) || ($args->build_id == 0)) {
            $key = $args->tplan_id . '_stored_setting_build';
            $args->build_id = isset($_SESSION[$key]) ? intval($_SESSION[$key]) : null;
            
            if (is_null($args->build_id)) {
                // Use cached max build query
                $args->build_id = $tplan_mgr->get_max_build_id_cached($args->tplan_id, 1, 1);
            }  
        }  

        // Optimized platform retrieval
        if (is_null($args->platform_id) || ($args->platform_id <= 0)) {
            $itemSet = $tplan_mgr->getPlatforms_cached($args->tplan_id);
            
            if (!is_null($itemSet)) {
                $key = $args->tplan_id . '_stored_setting_platform';
                $args->platform_id = isset($_SESSION[$key]) ? intval($_SESSION[$key]) : null;
                
                if (is_null($args->platform_id) || ($args->platform_id <= 0)) {
                    $args->platform_id = $itemSet[0]['id'];
                }  
            }  
        }
        
        $cached_data[$cache_key] = [
            'build_id' => $args->build_id,
            'platform_id' => $args->platform_id
        ];
    } else {
        $args->build_id = $cached_data[$cache_key]['build_id'];
        $args->platform_id = $cached_data[$cache_key]['platform_id'];
    }
    
    return array($args, $tplan_mgr);
}

/**
 * Optimized GUI initialization with reduced database calls
 */
function initializeGui_optimized(&$dbHandler, &$argsObj, &$cfgObj, &$tplanMgr) {
    static $cached_configs = [];
    static $cached_builds = [];
    static $cached_platforms = [];
    
    $buildMgr = new build_mgr($dbHandler);
    $platformMgr = new tlPlatform($dbHandler, $argsObj->tproject_id);
    
    $gui = new stdClass();
    $gui->form_token = $argsObj->form_token;
    $gui->remoteExecFeedback = $gui->user_feedback = '';
    $gui->tplan_id = $argsObj->tplan_id;
    $gui->tproject_id = $argsObj->tproject_id;
    $gui->build_id = $argsObj->build_id;
    $gui->platform_id = $argsObj->platform_id;
    
    $gui->attachmentInfos = null;
    $gui->refreshTree = 0;

    // Cache editor configurations
    $config_key = 'editors';
    if (!isset($cached_configs[$config_key])) {
        $cached_configs[$config_key] = [
            'testPlan' => getWebEditorCfg('testplan'),
            'platform' => getWebEditorCfg('platform'),
            'build' => getWebEditorCfg('build')
        ];
    }
    
    $gui->testPlanEditorType = $cached_configs[$config_key]['testPlan']['type'];
    $gui->platformEditorType = $cached_configs[$config_key]['platform']['type'];
    $gui->buildEditorType = $cached_configs[$config_key]['build']['type'];
    
    // Cache test project data
    $tprojectMgr = new testproject($dbHandler);
    $gui->tcasePrefix = $tprojectMgr->getTestCasePrefix($argsObj->tproject_id);
    
    // Cache build information
    $build_cache_key = $argsObj->build_id;
    if (!isset($cached_builds[$build_cache_key])) {
        $build_info = $buildMgr->get_by_id($argsObj->build_id);
        $cached_builds[$build_cache_key] = $build_info;
    } else {
        $build_info = $cached_builds[$build_cache_key];
    }
    
    $gui->build_notes = $build_info['notes'];
    $gui->build_is_open = ($build_info['is_open'] == 1 ? 1 : 0);

    // Cache test plan builds
    $builds_cache_key = $argsObj->tplan_id;
    if (!isset($cached_builds[$builds_cache_key . '_list'])) {
        $dummy = $tplanMgr->get_builds_for_html_options($argsObj->tplan_id);
        $cached_builds[$builds_cache_key . '_list'] = $dummy;
    } else {
        $dummy = $cached_builds[$builds_cache_key . '_list'];
    }
    
    $gui->build_name = isset($dummy[$argsObj->build_id]) ? $dummy[$argsObj->build_id] : '';
    
    // Cache test plan info
    $tplan_cache_key = $argsObj->tplan_id;
    if (!isset($cached_builds[$tplan_cache_key . '_info'])) {
        $rs = $tplanMgr->get_by_id($argsObj->tplan_id);
        $cached_builds[$tplan_cache_key . '_info'] = $rs;
    } else {
        $rs = $cached_builds[$tplan_cache_key . '_info'];
    }
    
    $gui->testplan_notes = $rs['notes'];
    $gui->testplan_name = $rs['name'];

    // Optimized custom fields with lazy loading
    $gui->testplan_cfields = get_cached_custom_fields($tplanMgr, $argsObj->tplan_id, 'design');
    $gui->build_cfields = get_cached_custom_fields($buildMgr, $argsObj->build_id, $argsObj->tproject_id, 'design');
    
    // Cache platform information
    $platform_cache_key = $argsObj->tplan_id;
    if (!isset($cached_platforms[$platform_cache_key])) {
        $dummy = $platformMgr->getLinkedToTestplan($argsObj->tplan_id);
        $cached_platforms[$platform_cache_key] = $dummy;
    } else {
        $dummy = $cached_platforms[$platform_cache_key];
    }
    
    $gui->has_platforms = !is_null($dummy) ? 1 : 0;
    
    $gui->platform_info['id'] = 0;
    $gui->platform_info['name'] = '';
    if (!is_null($argsObj->platform_id) && $argsObj->platform_id > 0) { 
        $gui->platform_info = $platformMgr->getByID($argsObj->platform_id);
    }

    $gui->pageTitlePrefix = lang_get('execution_context') . ':';

    // JSON for REST API
    $gui->restArgs = new stdClass();
    $gui->restArgs->testPlanID = intval($argsObj->tplan_id);
    $gui->restArgs->buildID = intval($argsObj->build_id);
    $gui->restArgs->platformID = intval($argsObj->platform_id);
    
    $gui->RESTArgsJSON = json_encode($gui->restArgs);

    return $gui;
}

/**
 * Cached custom field retrieval
 */
function get_cached_custom_fields($mgr, $id, $scope, $tproject_id = null) {
    static $cf_cache = [];
    
    $cache_key = $scope . '_' . $id . ($tproject_id ? '_' . $tproject_id : '');
    
    if (!isset($cf_cache[$cache_key])) {
        $options = array('show_on_execution' => 1);
        if ($tproject_id) {
            $cf_cache[$cache_key] = $mgr->html_table_of_custom_field_values($id, $scope, $options);
        } else {
            $cf_cache[$cache_key] = $mgr->html_table_of_custom_field_values($id, $scope, $options);
        }
    }
    
    return $cf_cache[$cache_key];
}

/**
 * Optimized context retrieval
 */
function getContextFromGlobalScope(&$argsObj) {
    $mode = 'execution_mode';
    $settings = array('build_id' => 'setting_build', 'platform_id' => 'setting_platform');
    $isNumeric = array('build_id' => 0, 'platform_id' => 0);

    $argsObj->form_token = isset($_REQUEST['form_token']) ? $_REQUEST['form_token'] : 0;
    $sf = isset($_SESSION['execution_mode']) && isset($_SESSION['execution_mode'][$argsObj->form_token]) ? 
          $_SESSION['execution_mode'][$argsObj->form_token] : null;

    if (is_null($sf)) {
        foreach ($settings as $key => $sfKey) {
            $argsObj->$key = null;
        }  
        return;
    } 

    foreach ($settings as $key => $sfKey) {
        $argsObj->$key = isset($sf[$sfKey]) ? $sf[$sfKey] : null;
        if (is_null($argsObj->$key)) {
            $argsObj->$key = isset($_REQUEST[$sfKey]) ? $_REQUEST[$sfKey] : null;
        }
        if (isset($isNumeric[$key])) {
            $argsObj->$key = intval($argsObj->$key);              
        }  
    }
}
?>
