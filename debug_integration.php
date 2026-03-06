<?php
// Simplified TestLink Bootstrap
// Enables standalone execution of scripts that require the TestLink framework
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define NOCRYPT to avoid session/encryption errors in CLI
define('NOCRYPT', true);

// Get the correct base path for both web and CLI contexts
$basePath = dirname(__DIR__); // Assumes this file is in the root
if (!file_exists($basePath . '/config.inc.php')) {
    $basePath = __DIR__;
}

// Required TestLink files
require_once($basePath . '/config.inc.php');
require_once($basePath . '/lib/functions/common.php');
require_once($basePath . '/lib/functions/database.class.php');

// Establish database connection
try {
    $db = new database(DB_TYPE);
    $link = doDBConnect($db, database::ONERROREXIT); // Use ONERROREXIT for immediate feedback
    echo "Database connection successful.<br>";
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

$tproject_id = 448287;

echo "<h1>Integration Diagnostic for Test Project ID: {$tproject_id}</h1>";

// 1. Check for mappings for the project
echo "<h2>1. Checking 'custom_bugtrack_project_mapping' table...</h2>";
$sql_map = "SELECT * FROM custom_bugtrack_project_mapping WHERE tproject_id = {$tproject_id}";
$result_map = $db->exec_query($sql_map);
$mappings = [];
while ($row = $db->fetch_array($result_map)) {
    $mappings[] = $row;
}

if (count($mappings) > 0) {
    echo "<p style='color:green;'>Found " . count($mappings) . " mapping(s) for this project.</p>";
    echo "<table border='1'><tr><th>ID</th><th>Integration ID</th><th>Is Active?</th></tr>";
    foreach ($mappings as $mapping) {
        echo "<tr><td>{$mapping['id']}</td><td>{$mapping['integration_id']}</td><td>" . ($mapping['is_active'] ? 'YES' : 'NO') . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>No mappings found for this project in 'custom_bugtrack_project_mapping'.</p>";
    exit;
}

// 2. Check the integrations linked by the mappings
echo "<h2>2. Checking 'custom_bugtrack_integrations' table for linked integrations...</h2>";
$integration_ids = array_column($mappings, 'integration_id');
$sql_int = "SELECT * FROM custom_bugtrack_integrations WHERE id IN (" . implode(',', $integration_ids) . ")";
$result_int = $db->exec_query($sql_int);
$integrations = [];
while ($row = $db->fetch_array($result_int)) {
    $integrations[$row['id']] = $row;
}

if (count($integrations) > 0) {
    echo "<p style='color:green;'>Found " . count($integrations) . " linked integration(s).</p>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Type</th><th>URL</th><th>Is Active?</th></tr>";
    foreach ($integrations as $integration) {
        echo "<tr><td>{$integration['id']}</td><td>{$integration['name']}</td><td>{$integration['type']}</td><td>{$integration['url']}</td><td>" . ($integration['is_active'] ? 'YES' : 'NO') . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>No integrations found for the mapped IDs.</p>";
    exit;
}

// 3. Run the final query from the application
echo "<h2>3. Running the application's query...</h2>";
$sql_app = "SELECT i.id, i.name, i.type, i.url 
            FROM custom_bugtrack_integrations i
            INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
            WHERE m.tproject_id = {$tproject_id}
            AND m.is_active = 1
            AND i.is_active = 1
            ORDER BY i.name ASC";
$result_app = $db->exec_query($sql_app);
$app_integrations = [];
while ($row = $db->fetch_array($result_app)) {
    $app_integrations[] = $row;
}

if (count($app_integrations) > 0) {
    echo "<p style='color:green;'>The application query returned " . count($app_integrations) . " integration(s).</p>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Type</th><th>URL</th></tr>";
    foreach ($app_integrations as $integration) {
        echo "<tr><td>{$integration['id']}</td><td>{$integration['name']}</td><td>{$integration['type']}</td><td>{$integration['url']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>The application query returned 0 integrations.</p>";
    echo "<p>This is likely because either the mapping or the integration is not marked as 'is_active = 1'.</p>";
}

?>
