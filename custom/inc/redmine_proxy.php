<?php
/**
 * Redmine Proxy for TestLink
 * 
 * This file acts as a proxy to fetch bug details from Redmine
 * and return them to the browser without exposing the API key.
 */

// Include our serialization fix
require_once(dirname(dirname(dirname(__FILE__))) . '/custom_config.inc.php');

// Get the bug ID from the request
$bugId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$bugId) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No bug ID provided']);
    exit;
}

// Get the Redmine configuration from TestLink
global $tlCfg;
$redmineUrl = $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'];
$redmineApiKey = $tlCfg->issueTracker->toolsDefaultValues['redmine']['apikey'];

// Log the request
$logFile = dirname(dirname(dirname(__FILE__))) . '/redmine_integration_log.txt';
file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] Proxy fetching bug details for: ' . $bugId . "\n", FILE_APPEND);

// Fetch the bug details from Redmine
$url = $redmineUrl . '/issues/' . $bugId . '.json';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'X-Redmine-API-Key: ' . $redmineApiKey
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Log the response
file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] Redmine response code: ' . $httpCode . "\n", FILE_APPEND);

// Return the response to the browser
header('Content-Type: application/json');
echo $response;
