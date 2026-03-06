<?php
/**
 * Simple test for API endpoint
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

echo "=== Simple API Test ===\n";

// Test the API endpoint directly
$url = 'http://test-management.nmbtz.com:9443/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=242099';

echo "Testing URL: $url\n";

// Use file_get_contents for simple test
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

$response = file_get_contents($url, false, $context);

if ($response === false) {
    echo "ERROR: Failed to call API\n";
    echo "Error: " . error_get_last()['message'] . "\n";
} else {
    echo "Response: " . $response . "\n";
    
    // Try to decode JSON
    $data = json_decode($response, true);
    if ($data) {
        echo "Decoded JSON:\n";
        print_r($data);
    } else {
        echo "Failed to decode JSON\n";
    }
}
?>
