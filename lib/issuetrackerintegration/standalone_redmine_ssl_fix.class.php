<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Standalone Redmine REST API interface with SSL verification disabled
 * This version doesn't extend any TestLink classes to avoid dependency issues
 *
 * @package     TestLink
 * @author      Mwaimu Mtingele
 *
 **/

/**
 * Standalone Redmine SSL Fix class that implements just what's needed
 * to test the connection to Redmine without extending problematic classes
 */
class standaloneRedmineSslFix 
{
    // Configuration
    private $url;
    private $apiKey;
    private $projectId;
    
    /**
     * Constructor
     * 
     * @param string $url Redmine URL
     * @param string $apiKey Redmine API key
     * @param string $projectId Redmine project identifier
     */
    function __construct($url, $apiKey, $projectId) {
        $this->url = rtrim($url, '/'); // Remove trailing slash if present
        $this->apiKey = $apiKey;
        $this->projectId = $projectId;
    }
    
    /**
     * Test connection to Redmine with SSL verification disabled
     * 
     * @return bool true if connection is successful, false otherwise
     */
    public function testConnection() {
        // Test the connection by accessing the projects endpoint
        $url = $this->url . '/projects.xml?limit=1';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // CRITICAL: Disable SSL verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Add API key header
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Redmine-API-Key: ' . $this->apiKey));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        if ($response === false) {
            error_log('Redmine Connection Error: ' . curl_error($curl));
            curl_close($curl);
            return false;
        }
        
        curl_close($curl);
        
        return $httpCode == 200;
    }
    
    /**
     * Check if the specified project exists in Redmine
     * 
     * @return bool true if project exists, false otherwise
     */
    public function checkProjectExists() {
        $url = $this->url . '/projects/' . $this->projectId . '.xml';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // CRITICAL: Disable SSL verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Add API key header
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Redmine-API-Key: ' . $this->apiKey));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);
        
        return $httpCode == 200;
    }
    
    /**
     * Get diagnostic information about the connection
     * 
     * @return array Diagnostic information array
     */
    public function getDiagnosticInfo() {
        $result = array();
        
        // Test API connection
        $url = $this->url . '/users/current.xml';
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        
        // CRITICAL: Disable SSL verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Add API key header
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Redmine-API-Key: ' . $this->apiKey));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        
        $result['connection_status'] = ($httpCode == 200) ? 'OK' : 'Failed';
        $result['http_code'] = $httpCode;
        
        if ($response === false) {
            $result['error'] = curl_error($curl);
        } else {
            $headers = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);
            
            $result['response_headers'] = $headers;
            $result['response_body'] = $body;
        }
        
        curl_close($curl);
        
        // Check project
        $result['project_exists'] = $this->checkProjectExists();
        
        return $result;
    }
    
    /**
     * Get the diagnostic results as HTML for display
     * 
     * @return string HTML formatted diagnostic results
     */
    public function getDiagnosticHtml() {
        $info = $this->getDiagnosticInfo();
        
        $html = '<h2>Redmine Connection Diagnostic</h2>';
        
        $html .= '<h3>Configuration</h3>';
        $html .= '<ul>';
        $html .= '<li><strong>Redmine URL:</strong> ' . htmlspecialchars($this->url) . '</li>';
        $html .= '<li><strong>API Key:</strong> ' . substr($this->apiKey, 0, 5) . '...' . substr($this->apiKey, -5) . '</li>';
        $html .= '<li><strong>Project ID:</strong> ' . htmlspecialchars($this->projectId) . '</li>';
        $html .= '</ul>';
        
        $html .= '<h3>Connection Test</h3>';
        if ($info['connection_status'] === 'OK') {
            $html .= '<div style="color:green;">✓ Connection successful (HTTP ' . $info['http_code'] . ')</div>';
        } else {
            $html .= '<div style="color:red;">✗ Connection failed (HTTP ' . $info['http_code'] . ')</div>';
            if (isset($info['error'])) {
                $html .= '<div>Error: ' . htmlspecialchars($info['error']) . '</div>';
            }
        }
        
        $html .= '<h3>Project Check</h3>';
        if ($info['project_exists']) {
            $html .= '<div style="color:green;">✓ Project "' . htmlspecialchars($this->projectId) . '" exists</div>';
        } else {
            $html .= '<div style="color:red;">✗ Project "' . htmlspecialchars($this->projectId) . '" not found</div>';
        }
        
        if (isset($info['response_body'])) {
            $html .= '<h3>Response Details</h3>';
            $html .= '<div style="font-family:monospace;white-space:pre;overflow:auto;max-height:200px;">' . 
                htmlspecialchars($info['response_body']) . '</div>';
        }
        
        return $html;
    }
}
