<?php
/**
 * Test script for custom_bugtrack_integrator_simple.php
 */

// Test data
$testData = array(
    'tproject_id' => 448287,
    'tc_id' => 431184,
    'execution_id' => 81646,
    'summary' => 'TESTLINK INTEGRATION TEST',
    'description' => 'This is a test issue created via API',
    'priority' => 'Normal',
    'assigned_to' => 2635,
    'integration_id' => 9
);

echo "Testing custom_bugtrack_integrator_simple.php...\n";
echo "POST data: " . json_encode($testData) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8086/lib/execute/custom_bugtrack_integrator_simple.php?action=create_issue');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "cURL Error: $error\n";
echo "Response:\n$response\n";

// Also check if log file exists and show its contents
$logFile = 'lib/execute/custom_integration.log';
if (file_exists($logFile)) {
    echo "\n--- Log file contents ---\n";
    echo file_get_contents($logFile);
} else {
    echo "\nLog file does not exist: $logFile\n";
}
?>
