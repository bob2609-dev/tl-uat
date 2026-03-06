<?php
/**
 * Traceability Dump Export - Standalone Export Endpoint
 * 
 * This file bypasses TestLink's framework to provide clean CSV exports
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Define log file path
$__procLog = __DIR__ . '/other_custom_reports_debug.log';
@file_put_contents($__procLog, date('Y-m-d H:i:s') . " | INFO | Export endpoint accessed\n", FILE_APPEND);

// Get testplan_id from GET parameters
$testplan_id = isset($_GET['testplan_id']) ? intval($_GET['testplan_id']) : 0;

if (!$testplan_id) {
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | No test plan ID provided\n", FILE_APPEND);
    die('No test plan selected');
}

// Minimal database connection - no TestLink framework
try {
    // Include only essential database config
    $configFile = __DIR__ . '/../../config.inc.php';
    if (!file_exists($configFile)) {
        die('Database configuration not found');
    }
    
    require_once($configFile);
    
    // Include TestLink's database setup (minimal)
    require_once(__DIR__ . '/common.php');
    
    // Database is now available as global $db
    global $db;
    if (!$db) {
        die('Database connection failed');
    }
    
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Database connected for testplan $testplan_id\n", FILE_APPEND);
    
} catch (Exception $e) {
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | DB connection error: " . $e->getMessage() . "\n", FILE_APPEND);
    die('Database connection error: ' . $e->getMessage());
}

// Execute the traceability dump query
$sqlFile = dirname(__DIR__) . '/sql/TRACEABILITY_DUMP_SCRIPT_UPDATED.sql';
if (!file_exists($sqlFile)) {
    die('Traceability dump SQL file not found');
}

$sql = file_get_contents($sqlFile);

// Extract only the main SELECT query (skip USE, CALL, SET statements)
$mainQueryStart = strpos($sql, '-- =====================================================================================');
$mainQueryStart = strpos($sql, '-- MAIN QUERY', $mainQueryStart);
$mainQueryStart = strpos($sql, 'WITH', $mainQueryStart);

if ($mainQueryStart === false) {
    die('Could not find main query in SQL file');
}

$sql = substr($sql, $mainQueryStart);
$sql = str_replace('@testplan_id', intval($testplan_id), $sql);

@file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Executing export SQL\n", FILE_APPEND);

try {
    // Execute the query
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
    
    // Clean any output buffer
    while(ob_get_level()) {
        ob_end_clean();
    }
    
    // Remove any existing headers that might interfere
    header_remove();
    
    // Set explicit headers to override server defaults
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="traceability_dump_' . date('Y-m-d') . '.csv"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Content-Transfer-Encoding: binary');
    
    // Log buffer levels for debugging
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | Buffer level before output: " . ob_get_level() . "\n", FILE_APPEND);
    
    // Output CSV
    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | DEBUG | CSV export completed successfully\n", FILE_APPEND);
    
    // Exit immediately to prevent any framework interference
    exit();
    
} catch (Exception $e) {
    @file_put_contents($__procLog, date('Y-m-d H:i:s') . " | ERROR | Export exception: " . $e->getMessage() . "\n", FILE_APPEND);
    die('Error generating export: ' . $e->getMessage());
}
?>
