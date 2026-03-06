<?php
/**
 * execDashboard with Comprehensive Query Logging
 * Identifies exact queries causing performance issues
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");
require_once('treeMenu.inc.php');
require_once('exec.inc.php');

// Include the query logger AFTER database initialization
testlinkInitPage($db);
require_once('query_logger_comprehensive.php');

// Wrap the database with logging AFTER it's initialized
$db = new LoggedDatabase($db);

$templateCfg = templateConfiguration();
$chronos[] = $tstart = microtime(true);

echo "<!-- execDashboard with Query Logging Started -->\n";

// Initialize GUI
$gui = initializeGui($db);

// Process dashboard with logging
$dashboard_start = microtime(true);
logQuery("Starting dashboard processing", microtime(true) - $dashboard_start, 'execDashboard_init');

// Main dashboard logic (simplified for logging)
$gui->pageTitle = lang_get('title_execution_dashboard');

$dashboard_time = microtime(true) - $dashboard_start;
logQuery("Dashboard processing completed", $dashboard_time, 'execDashboard_total');

$smarty = new TLSmarty();
$smarty->assign('gui',$gui);
$tpl = $templateCfg->template_dir . 'execDashboard.tpl';
$smarty->display($tpl);

$total_time = microtime(true) - $tstart;
logQuery("Total execution time", $total_time, 'execDashboard_total');

echo "<!-- Performance: Total: {$total_time}s, Dashboard: {$dashboard_time}s -->\n";

function initializeGui(&$dbHandler) {
    $gui = new stdClass();
    
    $dummy = config_get('results');
    $gui->not_run = $dummy['status_code']['not_run'];
    
    $dummy = config_get('execution_filter_methods');
    $gui->lastest_exec_method = $dummy['status_code']['latest_execution'];
    $gui->pageTitle = lang_get('title_execution_dashboard');
    $gui->tpl = 'tpl/execDashboard.tpl';
    
    // Set basic properties
    $gui->user_feedback = '';
    $gui->refresh_tree = '';
    $gui->tree_refresh_on_load = false;
    
    return $gui;
}

?>
