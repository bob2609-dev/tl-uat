<?php
/**
 * Test API bypassing authentication
 */

// Change to the correct directory context
chdir(dirname(__FILE__));

echo "=== API Bypass Test ===\n";

// Test with curl to bypass any authentication issues
$ch = curl_init();
$url = 'http://localhost/tl-uat/lib/execute/custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=242099';

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'TestLink-API-Test/1.0');

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response: " . $response . "\n";

if ($http_code == 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "SUCCESS: API call worked!\n";
        print_r($data);
    } else {
        echo "ERROR: Failed to decode JSON\n";
    }
} else {
    echo "ERROR: HTTP $http_code\n";
}
?>
