<?php
/**
 * Improved Redmine API Test Script
 * 
 * This script tests the connection to Redmine API with improved error handling
 * and fixes for common issues like URL formatting and SSL verification.
 * 
 * Usage:
 * 1. Configure the variables below
 * 2. Run: php redmine-api-test.php
 */

// Configuration (replace with your actual values)
$redmineUrl = 'https://support.profinch.com'; // Removed trailing slash
$apiKey = 'a597e200f8923a85484e81ca81d731827b8dbf3d'; 
$projectId = 'nmb-fcubs-14-7-uat2';
$impersonateUser = 'nmb'; // If using XRedmineSwitchUser, put the username here

// Helper function to normalize URL (prevent double slashes)
function buildUrl($baseUrl, $endpoint) {
    return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
}

// Test 1: Verify API connection by getting users
function testApiConnection($redmineUrl, $apiKey, $impersonateUser = null) {
    echo "=== Testing API Connection ===\n";
    
    $ch = curl_init(buildUrl($redmineUrl, 'users.xml?limit=1'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $headers = [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/xml'
    ];
    
    if (!empty($impersonateUser)) {
        $headers[] = 'X-Redmine-Switch-User: ' . $impersonateUser;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        echo "❌ CURL Error: " . curl_error($ch) . "\n";
        return false;
    }
    
    $info = curl_getinfo($ch);
    
    echo "Request URL: " . $info['url'] . "\n";
    echo "HTTP Code: " . $info['http_code'] . "\n";
    echo "SSL Verify Result: " . curl_getinfo($ch, CURLINFO_SSL_VERIFYRESULT) . " (0 = success)\n";
    echo "Response Header Size: " . $info['header_size'] . "\n";
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    echo "Response Headers:\n" . $header . "\n";
    echo "Response Body:\n" . $body . "\n";
    
    if ($info['http_code'] == 200) {
        echo "✅ API Connection Successful\n";
        return true;
    } else {
        echo "❌ API Connection Failed (HTTP Code: " . $info['http_code'] . ")\n";
        
        // More detailed error information based on HTTP code
        if ($info['http_code'] == 403) {
            echo "⚠️ 403 Forbidden: This usually means either:\n";
            echo "   - The API key is invalid\n";
            echo "   - REST API is not enabled in Redmine\n";
            echo "   - The user doesn't have sufficient permissions\n";
        } else if ($info['http_code'] == 401) {
            echo "⚠️ 401 Unauthorized: This suggests your API key is invalid\n";
        } else if ($info['http_code'] == 0) {
            echo "⚠️ Connection failed: This could be a network or SSL issue\n";
        }
        
        return false;
    }
}

// Test 2: Create a test issue
function createTestIssue($redmineUrl, $apiKey, $projectId, $impersonateUser = null) {
    echo "\n=== Testing Issue Creation ===\n";
    
    // Create XML for the issue
    $issueXml = '<?xml version="1.0"?>
<issue>
    <subject>Test Issue from API Script - ' . date('Y-m-d H:i:s') . '</subject>
    <description>This is a test issue created to verify API functionality.</description>
    <project_id>' . htmlspecialchars($projectId) . '</project_id>
</issue>';
    
    echo "Issue XML:\n" . $issueXml . "\n\n";
    
    $ch = curl_init(buildUrl($redmineUrl, 'issues.xml'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $issueXml);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $headers = [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/xml'
    ];
    
    if (!empty($impersonateUser)) {
        $headers[] = 'X-Redmine-Switch-User: ' . $impersonateUser;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        echo "❌ CURL Error: " . curl_error($ch) . "\n";
        return false;
    }
    
    $info = curl_getinfo($ch);
    
    echo "Request URL: " . $info['url'] . "\n";
    echo "HTTP Code: " . $info['http_code'] . "\n";
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    echo "Response Headers:\n" . $header . "\n";
    echo "Response Body:\n" . $body . "\n";
    
    if ($info['http_code'] == 201) {
        echo "✅ Issue Creation Successful\n";
        
        // Try to extract the issue ID from the response
        if (preg_match('/<id>(\d+)<\/id>/', $body, $matches)) {
            echo "🎯 Created Issue ID: " . $matches[1] . "\n";
        }
        
        return true;
    } else {
        echo "❌ Issue Creation Failed (HTTP Code: " . $info['http_code'] . ")\n";
        
        // More detailed error information based on HTTP code
        if ($info['http_code'] == 403) {
            echo "⚠️ 403 Forbidden: You don't have permission to create issues\n";
        } else if ($info['http_code'] == 422) {
            echo "⚠️ 422 Unprocessable Entity: The issue data is invalid (possibly missing required fields)\n";
        } else if ($info['http_code'] == 404) {
            echo "⚠️ 404 Not Found: The project ID might be incorrect\n";
        }
        
        return false;
    }
}

// Test 3: Check projects endpoint
function checkProjects($redmineUrl, $apiKey, $impersonateUser = null) {
    echo "\n=== Testing Project Access ===\n";
    
    $ch = curl_init(buildUrl($redmineUrl, 'projects.xml'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $headers = [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/xml'
    ];
    
    if (!empty($impersonateUser)) {
        $headers[] = 'X-Redmine-Switch-User: ' . $impersonateUser;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        echo "❌ CURL Error: " . curl_error($ch) . "\n";
        return false;
    }
    
    $info = curl_getinfo($ch);
    
    echo "Request URL: " . $info['url'] . "\n";
    echo "HTTP Code: " . $info['http_code'] . "\n";
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    echo "Response Headers:\n" . $header . "\n";
    
    // Checking if response contains valid XML
    if ($info['http_code'] == 200 && !empty($body)) {
        // Check if we can find the project we're looking for
        $projectFound = false;
        if (strpos($body, $projectId) !== false) {
            $projectFound = true;
            echo "✅ Project '$projectId' found in response\n";
        }
        
        echo "Response Body (first 500 chars):\n" . substr($body, 0, 500) . "...\n";
        
        echo "✅ Project Access Successful\n";
        return true;
    } else {
        echo "Response Body:\n" . $body . "\n";
        echo "❌ Project Access Failed (HTTP Code: " . $info['http_code'] . ")\n";
        
        // More detailed error information based on HTTP code
        if ($info['http_code'] == 403) {
            echo "⚠️ 403 Forbidden: You don't have permission to view projects\n";
        }
        
        return false;
    }
}

// Test 4: Try alternative approach with basic authentication
function testBasicAuth($redmineUrl, $apiKey) {
    echo "\n=== Testing Alternative Authentication Methods ===\n";
    
    // Try using the API key as username with 'X' as password (Redmine alternative method)
    $ch = curl_init(buildUrl($redmineUrl, 'users/current.xml'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':X');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        echo "❌ CURL Error: " . curl_error($ch) . "\n";
        return false;
    }
    
    $info = curl_getinfo($ch);
    
    echo "Request URL: " . $info['url'] . "\n";
    echo "HTTP Code: " . $info['http_code'] . "\n";
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    echo "Response Headers:\n" . $header . "\n";
    echo "Response Body:\n" . $body . "\n";
    
    if ($info['http_code'] == 200) {
        echo "✅ Alternative Authentication Successful\n";
        return true;
    } else {
        echo "❌ Alternative Authentication Failed\n";
        return false;
    }
}

// Run tests
echo "Starting Redmine API Tests\n";
echo "Redmine URL: $redmineUrl\n";
echo "Project ID: $projectId\n";
echo "API Key: " . substr($apiKey, 0, 5) . "..." . substr($apiKey, -5) . "\n";
if (!empty($impersonateUser)) {
    echo "Impersonating User: $impersonateUser\n";
}
echo "\n";

// First try without impersonation
echo "First attempt - without user impersonation\n";
$connectionOk = testApiConnection($redmineUrl, $apiKey, null);

// If it fails, try with impersonation (if set)
if (!$connectionOk && !empty($impersonateUser)) {
    echo "\nRetrying with user impersonation...\n";
    $connectionOk = testApiConnection($redmineUrl, $apiKey, $impersonateUser);
}

// If both fail, try alternative authentication
if (!$connectionOk) {
    echo "\nTrying alternative authentication method...\n";
    $connectionOk = testBasicAuth($redmineUrl, $apiKey);
}

// Only proceed with projects and issue tests if connection is successful
$projectsOk = $connectionOk ? checkProjects($redmineUrl, $apiKey, $impersonateUser) : false;
$issueOk = $projectsOk ? createTestIssue($redmineUrl, $apiKey, $projectId, $impersonateUser) : false;

echo "\n=== Test Summary ===\n";
echo "API Connection: " . ($connectionOk ? "✅ SUCCESS" : "❌ FAILED") . "\n";
echo "Project Access: " . ($projectsOk ? "✅ SUCCESS" : "❌ FAILED") . "\n";
echo "Issue Creation: " . ($issueOk ? "✅ SUCCESS" : "❌ FAILED") . "\n";

// Detailed troubleshooting tips based on which tests failed
if (!$connectionOk) {
    echo "\nTROUBLESHOOTING TIPS:\n";
    echo "- Check that your API key is correct\n";
    echo "- Verify the Redmine URL is accessible\n";
    echo "- Make sure REST API is enabled in Redmine (Administration > Settings > API)\n";
    echo "- Check if your user account has API access enabled\n";
    echo "- Try accessing the Redmine site in a browser to verify connectivity\n";
    echo "- Try generating a new API key from your Redmine account settings\n";
    
    // TestLink specific advice
    echo "\nFor TestLink Integration:\n";
    echo "- Check that the API key in TestLink's config.inc.php is correct\n";
    echo "- Make sure TestLink's interface_bugs configuration uses the Redmine URL without trailing slash\n";
    echo "- Add 'skip_ssl_verification' => true to your interface_bugs_configuration array\n";
} else if (!$projectsOk) {
    echo "\nTROUBLESHOOTING TIPS:\n";
    echo "- Verify the user associated with API key has access to view projects\n";
    echo "- Check if the user needs additional permissions in Redmine\n";
} else if (!$issueOk) {
    echo "\nTROUBLESHOOTING TIPS:\n";
    echo "- Make sure project ID '$projectId' exists and is correctly formatted\n";
    echo "- Verify the user has permission to create issues in this project\n";
    echo "- Check if required custom fields are missing in the issue XML\n";
    echo "- Try creating an issue manually in Redmine UI to verify permissions\n";
}
?>