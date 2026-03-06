<?php
// Include PHP 8 compatibility layer
// require_once('custom/inc/php8_init.php');
/**
 * Redmine Status API
 * 
 * This file provides a simple API to get the status of a Redmine issue
 * using the same approach as the redminerestInterface.getIssue method
 */

// Production-safe error handling: log errors, do not display
error_reporting(E_ALL);
ini_set('display_errors', '0');  // Disable display to prevent HTML output
ini_set('log_errors', '1');

// Always JSON and nosniff
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Check if we have a bug ID or execution ID
if (isset($_GET['execution_id'])) {
    // Handle execution_id parameter - return mock data for testing
    $executionID = intval($_GET['execution_id']);
    
    // Early debug log
    file_put_contents(
        'redmine_status_api.log',
        date('[Y-m-d H:i:s] ') . "API called with execution_id: $executionID\n",
        FILE_APPEND
    );
    
    // Log the request
    file_put_contents(
        'redmine_status_api.log',
        date('[Y-m-d H:i:s] ') . "Execution ID request: $executionID\n",
        FILE_APPEND
    );
    
    // Real implementation: Fetch bugs from database for this execution
    $execution_bugs = array();
    
    // Initialize database connection
    require_once('config.inc.php');
    require_once('common.php');
    // Use SAFE custom integration with fallback
    require_once('lib/execute/custom_issue_integration_safe.php');
    $db = new database(DB_TYPE);
    doDBConnect($db, database::ONERROREXIT);
    
    try {
        // For now, use hardcoded project ID to avoid JOIN issues
        // TODO: Fix JOIN query later
        $tproject_id = 242099; // From your mapping table
        
        // Query execution_bugs table to get bug IDs for this execution
        $sql = "SELECT bug_id FROM execution_bugs WHERE execution_id = " . intval($executionID);
        
        // Use TestLink's database methods
        $result = $db->exec_query($sql);
        $bug_ids_found = array();
        
        if ($result) {
            while ($row = $db->fetch_array($result)) {
                $bug_id = $row['bug_id'];
                $bug_ids_found[] = $bug_id;
                
                // Use custom integration from database
                $bug_data = getCustomIssueData($db, $tproject_id, $bug_id);
                
                if ($bug_data) {
                    $execution_bugs[] = $bug_data;
                    file_put_contents(
                        'redmine_status_api.log',
                        date('[Y-m-d H:i:s] ') . "Successfully fetched bug $bug_id using CUSTOM INTEGRATION\n",
                        FILE_APPEND
                    );
                } else {
                    // Try fallback if custom integration fails
                    file_put_contents(
                        'redmine_status_api.log',
                        date('[Y-m-d H:i:s] ') . "Custom integration failed for bug $bug_id, trying FALLBACK\n",
                        FILE_APPEND
                    );
                    
                    $bug_data = getRedmineIssueDataFallback($bug_id, $db);
                    if ($bug_data) {
                        $execution_bugs[] = $bug_data;
                        file_put_contents(
                            'redmine_status_api.log',
                            date('[Y-m-d H:i:s] ') . "Successfully fetched bug $bug_id using FALLBACK\n",
                            FILE_APPEND
                        );
                    } else {
                        file_put_contents(
                            'redmine_status_api.log',
                            date('[Y-m-d H:i:s] ') . "Failed to fetch bug $bug_id from BOTH custom integration and fallback\n",
                            FILE_APPEND
                        );
                    }
                }
            }
        }
        
        // Log what we found
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "Found bug IDs in DB: " . implode(', ', $bug_ids_found) . "\n",
            FILE_APPEND
        );
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "Successfully fetched from Redmine: " . count($execution_bugs) . " bugs\n",
            FILE_APPEND
        );
        
        // If no bugs found in database, return empty result (not mock data)
        echo json_encode([
            'success' => true,
            'execution_id' => $executionID,
            'bugs' => $execution_bugs,
            'count' => count($execution_bugs),
            'debug' => 'Fetched ' . count($execution_bugs) . ' bugs from database and Redmine',
            'debug_info' => array(
                'bug_ids_found' => $bug_ids_found,
                'tproject_id' => $tproject_id,
                'using_custom_integration' => true,
                'sql_query' => $sql
            )
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage(),
            'execution_id' => $executionID,
            'bugs' => array(),
            'count' => 0
        ]);
    }
    exit;
}

if (!isset($_GET['bug_id'])) {
    echo json_encode(['error' => 'No bug ID or execution ID provided']);
    exit;
}

// Get the bug ID as integer to avoid injection in URL path
$bugID = intval($_GET['bug_id']);

// Log the request
file_put_contents(
    'redmine_status_api_log.txt',
    date('Y-m-d H:i:s') . " - Getting status for bug ID: {$bugID}\n",
    FILE_APPEND
);

// Define the Redmine API parameters - same as in redminerestInterface
$baseUrl = 'https://support.profinch.com';
$apiKey = 'c16548f2503932a9ef6d6d8f9a59393436e67f39';
$url = $baseUrl . '/issues/' . $bugID . '.json';

try {
    // Make the API request - using the same approach as in redminerestInterface.makeApiRequest
    $ch = curl_init($url);

    // Set up cURL options - exactly the same as in redminerestInterface.makeApiRequest
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for self-signed certs
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Redmine-API-Key: ' . $apiKey,
        'Content-Type: application/json'
    ));

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // log request to log file
    file_put_contents(
        'redmine_status_api_log.txt',
        date('Y-m-d H:i:s') . " - API request: {$url}\n",
        FILE_APPEND
    );
    file_put_contents(
        'redmine_status_api_log.txt',
        date('Y-m-d H:i:s') . " - API request: {$apiKey}\n",
        FILE_APPEND
    );
  





    // Log curl errors if any
    if ($response === false) {
        $curlError = curl_error($ch);
        file_put_contents(
            'redmine_status_api_log.txt',
            date('Y-m-d H:i:s') . " - cURL Error: {$curlError}\n",
            FILE_APPEND
        );
    }

    curl_close($ch);

    // Log the response
    file_put_contents(
        'redmine_status_api_log.txt',
        date('Y-m-d H:i:s') . " - API response code: {$httpCode}\n",
        FILE_APPEND
    );

    // Log the full response for debugging
    file_put_contents(
        'redmine_status_api_log.txt',
        date('Y-m-d H:i:s') . " - API response body: {$response}\n",
        FILE_APPEND
    );

    // Check if we got a valid response - same logic as in redminerestInterface.getIssue
    if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
        $data = json_decode($response, true);

        if (isset($data['issue']) && isset($data['issue']['status']) && isset($data['issue']['status']['name'])) {
            $status = $data['issue']['status']['name'];
            
            // Get additional details
            $priority = isset($data['issue']['priority']['name']) ? $data['issue']['priority']['name'] : 'N/A';
            $assignee = isset($data['issue']['assigned_to']['name']) ? $data['issue']['assigned_to']['name'] : 'N/A';
            $updated_on = isset($data['issue']['updated_on']) ? $data['issue']['updated_on'] : null;
            
            // Return comprehensive bug information as JSON
            echo json_encode([
                'status' => $status,
                'priority' => $priority,
                'assignee' => $assignee,
                'updated_on' => $updated_on
            ]);
            exit;
        }
    }

    file_put_contents(
        'redmine_status_api_log.txt',
        date('Y-m-d H:i:s') . " - API request: {$response}\n",
        FILE_APPEND
    );

   


    // If we get here, we couldn't get the status
    // Return Unknown as a fallback - same as in redminerestInterface.getIssue
    echo json_encode([
        'status' => 'Unknown',
        'priority' => 'N/A',
        'assignee' => 'N/A',
        'updated_on' => null
    ]);
} catch (Exception $e) {
    // Log the error
    file_put_contents(
        'redmine_status_api_log.txt',
        date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n",
        FILE_APPEND
    );

    // Return an error
    echo json_encode([
        'status' => 'Unknown',
        'priority' => 'N/A',
        'assignee' => 'N/A',
        'updated_on' => null
    ]);
}

/**
 * Fallback function to fetch bug data from Redmine API (hardcoded)
 * @param int $bug_id
 * @param string $redmine_url
 * @param string $api_key
 * @return array|null
 */
function fetchBugFromRedmine($bug_id, $redmine_url, $api_key) {
    $url = $redmine_url . '/issues/' . $bug_id . '.json';
    
    // Add cache-busting timestamp to prevent caching
    $url .= '?_t=' . time();
    
    // Log the API call
    file_put_contents(
        'redmine_status_api.log',
        date('[Y-m-d H:i:s] ') . "Calling fallback Redmine API for bug $bug_id: $url\n",
        FILE_APPEND
    );
    
    try {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Redmine-API-Key: ' . $api_key,
            'Content-Type: application/json',
            'Cache-Control: no-cache, no-store, must-revalidate',
            'Pragma: no-cache',
            'Expires: 0'
        ));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        // Log the response
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "Fallback Redmine API response for bug $bug_id: HTTP $httpCode, Error: '$curlError'\n",
            FILE_APPEND
        );
        
        if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            
            if (isset($data['issue'])) {
                $bug_data = array(
                    'bug_id' => $bug_id,
                    'status' => isset($data['issue']['status']['name']) ? $data['issue']['status']['name'] : 'Unknown',
                    'priority' => isset($data['issue']['priority']['name']) ? $data['issue']['priority']['name'] : 'N/A',
                    'assignee' => isset($data['issue']['assigned_to']['name']) ? $data['issue']['assigned_to']['name'] : 'N/A',
                    'updated_on' => isset($data['issue']['updated_on']) ? $data['issue']['updated_on'] : null
                );
                
                // Log success
                file_put_contents(
                    'redmine_status_api.log',
                    date('[Y-m-d H:i:s] ') . "Fallback successfully parsed bug $bug_id: " . json_encode($bug_data) . "\n",
                    FILE_APPEND
                );
                
                return $bug_data;
            }
        } else {
            // Log response body for debugging
            file_put_contents(
                'redmine_status_api.log',
                date('[Y-m-d H:i:s] ') . "Fallback Redmine API response body for bug $bug_id: $response\n",
                FILE_APPEND
            );
        }
    } catch (Exception $e) {
        // Log exception
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "Fallback exception fetching bug $bug_id: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
    }
    
    return null;
}
?>
