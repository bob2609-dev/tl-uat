<?php
/**
 * SAFE Custom Issue Integration for TestLink
 * Uses proven working solution as foundation
 * 
 * @filesource  custom_issue_integration_safe.php
 * @author      TestLink Custom Integration
 * @version     1.0
 * @created     2025-02-26
 */

// Remove problematic requires - they're already loaded in main API
// require_once('../../config.inc.php');
// require_once('../functions/common.php');

/**
 * Returns ALL active integrations for a project.
 * Used to determine whether to show the picker or proceed directly.
 * Returns credentials-stripped data only (id, name, type, url).
 *
 * @param object $db          DB connection
 * @param int    $tproject_id TestLink project ID
 * @return array              Array of integrations. Empty array if none found.
 */
function getIntegrationsForProject($db, $tproject_id) {
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] getIntegrationsForProject called with tproject_id: $tproject_id\n",
        FILE_APPEND
    );
    
    try {
        $sql = "SELECT i.id, i.name, i.type, i.url 
                FROM custom_bugtrack_integrations i
                INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
                WHERE m.tproject_id = $tproject_id
                AND m.is_active = 1
                AND i.is_active = 1
                ORDER BY i.name ASC";
        
        file_put_contents(
            'logs/multi_integration_debug.log',
            date('[Y-m-d H:i:s] ') . "[DEBUG] SQL: $sql\n",
            FILE_APPEND
        );
        
        $result = $db->exec_query($sql);
        
        if ($result) {
            $integrations = array();
            while ($row = $db->fetch_array($result)) {
                $integrations[] = $row;
            }
            
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[DEBUG] Found " . count($integrations) . " integrations for project $tproject_id\n",
                FILE_APPEND
            );
            
            return $integrations;
        } else {
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[DEBUG] Database query failed for project $tproject_id\n",
                FILE_APPEND
            );
            return array();
        }
        
    } catch (Exception $e) {
        file_put_contents(
            'logs/multi_integration_debug.log',
            date('[Y-m-d H:i:s] ') . "[ERROR] Exception in getIntegrationsForProject: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
        return array();
    }
}

/**
 * Get custom integration for a specific project
 * Enhanced to support optional integration_id parameter for explicit selection
 * Uses safe database methods and working approach
 */
if (!function_exists('getCustomIntegrationForProject')) {
function getCustomIntegrationForProject($db, $tproject_id, $integration_id = null) {
    file_put_contents(
        'logs/multi_integration_debug.log',
        date('[Y-m-d H:i:s] ') . "[DEBUG] getCustomIntegrationForProject called with tproject_id: $tproject_id, integration_id: " . (isset($integration_id) ? $integration_id : 'null') . "\n",
        FILE_APPEND
    );
    
    try {
        if ($integration_id !== null) {
            // Fetch this specific integration
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[DEBUG] Fetching specific integration_id: $integration_id\n",
                FILE_APPEND
            );
            
            $sql = "SELECT i.* FROM custom_bugtrack_integrations i
                    INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
                    WHERE m.tproject_id = $tproject_id 
                    AND m.integration_id = $integration_id
                    AND m.is_active = 1
                    AND i.is_active = 1";
            
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[DEBUG] SQL for specific integration: $sql\n",
                FILE_APPEND
            );
            
            $result = $db->exec_query($sql);
            
            if ($result) {
                $row = $db->fetch_array($result);
                if ($row) {
                    file_put_contents(
                        'logs/multi_integration_debug.log',
                        date('[Y-m-d H:i:s] ') . "[DEBUG] Found specific integration: " . $row['name'] . " (ID: " . $row['id'] . ")\n",
                        FILE_APPEND
                    );
                    return $row; // Return full record including credentials for bug creation
                }
            }
            
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[ERROR] Integration_id $integration_id not found or not linked to project $tproject_id\n",
                FILE_APPEND
            );
            return null; // Invalid integration_id or not linked to project
        } else {
            // EXISTING behavior preserved: return first active integration
            file_put_contents(
                'logs/multi_integration_debug.log',
                date('[Y-m-d H:i:s] ') . "[DEBUG] Using existing first-found behavior\n",
                FILE_APPEND
            );
            
            return getFirstActiveIntegration($db, $tproject_id);
        }
        
    } catch (Exception $e) {
        file_put_contents(
            'logs/multi_integration_debug.log',
            date('[Y-m-d H:i:s] ') . "[ERROR] Exception in getCustomIntegrationForProject: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
        return null;
    }
}
}

/**
 * Helper function for existing behavior (first active integration)
 * Used by: bug display, redmine_status_api.php, fallback paths
 */
function getFirstActiveIntegration($db, $tproject_id) {
    try {
        $sql = "SELECT i.* FROM custom_bugtrack_integrations i
                INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
                WHERE m.tproject_id = $tproject_id
                AND m.is_active = 1
                AND i.is_active = 1
                ORDER BY i.name ASC
                LIMIT 1";
        
        $result = $db->exec_query($sql);
        
        if ($result) {
            $row = $db->fetch_array($result);
            if ($row) {
                file_put_contents(
                    'redmine_status_api.log',
                    date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] Using integration: " . $row['name'] . " (API key: " . substr($row['api_key'], 0, 8) . "...)\n",
                    FILE_APPEND
                );
                return $row;
            }
        }
        
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] No integration found for project $tproject_id, using fallback\n",
            FILE_APPEND
        );
        return null;
        
    } catch (Exception $e) {
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] ERROR: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
        return null;
    }
}

/**
 * Check if custom integration is available for project
 */
if (!function_exists('hasCustomIntegration')) {
function hasCustomIntegration($db, $tproject_id) {
    $integration = getCustomIntegrationForProject($db, $tproject_id);
    return !empty($integration);
}
}

/**
 * Get issue data using custom integration with fallback
 * Uses working Redmine API approach as fallback
 */
if (!function_exists('getCustomIssueData')) {
function getCustomIssueData($db, $tproject_id, $issue_id) {
    try {
        $integration = getCustomIntegrationForProject($db, $tproject_id);
        
        if ($integration && $integration['type'] === 'REDMINE') {
            return getRedmineIssueData($integration, $issue_id);
        } else {
            file_put_contents(
                'redmine_status_api.log',
                date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] No valid REDMINE integration found, using fallback\n",
                FILE_APPEND
            );
        }
        
        return getRedmineIssueDataFallback($issue_id, $db);
        
    } catch (Exception $e) {
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] ERROR: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
        return getRedmineIssueDataFallback($issue_id, $db);
    }
}
}

/**
 * Alias for getRedmineIssueDataSafe to maintain compatibility
 */
if (!function_exists('getRedmineIssueData')) {
function getRedmineIssueData($integration, $issue_id) {
    return getRedmineIssueDataSafe($integration, $issue_id);
}
}

/**
 * Get Redmine issue data using custom integration (safe version)
 */
function getRedmineIssueDataSafe($integration, $issue_id) {
    $url = $integration['url'] . '/issues/' . $issue_id . '.json';
    $url .= '?_t=' . time();
    
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
                
                file_put_contents(
                    'redmine_status_api.log',
                    date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] Successfully fetched bug $issue_id\n",
                    FILE_APPEND
                );
                return $issue_data;
            }
        } else {
            file_put_contents(
                'redmine_status_api.log',
                date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] API failed for bug $issue_id: HTTP $httpCode\n",
                FILE_APPEND
            );
        }
    } catch (Exception $e) {
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] ERROR: Exception for bug $issue_id: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
    }
    
    return null;
}

/**
 * Fallback Redmine issue data using working hardcoded approach
 */
function getRedmineIssueDataFallback($issue_id, $db_connection) {
    $redmine_url = 'https://support.profinch.com';
    $api_key = 'c16548f2503932a9ef6d6d8f9a59393436e67f3';
    $url = $redmine_url . '/issues/' . $issue_id . '.json?_t=' . time();
    
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
        
        if ($response !== false && $httpCode >= 200 && $httpCode < 300) {
            $data = json_decode($response, true);

            // log data
            file_put_contents(
                'redmine_status_api.log',
                date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] Fallback data: " . json_encode($data) . "\n",
                FILE_APPEND
            );


            
            if (isset($data['issue'])) {
                $issue_data = array(
                    'bug_id' => $issue_id,
                    'status' => isset($data['issue']['status']['name']) ? $data['issue']['status']['name'] : 'Unknown',
                    'priority' => isset($data['issue']['priority']['name']) ? $data['issue']['priority']['name'] : 'N/A',
                    'assignee' => isset($data['issue']['assigned_to']['name']) ? $data['issue']['assigned_to']['name'] : 'N/A',
                    'updated_on' => isset($data['issue']['updated_on']) ? $data['issue']['updated_on'] : null
                );

                // log issue_data
                file_put_contents(
                    'redmine_status_api.log',
                    date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] Fallback issue_data: " . json_encode($issue_data) . "\n",
                    FILE_APPEND
                );
                
                file_put_contents(
                    'redmine_status_api.log',
                    date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] Fallback successfully fetched bug $issue_id\n",
                    FILE_APPEND
                );
                return $issue_data;
            }
        } else {
            file_put_contents(
                'redmine_status_api.log',
                date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] Fallback failed for bug $issue_id: HTTP $httpCode\n",
                FILE_APPEND
            );
        }
    } catch (Exception $e) {
        file_put_contents(
            'redmine_status_api.log',
            date('[Y-m-d H:i:s] ') . "[CUSTOM_INTEGRATION] Fallback ERROR for bug $issue_id: " . $e->getMessage() . "\n",
            FILE_APPEND
        );
    }
    
    return null;
}
?>
