<?php
// Optimized Suite Execution Summary with caching and performance improvements
ini_set('display_errors', '0'); // Production mode - reduce error overhead
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Simplified error handling for performance
$__procLog = __DIR__ . '/suite_execution_summary_proc_debug.log';
$debugMode = false; // Set to true for debugging
function logMessage($level, $message) {
    global $__procLog, $debugMode;
    if ($debugMode) {
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | $level | $message\n", FILE_APPEND);
    }
}

testlinkInitPage($db);
logMessage('INFO', 'after testlinkInitPage');
$currentUser = $_SESSION['currentUser'];
// Basic guard: ensure session user exists before proceeding
if (!is_object($currentUser)) {
    logMessage('INFO', 'no currentUser in session');
    $gui = new stdClass();
    $gui->pageTitle = 'Suite Execution Summary (Proc)';
    $gui->warning_msg = lang_get('no_permissions');
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}
if (!$currentUser->hasRight($db, 'testplan_metrics')) {
    logMessage('INFO', 'user lacks testplan_metrics');
    $gui = new stdClass();
    $gui->pageTitle = 'Suite Execution Summary (Proc)';
    $gui->warning_msg = lang_get('no_permissions');
    $smarty = new TLSmarty();
    $smarty->assign('gui', $gui);
    $smarty->display('workAreaSimple.tpl');
    exit();
}

$gui = new stdClass();
$gui->pageTitle = 'Test Suite Execution Summary (Stored Procedure)';
$gui->warning_msg = '';

$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedPlan = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
$selectedBuild = isset($_REQUEST['build_id']) ? intval($_REQUEST['build_id']) : 0;
$selectedStatus = isset($_REQUEST['status']) ? trim($_REQUEST['status']) : '';
$selectedExecutionPath = isset($_REQUEST['execution_path']) ? trim($_REQUEST['execution_path']) : '';
$startDate = isset($_REQUEST['start_date']) ? trim($_REQUEST['start_date']) : '';
$endDate = isset($_REQUEST['end_date']) ? trim($_REQUEST['end_date']) : '';
// Run trigger: only execute report when explicitly requested
$run = isset($_REQUEST['run']) ? intval($_REQUEST['run']) : 0;

$gui->statuses = array('p' => 'Passed','f' => 'Failed','b' => 'Blocked','n' => 'Not Run');

// Cache key for dropdowns (using session instead of APCu)
$cacheKey = 'suite_summary_dropdowns_' . $currentUser->dbID;
$cachedData = isset($_SESSION[$cacheKey]) ? $_SESSION[$cacheKey] : null;
$success = ($cachedData !== null);

// Force cache refresh if requested or if data format is old
$forceRefresh = isset($_REQUEST['refresh_cache']) || ($success && !is_array($cachedData['testprojects']));
if ($forceRefresh) {
    $success = false;
    unset($_SESSION[$cacheKey]);
    $cachedData = null;
    logMessage('INFO', 'Force refreshing dropdown cache');
}

// Always force refresh for now to ensure clean data
if ($success) {
    $success = false;
    unset($_SESSION[$cacheKey]);
    $cachedData = null;
    logMessage('INFO', 'Force refreshing dropdown cache for debugging');
}

if ($success && isset($_REQUEST['ajax'])) {
    // Return cached data for AJAX requests
    header('Content-Type: application/json');
    echo json_encode($cachedData);
    exit;
}

if ($success) {
    // Use cached data directly for now to avoid breaking existing functionality
    $gui->testprojects = $cachedData['testprojects'];
    logMessage('INFO', 'Using cached dropdown data');
} else {
    try {
        $testProjectMgr = new testproject($db);
        $rawProjects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);
        
        // Use raw project data without complex cleaning for now
        $gui->testprojects = $rawProjects;
        
        // Cache the data for 30 minutes in session
        $cacheData = array('testprojects' => $gui->testprojects);
        $_SESSION[$cacheKey] = $cacheData;
        logMessage('INFO', 'Cached dropdown data');
    } catch (Exception $e) {
        logMessage('ERROR', 'testproject load: ' . $e->getMessage());
        $gui->testprojects = array();
    }
}

$gui->testplans = array();
if ($selectedProject > 0) {
    $tplanSql = 'SELECT id, notes FROM testplans WHERE testproject_id = ' . intval($selectedProject) . ' AND active = 1 ORDER BY notes';
    $tplanResult = $db->exec_query($tplanSql);
    if ($tplanResult) {
        while ($row = $db->fetch_array($tplanResult)) {
            // Clean HTML tags and format properly
            $planName = strip_tags($row['notes']);
            $planName = html_entity_decode($planName, ENT_QUOTES, 'UTF-8');
            $planName = trim($planName);
            if (empty($planName)) {
                $planName = 'Test Plan #' . $row['id'];
            }
            $gui->testplans[] = array('id' => $row['id'], 'name' => $planName);
        }
    }
}

$gui->builds = array();
if ($selectedPlan > 0) {
    $buildSql = 'SELECT id, name FROM builds WHERE testplan_id = ' . intval($selectedPlan) . ' AND active = 1 ORDER BY name';
    $buildResult = $db->exec_query($buildSql);
    if ($buildResult) {
        while ($row = $db->fetch_array($buildResult)) {
            // Clean HTML tags and format properly
            $buildName = strip_tags($row['name']);
            $buildName = html_entity_decode($buildName, ENT_QUOTES, 'UTF-8');
            $buildName = trim($buildName);
            if (empty($buildName)) {
                $buildName = 'Build #' . $row['id'];
            }
            $gui->builds[] = array('id' => $row['id'], 'name' => $buildName);
        }
    }
}

$suiteData = array();
$totalTestCases = 0;
$totalPassed = 0;
$totalFailed = 0;
$totalBlocked = 0;
$totalNotRun = 0;

// Check if this is an AJAX request for dynamic updates - handle BEFORE any template processing
if (isset($_REQUEST['ajax'])) {
    // Handle AJAX requests for dropdowns
    if (isset($_REQUEST['get_plans'])) {
        $plans = array();
        if ($selectedProject > 0) {
            $tplanSql = 'SELECT id, notes FROM testplans WHERE testproject_id = ' . intval($selectedProject) . ' AND active = 1 ORDER BY notes';
            $tplanResult = $db->exec_query($tplanSql);
            if ($tplanResult) {
                while ($row = $db->fetch_array($tplanResult)) {
                    // Clean HTML tags and format properly
                    $planName = strip_tags($row['notes']);
                    $planName = html_entity_decode($planName, ENT_QUOTES, 'UTF-8');
                    $planName = trim($planName);
                    if (empty($planName)) {
                        $planName = 'Test Plan #' . $row['id'];
                    }
                    $plans[] = array('id' => $row['id'], 'name' => $planName);
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode(array('plans' => $plans));
        exit;
    }
    
    if (isset($_REQUEST['get_builds'])) {
        $builds = array();
        if ($selectedPlan > 0) {
            $buildSql = 'SELECT id, name FROM builds WHERE testplan_id = ' . intval($selectedPlan) . ' AND active = 1 ORDER BY name';
            $buildResult = $db->exec_query($buildSql);
            if ($buildResult) {
                while ($row = $db->fetch_array($buildResult)) {
                    // Clean HTML tags and format properly
                    $buildName = strip_tags($row['name']);
                    $buildName = html_entity_decode($buildName, ENT_QUOTES, 'UTF-8');
                    $buildName = trim($buildName);
                    if (empty($buildName)) {
                        $buildName = 'Build #' . $row['id'];
                    }
                    $builds[] = array('id' => $row['id'], 'name' => $buildName);
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode(array('builds' => $builds));
        exit;
    }
    
    // Handle AJAX request for report data
    if (isset($_REQUEST['run']) && $_REQUEST['run'] == '1') {
        // Force report execution for AJAX request
        $run = 1;
        
        // Clean any previous output
        if (ob_get_length()) ob_clean();
        
        // Execute the report using the same approach as the main report
        if ($selectedProject > 0) {
            try {
                // Prepare parameters for stored procedure (using same approach as main code)
                $statusParam = ($selectedStatus !== '') ? "'" . addslashes($selectedStatus) . "'" : "NULL";
                $pathParam = ($selectedExecutionPath !== '') ? "'" . addslashes($selectedExecutionPath) . "'" : "NULL";
                $startParam = ($startDate !== '') ? "'" . addslashes($startDate . ' 00:00:00') . "'" : "NULL";
                $endParam = ($endDate !== '') ? "'" . addslashes($endDate . ' 23:59:59') . "'" : "NULL";
                
                $call = 'CALL suite_execution_summary(' . intval($selectedProject) . ', ' . intval($selectedPlan) . ', ' . intval($selectedBuild) . ', ' . $statusParam . ', ' . $pathParam . ', ' . $startParam . ', ' . $endParam . ')';
                logMessage('INFO', "AJAX SQL: $call");

                $result = $db->exec_query($call);
                if (!$result) {
                    $errorMsg = method_exists($db,'error_msg') ? $db->error_msg() : 'Unknown error';
                    logMessage('ERROR', "AJAX SP exec failed: $errorMsg");
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'error' => 'Database error: ' . $errorMsg
                    ]);
                    exit;
                }

                $suiteData = array();
                $totalTestCases = 0;
                $totalPassed = 0;
                $totalFailed = 0;
                $totalBlocked = 0;
                $totalNotRun = 0;

                if ($result) {
                    $rowCount = 0;
                    while ($row = $db->fetch_array($result)) {
                        $rowCount++;
                        $suiteData[] = array(
                            'test_path' => $row['test_path'],
                            'total_testcases' => isset($row['total_testcases']) ? $row['total_testcases'] : 0,
                            'testcase_count' => isset($row['testcase_count']) ? $row['testcase_count'] : 0,
                            'passed_count' => isset($row['passed_count']) ? $row['passed_count'] : 0,
                            'failed_count' => isset($row['failed_count']) ? $row['failed_count'] : 0,
                            'blocked_count' => isset($row['blocked_count']) ? $row['blocked_count'] : 0,
                            'not_run_count' => isset($row['not_run_count']) ? $row['not_run_count'] : 0,
                            'pass_rate' => isset($row['pass_rate']) ? round($row['pass_rate'], 2) : 0,
                            'fail_rate' => isset($row['fail_rate']) ? round($row['fail_rate'], 2) : 0,
                            'block_rate' => isset($row['block_rate']) ? round($row['block_rate'], 2) : 0,
                            'pending_rate' => isset($row['pending_rate']) ? round($row['pending_rate'], 2) : 0
                        );
                        
                        $totalTestCases += isset($row['total_testcases']) ? $row['total_testcases'] : 0;
                        $totalPassed += isset($row['passed_count']) ? $row['passed_count'] : 0;
                        $totalFailed += isset($row['failed_count']) ? $row['failed_count'] : 0;
                        $totalBlocked += isset($row['blocked_count']) ? $row['blocked_count'] : 0;
                        $totalNotRun += isset($row['not_run_count']) ? $row['not_run_count'] : 0;
                    }
                }
                
                $totalExecuted = $totalPassed + $totalFailed;
                $overallPassRate = $totalExecuted > 0 ? round(($totalPassed / $totalExecuted) * 100, 2) : 0;
                $overallFailRate = $totalExecuted > 0 ? round(($totalFailed / $totalExecuted) * 100, 2) : 0;
                $overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked / $totalTestCases) * 100, 2) : 0;
                $totalNonBlocked = $totalTestCases - $totalBlocked;
                $overallPendingRate = $totalNonBlocked > 0 ? round(($totalNotRun / $totalNonBlocked) * 100, 2) : 0;
                
                // Clean any output buffer before sending JSON
                if (ob_get_length()) ob_clean();
                
                header('Content-Type: application/json');
                $response = [
                    'success' => true,
                    'data' => $suiteData,
                    'summary' => array(
                        'totalTestCases' => $totalTestCases,
                        'totalPassed' => $totalPassed,
                        'totalFailed' => $totalFailed,
                        'totalBlocked' => $totalBlocked,
                        'totalNotRun' => $totalNotRun,
                        'overallPassRate' => $overallPassRate,
                        'overallFailRate' => $overallFailRate,
                        'overallBlockRate' => $overallBlockRate,
                        'overallPendingRate' => $overallPendingRate
                    ),
                    'rowCount' => count($suiteData)
                ];
                
                echo json_encode($response);
                exit;
                
            } catch (Exception $e) {
                // Clean any output buffer before sending error JSON
                if (ob_get_length()) ob_clean();
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Database error: ' . $e->getMessage()
                ]);
                exit;
            }
        } else {
            // Clean any output buffer before sending error JSON
            if (ob_get_length()) ob_clean();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Please select a project'
            ]);
            exit;
        }
    }
    
    // Return cached data for AJAX requests (fallback)
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Invalid AJAX request'
    ]);
    exit;
}

// Only run the heavy stored procedure when explicitly requested and with sufficient scoping
$hasAdditionalFilters = ($selectedPlan > 0) || ($selectedBuild > 0) || ($selectedStatus !== '') || ($selectedExecutionPath !== '') || ($startDate !== '') || ($endDate !== '');
if ($run === 1 && $selectedProject > 0 && $hasAdditionalFilters) {
    logMessage('INFO', "executing SP with filters");
    logMessage('INFO', "Params: proj=$selectedProject plan=$selectedPlan build=$selectedBuild status='$selectedStatus' path='$selectedExecutionPath' start='$startDate' end='$endDate'");
    
    $statusParam = ($selectedStatus !== '') ? "'" . addslashes($selectedStatus) . "'" : "NULL";
    $pathParam = ($selectedExecutionPath !== '') ? "'" . addslashes($selectedExecutionPath) . "'" : "NULL";
    $startParam = ($startDate !== '') ? "'" . addslashes($startDate . ' 00:00:00') . "'" : "NULL";
    $endParam = ($endDate !== '') ? "'" . addslashes($endDate . ' 23:59:59') . "'" : "NULL";

    $call = 'CALL suite_execution_summary(' . intval($selectedProject) . ', ' . intval($selectedPlan) . ', ' . intval($selectedBuild) . ', ' . $statusParam . ', ' . $pathParam . ', ' . $startParam . ', ' . $endParam . ')';
    logMessage('INFO', "SQL: $call");

    $result = $db->exec_query($call);
    if (!$result) {
        $errorMsg = method_exists($db,'error_msg') ? $db->error_msg() : 'Unknown error';
        logMessage('ERROR', "SP exec failed: $errorMsg");
        $gui->warning_msg = 'Error executing stored procedure: ' . $errorMsg;
    }

    if ($result) {
        $rowCount = 0;
        while ($row = $db->fetch_array($result)) {
            $rowCount++;
            $suiteData[] = array(
                'test_path' => $row['test_path'],
                'total_testcases' => isset($row['total_testcases']) ? $row['total_testcases'] : 0,
                'testcase_count' => isset($row['testcase_count']) ? $row['testcase_count'] : 0,
                'passed_count' => isset($row['passed_count']) ? $row['passed_count'] : 0,
                'failed_count' => isset($row['failed_count']) ? $row['failed_count'] : 0,
                'blocked_count' => isset($row['blocked_count']) ? $row['blocked_count'] : 0,
                'not_run_count' => isset($row['not_run_count']) ? $row['not_run_count'] : 0,
                'pass_rate' => isset($row['pass_rate']) ? $row['pass_rate'] : 0,
                'fail_rate' => isset($row['fail_rate']) ? $row['fail_rate'] : 0,
                'block_rate' => isset($row['block_rate']) ? $row['block_rate'] : 0,
                'pending_rate' => isset($row['pending_rate']) ? $row['pending_rate'] : 0,
            );
            $totalTestCases += (int)$row['testcase_count'];
            $totalPassed += (int)$row['passed_count'];
            $totalFailed += (int)$row['failed_count'];
            $totalBlocked += (int)$row['blocked_count'];
            $totalNotRun += (int)$row['not_run_count'];
        }
        @file_put_contents($__procLog, date('Y-m-d H:i:s')." | INFO | SP returned $rowCount rows\n", FILE_APPEND);
    }
} else {
    if ($selectedProject <= 0) {
        $gui->warning_msg = $gui->warning_msg ?: 'Please select a test project.';
    } elseif (!$hasAdditionalFilters && $run === 1) {
        $gui->warning_msg = $gui->warning_msg ?: 'Please add at least one additional filter (plan, build, status, path, or date) and click Run Report.';
    }
}

$totalExecuted = $totalPassed + $totalFailed;
$overallPassRate = $totalExecuted > 0 ? round(($totalPassed / $totalExecuted) * 100, 2) : 0;
$overallFailRate = $totalExecuted > 0 ? round(($totalFailed / $totalExecuted) * 100, 2) : 0;
$overallBlockRate = $totalTestCases > 0 ? round(($totalBlocked / $totalTestCases) * 100, 2) : 0;
$totalNonBlocked = $totalTestCases - $totalBlocked;
$overallPendingRate = $totalNonBlocked > 0 ? round(($totalNotRun / $totalNonBlocked) * 100, 2) : 0;

$gui->suiteData = $suiteData;
$gui->totalTestCases = $totalTestCases;
$gui->totalPassed = $totalPassed;
$gui->totalFailed = $totalFailed;
$gui->totalBlocked = $totalBlocked;
$gui->totalNotRun = $totalNotRun;
$gui->overallPassRate = $overallPassRate;
$gui->overallFailRate = $overallFailRate;
$gui->overallBlockRate = $overallBlockRate;
$gui->overallPendingRate = $overallPendingRate;

$gui->selectedProject = $selectedProject;
$gui->selectedPlan = $selectedPlan;
$gui->selectedBuild = $selectedBuild;
$gui->selectedStatus = $selectedStatus;
$gui->selectedExecutionPath = $selectedExecutionPath;
$gui->startDate = $startDate;
$gui->endDate = $endDate;
$gui->run = $run;

$templateCfg = templateConfiguration();
$smarty = new TLSmarty();
$smarty->assign('gui', $gui);

// Use the appropriate template based on request
if (isset($_REQUEST['optimized']) && $_REQUEST['optimized'] == '1') {
    $smarty->display($templateCfg->template_dir . 'suite_execution_summary_proc_simple.tpl');
} else {
    $smarty->display($templateCfg->template_dir . 'suite_execution_summary_proc.tpl');
}
