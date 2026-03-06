<?php
/**
 * Redmine API Test Script for TestLink Integration
 * 
 * This script tests the connection to Redmine API and helps diagnose issues
 * that might be preventing TestLink from connecting to your Redmine instance.
 * 
 * Usage:
 * 1. Configure the variables below
 * 2. Access this file from your browser
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration - Edit these values to match your setup
$redmineUrl = 'https://support.profinch.com'; // No trailing slash
$apiKey = 'a597e200f8923a85484e81ca81d731827b8dbf3d';
$projectId = 'nmb-fcubs-14-7-uat2';

// Helper function to normalize URL
function buildUrl($baseUrl, $endpoint) {
    return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
}

// Helper function to output results in formatted HTML
function outputResult($title, $success, $details = '') {
    echo "<div class='test-result " . ($success ? 'success' : 'failure') . "'>";
    echo "<h3>" . ($success ? '✅' : '❌') . " $title</h3>";
    if (!empty($details)) {
        echo "<div class='details'>$details</div>";
    }
    echo "</div>";
    return $success;
}

// Test 1: Basic cURL availability
function testCurlAvailability() {
    echo "<h2>Testing cURL Availability</h2>";
    
    if (!function_exists('curl_version')) {
        return outputResult('cURL Extension', false, 
            'The cURL extension is not available. This is required for TestLink to connect to Redmine.');
    }
    
    $curlInfo = curl_version();
    $details = "cURL Version: {$curlInfo['version']}<br>";
    $details .= "SSL Version: {$curlInfo['ssl_version']}<br>";
    $details .= "libz Version: {$curlInfo['libz_version']}";
    
    return outputResult('cURL Extension', true, $details);
}

// Test 2: Verify API connection
function testApiConnection($redmineUrl, $apiKey) {
    echo "<h2>Testing Redmine API Connection</h2>";
    
    $ch = curl_init(buildUrl($redmineUrl, 'users/current.xml'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);     // Disable host verification
    
    $headers = [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/xml'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        return outputResult('API Connection', false, 
            'cURL Error: ' . curl_error($ch));
    }
    
    $info = curl_getinfo($ch);
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    $details = "Request URL: {$info['url']}<br>";
    $details .= "HTTP Code: {$info['http_code']}<br>";
    $details .= "SSL Verify Result: " . curl_getinfo($ch, CURLINFO_SSL_VERIFYRESULT) . " (0 = success)<br>";
    $details .= "Response Headers:<br><pre>" . htmlspecialchars($header) . "</pre>";
    
    if ($info['http_code'] == 200) {
        $details .= "Response Body (first 500 chars):<br><pre>" . htmlspecialchars(substr($body, 0, 500)) . "...</pre>";
        return outputResult('API Connection', true, $details);
    } else {
        $details .= "Response Body:<br><pre>" . htmlspecialchars($body) . "</pre>";
        
        // Add troubleshooting tips based on error code
        if ($info['http_code'] == 403) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- The API key may be invalid<br>";
            $details .= "- REST API might not be enabled in Redmine<br>";
            $details .= "- The user doesn't have sufficient permissions<br>";
        } else if ($info['http_code'] == 401) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- Your API key is likely invalid<br>";
        }
        
        return outputResult('API Connection', false, $details);
    }
}

// Test 3: Check projects endpoint
function checkProjects($redmineUrl, $apiKey, $projectId) {
    echo "<h2>Testing Project Access</h2>";
    
    $ch = curl_init(buildUrl($redmineUrl, 'projects.xml'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $headers = [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/xml'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        return outputResult('Project Access', false, 
            'cURL Error: ' . curl_error($ch));
    }
    
    $info = curl_getinfo($ch);
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    $details = "Request URL: {$info['url']}<br>";
    $details .= "HTTP Code: {$info['http_code']}<br>";
    $details .= "Response Headers:<br><pre>" . htmlspecialchars($header) . "</pre>";
    
    // Check if we got a successful response
    if ($info['http_code'] == 200) {
        // Check if the specified project ID is in the response
        $projectFound = strpos($body, $projectId) !== false;
        
        $details .= "Response Body (first 500 chars):<br><pre>" . htmlspecialchars(substr($body, 0, 500)) . "...</pre>";
        
        if ($projectFound) {
            $details .= "<br><strong>Project found!</strong> The project ID '$projectId' was found in the response.";
        } else {
            $details .= "<br><strong>Warning:</strong> Project ID '$projectId' was not found in the response. Please verify the project identifier.";
        }
        
        return outputResult('Project Access', true, $details);
    } else {
        $details .= "Response Body:<br><pre>" . htmlspecialchars($body) . "</pre>";
        
        // Add troubleshooting tips
        if ($info['http_code'] == 403) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- You don't have permission to view projects<br>";
        }
        
        return outputResult('Project Access', false, $details);
    }
}

// Test 4: Check specific project endpoint
function checkSpecificProject($redmineUrl, $apiKey, $projectId) {
    echo "<h2>Testing Specific Project Access</h2>";
    
    $ch = curl_init(buildUrl($redmineUrl, "projects/$projectId.xml"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $headers = [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/xml'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        return outputResult('Specific Project Access', false, 
            'cURL Error: ' . curl_error($ch));
    }
    
    $info = curl_getinfo($ch);
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    $details = "Request URL: {$info['url']}<br>";
    $details .= "HTTP Code: {$info['http_code']}<br>";
    $details .= "Response Headers:<br><pre>" . htmlspecialchars($header) . "</pre>";
    
    if ($info['http_code'] == 200) {
        $details .= "Response Body:<br><pre>" . htmlspecialchars($body) . "</pre>";
        return outputResult('Specific Project Access', true, $details);
    } else {
        $details .= "Response Body:<br><pre>" . htmlspecialchars($body) . "</pre>";
        
        // Add troubleshooting tips
        if ($info['http_code'] == 404) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- Project ID '$projectId' does not exist<br>";
            $details .= "- Check if the project identifier is correct (case sensitive)<br>";
        }
        
        return outputResult('Specific Project Access', false, $details);
    }
}

// Test 5: Try issues endpoint
function checkIssuesEndpoint($redmineUrl, $apiKey, $projectId) {
    echo "<h2>Testing Issues Endpoint</h2>";
    
    $ch = curl_init(buildUrl($redmineUrl, "issues.xml?project_id=$projectId&limit=1"));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $headers = [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/xml'
    ];
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        return outputResult('Issues Endpoint', false, 
            'cURL Error: ' . curl_error($ch));
    }
    
    $info = curl_getinfo($ch);
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    $details = "Request URL: {$info['url']}<br>";
    $details .= "HTTP Code: {$info['http_code']}<br>";
    $details .= "Response Headers:<br><pre>" . htmlspecialchars($header) . "</pre>";
    
    if ($info['http_code'] == 200) {
        $details .= "Response Body (first 500 chars):<br><pre>" . htmlspecialchars(substr($body, 0, 500)) . "...</pre>";
        return outputResult('Issues Endpoint', true, $details);
    } else {
        $details .= "Response Body:<br><pre>" . htmlspecialchars($body) . "</pre>";
        
        // Add troubleshooting tips
        if ($info['http_code'] == 403) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- You don't have permission to view issues<br>";
        } else if ($info['http_code'] == 404) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- Project ID '$projectId' might be incorrect<br>";
        }
        
        return outputResult('Issues Endpoint', false, $details);
    }
}

// Test 6: Try creating a test issue
function createTestIssue($redmineUrl, $apiKey, $projectId) {
    echo "<h2>Testing Issue Creation</h2>";
    
    // Create XML for the issue
    $issueXml = '<?xml version="1.0"?>
<issue>
    <subject>Test Issue from TestLink Diagnostic Tool - ' . date('Y-m-d H:i:s') . '</subject>
    <description>This is a test issue created to verify API functionality with TestLink.</description>
    <project_id>' . htmlspecialchars($projectId) . '</project_id>
</issue>';
    
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
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    
    if ($response === false) {
        return outputResult('Issue Creation', false, 
            'cURL Error: ' . curl_error($ch));
    }
    
    $info = curl_getinfo($ch);
    
    // Separate header and body
    $header = substr($response, 0, $info['header_size']);
    $body = substr($response, $info['header_size']);
    
    $details = "Issue XML:<br><pre>" . htmlspecialchars($issueXml) . "</pre><br>";
    $details .= "Request URL: {$info['url']}<br>";
    $details .= "HTTP Code: {$info['http_code']}<br>";
    $details .= "Response Headers:<br><pre>" . htmlspecialchars($header) . "</pre>";
    $details .= "Response Body:<br><pre>" . htmlspecialchars($body) . "</pre>";
    
    if ($info['http_code'] == 201) {
        // Try to extract the issue ID from the response
        if (preg_match('/<id>(\d+)<\/id>/', $body, $matches)) {
            $details .= "<br><strong>Created Issue ID:</strong> " . $matches[1];
        }
        
        return outputResult('Issue Creation', true, $details);
    } else {
        // Add troubleshooting tips
        if ($info['http_code'] == 403) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- You don't have permission to create issues<br>";
        } else if ($info['http_code'] == 422) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- The issue data might be missing required fields<br>";
            $details .= "- Check if there are mandatory custom fields not included<br>";
        } else if ($info['http_code'] == 404) {
            $details .= "<br><strong>Troubleshooting Tips:</strong><br>";
            $details .= "- Project ID '$projectId' might be incorrect<br>";
        }
        
        return outputResult('Issue Creation', false, $details);
    }
}

// Generate TestLink specific configuration advice
function generateTestLinkConfigAdvice($redmineUrl, $apiKey, $projectId) {
    echo "<h2>TestLink Configuration Advice</h2>";
    
    $configCode = "<?php\n";
    $configCode .= "// Issue tracker integration - Redmine with SSL fix\n";
    $configCode .= "\$tlCfg->interface_bugs = true;\n";
    $configCode .= "\$tlCfg->exec_cfg->user_can_create_bugs = true;\n";
    $configCode .= "\$tlCfg->issue_tracker_enabled = true;\n\n";
    
    $configCode .= "// Ensure the bug tracker configuration object exists\n";
    $configCode .= "if (!property_exists(\$tlCfg, 'issueTracker')) {\n";
    $configCode .= "    \$tlCfg->issueTracker = new stdClass();\n";
    $configCode .= "}\n\n";
    
    $configCode .= "// Define the default issue tracker\n";
    $configCode .= "\$tlCfg->issueTracker->toolsDefaultValues = array();\n";
    $configCode .= "\$tlCfg->issueTracker->toolsDefaultValues['redmine'] = array();\n";
    $configCode .= "\$tlCfg->issueTracker->toolsDefaultValues['redmine']['urlencode_ctl'] = 0;\n";
    $configCode .= "\$tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] = '{$redmineUrl}';\n";
    $configCode .= "\$tlCfg->issueTracker->toolsDefaultValues['redmine']['APIKey'] = '{$apiKey}';\n";
    $configCode .= "\$tlCfg->issueTracker->toolsDefaultValues['redmine']['projectkey'] = '{$projectId}';\n\n";
    
    $configCode .= "// Register our custom SSL fix class with TestLink\n";
    $configCode .= "require_once('lib/issuetrackerintegration/redminerestSslFix.class.php');\n";
    $configCode .= "\$tlCfg->issueTrackerIntegration = new stdClass();\n";
    $configCode .= "\$tlCfg->issueTrackerIntegration->enabled = true;\n";
    $configCode .= "\$tlCfg->issueTrackerIntegration->interfaceClass = array();\n";
    $configCode .= "\$tlCfg->issueTrackerIntegration->interfaceClass['REDMINERESTSSLFIX'] = 'redminerestSslFix';\n";
    
    $details = "<p>Add this code to your <strong>custom_config.inc.php</strong> file:</p>";
    $details .= "<pre>" . htmlspecialchars($configCode) . "</pre>";
    
    $details .= "<p>XML configuration for your TestLink Issue Tracker:</p>";
    $xmlConfig = "<issuetracker>\n";
    $xmlConfig .= "  <apikey>{$apiKey}</apikey>\n";
    $xmlConfig .= "  <uribase>{$redmineUrl}</uribase>\n";
    $xmlConfig .= "  <uriview>{$redmineUrl}/issues/</uriview>\n";
    $xmlConfig .= "  <projectidentifier>{$projectId}</projectidentifier>\n";
    $xmlConfig .= "  <skipsslerification>true</skipsslerification>\n";
    $xmlConfig .= "</issuetracker>";
    
    $details .= "<pre>" . htmlspecialchars($xmlConfig) . "</pre>";
    
    outputResult('TestLink Configuration', true, $details);
}

// Page header with styling
echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>Redmine API Test for TestLink Integration</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; }\n";
echo "h1 { color: #1a73e8; }\n";
echo "h2 { margin-top: 30px; color: #1a73e8; border-bottom: 1px solid #ddd; padding-bottom: 5px; }\n";
echo ".test-result { margin-bottom: 20px; padding: 15px; border-radius: 5px; }\n";
echo ".success { background-color: #e6ffed; border: 1px solid #34d058; }\n";
echo ".failure { background-color: #ffeef0; border: 1px solid #f97583; }\n";
echo ".details { margin-top: 10px; }\n";
echo "pre { background-color: #f6f8fa; padding: 10px; border-radius: 3px; overflow-x: auto; }\n";
echo "summary { cursor: pointer; font-weight: bold; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

// Page header
echo "<h1>Redmine API Test for TestLink Integration</h1>\n";
echo "<p>This tool tests the connection between TestLink and your Redmine instance.</p>\n";

// Display configuration
echo "<h2>Configuration</h2>\n";
echo "<p><strong>Redmine URL:</strong> {$redmineUrl}</p>\n";
echo "<p><strong>API Key:</strong> " . substr($apiKey, 0, 5) . "..." . substr($apiKey, -5) . "</p>\n";
echo "<p><strong>Project ID:</strong> {$projectId}</p>\n";

// Run all tests
$curlOk = testCurlAvailability();

if ($curlOk) {
    $connectionOk = testApiConnection($redmineUrl, $apiKey);
    
    if ($connectionOk) {
        $projectsOk = checkProjects($redmineUrl, $apiKey, $projectId);
        $specificProjectOk = checkSpecificProject($redmineUrl, $apiKey, $projectId);
        $issuesOk = checkIssuesEndpoint($redmineUrl, $apiKey, $projectId);
        $createIssueOk = createTestIssue($redmineUrl, $apiKey, $projectId);
        
        // Generate configuration advice
        generateTestLinkConfigAdvice($redmineUrl, $apiKey, $projectId);
    }
}

// Summary
echo "<h2>Summary</h2>\n";
echo "<ul>\n";
echo "<li>cURL Extension: " . ($curlOk ? "✅ Available" : "❌ Not available") . "</li>\n";
echo "<li>API Connection: " . (isset($connectionOk) && $connectionOk ? "✅ Success" : "❌ Failed") . "</li>\n";
echo "<li>Project Access: " . (isset($projectsOk) && $projectsOk ? "✅ Success" : "❌ Failed") . "</li>\n";
echo "<li>Specific Project Access: " . (isset($specificProjectOk) && $specificProjectOk ? "✅ Success" : "❌ Failed") . "</li>\n";
echo "<li>Issues Access: " . (isset($issuesOk) && $issuesOk ? "✅ Success" : "❌ Failed") . "</li>\n";
echo "<li>Issue Creation: " . (isset($createIssueOk) && $createIssueOk ? "✅ Success" : "❌ Failed") . "</li>\n";
echo "</ul>\n";

// Footer
echo "<hr>\n";
echo "<p><em>Generated on: " . date('Y-m-d H:i:s') . "</em></p>\n";
echo "</body>\n</html>";
?>
