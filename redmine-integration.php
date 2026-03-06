<?php
/**
 * TestLink Redmine Integration
 * 
 * This script is a completely standalone solution for integrating Redmine with TestLink
 * It follows our successful approach with the image display issues - bypassing TestLink's
 * complex core code entirely and providing a simpler, direct implementation
 */

// Include TestLink core files for session validation
require_once(dirname(__FILE__) . '/config.inc.php');

// Start session if not already started
session_start();

// Simple session validation - check if user is logged in
if (!isset($_SESSION['userID']) || $_SESSION['userID'] <= 0) {
    // User is not logged in, redirect to login page
    $loginPage = 'login.php';
    $destination = '&destination=' . urlencode($_SERVER['REQUEST_URI']);
    header('Location: ' . $loginPage . '?note=expired' . $destination);
    exit();
}

// Update last activity time
$_SESSION['lastActivity'] = time();

// Configuration
$config = array(
    'redmine_url' => 'https://support.profinch.com',
    'api_key' => 'a597e200f8923a85484e81ca81d731827b8dbf3d',
    'project_id' => 'nmb-fcubs-14-7-uat2'
);

// Get test case info from query parameters or POST data
$testCaseName = isset($_REQUEST['testcase']) ? $_REQUEST['testcase'] : '';
$testPlan = isset($_REQUEST['testplan']) ? $_REQUEST['testplan'] : '';
$buildName = isset($_REQUEST['build']) ? $_REQUEST['build'] : '';
$result = isset($_REQUEST['result']) ? $_REQUEST['result'] : '';
$operation = isset($_REQUEST['operation']) ? $_REQUEST['operation'] : 'create';
$bugId = isset($_REQUEST['bug_id']) ? $_REQUEST['bug_id'] : '';

// Pagination parameters
$page = isset($_REQUEST['page']) ? max(1, intval($_REQUEST['page'])) : 1;
$perPage = isset($_REQUEST['per_page']) ? intval($_REQUEST['per_page']) : 5;
// Ensure per_page is within allowed values (5, 10, 15, 20, 25, ..., 50)
$perPage = min(50, max(5, $perPage));
if ($perPage % 5 !== 0) {
    $perPage = 5; // Default to 5 if not a multiple of 5
}

// Set up HTML page
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redmine Integration for TestLink</title>
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
        h1 {
            color: #0066cc;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        h2 {
            color: #444;
            margin-top: 25px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: inherit;
        }
        textarea {
            height: 150px;
            resize: vertical;
        }
        button {
            background-color: #0066cc;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0055aa;
        }
        button:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
            opacity: 0.7;
        }
        button.processing {
            background-color: #999999;
            position: relative;
        }
        .status {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-left: 5px solid #28a745;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-left: 5px solid #dc3545;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .issue-container {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
        }
        .issue-container h3 {
            margin-top: 0;
            color: #0066cc;
        }
        .issue-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 14px;
            color: white;
            margin-right: 10px;
        }
        .status-new { background-color: #1e88e5; }
        .status-in-progress { background-color: #fb8c00; }
        .status-resolved { background-color: #43a047; }
        .status-closed { background-color: #757575; }
        .issue-meta {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .button-container {
            margin-top: 20px;
            text-align: right;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 15px;
            cursor: pointer;
            background-color: #f1f1f1;
            margin-right: 5px;
            border: 1px solid #ddd;
            border-bottom: none;
            border-radius: 5px 5px 0 0;
        }
        .tab.active {
            background-color: #fff;
            border-bottom: 1px solid white;
            margin-bottom: -1px;
            font-weight: bold;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            margin: 0 4px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #0066cc;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #f5f5f5;
        }
        .pagination .current {
            background-color: #0066cc;
            color: white;
            border-color: #0066cc;
        }
        .pagination .disabled {
            color: #999;
            cursor: not-allowed;
        }
        .per-page-selector {
            margin: 20px 0;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Redmine Integration for TestLink</h1>
        
        <?php if (isset($_GET['testcase'])): ?>
        <div class="status success">
            <strong>TestLink Information:</strong><br>
            Test Case: <?php echo htmlspecialchars($testCaseName); ?><br>
            Test Plan: <?php echo htmlspecialchars($testPlan); ?><br>
            Build: <?php echo htmlspecialchars($buildName); ?><br>
            Result: <?php echo htmlspecialchars($result); ?>
        </div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="showTab('create-issue')">Create Issue</div>
            <div class="tab" onclick="showTab('link-issue')">Link Issue</div>
            <div class="tab" onclick="showTab('view-issues')">View Issues</div>
        </div>
        
        <div id="create-issue" class="tab-content active">
            <h2>Create New Issue in Redmine</h2>
            
            <?php
            // Process form submission for creating an issue
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Handle bug creation
                if (isset($_POST['action']) && $_POST['action'] === 'create') {
                    $subject = $_POST['subject'];
                    $description = $_POST['description'];
                    
                    if (empty($subject)) {
                        echo '<div class="status error">Subject cannot be empty</div>';
                    } else {
                        // Prepare the issue data
                        $issueData = array(
                            'issue' => array(
                                'project_id' => $config['project_id'],
                                'subject' => $subject,
                                'description' => $description
                            )
                        );
                        
                        // Send the request to Redmine API
                        $response = makeRedmineApiRequest('issues.json', 'POST', $issueData);
                        
                        if ($response['success']) {
                            $issueId = $response['data']['issue']['id'];
                            echo '<div class="status success">Issue #' . $issueId . ' created successfully! <a href="' . 
                                 $config['redmine_url'] . '/issues/' . $issueId . '" target="_blank">View in Redmine</a></div>';
                        } else {
                            echo '<div class="status error">Failed to create issue: ' . 
                                 htmlspecialchars($response['error']) . '</div>';
                        }
                    }
                }
                // Handle bug linking
                else if (isset($_POST['action']) && $_POST['action'] === 'link' && !empty($_POST['bug_id'])) {
                    $bugId = $_POST['bug_id'];
                    
                    // Verify the bug exists
                    $response = makeRedmineApiRequest('issues/' . $bugId . '.json');
                    
                    if ($response['success']) {
                        $issue = $response['data']['issue'];
                        echo '<div class="status success">Successfully linked to Bug #' . $bugId . ': ' . 
                             htmlspecialchars($issue['subject']) . ' <a href="' . 
                             $config['redmine_url'] . '/issues/' . $bugId . '" target="_blank">View in Redmine</a></div>';
                    } else {
                        echo '<div class="status error">Failed to link bug: Bug #' . $bugId . ' not found</div>';
                    }
                }
            }
            
            // Handle direct linking from URL parameters
            if ($operation === 'link' && !empty($bugId) && empty($_POST)) {
                // Verify the bug exists
                $response = makeRedmineApiRequest('issues/' . $bugId . '.json');
                
                if ($response['success']) {
                    $issue = $response['data']['issue'];
                    echo '<div class="status success">Successfully linked to Bug #' . $bugId . ': ' . 
                         htmlspecialchars($issue['subject']) . ' <a href="' . 
                         $config['redmine_url'] . '/issues/' . $bugId . '" target="_blank">View in Redmine</a></div>';
                } else {
                    echo '<div class="status error">Failed to link bug: Bug #' . $bugId . ' not found</div>';
                }
            }
            ?>
            
            <form method="post" action="">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" value="<?php 
                        // Format the test case path with slashes replaced by greater-than symbols
                        $formattedSummary = $testCaseName;
                        
                        // First, check if it matches the pattern with 'Test Case:' prefix
                        if ($testCaseName && preg_match('/Test Case: \/([^\/]+)\/([^\/]+)\/(.+?)(?:\s*-\s*Executed\s+ON.*)?$/', $testCaseName, $matches)) {
                            $projectName = $matches[1];
                            $testcasePath = $matches[2] . '/' . $matches[3];
                            // Replace slashes with greater-than symbols in the testcase path
                            $formattedPath = str_replace('/', ' > ', $testcasePath);
                            $formattedSummary = "$formattedPath";
                        }
                        // If it's a different format, try this pattern
                        elseif ($testCaseName && preg_match('/\/([^\/]+)\/([^\/]+)\/(.+?)(?:\s*-\s*Executed\s+ON.*)?$/', $testCaseName, $matches)) {
                            $projectName = $matches[1];
                            $testcasePath = $matches[2] . '/' . $matches[3];
                            // Replace slashes with greater-than symbols in the testcase path
                            $formattedPath = str_replace('/', ' > ', $testcasePath);
                            $formattedSummary = "$formattedPath";
                        }
                        
                        echo htmlspecialchars($formattedSummary ? $formattedSummary : '');
                    ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"><?php 
                    if ($testCaseName) {
                        echo "Test Case: " . htmlspecialchars($testCaseName) . "\n";
                        echo "Test Plan: " . htmlspecialchars($testPlan) . "\n";
                        echo "Build: " . htmlspecialchars($buildName) . "\n";
                        echo "Result: " . htmlspecialchars($result) . "\n\n";
                        echo "Please provide additional details about the issue:\n";
                    }
                    ?></textarea>
                </div>
                
                <div class="button-container">
                    <button type="submit" id="createIssueButton">Create Issue</button>
                </div>
            </form>
        </div>
        
        <div id="link-issue" class="tab-content">
            <h2>Link to Existing Issue in Redmine</h2>
            
            <form method="post" action="">
                <input type="hidden" name="action" value="link">
                <input type="hidden" name="testcase" value="<?php echo htmlspecialchars($testCaseName); ?>">
                <input type="hidden" name="testplan" value="<?php echo htmlspecialchars($testPlan); ?>">
                <input type="hidden" name="build" value="<?php echo htmlspecialchars($buildName); ?>">
                <input type="hidden" name="result" value="<?php echo htmlspecialchars($result); ?>">
                
                <div class="form-group">
                    <label for="bug_id">Bug ID:</label>
                    <input type="text" id="bug_id" name="bug_id" placeholder="Enter Redmine bug ID" required>
                </div>
                
                <div class="button-container">
                    <button type="submit" id="linkBugButton">Link Bug</button>
                </div>
            </form>
        </div>
        
        <div id="view-issues" class="tab-content">
            <h2>Redmine Issues</h2>
            
            <div class="per-page-selector">
                <form method="get" action="" id="per-page-form">
                    <!-- Preserve other query parameters -->
                    <?php foreach ($_GET as $key => $value): 
                        if ($key != 'per_page' && $key != 'page'): ?>
                    <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($value); ?>">
                    <?php endif; endforeach; ?>
                    
                    <label for="per_page">Records per page:</label>
                    <select id="per_page" name="per_page" onchange="document.getElementById('per-page-form').submit();">
                        <?php 
                        $options = array(5, 10, 15, 20, 25, 30, 40, 50);
                        foreach ($options as $option) {
                            echo '<option value="' . $option . '"' . ($perPage == $option ? ' selected' : '') . '>' . $option . '</option>';
                        }
                        ?>
                    </select>
                </form>
            </div>
            
            <?php
            // Get issues from Redmine with pagination
            $offset = ($page - 1) * $perPage;
            $response = makeRedmineApiRequest('issues.json?limit=' . $perPage . '&offset=' . $offset . '&sort=created_on:desc');
            
            if ($response['success'] && isset($response['data']['issues'])) {
                // Get total count for pagination
                $totalCount = isset($response['data']['total_count']) ? $response['data']['total_count'] : 0;
                $totalPages = ceil($totalCount / $perPage);
                
                // Display issues
                foreach ($response['data']['issues'] as $issue) {
                    $statusClass = 'status-new';
                    switch($issue['status']['name']) {
                        case 'In Progress': $statusClass = 'status-in-progress'; break;
                        case 'Resolved': $statusClass = 'status-resolved'; break;
                        case 'Closed': $statusClass = 'status-closed'; break;
                    }
                    
                    echo '<div class="issue-container">';
                    echo '<h3>#' . $issue['id'] . ': ' . htmlspecialchars($issue['subject']) . '</h3>';
                    echo '<div>';
                    echo '<span class="issue-status ' . $statusClass . '">' . htmlspecialchars($issue['status']['name']) . '</span>';
                    echo '</div>';
                    echo '<div class="issue-meta">Created: ' . date('Y-m-d H:i', strtotime($issue['created_on'])) . '</div>';
                    
                    if (!empty($issue['description'])) {
                        echo '<div>' . nl2br(htmlspecialchars($issue['description'])) . '</div>';
                    }
                    
                    echo '<div class="button-container">';
                    echo '<a href="' . $config['redmine_url'] . '/issues/' . $issue['id'] . '" target="_blank">';
                    echo '<button type="button">View in Redmine</button></a>';
                    echo '</div>';
                    echo '</div>';
                }
                
                // Pagination controls
                if ($totalPages > 1) {
                    echo '<div class="pagination">';
                    
                    // Build the base URL for pagination links
                    $baseUrl = '?' . http_build_query(array_merge($_GET, array('per_page' => $perPage))) . '&page=';
                    $baseUrl = str_replace('&page=' . $page, '', $baseUrl); // Remove current page from URL
                    
                    // Previous page link
                    if ($page > 1) {
                        echo '<a href="' . $baseUrl . ($page - 1) . '">&laquo; Prev</a>';
                    } else {
                        echo '<span class="disabled">&laquo; Prev</span>';
                    }
                    
                    // Page numbers
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    if ($startPage > 1) {
                        echo '<a href="' . $baseUrl . '1">1</a>';
                        if ($startPage > 2) {
                            echo '<span class="disabled">...</span>';
                        }
                    }
                    
                    for ($i = $startPage; $i <= $endPage; $i++) {
                        if ($i == $page) {
                            echo '<span class="current">' . $i . '</span>';
                        } else {
                            echo '<a href="' . $baseUrl . $i . '">' . $i . '</a>';
                        }
                    }
                    
                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<span class="disabled">...</span>';
                        }
                        echo '<a href="' . $baseUrl . $totalPages . '">' . $totalPages . '</a>';
                    }
                    
                    // Next page link
                    if ($page < $totalPages) {
                        echo '<a href="' . $baseUrl . ($page + 1) . '">Next &raquo;</a>';
                    } else {
                        echo '<span class="disabled">Next &raquo;</span>';
                    }
                    
                    echo '</div>';
                }
            } else {
                echo '<div class="status error">Failed to load issues.</div>';
            }
            ?>
        </div>
    </div>
    
    <script>
        // Track if a form has been submitted
        let formSubmitted = false;
        
        // Function to handle form submission
        function handleFormSubmit(form) {
            // Check if this form has already been submitted
            if (formSubmitted) {
                // Ask for confirmation before submitting again
                if (!confirm('You have already submitted this form. Are you sure you want to submit again?')) {
                    return false;
                }
            }
            
            // Clear any existing status messages
            const existingStatus = form.parentNode.querySelectorAll('.status');
            existingStatus.forEach(function(element) {
                element.remove();
            });
            
            // Find the submit button in the form
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                // Disable the button and change text to show it's processing
                submitButton.disabled = true;
                submitButton.originalText = submitButton.textContent;
                submitButton.textContent = 'Processing...';
                
                // Add a processing class for styling
                submitButton.classList.add('processing');
            }
            
            // Mark that a submission has occurred
            formSubmitted = true;
            
            // Allow the form to submit
            return true;
        }
        
        // Initialize form handlers when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Add submit handler to the create issue form
            const createForm = document.querySelector('#create-issue form');
            if (createForm) {
                createForm.addEventListener('submit', function(e) {
                    return handleFormSubmit(this);
                });
            }
            
            // Add submit handler to the link issue form
            const linkForm = document.querySelector('#link-issue form');
            if (linkForm) {
                linkForm.addEventListener('submit', function(e) {
                    return handleFormSubmit(this);
                });
            }
        });
        
        function showTab(tabId) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Deactivate all tabs
            const tabs = document.getElementsByClassName('tab');
            for (let i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove('active');
            }
            
            // Activate the selected tab and content
            document.getElementById(tabId).classList.add('active');
            
            // Find and activate the tab button
            const tabButtons = document.getElementsByClassName('tab');
            for (let i = 0; i < tabButtons.length; i++) {
                if (tabButtons[i].textContent.toLowerCase().includes(tabId.replace('-', ' '))) {
                    tabButtons[i].classList.add('active');
                }
            }
            
            // Reset the form submitted flag when changing tabs
            formSubmitted = false;
            
            // Re-enable any disabled buttons
            const submitButtons = document.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(function(button) {
                if (button.disabled && button.originalText) {
                    button.disabled = false;
                    button.textContent = button.originalText;
                    button.classList.remove('processing');
                }
            });
        }
    </script>
</body>
</html>

<?php
/**
 * Make an API request to Redmine
 * 
 * @param string $endpoint API endpoint to call
 * @param string $method HTTP method (GET, POST, etc.)
 * @param array $data Data to send for POST requests
 * @return array Response information
 */
function makeRedmineApiRequest($endpoint, $method = 'GET', $data = null) {
    global $config;
    
    // Build the full URL
    $url = rtrim($config['redmine_url'], '/') . '/' . ltrim($endpoint, '/');
    
    // Initialize curl
    $ch = curl_init($url);
    
    // Disable SSL verification for self-signed certificates
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    // Set up request options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Redmine-API-Key: ' . $config['api_key'],
        'Content-Type: application/json'
    ));
    
    // Set method-specific options
    if ($method === 'POST' && !is_null($data)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    // Prepare result array
    $result = array(
        'success' => ($httpCode >= 200 && $httpCode < 300),
        'http_code' => $httpCode,
        'error' => $error,
        'data' => null
    );
    
    // Parse JSON response if successful
    if ($result['success'] && !empty($response)) {
        $result['data'] = json_decode($response, true);
    }
    
    return $result;
}
?>
