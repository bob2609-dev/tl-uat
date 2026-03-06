<?php
/**
 * Minimal Traceability Dump Export - NO TestLink Framework
 * 
 * This file uses raw database connection to bypass all framework interference
 */

// Disable all output buffering
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');
while(ob_get_level()) ob_end_clean();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get testplan_id
$testplan_id = isset($_GET['testplan_id']) ? intval($_GET['testplan_id']) : 0;
if (!$testplan_id) {
    die('No test plan selected');
}

// Include database config file which defines DB_HOST, DB_NAME, DB_USER, DB_PASS constants
$configDbFile = __DIR__ . '/../../config_db.inc.php';
if (!file_exists($configDbFile)) {
    die('Database config file not found');
}

require_once($configDbFile);

// Use the defined constants
$host = DB_HOST;
$database = DB_NAME;
$username = DB_USER;
$password = DB_PASS;

// Create raw MySQLi connection
$mysqli = new mysqli($host, $username, $password, $database);
if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Load and prepare SQL - Use MySQL 8 compatible version for better performance
$sqlFile = dirname(dirname(__DIR__)) . '/sql/TRACEABILITY_DUMP_MYSQL8.sql';
if (!file_exists($sqlFile)) {
    // Fallback to original if MySQL 8 version doesn't exist
    $sqlFile = dirname(dirname(__DIR__)) . '/sql/TRACEABILITY_DUMP_SCRIPT_UPDATED.sql';
}
if (!file_exists($sqlFile)) {
    die('SQL file not found');
}

$sql = file_get_contents($sqlFile);

// Handle different SQL file structures
if (strpos($sqlFile, 'MYSQL8') !== false) {
    // MySQL 8 version - find the main SELECT after SET SESSION
    $mainQueryStart = strpos($sql, 'SELECT');
    if ($mainQueryStart === false) {
        die('Could not find main query in MySQL8 file');
    }
} else {
    // Original version - find the main query after comments
    $mainQueryStart = strpos($sql, '-- MAIN QUERY');
    $mainQueryStart = strpos($sql, 'WITH', $mainQueryStart);
    if ($mainQueryStart === false) {
        die('Could not find main query in original file');
    }
}

$sql = substr($sql, $mainQueryStart);
$sql = str_replace('@testplan_id', $testplan_id, $sql);

// Execute query
$result = $mysqli->query($sql);
if (!$result) {
    die('Query failed: ' . $mysqli->error);
}

// Fetch all data
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

if (empty($rows)) {
    die('No data found');
}

// Set headers BEFORE any output
header_remove();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="traceability_dump_' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
header('Content-Transfer-Encoding: binary');

// Output CSV
$output = fopen('php://output', 'w');
fputcsv($output, array_keys($rows[0]));
foreach ($rows as $row) {
    fputcsv($output, $row);
}
fclose($output);

// Force exit
exit;
?>
