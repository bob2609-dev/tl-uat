<?php
/**
 * Other Custom Reports
 * 
 * Provides additional custom reporting functionality
 * 
 * Updated: 2025-12-01 16:25:00
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define log file path
$__procLog = __DIR__ . '/other_custom_reports_debug.log';
@file_put_contents($__procLog, date('Y-m-d H:i:s') . " | INFO | Starting request\n", FILE_APPEND);

// Set up error handlers
function errorHandler($errno, $errstr, $errfile, $errline) {
    global $__procLog;
    $msg = date('Y-m-d H:i:s') . " | ERR:$errno | $errstr | $errfile:$errline\n";
    @file_put_contents($__procLog, $msg, FILE_APPEND);
    return false;
}

function exceptionHandler($ex) {
    global $__procLog;
    $msg = date('Y-m-d H:i:s') . " | EXC | " . $ex->getMessage() . " @ " . $ex->getFile() . ":" . $ex->getLine() . "\n";
    @file_put_contents($__procLog, $msg, FILE_APPEND);
}

function shutdownFunction() {
    global $__procLog;
    $e = error_get_last();
    if ($e) {
        $msg = date('Y-m-d H:i:s') . " | SHUTDOWN | " . $e['message'] . " @ " . $e['file'] . ":" . $e['line'] . "\n";
        @file_put_contents($__procLog, $msg, FILE_APPEND);
    }
}

// Register handlers
set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
register_shutdown_function('shutdownFunction');

// Include required files
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Main execution
try {
    // Initialize TestLink page
    testlinkInitPage($db, false, false);
    $currentUser = $_SESSION['currentUser'];

    // Check user permissions
    if (!is_object($currentUser) || !$currentUser->hasRight($db, 'testplan_metrics')) {
        $gui = new stdClass();
        $gui->pageTitle = lang_get('other_custom_reports');
        $gui->warning_msg = lang_get('no_permissions');
        $smarty = new TLSmarty();
        $smarty->assign('gui', $gui);
        $smarty->display('workAreaSimple.tpl');
        exit();
    }

// Initialize GUI object
$gui = initGui($db);

// Process actions
if (isset($_REQUEST['doAction'])) {
    try {
        $output = processAction($db, $gui);
        if (isset($_REQUEST['isAjax'])) {
            echo $output;
            exit();
        }
        // For export actions, we should have already exited in processAction
        if ($_REQUEST['doAction'] === 'export') {
            exit();
        }
    } catch (Exception $e) {
        $errorMsg = 'Error processing action: ' . $e->getMessage();
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
        
        if (isset($_REQUEST['isAjax'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $errorMsg]);
            exit();
        }
        
        $gui->error_msg = $errorMsg;
    }
}

// Display the main template
$smarty = new TLSmarty();
$smarty->assign('gui', $gui);
$smarty->display('other_custom_reports.tpl');

} catch (Exception $e) {
    // Error handling
    $errorMsg = 'Error in other_custom_reports.php: ' . $e->getMessage();
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | FATAL | " . $errorMsg . "\n", FILE_APPEND);
    
    if (isset($_REQUEST['isAjax'])) {
        header('Content-Type: application/json');
        echo json_encode(['error' => true, 'message' => $errorMsg]);
    } else {
        echo "<div class='alert alert-danger'><strong>Error:</strong> " . htmlspecialchars($errorMsg) . "</div>";
    }
}

/**
 * Export traceability dump to CSV or Excel
 */
function exportTraceabilityDump(&$db, $format = 'csv', &$gui) {
    global $__procLog;
    
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | exportTraceabilityDump called\n", FILE_APPEND);
    
    // Get testplan_id from GET data for URL navigation requests
    $testplan_id = isset($_GET['testplan_id']) ? intval($_GET['testplan_id']) : $gui->testplan_id;
    
    if (!$testplan_id) {
        if (isset($_REQUEST['isAjax'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'No test plan selected']);
            exit();
        } else {
            die('No test plan selected');
        }
    }
    
    // Execute the traceability dump query
    $sqlFile = dirname(dirname(__DIR__)) . '/sql/TRACEABILITY_DUMP_SCRIPT_UPDATED.sql';
    if (!file_exists($sqlFile)) {
        die('Traceability dump SQL file not found');
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Extract only the main SELECT query (same as generateReportContent)
    $mainQueryStart = strpos($sql, '-- =====================================================================================');
    $mainQueryStart = strpos($sql, '-- MAIN QUERY', $mainQueryStart);
    $mainQueryStart = strpos($sql, 'WITH', $mainQueryStart);
    
    if ($mainQueryStart === false) {
        die('Could not find main query in SQL file');
    }
    
    $sql = substr($sql, $mainQueryStart);
    $sql = str_replace('@testplan_id', intval($testplan_id), $sql);
    
    try {
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Export executing SQL\n", FILE_APPEND);
        
        // Execute the query using exec_query (same as generateReportContent)
        $result = $db->exec_query($sql);
        
        if (!$result) {
            $mysqlError = $db->error_msg();
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | Export query failed: " . $mysqlError . "\n", FILE_APPEND);
            die('Query execution failed: ' . $mysqlError);
        }
        
        // Fetch all rows into an array
        $rows = array();
        while ($row = $db->fetch_array($result)) {
            $rows[] = $row;
        }
        
        if (empty($rows)) {
            die('No data found for the selected test plan');
        }
        
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Export fetched " . count($rows) . " rows\n", FILE_APPEND);
        
        // Get column headers from the first row
        $headers = array_keys($rows[0]);
        
        if ($format === 'csv') {
            // Clean ALL output buffer levels to ensure pure CSV output
            while(ob_get_level()) {
                ob_end_clean();
            }
            
            // Set headers for all requests
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="traceability_dump_' . date('Y-m-d') . '.csv"');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');
            
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            
            fclose($output);
            
            // Force immediate exit to prevent framework from wrapping output in HTML
            exit();
        } else {
            // Excel format
            if (!isset($_REQUEST['isAjax'])) {
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename="traceability_dump_' . date('Y-m-d') . '.xls"');
            }
            
            echo "<table border='1'>";
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th>" . htmlspecialchars($header) . "</th>";
            }
            echo "</tr>";
            
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($headers as $header) {
                    echo "<td>" . htmlspecialchars($row[$header] ?? '') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (Exception $e) {
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | Export exception: " . $e->getMessage() . "\n", FILE_APPEND);
        die('Error generating export: ' . $e->getMessage());
    }
}

/**
 * Initialize GUI object
 */
function initGui(&$db) {
    $gui = new stdClass();
    
    // Basic page setup
    $gui->pageTitle = lang_get('other_custom_reports');
    
    // Get the project ID from session if not in request
    $gui->tproject_id = isset($_REQUEST['tproject_id']) ? intval($_REQUEST['tproject_id']) : 0;
    if ($gui->tproject_id == 0 && isset($_SESSION['testprojectID'])) {
        $gui->tproject_id = intval($_SESSION['testprojectID']);
    }
    
    // Set a default project ID for testing if still not set
    if ($gui->tproject_id == 0) {
        // Try to get the first available project
        $sql = "SELECT id FROM testprojects WHERE active = 1 ORDER BY id LIMIT 1";
        $result = $db->fetchFirstRow($sql);
        if ($result) {
            $gui->tproject_id = $result['id'];
        }
    }
    
    // Define our custom reports
    $gui->report_types = array(
        'traceability_dump' => 'Traceability Dump',
        'failed_no_defect' => 'Failed - No Defect Logged',
        'report3' => 'Custom Report 3'
    );
    
    // Initialize test plan ID from request or use a default
    $gui->testplan_id = isset($_REQUEST['testplan_id']) ? intval($_REQUEST['testplan_id']) : 0;
    $gui->testPlans = array();

    // Only try to get test plans if we have a project ID
    if ($gui->tproject_id > 0) {
        $sql = "SELECT tp.id, nh.name, tp.notes 
                FROM testplans tp
                JOIN nodes_hierarchy nh ON nh.id = tp.id
                WHERE tp.testproject_id = " . $gui->tproject_id . " 
                AND tp.active = 1 
                ORDER BY nh.name";
        
        $testPlans = $db->fetchRowsIntoMap($sql, 'id');
        
        if (is_array($testPlans) && !empty($testPlans)) {
            $gui->testPlans = $testPlans;
            
            // If no test plan is selected, pick the first one
            if ($gui->testplan_id <= 0) {
                $firstPlanId = key($testPlans);
                $gui->testplan_id = $firstPlanId;
            }
        } else {
            tLog("No active test plans found for project ID: " . $gui->tproject_id, 'WARNING');
        }
    }
    
    // Initialize empty results
    $gui->results = array();
    $gui->has_results = false;
    
    return $gui;
}

/**
 * Process form actions
 */
function processAction(&$db, &$gui) {
    $action = $_REQUEST['doAction'];
    
    switch($action) {
        case 'loadReport':
            if (isset($_REQUEST['reportId'])) {
                header('Content-Type: text/html');
                echo generateReportContent($db, $_REQUEST['reportId'], $gui);
                exit();
            }
            break;
            
            
        case 'export':
            if (isset($_REQUEST['reportId']) && $_REQUEST['reportId'] === 'traceability_dump') {
                // Check if this is an AJAX request
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    $_REQUEST['isAjax'] = true;
                }
                exportTraceabilityDump($db, isset($_REQUEST['format']) ? $_REQUEST['format'] : 'csv', $gui);
                exit();
            }
            break;
            
        // Add more actions as needed
    }
    
    return '';
}

/**
 * Generate report content based on report ID
 */
function generateReportContent($db, $reportId, $gui) {
    global $__procLog;
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | generateReportContent called for report: $reportId\n", FILE_APPEND);
    $content = '';

    // Get test plan id from GUI
    $testplanId = isset($gui->testplan_id) ? intval($gui->testplan_id) : 0;
    
    // Determine SQL file and report settings based on report ID
    $reportTitle = '';
    $exportEndpoint = '';
    $needsTestPlanFilter = true;
    
    switch ($reportId) {
        case 'traceability_dump':
            $reportTitle = 'Traceability Dump';
            // Force use of MySQL 8 compatible version for better performance
            $mysql8File = dirname(dirname(__DIR__)) . '/sql/TRACEABILITY_DUMP_UI_LIMITED_MYSQL8.sql';
            $originalFile = dirname(dirname(__DIR__)) . '/sql/TRACEABILITY_DUMP_UI_LIMITED.sql';
            
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Checking MySQL8 file: " . $mysql8File . "\n", FILE_APPEND);
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | MySQL8 file exists: " . (file_exists($mysql8File) ? 'YES' : 'NO') . "\n", FILE_APPEND);
            
            if (file_exists($mysql8File)) {
                $sqlFile = $mysql8File;
                @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Using MySQL8 version\n", FILE_APPEND);
            } else {
                $sqlFile = $originalFile;
                @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Using original version (fallback)\n", FILE_APPEND);
            }
            
            $exportEndpoint = 'export_traceability_minimal.php';
            $needsTestPlanFilter = true;
            break;
            
        case 'failed_no_defect':
            $reportTitle = 'Failed Test Cases - No Defect Logged';
            $sqlFile = dirname(dirname(__DIR__)) . '/sql/FAILED_NO_DEFECT_UI_LIMITED.sql';
            $exportEndpoint = 'export_failed_no_defect.php';
            $needsTestPlanFilter = false; // This report doesn't filter by test plan
            break;
            
        default:
            return "<div class='alert alert-error'>Unknown report ID: " . htmlspecialchars($reportId) . "</div>";
    }
    
    // Check test plan for reports that need it
    if ($needsTestPlanFilter && $testplanId <= 0) {
        return "<div class='alert alert-error'>No test plan selected.</div>";
    }

    if (!file_exists($sqlFile)) {
        $errorMsg = "SQL file not found at: " . htmlspecialchars($sqlFile);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
        return "<div class='alert alert-error'>" . $errorMsg . "</div>";
    }
    
    if (!is_readable($sqlFile)) {
        $errorMsg = "SQL file not readable at: " . htmlspecialchars($sqlFile);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
        return "<div class='alert alert-error'>" . $errorMsg . "</div>";
    }
    
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        $errorMsg = "Failed to read SQL file at: " . htmlspecialchars($sqlFile);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
        return "<div class='alert alert-error'>" . $errorMsg . "</div>";
    }
    
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | SQL file loaded successfully. Size: " . strlen($sql) . " bytes\n", FILE_APPEND);
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Using SQL file: " . basename($sqlFile) . "\n", FILE_APPEND);
    
    // Execute SET SESSION statements first (for MySQL Enterprise compatibility)
    $sessionStatements = [];
    if (strpos($sql, 'SET SESSION group_concat_max_len') !== false) {
        preg_match('/SET SESSION group_concat_max_len\s*=\s*[^;]+;/', $sql, $matches);
        if ($matches) {
            $sessionStatements[] = $matches[0];
        }
    }
    
    // Execute session settings first
    foreach ($sessionStatements as $sessionSql) {
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Setting session: " . trim($sessionSql) . "\n", FILE_APPEND);
        $sessionResult = $db->exec_query($sessionSql);
        if (!$sessionResult) {
            $sessionError = $db->error_msg();
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | WARNING | Session setting failed: " . $sessionError . "\n", FILE_APPEND);
        }
    }
    
    // Extract only the main SELECT query based on report type
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Processing SQL for $reportId\n", FILE_APPEND);
    
    if ($reportId === 'traceability_dump') {
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | SQL file path: " . $sqlFile . "\n", FILE_APPEND);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Contains MYSQL8: " . (strpos($sqlFile, 'MYSQL8') !== false ? 'YES' : 'NO') . "\n", FILE_APPEND);
        
        // Check if we're using the MySQL 8 compatible version
        if (strpos($sqlFile, 'MYSQL8') !== false) {
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Using MySQL 8 compatible query processing\n", FILE_APPEND);
            // MySQL 8 version - find the main query after SET SESSION
            $mainQueryStart = strpos($sql, 'SELECT');
            if ($mainQueryStart !== false) {
                $sql = substr($sql, $mainQueryStart);
            }
        } else {
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Using original query processing\n", FILE_APPEND);
            // Original version - find the start of the main query (after comments)
            $mainQueryStart = strpos($sql, '-- MAIN QUERY');
            if ($mainQueryStart !== false) {
                $mainQueryStart = strpos($sql, 'WITH', $mainQueryStart);
                if ($mainQueryStart !== false) {
                    $sql = substr($sql, $mainQueryStart);
                }
            }
        }
        // Replace @testplan_id with actual value
        $sql = str_replace('@testplan_id', intval($testplanId), $sql);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Test plan ID replaced: " . intval($testplanId) . "\n", FILE_APPEND);
    } else if ($reportId === 'failed_no_defect') {
        // Find the SELECT statement
        $mainQueryStart = strpos($sql, 'SELECT');
        if ($mainQueryStart !== false) {
            $sql = substr($sql, $mainQueryStart);
        }
    }
    
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | SQL prepared for execution\n", FILE_APPEND);
    
    // Test database connection with a simple query first
    try {
        $testResult = $db->exec_query("SELECT 1 as test");
        if (!$testResult) {
            $errorMsg = "Database connection test failed: " . $db->error_msg();
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
            return "<div class='alert alert-error'>Database connection test failed: " . htmlspecialchars($db->error_msg()) . "</div>";
        }
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Database connection test passed\n", FILE_APPEND);
        
        // Log database connection details (without sensitive info)
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Database connection established\n", FILE_APPEND);
        
        // Get database version and type
        $versionResult = $db->exec_query("SELECT VERSION() as version, @@version_comment as comment");
        if ($versionResult) {
            $versionRow = $db->fetch_array($versionResult);
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Database: " . $versionRow['comment'] . " " . $versionRow['version'] . "\n", FILE_APPEND);
        }
        
    } catch (Exception $dbTestException) {
        $errorMsg = "Database connection test exception: " . $dbTestException->getMessage();
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
        return "<div class='alert alert-error'>Database connection test failed: " . htmlspecialchars($dbTestException->getMessage()) . "</div>";
    }
    
    try {
        // Log the SQL for debugging
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Executing SQL: " . substr($sql, 0, 500) . "...\n", FILE_APPEND);
        
        // Set execution timeout for MySQL 8 Enterprise compatibility
        $startTime = microtime(true);
        $maxExecutionTime = 30; // 30 seconds max
        
        // Execute the query using exec_query (more reliable for complex queries)
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | About to execute query. SQL length: " . strlen($sql) . "\n", FILE_APPEND);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | SQL preview: " . substr($sql, 0, 200) . "...\n", FILE_APPEND);
        
        // Log the full SQL query to a separate file for debugging
        $sqlLogFile = dirname(__FILE__) . '/current_query.sql';
        file_put_contents($sqlLogFile, $sql);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Full SQL written to: " . basename($sqlLogFile) . "\n", FILE_APPEND);
        
        // Set a hard timeout for query execution
        $originalTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 10); // 10 second timeout
        
        try {
            $result = $db->exec_query($sql);
        } catch (Exception $execException) {
            // Restore timeout
            ini_set('default_socket_timeout', $originalTimeout);
            
            $errorMsg = "Exception during exec_query: " . $execException->getMessage() . " (Code: " . $execException->getCode() . ")";
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | Exception trace: " . $execException->getTraceAsString() . "\n", FILE_APPEND);
            return "<div class='alert alert-error'>Database exception: " . htmlspecialchars($execException->getMessage()) . "</div>";
        }
        
        // Restore timeout
        ini_set('default_socket_timeout', $originalTimeout);
        
        $executionTime = microtime(true) - $startTime;
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Query execution time: " . round($executionTime, 2) . " seconds\n", FILE_APPEND);
        
        if (!$result) {
            // Get the actual MySQL error
            $mysqlError = $db->error_msg();
            $mysqlNo = $db->error_no();
            $errorMsg = "Query execution failed (Error #$mysqlNo): " . $mysqlError;
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | Full SQL: " . $sql . "\n", FILE_APPEND);
            return "<div class='alert alert-error'>Query execution failed (Error #$mysqlNo): " . htmlspecialchars($mysqlError) . "</div>";
        }
        
        // Check if query timed out or took too long (MySQL 8 Enterprise performance issue)
        if ($executionTime > $maxExecutionTime) {
            @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | WARNING | Query took too long (" . round($executionTime, 2) . "s), possible MySQL 8 Enterprise performance issue\n", FILE_APPEND);
            return "<div class='alert alert-warning'>Query took too long to execute (" . round($executionTime, 2) . " seconds). This may be a MySQL 8 Enterprise performance issue. Please try simplifying the query or contact administrator.</div>";
        }
        
        // Fetch all rows into an array
        $rows = array();
        $rowCount = 0;
        while ($row = $db->fetch_array($result)) {
            $rows[] = $row;
            $rowCount++;
        }
        
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Fetched $rowCount rows\n", FILE_APPEND);
        
        if (empty($rows)) {
            return "<div class='alert alert-info'>No data found for the selected test plan.</div>";
        }
        
        // Get column names from the first row
        $firstRow = $rows[0];
        $columns = array_keys($firstRow);
        
        // Add download button and info (SQL already limits to 100 records for UI)
        $displayRows = $rows; // All rows from limited SQL query
        
    } catch (Exception $e) {
        $errorMsg = "Exception caught: " . $e->getMessage() . " (Code: " . $e->getCode() . ")";
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | " . $errorMsg . "\n", FILE_APPEND);
        @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | Stack trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
        return "<div class='alert alert-error'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    $displayCount = count($displayRows);
    
    $content .= "<h3 style='margin-bottom: 10px; color: #333;'>" . htmlspecialchars($reportTitle) . "</h3>";
    $content .= "<div style='margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;'>";
    $content .= "<p style='margin: 0 0 10px 0; color: #666;'>";
    $content .= "Showing " . number_format($displayCount) . " records (limited to 100 for UI performance)";
    $content .= " - Use download button for complete data set";
    $content .= "</p>";
    
    // Add download button with proper styling
    $content .= "<input type='button' id='downloadBtn' value='📥 Download Full Report (All Records)' onclick='downloadFullDump()' class='filter_button export_button' style='background:#224a7a !important; border-color: #224a7a !important; color: #dadada !important; font-weight: bold; padding: 10px 20px !important; cursor: pointer !important; border-radius: 4px !important; border: 1px solid #224a7a !important; font-size: 14px !important; transition: background-color 0.3s ease !important;' onmouseover=\"this.style.backgroundColor='#1a3a5c'\" onmouseout=\"this.style.backgroundColor='#224a7a'\" data-export-endpoint='" . htmlspecialchars($exportEndpoint) . "'>";
    $content .= "</div>";
    
    // Generate HTML table
    $content .= "<div class='table-responsive' style='overflow-x:auto; max-width: 100%;'>";
    $content .= "<table class='simple' style='width:100%; border-collapse: collapse;'>";
    
    // Table header
    $content .= "<thead><tr style='background-color: #f5f5f5;'>";
    foreach ($columns as $column) {
        $content .= "<th style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($column) . "</th>";
    }
    $content .= "</tr></thead><tbody>";
    
    // Table rows (limited to last 200)
    foreach ($displayRows as $row) {
        $content .= "<tr>";
        foreach ($columns as $column) {
            $value = isset($row[$column]) ? $row[$column] : '';
            $content .= "<td style='padding: 8px; border: 1px solid #ddd;'>" . nl2br(htmlspecialchars($value)) . "</td>";
        }
        $content .= "</tr>";
    }
    
    $content .= "</tbody></table></div>";
    
    // Add JavaScript for download functionality
    $content .= "<script>
function downloadFullDump() {
    var btn = document.getElementById('downloadBtn');
    var originalValue = btn.value;
    
    // Show downloading state
    btn.value = '⏳ Downloading...';
    btn.disabled = true;
    btn.style.backgroundColor = '#6c757d';
    btn.style.cursor = 'wait';
    
    // Get export endpoint from button data attribute
    var exportEndpoint = btn.getAttribute('data-export-endpoint');
    
    // Get current test plan ID from the dropdown (if it exists)
    var testplanIdElement = document.getElementById('testplan_id');
    var testplanId = testplanIdElement ? testplanIdElement.value : '';
    
    // Build URL with export endpoint
    var url = 'lib/execute/' + exportEndpoint + '?testplan_id=' + testplanId;
    
    // Use anchor element with download attribute for reliable file download
    var link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Restore button after a delay (download should have started)
    setTimeout(function() {
        btn.value = '✅ Download Complete!';
        btn.style.backgroundColor = '#28a745';
        
        setTimeout(function() {
            btn.value = originalValue;
            btn.disabled = false;
            btn.style.backgroundColor = '#224a7a';
            btn.style.cursor = 'pointer';
        }, 2000);
    }, 2000);
}
</script>";
    
    return $content;
}
