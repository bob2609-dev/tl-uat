<?php
// Simple test script for Redmine connectivity
error_reporting(E_ALL);
ini_set('display_errors', 1);

$url = 'https://support.profinch.com/issues.xml';
$apiKey = 'a597e200f8923a85484e81ca81d731827b8dbf3d';

echo "<h2>Redmine Connection Test</h2>";
echo "Attempting to connect to: $url<br>";

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/xml',
    'X-Redmine-API-Key: ' . $apiKey
));

// Execute request
echo "Executing cURL request...<br>";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if ($response === false) {
    echo "<p style='color:red;'>cURL Error: " . curl_error($ch) . "</p>";
} else {
    echo "<p>HTTP Status Code: $httpCode</p>";
    
    if ($httpCode == 200) {
        echo "<p style='color:green;'>Connection Successful!</p>";
        echo "<p>Response Preview (first 300 chars):</p>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 300)) . "</pre>";
    } else {
        echo "<p style='color:red;'>Connection Failed! HTTP Status: $httpCode</p>";
        echo "<p>Response:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

// Close cURL session
curl_close($ch);
?>
