<?php
/**
 * SAFE Custom Issue Integration for TestLink
 * Uses the proven working solution as the foundation
 * 
 * @filesource  custom_issue_integration_safe.php
 * @author      TestLink Custom Integration
 * @version     1.0
 * @created     2025-02-26
 */

require_once('../../config.inc.php');
require_once('../functions/common.php');

/**
 * Get custom integration for a specific project
 * Uses safe database methods and working approach
 */
function getCustomIntegrationForProject($db, $tproject_id) {
    // Debug logging
    error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: getCustomIntegrationForProject called with tproject_id: $tproject_id");
    
    try {
        $sql = "SELECT i.* FROM custom_bugtrack_integrations i
                JOIN custom_bugtrack_project_mapping m ON i.id = m.integration_id
                WHERE m.tproject_id = $tproject_id AND m.is_active = 1 AND i.is_active = 1
                ORDER BY m.created_on DESC
                LIMIT 1";
        
        error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: SQL: $sql");
        
        $result = $db->exec_query($sql);
        
        if ($result) {
            $row = $db->fetch_array($result);
            if ($row) {
                error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Found integration: " . json_encode($row));
                return $row;
            }
        }
        
        error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: No integration found for project $tproject_id");
        return null;
        
    } catch (Exception $e) {
        error_log("[CUSTOM_INTEGRATION_SAFE] ERROR: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if custom integration is available for project
 */
function hasCustomIntegration($db, $tproject_id) {
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    return !empty($integration);
}

/**
 * Get issue data using custom integration with fallback
 * Uses the working Redmine API approach as fallback
 */
function getCustomIssueData($db, $tproject_id, $issue_id) {
    error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: getCustomIssueData called with tproject_id: $tproject_id, issue_id: $issue_id");
    
    try {
        // Try custom integration first
        $integration = getCustomIntegrationForProject($db, $tproject_id);
        
        if ($integration) {
            error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Using integration: " . $integration['name'] . " (Type: " . $integration['type'] . ")");
            
            if ($integration['type'] === 'REDMINE') {
                return getRedmineIssueDataSafe($integration, $issue_id);
            } else {
                error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Unsupported integration type: " . $integration['type']);
            }
        } else {
            error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: No custom integration found, using fallback");
        }
        
        // Fallback to hardcoded working solution
        return getRedmineIssueDataFallback($issue_id);
        
    } catch (Exception $e) {
        error_log("[CUSTOM_INTEGRATION_SAFE] ERROR: " . $e->getMessage());
        return getRedmineIssueDataFallback($issue_id);
    }
}

/**
 * Get Redmine issue data using custom integration (safe version)
 */
function getRedmineIssueDataSafe($integration, $issue_id) {
    $url = $integration['url'] . '/issues/' . $issue_id . '.json';
    
    // Add cache-busting
    $url .= '?_t=' . time();
    
    error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Calling Redmine API: $url");
    
    try {
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Redmine-API-Key: ' . $integration['api_key'],
            'Content-Type: application/json',
            'Cache-Control: no-cache, no-store, must-revalidate',
            'Pragma: no-cache',
            'Expires: 0'
        ));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Redmine API response: HTTP $httpCode, Error: '$curlError'");
        
        if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            
            if (isset($data['issue'])) {
                $issue_data = array(
                    'bug_id' => $issue_id,
                    'status' => isset($data['issue']['status']['name']) ? $data['issue']['status']['name'] : 'Unknown',
                    'priority' => isset($data['issue']['priority']['name']) ? $data['issue']['priority']['name'] : 'N/A',
                    'assignee' => isset($data['issue']['assigned_to']['name']) ? $data['issue']['assigned_to']['name'] : 'N/A',
                    'updated_on' => isset($data['issue']['updated_on']) ? $data['issue']['updated_on'] : null
                );
                
                error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Successfully parsed issue $issue_id: " . json_encode($issue_data));
                return $issue_data;
            }
        } else {
            error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Redmine API response body: $response");
        }
    } catch (Exception $e) {
        error_log("[CUSTOM_INTEGRATION_SAFE] ERROR: Exception fetching issue $issue_id: " . $e->getMessage());
    }
    
    return null;
}

/**
 * Fallback Redmine issue data using working hardcoded approach
 */
function getRedmineIssueDataFallback($issue_id) {
    $redmine_url = 'https://support.profinch.com';
    $api_key = 'c16548f2503932a9ef6d6d8f9a59393436e67f39';
    $url = $redmine_url . '/issues/' . $issue_id . '.json';
    
    // Add cache-busting
    $url .= '?_t=' . time();
    
    error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Calling fallback Redmine API: $url");
    
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
        
        error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Fallback Redmine API response: HTTP $httpCode, Error: '$curlError'");
        
        if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);
            
            if (isset($data['issue'])) {
                $issue_data = array(
                    'bug_id' => $issue_id,
                    'status' => isset($data['issue']['status']['name']) ? $data['issue']['status']['name'] : 'Unknown',
                    'priority' => isset($data['issue']['priority']['name']) ? $data['issue']['priority']['name'] : 'N/A',
                    'assignee' => isset($data['issue']['assigned_to']['name']) ? $data['issue']['assigned_to']['name'] : 'N/A',
                    'updated_on' => isset($data['issue']['updated_on']) ? $data['issue']['updated_on'] : null
                );
                
                error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Fallback successfully parsed issue $issue_id: " . json_encode($issue_data));
                return $issue_data;
            }
        } else {
            error_log("[CUSTOM_INTEGRATION_SAFE] DEBUG: Fallback Redmine API response body: $response");
        }
    } catch (Exception $e) {
        error_log("[CUSTOM_INTEGRATION_SAFE] ERROR: Fallback exception fetching issue $issue_id: " . $e->getMessage());
    }
    
    return null;
}
?>
