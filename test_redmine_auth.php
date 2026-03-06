<?php
$apiKey = 'c16548f2503932a9ef6d6d8f9a59393436e67f39';
$testUrl = 'https://support.profinch.com/issues/76296.json';

$ch = curl_init($testUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_HTTPHEADER => [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/json'
    ],
    CURLOPT_VERBOSE => true,
    CURLOPT_HEADER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "HTTP Status: $httpCode\n";
echo "Response Headers:\n$headers\n";
echo "Response Body:\n$body\n";

// Test with basic auth as alternative
$ch = curl_init($testUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_USERPWD => "$apiKey:password", // Some Redmine instances use basic auth with API key as username
    CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
    CURLOPT_VERBOSE => true,
    CURLOPT_HEADER => true
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "\nTrying with Basic Auth:\n";
echo "HTTP Status: $httpCode\n";
echo "Response Headers:\n$headers\n";
echo "Response Body:\n$body\n";
