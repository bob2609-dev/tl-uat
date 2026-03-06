<?php
/**
 * Direct Redmine Integration
 *
 * This is a completely standalone Redmine integration that doesn't rely on TestLink's
 * complex issue tracker integration system. It follows the same approach we used for the
 * image display issue fix - create a simple standalone solution that bypasses the
 * problematic code entirely.
 */

// Configuration
$redmineUrl = 'https://support.profinch.com';
$apiKey = 'a597e200f8923a85484e81ca81d731827b8dbf3d';
$projectId = 'nmb-fcubs-14-7-uat1';

/**
 * Simple Function to make Redmine API calls with SSL verification disabled
 */
function redmineApiCall($endpoint, $method = 'GET', $data = null) {
    global $redmineUrl, $apiKey;
    
    $url = rtrim($redmineUrl, '/') . '/' . ltrim($endpoint, '/');
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);    // Disable host verification
    
    // Set method
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }
    
    // Set headers
    $headers = array(
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/json'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return array(
        'status' => $status,
        'response' => json_decode($response, true),
        'error' => $error
    );
}

/**
 * Get issue details from Redmine
 */
function getIssue($issueId) {
    $result = redmineApiCall('issues/' . $issueId . '.json');
    
    if ($result['status'] == 200 && isset($result['response']['issue'])) {
        return $result['response']['issue'];
    }
    
    return null;
}

/**
 * Create a new issue in Redmine
 */
function createIssue($summary, $description) {
    global $projectId;
    
    $data = array(
        'issue' => array(
            'project_id' => $projectId,
            'subject' => $summary,
            'description' => $description
        )
    );
    
    $result = redmineApiCall('issues.json', 'POST', $data);
    
    if ($result['status'] == 201 && isset($result['response']['issue'])) {
        return $result['response']['issue'];
    }
    
    return array('error' => 'Failed to create issue: HTTP ' . $result['status']);
}

/**
 * Display an issue with formatting
 */
function displayIssue($issue) {
    if (!$issue) {
        echo "<p>Issue not found</p>";
        return;
    }
    
    echo "<div style='border:1px solid #ddd; border-radius:5px; padding:10px; margin:10px;'>";
    echo "<h2>#{$issue['id']}: {$issue['subject']}</h2>";
    
    if (isset($issue['status'])) {
        $statusColor = '#777';
        switch($issue['status']['name']) {
            case 'New': $statusColor = '#1e88e5'; break;
            case 'In Progress': $statusColor = '#fb8c00'; break;
            case 'Resolved': $statusColor = '#43a047'; break;
            case 'Closed': $statusColor = '#757575'; break;
        }
        
        echo "<span style='background-color:{$statusColor}; color:white; padding:3px 8px; border-radius:3px;'>{$issue['status']['name']}</span>";
    }
    
    if (isset($issue['description'])) {
        echo "<div style='margin-top:10px; white-space:pre-wrap;'>{$issue['description']}</div>";
    }
    
    echo "<div style='margin-top:10px;'><a href='{$redmineUrl}/issues/{$issue['id']}' target='_blank'>View in Redmine</a></div>";
    echo "</div>";
}

// Handle requests
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// HTML Header
echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink Redmine Integration</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2 { color: #333; }\n";
echo "form { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }\n";
echo "label { display: block; margin-bottom: 5px; font-weight: bold; }\n";
echo "input[type=text], textarea { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 3px; }\n";
echo "textarea { height: 150px; }\n";
echo "input[type=submit] { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }\n";
echo "input[type=submit]:hover { background-color: #45a049; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink Redmine Integration</h1>\n";

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['issue_id'])) {
        // View specific issue
        $id = intval($_POST['issue_id']);
        $issue = getIssue($id);
        if ($issue) {
            displayIssue($issue);
        } else {
            echo "<p>Issue #{$id} not found</p>";
        }
    } elseif (isset($_POST['summary']) && isset($_POST['description'])) {
        // Create new issue
        $result = createIssue($_POST['summary'], $_POST['description']);
        if (isset($result['id'])) {
            echo "<p style='color:green'>Issue #{$result['id']} created successfully!</p>";
            $issue = getIssue($result['id']);
            if ($issue) {
                displayIssue($issue);
            }
        } else {
            echo "<p style='color:red'>Error creating issue: " . ($result['error'] ?? 'Unknown error') . "</p>";
        }
    }
}

// Show view issue form
echo "<h2>View Issue</h2>\n";
echo "<form method='post'>\n";
echo "  <label for='issue_id'>Issue ID:</label>\n";
echo "  <input type='text' id='issue_id' name='issue_id' required>\n";
echo "  <input type='submit' value='View Issue'>\n";
echo "</form>\n";

// Show create issue form
echo "<h2>Create New Issue</h2>\n";
echo "<form method='post'>\n";
echo "  <label for='summary'>Summary:</label>\n";
echo "  <input type='text' id='summary' name='summary' required>\n";
echo "  <label for='description'>Description:</label>\n";
echo "  <textarea id='description' name='description' required></textarea>\n";
echo "  <input type='submit' value='Create Issue'>\n";
echo "</form>\n";

// Display issue if ID is provided in URL
if ($id) {
    $issue = getIssue($id);
    if ($issue) {
        displayIssue($issue);
    } else {
        echo "<p>Issue #{$id} not found</p>";
    }
}

echo "<p><i>This is a standalone script that directly interfaces with Redmine, bypassing TestLink's complex integration system.</i></p>\n";
echo "<p><i>It follows the same approach as the image display fix - create a simple solution that works independently.</i></p>\n";

echo "</body>\n</html>";
?>
