<?php
/**
 * Optimized Execution Module - High Performance Alternative to execNavigator/execDashboard
 * 
 * Features:
 * - Lazy-loading tree navigation
 * - SPA-style unified execution workspace  
 * - Real-time status tracking
 * - AJAX-based updates
 * - Color-coded execution states
 * - Quick-action execution buttons
 */

require_once('../../config.inc.php');
require_once('common.php');
require_once("users.inc.php");
require_once('treeMenu.inc.php');
require_once('exec.inc.php');

// Initialize database and get args
$args = init_args();
testlinkInitPage($db);

// Security check - user must have execution rights
if (!isset($args->user) || !$args->user->hasRight($db,"testplan_execute")) {
    redirect($_SESSION['basehref'] . "login.php?note=logout");
    exit();
}

$templateCfg = templateConfiguration();

// Initialize GUI object
$gui = new stdClass();
$gui->tproject_id = isset($_SESSION['testprojectID']) ? $_SESSION['testprojectID'] : 0;
$gui->tplan_id = isset($_SESSION['testplanID']) ? $_SESSION['testplanID'] : 0;
$gui->tproject_name = isset($_SESSION['testprojectName']) ? $_SESSION['testprojectName'] : '';
$gui->tplan_name = isset($_SESSION['testplanName']) ? $_SESSION['testplanName'] : '';

// Get available builds for current test plan
$gui->builds = getBuilds($db, $gui->tplan_id);
$gui->current_build = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 
                     (isset($gui->builds[0]) ? $gui->builds[0]['id'] : 0);

// Get available platforms
$gui->platforms = getPlatforms($db, $gui->tplan_id);
$gui->current_platform = isset($_REQUEST['platform_id']) ? intval($_REQUEST['platform_id']) : 
                         (isset($gui->platforms[0]) ? $gui->platforms[0]['id'] : 0);

// Initialize execution statistics
$gui->stats = array(
    'passed' => 0,
    'failed' => 0, 
    'blocked' => 0,
    'not_run' => 0,
    'total' => 0
);

// Get initial execution statistics
$gui->stats = getExecutionStats($db, $gui->tplan_id, $gui->current_build, $gui->current_platform);

$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->assign('menuUrl', '');
$smarty->assign('args', $args);

$tpl = $templateCfg->template_dir . 'optimized_execution_module.tpl';
$smarty->display($tpl);

/**
 * Get execution statistics for current test plan/build/platform
 */
function getExecutionStats($db, $tplan_id, $build_id, $platform_id) {
    $stats = array(
        'passed' => 0,
        'failed' => 0,
        'blocked' => 0,
        'not_run' => 0,
        'total' => 0
    );
    
    $sql = "SELECT e.status, COUNT(*) as count 
            FROM executions e
            JOIN testplan_tcversions tptc ON e.testplan_id = tptc.testplan_id 
                                           AND e.tcversion_id = tptc.tcversion_id
            WHERE e.testplan_id = ? 
              AND e.build_id = ? 
              AND e.platform_id = ?
            GROUP BY e.status";
    
    $result = $db->GetAll($sql, array($tplan_id, $build_id, $platform_id));
    
    if ($result) {
        foreach ($result as $row) {
            switch ($row['status']) {
                case 'p': $stats['passed'] = $row['count']; break;
                case 'f': $stats['failed'] = $row['count']; break;
                case 'b': $stats['blocked'] = $row['count']; break;
                case 'n': $stats['not_run'] = $row['count']; break;
            }
        }
    }
    
    // Get total test cases
    $sql = "SELECT COUNT(*) as total 
            FROM testplan_tcversions 
            WHERE testplan_id = ?";
    $result = $db->GetOne($sql, array($tplan_id));
    $stats['total'] = $result ? $result : 0;
    
    // Calculate not run from total minus executed
    $executed = $stats['passed'] + $stats['failed'] + $stats['blocked'];
    $stats['not_run'] = max(0, $stats['total'] - $executed);
    
    return $stats;
}

/**
 * Get builds for current test plan
 */
function getBuilds($db, $tplan_id) {
    $sql = "SELECT id, name 
            FROM builds 
            WHERE testplan_id = ? 
            AND is_open = 1 
            ORDER BY name";
    return $db->GetAll($sql, array($tplan_id));
}

/**
 * Get platforms for current test plan
 */
function getPlatforms($db, $tplan_id) {
    $sql = "SELECT p.id, p.name 
            FROM platforms p
            JOIN testplan_platforms tp ON p.id = tp.platform_id
            WHERE tp.testplan_id = ?
            ORDER BY p.name";
    return $db->GetAll($sql, array($tplan_id));
}
?>
