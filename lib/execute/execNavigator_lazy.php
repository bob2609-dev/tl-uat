<?php
/**
 * Lazy Loading execNavigator
 * Only loads visible tree nodes initially, loads children on demand
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

function logMessage($msg) {
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    echo "[$timestamp] $msg<br>\n";
    error_log("execNavigator_lazy: $msg");
}

logMessage("Starting lazy loading execNavigator...");

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
    
    logMessage("Building lazy tree...");
    $gui = initializeGui($db,$control);
    
    // Override the tree building to use lazy loading
    $tree_start = microtime(true);
    buildLazyTreeMenu($control, $gui);
    $tree_time = microtime(true) - $tree_start;
    
    logMessage("Lazy tree built in {$tree_time}s");
    
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
    echo "<!-- Performance: Total: {$total_time}s, Tree: {$tree_time}s, Lazy Loading: YES -->";
    
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
    echo "<br><strong>Error: " . $e->getMessage() . "</strong>";
}

/**
 * Lazy loading tree menu builder
 * Only loads top-level nodes initially
 */
function buildLazyTreeMenu(&$control, &$gui) {
    global $db;
    
    // Get basic tree structure (only top levels)
    $filters = $control->get_active_filters();
    $context = array(
        'tproject_id' => $control->args->testproject_id,
        'tproject_name' => $control->args->testproject_name,
        'tplan_id' => $control->args->testplan_id,
        'tplan_name' => $control->args->testplan_name
    );
    
    // Limit depth to reduce initial load
    $options = array(
        'max_depth' => 2,  // Only load first 2 levels initially
        'limit_nodes' => 50,  // Limit total nodes initially
        'lazy_load' => true
    );
    
    // Build minimal tree
    $gui->tree_menu = buildMinimalTree($db, $context, $filters, $options);
    $gui->testcases_to_show = array();  // Empty initially, loaded via AJAX
    
    // Add lazy loading JavaScript
    $gui->lazy_loading_enabled = true;
}

/**
 * Build minimal tree structure for initial display
 */
function buildMinimalTree(&$db, $context, $filters, $options) {
    $max_depth = $options['max_depth'];
    $limit_nodes = $options['limit_nodes'];
    
    // Get test plan structure with depth limit
    $tplan_mgr = new testplan($db);
    
    // Get only top-level test suites
    $sql = "SELECT id, name, node_type_id 
            FROM nodes_hierarchy 
            WHERE parent_id = " . $context['tplan_id'] . "
            AND node_type_id IN (1, 2)  // Test suites and test cases
            ORDER BY name
            LIMIT " . $limit_nodes;
    
    $nodes = $db->fetchRowsIntoMap($sql, 'id');
    
    $tree = array();
    $node_count = 0;
    
    foreach ($nodes as $node_id => $node) {
        if ($node_count >= $limit_nodes) break;
        
        $tree_item = array(
            'id' => $node_id,
            'name' => $node['name'],
            'node_type_id' => $node['node_type_id'],
            'children_loaded' => false,
            'has_children' => hasChildren($db, $node_id),
            'leaf' => ($node['node_type_id'] == 3)  // Test case = leaf
        );
        
        $tree[] = $tree_item;
        $node_count++;
    }
    
    return $tree;
}

/**
 * Check if node has children
 */
function hasChildren(&$db, $node_id) {
    $sql = "SELECT COUNT(*) as child_count 
            FROM nodes_hierarchy 
            WHERE parent_id = " . $node_id;
    
    $result = $db->fetchRowsIntoMap($sql, 'child_count');
    return isset($result[0]) && $result[0]['child_count'] > 0;
}

/**
 * AJAX endpoint to load children on demand
 */
function loadTreeChildren($parent_id, $context, $filters) {
    global $db;
    
    $sql = "SELECT id, name, node_type_id 
            FROM nodes_hierarchy 
            WHERE parent_id = " . intval($parent_id) . "
            AND node_type_id IN (1, 2, 3)  // All types
            ORDER BY name
            LIMIT 100";  // Reasonable limit
    
    $nodes = $db->fetchRowsIntoMap($sql, 'id');
    
    $children = array();
    foreach ($nodes as $node_id => $node) {
        $child = array(
            'id' => $node_id,
            'name' => $node['name'],
            'node_type_id' => $node['node_type_id'],
            'children_loaded' => false,
            'has_children' => hasChildren($db, $node_id),
            'leaf' => ($node['node_type_id'] == 3)
        );
        $children[] = $child;
    }
    
    return $children;
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
