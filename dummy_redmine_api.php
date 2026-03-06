<?php
/**
 * Dummy Redmine API Server
 * 
 * This script simulates a Redmine API server for testing TestLink bug tracking integration.
 * It responds to various API endpoints with realistic responses and can be configured
 * to simulate delays and failures.
 */

// Configuration
$config = [
    'delay_seconds' => 1,           // Reduced delay to 1 second (was 3)
    'failure_rate' => 0,            // Percentage chance of failure (0-100)
    'log_requests' => true,         // Whether to log requests
    'log_file' => 'C:/xampp/htdocs/tl-uat/dummy_api.log', // Specific log file location
    'next_issue_id' => 10000,       // Starting ID for new issues
    'storage_file' => 'C:/xampp/htdocs/tl-uat/dummy_redmine_issues.json', // File to store created issues
];

// Write a startup message to the log file to confirm the script is being accessed
$logFile = $config['log_file']; // Use the full path from config
$timestamp = date('Y-m-d H:i:s');
$startupMessage = "[$timestamp] Dummy Redmine API started\n";
file_put_contents($logFile, $startupMessage, FILE_APPEND);
error_log("DUMMY REDMINE API: Started");

// Also write to a known location
file_put_contents('C:/xampp/htdocs/tl-uat/dummy_startup.log', "[$timestamp] Dummy Redmine API started\n", FILE_APPEND);

// Helper function to log requests
function log_request($message) {
    global $config;
    if (!$config['log_requests']) return;
    
    $logFile = $config['log_file']; // Use the full path from config
    $timestamp = date('Y-m-d H:i:s');
    
    // Get client IP and user agent for better debugging
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'unknown';
    
    // Create a more detailed log message
    $logMessage = "[$timestamp] [IP: $ip] [UA: $userAgent] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    // Also write to a known location for guaranteed visibility
    file_put_contents('C:/xampp/htdocs/tl-uat/dummy_requests.log', "[$timestamp] $message\n", FILE_APPEND);
    
    // Also write to PHP error log for visibility
    error_log("DUMMY REDMINE API: $message");
}

// Helper function to load stored issues
function load_issues() {
    global $config;
    $storageFile = dirname(__FILE__) . '/' . $config['storage_file'];
    
    if (file_exists($storageFile)) {
        $content = file_get_contents($storageFile);
        return json_decode($content, true) ?: [];
    }
    
    return [];
}

// Helper function to save issues
function save_issues($issues) {
    global $config;
    $storageFile = dirname(__FILE__) . '/' . $config['storage_file'];
    file_put_contents($storageFile, json_encode($issues, JSON_PRETTY_PRINT));
}

// Helper function to get the next issue ID
function get_next_issue_id() {
    global $config;
    $issues = load_issues();
    
    if (empty($issues)) {
        $nextId = $config['next_issue_id'];
    } else {
        $ids = array_keys($issues);
        $nextId = max($ids) + 1;
    }
    
    return $nextId;
}

// Simulate network delay
if ($config['delay_seconds'] > 0) {
    sleep($config['delay_seconds']);
}

// Simulate random failures
if ($config['failure_rate'] > 0) {
    $randomValue = mt_rand(1, 100);
    if ($randomValue <= $config['failure_rate']) {
        log_request("Simulating failure (random value: $randomValue <= {$config['failure_rate']})\n");
        header('HTTP/1.1 500 Internal Server Error');
        echo json_encode(['error' => 'Simulated server error']);
        exit;
    }
}

// Get request data - support both direct inclusion and HTTP requests
if (isset($GLOBALS['_DUMMY_API_INPUT_DATA'])) {
    // Direct inclusion mode
    $method = $_SERVER['REQUEST_METHOD']; // Should be set by the including file
    $uri = $_SERVER['REQUEST_URI']; // Should be set by the including file
    $data = $GLOBALS['_DUMMY_API_INPUT_DATA'];
    log_request('DIRECT INCLUSION MODE: Using provided data');
} else {
    // Normal HTTP request mode
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];
    $inputData = file_get_contents('php://input');
    $data = json_decode($inputData, true);
}

// Log the request
log_request("$method request to $uri");
if ($data) {
    log_request("Request data: " . json_encode($data));
}

// Parse the URI to determine the endpoint
$uriParts = parse_url($uri);
$path = $uriParts['path'];

// Set content type to JSON
header('Content-Type: application/json');

// Handle different API endpoints
if (strpos($path, '/issues.json') !== false && $method === 'POST') {
    // Creating a new issue
    handle_create_issue($data);
} elseif (preg_match('#/issues/(\d+)\.json#', $path, $matches) && $method === 'GET') {
    // Getting issue details
    $issueId = $matches[1];
    handle_get_issue($issueId);
} elseif (preg_match('#/issues/(\d+)\.json#', $path, $matches) && $method === 'PUT') {
    // Updating an issue (adding a note)
    $issueId = $matches[1];
    handle_update_issue($issueId, $data);
} elseif (strpos($path, '/projects.json') !== false && $method === 'GET') {
    // Getting projects list
    handle_get_projects();
} elseif (strpos($path, '/custom_fields.json') !== false && $method === 'GET') {
    // Getting custom fields
    handle_get_custom_fields();
} else {
    // Unhandled endpoint
    log_request("Unhandled endpoint: $path with method $method");
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Endpoint not found']);
}

/**
 * Handle creating a new issue
 */
function handle_create_issue($data) {
    $issueId = get_next_issue_id();
    log_request("Creating issue #$issueId");
    
    // Extract issue details
    $subject = isset($data['issue']['subject']) ? $data['issue']['subject'] : 'Dummy Issue';
    $description = isset($data['issue']['description']) ? $data['issue']['description'] : 'No description provided';
    
    // Create the issue object
    $issue = [
        'id' => $issueId,
        'subject' => $subject,
        'description' => $description,
        'status' => ['id' => 1, 'name' => 'New'],
        'created_on' => date('Y-m-d\\TH:i:s\\Z'),
        'updated_on' => date('Y-m-d\\TH:i:s\\Z')
    ];
    
    // Store the issue
    $issues = load_issues();
    $issues[$issueId] = $issue;
    save_issues($issues);
    
    // Return the response
    echo json_encode(['issue' => $issue]);
}

/**
 * Handle getting issue details
 */
function handle_get_issue($issueId) {
    log_request("Getting issue #$issueId");
    
    $issues = load_issues();
    
    if (isset($issues[$issueId])) {
        echo json_encode(['issue' => $issues[$issueId]]);
    } else {
        // Create a generic issue if not found
        $issue = [
            'id' => $issueId,
            'subject' => "Dummy Issue #$issueId",
            'description' => 'This is a dummy issue created by the TestLink dummy backend.',
            'status' => ['id' => 1, 'name' => 'New'],
            'created_on' => date('Y-m-d\\TH:i:s\\Z'),
            'updated_on' => date('Y-m-d\\TH:i:s\\Z')
        ];
        
        echo json_encode(['issue' => $issue]);
    }
}

/**
 * Handle updating an issue (adding a note)
 */
function handle_update_issue($issueId, $data) {
    log_request("Updating issue #$issueId");
    
    // Extract the note
    $notes = isset($data['issue']['notes']) ? $data['issue']['notes'] : 'No notes provided';
    
    $issues = load_issues();
    
    if (isset($issues[$issueId])) {
        $issues[$issueId]['notes'] = $notes;
        $issues[$issueId]['updated_on'] = date('Y-m-d\\TH:i:s\\Z');
        save_issues($issues);
    }
    
    echo json_encode([
        'issue' => [
            'id' => $issueId,
            'notes' => $notes,
            'updated_on' => date('Y-m-d\\TH:i:s\\Z')
        ]
    ]);
}

/**
 * Handle getting projects list
 */
function handle_get_projects() {
    log_request("Getting projects list");
    
    echo json_encode([
        'projects' => [
            [
                'id' => 1,
                'name' => 'Dummy Project',
                'identifier' => 'dummy-project',
                'description' => 'This is a dummy project for testing'
            ]
        ],
        'total_count' => 1,
        'offset' => 0,
        'limit' => 25
    ]);
}

/**
 * Handle getting custom fields
 */
function handle_get_custom_fields() {
    log_request("Getting custom fields");
    
    echo json_encode([
        'custom_fields' => [
            [
                'id' => 16,
                'name' => 'Reminders #',
                'customized_type' => 'issue',
                'field_format' => 'string'
            ],
            [
                'id' => 17,
                'name' => 'Submitted By',
                'customized_type' => 'issue',
                'field_format' => 'string'
            ]
        ]
    ]);
}
