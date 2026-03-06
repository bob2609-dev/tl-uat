<?php
/**
 * AJAX endpoint for fetching Redmine status for specific bug IDs
 * Returns JSON response with status information
 */

// Set JSON content type
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once('../../config_db.inc.php');
// Common utilities first, then CSRF library
require_once(dirname(__DIR__) . '/functions/common.php');
require_once(dirname(__DIR__) . '/functions/csrf.php');

// Start secure session and initialize CSRF guard
doSessionStart(false);
csrfguard_start();

// Simple logging class
class AjaxLogger {
    private static function getLogFile() {
        return __DIR__ . '/execBug.log';
    }
    
    public static function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [AJAX-{$level}] {$message}" . PHP_EOL;
        file_put_contents(self::getLogFile(), $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    public static function logError($message) { self::log($message, 'ERROR'); }
    public static function logDebug($message) { self::log($message, 'DEBUG'); }
}

// Function to fetch Redmine status (same as in bugs_view.php but optimized)
function getRedmineIssueStatus($issueId) {
    $redmineUrl = 'https://support.profinch.com';
    $apiKey = 'a597e200f8923a85484e81ca81d731827b8dbf3d';
    
    if (empty($apiKey)) {
        return [
            'id' => $issueId,
            'status' => 'API Not Configured',
            'priority' => 'Unknown',
            'assigned_to' => 'Unknown',
            'updated_on' => null,
            'error' => true
        ];
    }
    
    $url = $redmineUrl . '/issues/' . $issueId . '.json';
    AjaxLogger::logDebug("AJAX fetching from URL: " . $url);
    
    // Use cURL for better reliability
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Shorter timeout for AJAX
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/json',
        'User-Agent: TestLink-BugViewer-AJAX/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($response === false || !empty($curlError)) {
        AjaxLogger::logError("AJAX cURL error for issue " . $issueId . ": " . $curlError);
        return [
            'id' => $issueId,
            'status' => 'Connection Error',
            'priority' => 'Unknown',
            'assigned_to' => 'Unknown',
            'updated_on' => null,
            'error' => true
        ];
    }
    
    if ($httpCode !== 200) {
        AjaxLogger::logError("AJAX HTTP error " . $httpCode . " for issue " . $issueId);
        return [
            'id' => $issueId,
            'status' => 'HTTP ' . $httpCode,
            'priority' => 'Unknown',
            'assigned_to' => 'Unknown',
            'updated_on' => null,
            'error' => true
        ];
    }
    
    AjaxLogger::logDebug("AJAX received response for issue " . $issueId . ": " . substr($response, 0, 100));
    
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        AjaxLogger::logError("AJAX JSON decode error for issue " . $issueId . ": " . json_last_error_msg());
        return [
            'id' => $issueId,
            'status' => 'JSON Error',
            'priority' => 'Unknown',
            'assigned_to' => 'Unknown',
            'updated_on' => null,
            'error' => true
        ];
    }
    
    if (isset($data['issue'])) {
        $issue = $data['issue'];
        AjaxLogger::logDebug("AJAX successfully parsed issue " . $issueId . " with status: " . ($issue['status']['name'] ?? 'Unknown'));
        
        return [
            'id' => $issueId,
            'status' => $issue['status']['name'] ?? 'Unknown',
            'priority' => $issue['priority']['name'] ?? 'Unknown',
            'assigned_to' => isset($issue['assigned_to']) ? $issue['assigned_to']['name'] : 'Unassigned',
            'updated_on' => $issue['updated_on'] ?? null,
            'error' => false
        ];
    }
    
    AjaxLogger::logError("AJAX no issue data found for issue " . $issueId);
    return [
        'id' => $issueId,
        'status' => 'Not Found',
        'priority' => 'Unknown',
        'assigned_to' => 'Unknown',
        'updated_on' => null,
        'error' => true
    ];
}

// Main AJAX handler
try {
    AjaxLogger::log("AJAX request started");
    
    // Check if this is a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Only POST requests allowed');
    }
    
    // Validate CSRF token from headers (JSON requests do not populate $_POST)
    if (!function_exists('getallheaders')) {
        // Fallback for environments without getallheaders()
        function getallheaders() {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$key] = $value;
                }
            }
            return $headers;
        }
    }

    $headers = getallheaders();
    $csrfName = isset($headers['X-Csrf-Name']) ? trim($headers['X-Csrf-Name']) : (isset($headers['X-CSRF-Name']) ? trim($headers['X-CSRF-Name']) : '');
    $csrfToken = isset($headers['X-Csrf-Token']) ? trim($headers['X-Csrf-Token']) : (isset($headers['X-CSRF-Token']) ? trim($headers['X-CSRF-Token']) : '');

    if ($csrfName === '' || $csrfToken === '' || !csrfguard_validate_token($csrfName, $csrfToken)) {
        AjaxLogger::logError('AJAX CSRF validation failed');
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'CSRF validation failed'
        ]);
        exit;
    }
    
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input: ' . json_last_error_msg());
    }
    
    // Validate input
    if (!isset($data['bug_ids']) || !is_array($data['bug_ids'])) {
        throw new Exception('bug_ids array is required');
    }
    
    $bugIds = $data['bug_ids'];
    AjaxLogger::log("AJAX processing " . count($bugIds) . " bug IDs: " . implode(', ', $bugIds));
    
    // Limit to prevent abuse
    if (count($bugIds) > 100) {
        throw new Exception('Too many bug IDs requested (max 100)');
    }
    
    $results = [];
    foreach ($bugIds as $bugId) {
        if (!empty($bugId)) {
            $results[$bugId] = getRedmineIssueStatus($bugId);
        }
    }
    
    AjaxLogger::log("AJAX completed successfully for " . count($results) . " bugs");
    
    // Rotate CSRF token: generate next token for subsequent AJAX calls
    $nextName = 'AJAX_Redmine_Status_' . mt_rand(0, mt_getrandmax());
    $nextToken = csrfguard_generate_token($nextName);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $results,
        'count' => count($results),
        'next_csrf_name' => $nextName,
        'next_csrf_token' => $nextToken
    ]);
    
} catch (Exception $e) {
    AjaxLogger::logError("AJAX error: " . $e->getMessage());
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
