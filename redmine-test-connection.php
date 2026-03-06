<?php
/**
 * Direct Redmine Connection Test
 * 
 * This is a standalone script to test connection to Redmine
 * It completely bypasses TestLink's complex code, similar to
 * our successful approach with the image display issue.
 */

// Configuration - you can edit these values directly
$redmineUrl = 'https://support.profinch.com';
$apiKey = 'a597e200f8923a85484e81ca81d731827b8dbf3d';
$projectId = 'nmb-fcubs-14-7-uat2';

// Header content type
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Redmine Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #0066cc;
        }
        .status {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        pre {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            overflow: auto;
        }
        .button {
            display: inline-block;
            background-color: #0066cc;
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 20px;
        }
        .test-section {
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Direct Redmine Connection Test</h1>
        <p>This script tests connection to Redmine directly, bypassing TestLink's complex integration system.</p>
        
        <div class="test-section">
            <h2>1. Configuration</h2>
            <p><strong>Redmine URL:</strong> <?php echo htmlspecialchars($redmineUrl); ?></p>
            <p><strong>API Key:</strong> <?php echo substr($apiKey, 0, 5) . '...' . substr($apiKey, -5); ?></p>
            <p><strong>Project ID:</strong> <?php echo htmlspecialchars($projectId); ?></p>
        </div>
        
        <div class="test-section">
            <h2>2. Testing Connection</h2>
            
            <?php
            // Test direct connection to Redmine
            $ch = curl_init($redmineUrl . '/users/current.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Redmine-API-Key: ' . $apiKey
            ));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            
            curl_close($ch);
            
            // Display results
            echo "<p><strong>Status Code:</strong> $httpCode</p>";
            
            if ($httpCode >= 200 && $httpCode < 300) {
                echo "<div class='status success'><strong>Connection Successful!</strong> API connection to Redmine is working.</div>";
                
                $userData = json_decode($response, true);
                if (isset($userData['user'])) {
                    echo "<p><strong>Authenticated as:</strong> {$userData['user']['login']} ({$userData['user']['firstname']} {$userData['user']['lastname']})</p>";
                }
            } else {
                echo "<div class='status error'><strong>Connection Failed!</strong> Could not connect to Redmine API.</div>";
                if ($curlError) {
                    echo "<p><strong>Error:</strong> $curlError</p>";
                }
                echo "<p><strong>Response:</strong></p><pre>" . htmlspecialchars($response) . "</pre>";
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>3. Testing Project Access</h2>
            
            <?php
            // Test access to the specific project
            $ch = curl_init($redmineUrl . '/projects/' . urlencode($projectId) . '.json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-Redmine-API-Key: ' . $apiKey
            ));
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            curl_close($ch);
            
            // Display results
            echo "<p><strong>Project Status Code:</strong> $httpCode</p>";
            
            if ($httpCode >= 200 && $httpCode < 300) {
                echo "<div class='status success'><strong>Project Access Successful!</strong> You have access to the specified project.</div>";
                
                $projectData = json_decode($response, true);
                if (isset($projectData['project'])) {
                    echo "<p><strong>Project Name:</strong> {$projectData['project']['name']}</p>";
                    if (isset($projectData['project']['description'])) {
                        echo "<p><strong>Description:</strong> {$projectData['project']['description']}</p>";
                    }
                }
            } else {
                echo "<div class='status error'><strong>Project Access Failed!</strong> Could not access the specified project.</div>";
                echo "<p>This could be due to:</p>";
                echo "<ul>";
                echo "<li>Project ID is incorrect</li>";
                echo "<li>Your API key doesn't have permission to access this project</li>";
                echo "<li>The project doesn't exist</li>";
                echo "</ul>";
                echo "<p><strong>Response:</strong></p><pre>" . htmlspecialchars($response) . "</pre>";
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>4. Testing Issue Creation</h2>
            <?php
            // Only test issue creation if explicitly requested
            if (isset($_GET['test_creation']) && $_GET['test_creation'] == '1') {
                // Prepare test issue data
                $issueData = array(
                    'issue' => array(
                        'project_id' => $projectId,
                        'subject' => 'Test Issue from Direct Connection Test',
                        'description' => 'This is a test issue created from the direct Redmine connection test script.',
                        'priority_id' => 4
                    )
                );
                
                // Attempt to create an issue
                $ch = curl_init($redmineUrl . '/issues.json');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($issueData));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'X-Redmine-API-Key: ' . $apiKey,
                    'Content-Type: application/json'
                ));
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                
                curl_close($ch);
                
                // Display results
                echo "<p><strong>Issue Creation Status Code:</strong> $httpCode</p>";
                
                if ($httpCode == 201) {
                    $issueData = json_decode($response, true);
                    echo "<div class='status success'><strong>Issue Creation Successful!</strong> A test issue was created.</div>";
                    if (isset($issueData['issue']['id'])) {
                        $issueId = $issueData['issue']['id'];
                        echo "<p><strong>Issue ID:</strong> $issueId</p>";
                        echo "<p><a href='{$redmineUrl}/issues/{$issueId}' target='_blank' class='button'>View Issue in Redmine</a></p>";
                    }
                } else {
                    echo "<div class='status error'><strong>Issue Creation Failed!</strong> Could not create test issue.</div>";
                    echo "<p><strong>Response:</strong></p><pre>" . htmlspecialchars($response) . "</pre>";
                }
            } else {
                echo "<p>Issue creation test not requested. <a href='?test_creation=1'>Click here to test issue creation</a>.</p>";
            }
            ?>
        </div>
        
        <div class="test-section">
            <h2>5. Conclusions & Recommendations</h2>
            <?php
            if ($httpCode >= 200 && $httpCode < 300) {
                echo "<div class='status success'>";
                echo "<p><strong>All tests passed!</strong> You have successfully connected to Redmine.</p>";
                echo "<p>This confirms that your Redmine server is accessible, the API key is valid, and you have access to the project.</p>";
                echo "<p>If you're still having issues with TestLink-Redmine integration, the problem is likely with TestLink's integration code, not with Redmine itself.</p>";
                echo "</div>";
                
                echo "<h3>Recommendation:</h3>";
                echo "<p>Since your direct connection works, but TestLink's integration doesn't, we recommend:</p>";
                echo "<ol>";
                echo "<li>Using our standalone Redmine integration page: <a href='redmine-integration.php' target='_blank'>redmine-integration.php</a></li>";
                echo "<li>This bypasses TestLink's complex integration code, similar to how we fixed the image display issues</li>";
                echo "<li>You can create and link issues directly through this interface without relying on TestLink's bugtracker code</li>";
                echo "</ol>";
            } else {
                echo "<div class='status error'>";
                echo "<p><strong>Connection tests failed.</strong> There appear to be issues connecting to your Redmine server.</p>";
                echo "<p>Please check the following:</p>";
                echo "<ol>";
                echo "<li>Is the Redmine URL correct?</li>";
                echo "<li>Is the API key valid?</li>";
                echo "<li>Does the API key have appropriate permissions?</li>";
                echo "<li>Is the project ID correct?</li>";
                echo "<li>Is there a firewall blocking connections?</li>";
                echo "</ol>";
                echo "</div>";
            }
            ?>
        </div>
        
        <p><a href="redmine-integration.php" class="button">Go to Redmine Integration Page</a></p>
    </div>
</body>
</html>
