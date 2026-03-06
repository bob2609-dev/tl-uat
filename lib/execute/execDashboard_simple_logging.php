<?php
/**
 * execDashboard with Simple Query Logging
 * No infinite loops, captures actual SQL queries
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");
require_once('treeMenu.inc.php');
require_once('exec.inc.php');

// Initialize database first
testlinkInitPage($db);

// Include complete logger and wrap database
require_once('query_logger_complete.php');
$db = new CompleteLoggedDatabase($db);

$templateCfg = templateConfiguration();
$chronos[] = $tstart = microtime(true);

echo "<!-- execDashboard with Simple Query Logging Started -->\n";

// Initialize GUI
$gui = initializeGui($db);

// Process dashboard with logging
$dashboard_start = microtime(true);

// Main dashboard logic (simplified for logging)
$gui->pageTitle = lang_get('title_execution_dashboard');

$dashboard_time = microtime(true) - $dashboard_start;

$smarty = new TLSmarty();
$smarty->assign('gui',$gui);
$tpl = $templateCfg->template_dir . 'execDashboard.tpl';
$smarty->display($tpl);

$total_time = microtime(true) - $tstart;

echo "<!-- Performance: Total: {$total_time}s, Dashboard: {$dashboard_time}s -->\n";

function initializeGui(&$dbHandler) {
    $gui = new stdClass();
    
    $dummy = config_get('results');
    $gui->not_run = $dummy['status_code']['not_run'];
    
    $dummy = config_get('execution_filter_methods');
    $gui->lastest_exec_method = $dummy['status_code']['latest_execution'];
    $gui->pageTitle = lang_get('title_execution_dashboard');
    
    // Set basic properties
    $gui->user_feedback = '';
    $gui->refresh_tree = '';
    $gui->tree_refresh_on_load = false;
    
    return $gui;
}

?>
