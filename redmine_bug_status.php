<?php
/**
 * Redmine Bug Status API for TestLink
 * 
 * This script provides an API endpoint to fetch the status of a Redmine bug.
 * It is called by the redmine_bug_display.js script to enhance the bug display
 * in the test execution page.
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers to return JSON
header('Content-Type: application/json');

// Write to a log file directly for debugging
file_put_contents('redmine_bug_status_log.txt', date('Y-m-d H:i:s') . " - API called with params: " . print_r($_GET, true) . "\n", FILE_APPEND);

// Include required files
try {
    require_once('config.inc.php');
    require_once('custom/inc/redmine_serialization_fix.php');
    
    // Initialize response
    $response = array('status' => 'Unknown', 'id' => null, 'debug' => 'Initialization successful');
    
    // Get bug ID from query parameter
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $bugId = (int)$_GET['id'];
        $response['id'] = $bugId;
        $response['debug'] .= ' | Bug ID: ' . $bugId;
        
        try {
            // Log the request - use file_put_contents if bug_display_log isn't available
            if (function_exists('bug_display_log')) {
                bug_display_log("Fetching status for bug ID: {$bugId}");
            } else {
                file_put_contents('redmine_bug_status_log.txt', date('Y-m-d H:i:s') . " - Fetching status for bug ID: {$bugId}\n", FILE_APPEND);
            }
            $response['debug'] .= ' | Logging successful';
        
        // Create Redmine interface
        $redmineInterface = new redminerestInterface();
        $response['debug'] .= ' | Created Redmine interface';
        
        // Get issue details
        $issue = $redmineInterface->getIssue($bugId);
        $response['debug'] .= ' | Called getIssue';
        
        if ($issue && isset($issue->statusVerbose)) {
            // Set status in response
            $response['status'] = $issue->statusVerbose;
            $response['debug'] .= ' | Got status: ' . $issue->statusVerbose;
            
            if (function_exists('bug_display_log')) {
                bug_display_log("Retrieved status for bug ID {$bugId}: {$issue->statusVerbose}");
            } else {
                file_put_contents('redmine_bug_status_log.txt', date('Y-m-d H:i:s') . " - Retrieved status for bug ID {$bugId}: {$issue->statusVerbose}\n", FILE_APPEND);
            }
        } else {
            $response['debug'] .= ' | Failed to get status';
            if (function_exists('bug_display_log')) {
                bug_display_log("Failed to retrieve status for bug ID {$bugId}");
            } else {
                file_put_contents('redmine_bug_status_log.txt', date('Y-m-d H:i:s') . " - Failed to retrieve status for bug ID {$bugId}\n", FILE_APPEND);
            }
        }
    } catch (Exception $e) {
        $errorMsg = "Error fetching bug status: " . $e->getMessage();
        $response['debug'] .= ' | Exception: ' . $e->getMessage();
        $response['error'] = $e->getMessage();
        
        if (function_exists('bug_display_log')) {
            bug_display_log($errorMsg);
        } else {
            file_put_contents('redmine_bug_status_log.txt', date('Y-m-d H:i:s') . " - {$errorMsg}\n", FILE_APPEND);
        }
    }
} else {
    $response['error'] = 'Invalid bug ID';
    $response['debug'] .= ' | Invalid bug ID parameter';
}
} catch (Exception $e) {
    // Handle exceptions during initialization
    $response = array(
        'status' => 'Error',
        'error' => 'Initialization error: ' . $e->getMessage(),
        'debug' => 'Exception during initialization: ' . $e->getMessage()
    );
    file_put_contents('redmine_bug_status_log.txt', date('Y-m-d H:i:s') . " - Initialization error: " . $e->getMessage() . "\n", FILE_APPEND);
}

// Return JSON response
file_put_contents('redmine_bug_status_log.txt', date('Y-m-d H:i:s') . " - Sending response: " . json_encode($response) . "\n", FILE_APPEND);
echo json_encode($response);
