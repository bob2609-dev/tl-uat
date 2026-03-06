<?php
/**
 * Performance Test Script
 * Focus on tree building performance analysis
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$log_file = __DIR__ . '/performance_log.txt';
file_put_contents($log_file, "=== Performance Test Started: " . date('Y-m-d H:i:s') . " ===\n");

function perf_log($message) {
    global $log_file;
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
    echo "[$timestamp] $message<br>\n";
}

perf_log("Starting performance test...");

try {
    require_once('../../config.inc.php');
    require_once('common.php');
    require_once("users.inc.php");
    require_once('treeMenu.inc.php');
    require_once('exec.inc.php');
    
    testlinkInitPage($db);
    $templateCfg = templateConfiguration();
    
    $total_start = microtime(true);
    
    $control = new tlTestCaseFilterControl($db, 'execution_mode');
    $control->formAction = '';
    
    $gui = initializeGui($db,$control);
    
    // THIS IS THE BOTTLENECK - Let's analyze it in detail
    perf_log("=== TREE BUILDING ANALYSIS ===");
    $tree_start = microtime(true);
    
    perf_log("Starting build_tree_menu()...");
    $control->build_tree_menu($gui);
    
    $tree_time = microtime(true) - $tree_start;
    perf_log("build_tree_menu() COMPLETED in {$tree_time}s");
    
    $total_time = microtime(true) - $total_start;
    perf_log("Total time: {$total_time}s");
    perf_log("Tree building is " . round(($tree_time / $total_time) * 100, 1) . "% of total time");
    
    // Show some stats about what was built
    if (isset($gui->tree_menu)) {
        perf_log("Tree menu created successfully");
        if (is_array($gui->tree_menu)) {
            perf_log("Tree menu has " . count($gui->tree_menu) . " items");
        }
    }
    
    if (isset($gui->testcases_to_show)) {
        if (is_array($gui->testcases_to_show)) {
            perf_log("Test cases to show: " . count($gui->testcases_to_show));
        }
    }
    
    // Try to display a simple result instead of full template
    echo "<hr><h2>Performance Results</h2>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Operation</th><th>Time (seconds)</th><th>Percentage</th></tr>";
    echo "<tr><td>Tree Building</td><td>{$tree_time}</td><td>" . round(($tree_time / $total_time) * 100, 1) . "%</td></tr>";
    echo "<tr><td>Other Operations</td><td>" . ($total_time - $tree_time) . "</td><td>" . round((($total_time - $tree_time) / $total_time) * 100, 1) . "%</td></tr>";
    echo "<tr><td><strong>Total</strong></td><td><strong>{$total_time}</strong></td><td><strong>100%</strong></td></tr>";
    echo "</table>";
    
    echo "<h2>Recommendations</h2>";
    if ($tree_time > 3.0) {
        echo "<p style='color: red;'><strong>Tree building is very slow (>3s). Consider:</strong></p>";
        echo "<ul>";
        echo "<li>Implementing tree caching</li>";
        echo "<li>Limiting tree depth/size</li>";
        echo "<li>Optimizing database queries in tree building</li>";
        echo "<li>Using lazy loading for tree branches</li>";
        echo "</ul>";
    } elseif ($tree_time > 1.0) {
        echo "<p style='color: orange;'><strong>Tree building is slow (>1s). Some optimization needed.</strong></p>";
    } else {
        echo "<p style='color: green;'><strong>Tree building performance is acceptable.</strong></p>";
    }
    
    perf_log("=== Performance test completed successfully ===");
    
} catch (Exception $e) {
    perf_log("❌ ERROR: " . $e->getMessage());
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
