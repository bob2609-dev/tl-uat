<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Enhanced Redmine REST API interface with SSL verification disabled
 *
 * @package     TestLink
 * @author      Mwaimu Mtingele
 * @copyright   2025, TestLink community
 * @link        http://www.testlink.org
 *
 **/

// Include required interface classes
require_once(TL_ABS_PATH . '/lib/issuetrackerintegration/issueTrackerInterface.class.php');
require_once(TL_ABS_PATH . '/lib/issuetrackerintegration/redminerestInterface.class.php');

/**
 * redminerestSslFix - extends the standard Redmine REST interface to disable SSL verification
 * 
 * This class extends TestLink's native Redmine REST interface but disables SSL verification
 * at every point where a connection is made, solving issues with self-signed or invalid SSL certificates.
 */
class redminerestSslFix extends redminerestInterface
{
    /**
     * Constructor - initialize the object
     * 
     * @param string $type interface name
     * @param object $config configuration parameters
     * @param string $name identifier
     */
    function __construct($type, $config, $name)
    {
        parent::__construct($type, $config, $name);
        $this->interfaceVerbose = true; // Enable detailed logging
        
        // Add explicit log entry to confirm SSL fix is being used
        tLog(__METHOD__ . " SSL verification disabled constructor called for {$name}", 'DEBUG');
        
        // Set explicit flag for SSL verification
        $this->skipSSLverification = true;
    }
    
    /**
     * Execute the API call to Redmine using cURL with SSL verification DISABLED
     * 
     * This method overrides the parent method to ensure SSL verification is disabled
     * for all communications with the Redmine server.
     * 
     * @param string $action the API action to execute
     * 
     * @return mixed array of issue data if API call is successful, else false
     */
    protected function _callAPI($action)
    {
        // Log that we're using the SSL-disabled version
        tLog(__METHOD__ . " :: {$action} (SSL verification disabled)");
        
        // Build API URL
        $url = $this->cfg->uribase . $action;
        
        // Initialize cURL
        $curl = curl_init();
        
        // CRITICAL: Disable SSL verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); 
        
        // Set standard options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Add API key header
        if (!is_null($this->cfg->APIKey)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Redmine-API-Key: {$this->cfg->APIKey}"));
        }
        
        // Execute the API call
        $ret = curl_exec($curl);
        
        // Get request info
        $info = curl_getinfo($curl);
        tLog(__METHOD__ . " :: URL:$url - Status:{$info['http_code']}");
        
        // Check for errors
        if ($ret === false) {
            tLog(__METHOD__ . " :: CURL Error: " . curl_error($curl), 'ERROR');
            curl_close($curl);
            return false;
        }
        
        // Close the connection
        curl_close($curl);
        
        // Check if HTTP status code indicates success
        if ($info['http_code'] != 200) {
            tLog(__METHOD__ . " :: HTTP Error: {$info['http_code']} - Response: $ret", 'ERROR');
            return false;
        }
        
        // Try to parse the XML response
        try {
            $xml = simplexml_load_string($ret);
            return $this->_processIssueData($ret);
        } catch (Exception $e) {
            tLog(__METHOD__ . " :: XML Parse Error: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Get issue from Redmine with SSL verification disabled
     * 
     * @param int $issueID the ID of the issue to get
     * 
     * @return mixed the issue data or null on failure
     */
    public function getIssue($issueID)
    {
        tLog(__METHOD__ . " Getting issue #$issueID with SSL verification disabled");
        
        // Build the URL for the issue
        $url = $this->_buildGetBugURLString($issueID);
        
        // Initialize cURL
        $curl = curl_init();
        
        // CRITICAL: Disable SSL verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Add API key header
        if (!is_null($this->cfg->apikey)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Redmine-API-Key: {$this->cfg->apikey}"));
        }
        
        // Execute request
        $ret = curl_exec($curl);
        $info = curl_getinfo($curl);
        
        tLog(__METHOD__ . " URL:$url - Status:{$info['http_code']}");
        
        // Check for errors
        if ($ret === false) {
            tLog(__METHOD__ . " cURL Error: " . curl_error($curl), 'ERROR');
            curl_close($curl);
            return null;
        }
        
        // Close connection
        curl_close($curl);
        
        // Check HTTP status
        if ($info['http_code'] != 200) {
            tLog(__METHOD__ . " HTTP Error: {$info['http_code']} - Response: $ret", 'ERROR');
            return null;
        }
        
        // Process the response
        return $this->_processIssueData($ret);
    }
    
    /**
     * Call the Redmine API to check if the bug exists with SSL verification disabled
     * 
     * @param int $issueID the ID of the issue to check
     * 
     * @return bool true if the issue exists, false otherwise
     */
    public function checkBugIDExistence($issueID)
    {
        tLog(__METHOD__ . " Checking issue #$issueID existence with SSL verification disabled");
        
        // Check syntax first
        if (($status_ok = $this->checkBugIDSyntax($issueID))) {
            // Try to get the issue
            $issue = $this->getIssue($issueID);
            $status_ok = !is_null($issue) && (isset($issue['id']) || isset($issue['issueId']));
        }
        
        return $status_ok;
    }
    
    /**
     * Check connection with SSL verification disabled
     * 
     * This is the method called by TestLink when testing the connection
     * in the issue tracker interface configuration
     * 
     * @return bool always returns true
     */
    public function testConnection()
    {
        tLog(__METHOD__ . " Testing connection to Redmine with SSL verification disabled");
        
        // Call API to test access to the projects endpoint
        $url = $this->cfg->uribase . 'projects.xml?limit=1';
        
        $curl = curl_init();
        
        // CRITICAL: Disable SSL verification
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        // Add API key header
        if (!is_null($this->cfg->APIKey)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Redmine-API-Key: {$this->cfg->APIKey}"));
        }
        
        // Execute request
        $ret = curl_exec($curl);
        $info = curl_getinfo($curl);
        
        tLog(__METHOD__ . " URL:$url - Status:{$info['http_code']}");
        
        // Check for errors
        if ($ret === false) {
            tLog(__METHOD__ . " cURL Error: " . curl_error($curl), 'ERROR');
            curl_close($curl);
            return false;
        }
        
        // Close connection
        curl_close($curl);
        
        // Check HTTP status
        $status_ok = $info['http_code'] == 200;
        
        // Make sure we log the result
        if ($status_ok) {
            tLog(__METHOD__ . " Connection successful!");
        } else {
            tLog(__METHOD__ . " Connection failed! HTTP Code: {$info['http_code']}", 'ERROR');
        }
        
        return $status_ok;
    }
}
