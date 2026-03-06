<?php
/**
 * Redmine Custom Fields Fetcher
 * 
 * This script fetches custom fields from Redmine's /custom_fields.xml endpoint
 * and displays them in a structured format.
 */

// Custom logging function for Redmine integration
function redmine_log($message) {
    $logFile = __DIR__ . '/redmine_custom_fields_log.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
    // Also output to screen
    echo "$logMessage";
}

/**
 * Make an API request to Redmine
 */
function makeApiRequest($url, $apiKey, $method = 'GET', $data = null, $format = 'xml') {
    redmine_log("Making $method request to: $url");
    
    $ch = curl_init($url);
    
    // Set up cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification for self-signed certs
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    
    $headers = array(
        'X-Redmine-API-Key: ' . $apiKey
    );
    
    // Set content type based on requested format
    if ($format === 'json') {
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Accept: application/json';
    } else if ($format === 'xml') {
        $headers[] = 'Content-Type: application/xml';
        $headers[] = 'Accept: application/xml';
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    // Handle POST requests
    if ($method === 'POST' && !is_null($data)) {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($format === 'json') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }
    
    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Log the request details
    redmine_log("API Request completed: HTTP Code: $httpCode");
    
    // Check for errors
    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        $error = curl_error($ch);
        redmine_log("API Request Error: $error, HTTP Code: $httpCode");
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    // Return the response in the appropriate format
    if ($format === 'json') {
        return json_decode($response, true);
    } else {
        return $response; // Return raw XML
    }
}

/**
 * Parse XML response
 */
function parseXmlResponse($xmlString) {
    redmine_log("Parsing XML response");
    
    // Use simplexml_load_string but convert to array to avoid serialization issues
    $xml = simplexml_load_string($xmlString);
    if ($xml === false) {
        redmine_log("Failed to parse XML response");
        return false;
    }
    
    // Convert SimpleXMLElement to array
    $result = json_decode(json_encode($xml), true);
    return $result;
}

/**
 * Get custom fields from Redmine
 */
function getRedmineCustomFields($baseUrl, $apiKey, $format = 'xml') {
    // Construct the URL for custom fields
    $url = rtrim($baseUrl, '/') . '/custom_fields.' . $format;
    redmine_log("Fetching custom fields from: $url");
    
    // Make the API request
    $response = makeApiRequest($url, $apiKey, 'GET', null, $format);
    
    if ($response === false) {
        redmine_log("Error: Failed to fetch custom fields from Redmine.");
        return false;
    }
    
    // If XML format, parse the XML response
    if ($format === 'xml') {
        $data = parseXmlResponse($response);
    } else {
        $data = $response;
    }
    
    return $data;
}

/**
 * Display custom fields in text format
 */
function displayCustomFields($customFields, $format) {
    redmine_log("\n===== REDMINE CUSTOM FIELDS =====\n");
    
    if ($format === 'xml') {
        // For XML format
        if (isset($customFields['custom_field'])) {
            // Handle both single and multiple custom fields
            $fields = isset($customFields['custom_field'][0]) ? $customFields['custom_field'] : array($customFields['custom_field']);
            
            foreach ($fields as $field) {
                redmine_log("ID: {$field['id']}");
                redmine_log("Name: {$field['name']}");
                redmine_log("Field Format: {$field['field_format']}");
                
                // Handle trackers
                if (isset($field['trackers']) && isset($field['trackers']['tracker'])) {
                    $trackers = isset($field['trackers']['tracker'][0]) ? $field['trackers']['tracker'] : array($field['trackers']['tracker']);
                    $trackerNames = array();
                    foreach ($trackers as $tracker) {
                        $trackerNames[] = isset($tracker['name']) ? $tracker['name'] : $tracker;
                    }
                    redmine_log("Trackers: " . implode(", ", $trackerNames));
                }
                
                // Handle possible values
                if (isset($field['possible_values']) && isset($field['possible_values']['possible_value'])) {
                    $values = isset($field['possible_values']['possible_value'][0]) ? $field['possible_values']['possible_value'] : array($field['possible_values']['possible_value']);
                    $valueNames = array();
                    foreach ($values as $value) {
                        $valueNames[] = isset($value['value']) ? $value['value'] : $value;
                    }
                    redmine_log("Possible Values: " . implode(", ", $valueNames));
                }
                
                redmine_log("----------------------------");
            }
        } else {
            redmine_log("No custom fields found in the response.");
        }
    } else {
        // For JSON format
        if (isset($customFields['custom_fields'])) {
            foreach ($customFields['custom_fields'] as $field) {
                redmine_log("ID: {$field['id']}");
                redmine_log("Name: {$field['name']}");
                redmine_log("Field Format: {$field['field_format']}");
                
                // Handle trackers
                if (isset($field['trackers'])) {
                    $trackerNames = array();
                    foreach ($field['trackers'] as $tracker) {
                        $trackerNames[] = $tracker['name'];
                    }
                    redmine_log("Trackers: " . implode(", ", $trackerNames));
                }
                
                // Handle possible values
                if (isset($field['possible_values'])) {
                    $valueNames = array();
                    foreach ($field['possible_values'] as $value) {
                        $valueNames[] = $value['value'];
                    }
                    redmine_log("Possible Values: " . implode(", ", $valueNames));
                }
                
                redmine_log("----------------------------");
            }
        } else {
            redmine_log("No custom fields found in the response.");
        }
    }
    
    // Add raw data for debugging
    redmine_log("\n===== RAW RESPONSE DATA =====\n");
    redmine_log(print_r($customFields, true));
}

// Main execution

// Configuration - Using the same settings as in redmine_serialization_fix.php
$config = array(
    'baseUrl' => 'https://support.profinch.com', // From redmine_serialization_fix.php
    'apiKey' => 'a597e200f8923a85484e81ca81d731827b8dbf3d',  // From redmine_serialization_fix.php
    'projectId' => 'nmb-fcubs-14-7-uat2', // From redmine_serialization_fix.php
    'format' => 'xml' // 'xml' or 'json'
);

// Check if configuration is provided via command line arguments
$options = getopt('', array('baseUrl:', 'apiKey:', 'projectId::', 'format::'));
if (isset($options['baseUrl'])) {
    $config['baseUrl'] = $options['baseUrl'];
}
if (isset($options['apiKey'])) {
    $config['apiKey'] = $options['apiKey'];
}
if (isset($options['projectId'])) {
    $config['projectId'] = $options['projectId'];
}
if (isset($options['format'])) {
    $config['format'] = $options['format'];
}

// Configuration is already set with defaults from redmine_serialization_fix.php
// Display the configuration being used
redmine_log("Using configuration:");
redmine_log("Base URL: {$config['baseUrl']}");
redmine_log("Project ID: {$config['projectId']}");
redmine_log("Format: {$config['format']}");
redmine_log("API Key: " . substr($config['apiKey'], 0, 5) . '...' . substr($config['apiKey'], -5) . " (masked for security)");

// Fetch and display custom fields
$customFields = getRedmineCustomFields($config['baseUrl'], $config['apiKey'], $config['format']);
if ($customFields !== false) {
    displayCustomFields($customFields, $config['format']);
}
