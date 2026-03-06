<?php
/**
 * Failed No Defect Export - Standalone Export Endpoint
 * 
 * Exports test cases that have FAILED latest execution with NO defect IDs logged
 */

// Disable all output buffering
ini_set('output_buffering', 'Off');
ini_set('zlib.output_compression', 'Off');
while(ob_get_level()) ob_end_clean();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database config file
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

// Load and prepare SQL (use FULL version without limit)
$sqlFile = dirname(dirname(__DIR__)) . '/sql/FAILED_NO_DEFECT_FULL.sql';
if (!file_exists($sqlFile)) {
    die('SQL file not found');
}

$sql = file_get_contents($sqlFile);

// Find the SELECT statement
$mainQueryStart = strpos($sql, 'SELECT');
if ($mainQueryStart !== false) {
    $sql = substr($sql, $mainQueryStart);
}

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
header('Content-Disposition: attachment; filename="failed_no_defect_' . date('Y-m-d') . '.csv"');
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
