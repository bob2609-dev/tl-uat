<?php
/**
 * Test script for the dummy Redmine API
 * 
 * This script directly tests the dummy API to see if it's working properly
 */

// Set up basic logging
$logFile = 'C:/xampp/htdocs/tl-uat/test_script.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Test script started\n", FILE_APPEND);

// Try to include the dummy API file
$dummyApiPath = __DIR__ . '/dummy_redmine_api.php';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Looking for dummy API at: {$dummyApiPath}\n", FILE_APPEND);

if (file_exists($dummyApiPath)) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Dummy API file exists!\n", FILE_APPEND);
    
    // Try to create a test issue
    $testData = [
        'issue' => [
            'subject' => 'Test Issue from Diagnostic Script',
            'description' => 'This is a test issue created by the diagnostic script at ' . date('Y-m-d H:i:s')
        ]
    ];
    
    // Set up the environment for the dummy API
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REQUEST_URI'] = '/issues.json';
    $GLOBALS['_DUMMY_API_INPUT_DATA'] = $testData;
    
    // Capture the output
    ob_start();
    include($dummyApiPath);
    $response = ob_get_clean();
    
    // Log the response
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Dummy API response: {$response}\n", FILE_APPEND);
    
    // Show the result
    echo "<h1>Dummy API Test Results</h1>";
    echo "<p>Test completed at " . date('Y-m-d H:i:s') . "</p>";
    echo "<p>Check the log file at: {$logFile}</p>";
    echo "<h2>API Response:</h2>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
} else {
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: Dummy API file not found!\n", FILE_APPEND);
    echo "<h1>Error</h1>";
    echo "<p>Dummy API file not found at: {$dummyApiPath}</p>";
}
