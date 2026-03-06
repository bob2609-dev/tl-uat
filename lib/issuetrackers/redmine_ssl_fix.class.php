<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Enhanced Redmine REST API interface with SSL verification disabled
 */

if (!defined('TL_ABS_PATH')) {
    define('TL_ABS_PATH', dirname(dirname(dirname(__FILE__))));
}
require_once(TL_ABS_PATH . '/lib/issuetrackerintegration/issueTrackerInterface.class.php');

/**
 * Class that implements an interface for Redmine API with SSL fix
 */
class redmineSslFixInterface extends issueTrackerInterface
{
    // URL to call the API
    private $APIUrl = '';
  
    /**
     * Constructor
     * 
     * @param $type the issue tracker type
     * @param $config the issue tracker configuration
     */
    function __construct($type, $config, $name)
    {
        parent::__construct($type, $config, $name);
        $this->interfaceVerbose = true; // Enable extensive logging
        
        // Complete URL
        if (isset($this->cfg->uribase)) {
            $this->APIUrl = trim($this->cfg->uribase, "/") . '/'; 
        }
    }

    /**
     * Establish connection to the Redmine server
     *
     * @return bool 
     */
    public function connect()
    {
        $this->connected = false;
        
        // Debug info
        tLog(__METHOD__ . " Creating connection to: " . $this->APIUrl);
        
        if (!$this->APIUrl) {
            tLog(__METHOD__ . " Missing API URL");
            return false;
        }
        
        // Create the CURL resource with SSL verification DISABLED
        $this->curl = curl_init();
        if (!$this->curl) {
            tLog(__METHOD__ . " CURL init failed");
            return false;
        }
  
        // Set SSL options to disable verification
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Set common options
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        
        $this->connected = true;
        return true;
    }
    
    /**
     * Check if a issue id exists
     *
     * @param string issueID
     *
     * @return bool 
     */
    function checkBugIDExistence($issueID)
    {
        if (!$this->connected) {
            $this->connect();
        }
        
        $issue = $this->getIssue($issueID);
        return !is_null($issue);
    }
    
    /**
     * Get issue from Redmine
     *
     * @param int $issueID
     * @return mixed the issue object or null on failure
     */
    function getIssue($issueID)
    {
        tLog(__METHOD__ . " Trying to get issue #$issueID");
        
        if (!$this->connected) {
            $this->connect();
        }
        
        $url = $this->APIUrl . "issues/$issueID.xml";
        
        // Set URL
        curl_setopt($this->curl, CURLOPT_URL, $url);
        
        // Set headers
        $httpHeaders = array(
            'Content-Type: application/xml',
            'X-Redmine-API-Key: ' . $this->cfg->apikey
        );
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $httpHeaders);
        
        // Execute request
        $response = curl_exec($this->curl);
        $curlInfo = curl_getinfo($this->curl);
        
        tLog(__METHOD__ . " HTTP Status: " . $curlInfo['http_code'] . 
             " for issueID: $issueID");
        
        if ($response === false) {
            tLog(__METHOD__ . " cURL Error: " . curl_error($this->curl), 'ERROR');
            return null;
        }
        
        if ($curlInfo['http_code'] != 200) {
            tLog(__METHOD__ . " Issue #$issueID not found", 'WARNING');
            return null;
        }
        
        try {
            $xml = new SimpleXMLElement($response);
            return $xml;
        } catch (Exception $e) {
            tLog(__METHOD__ . " XML Parse Error: " . $e->getMessage(), 'ERROR');
            return null;
        }
    }
    
    /**
     * Get the status of the issue
     *
     * @param int $issueID
     * @return string returns the status (if found in array) or the bug ID
     */
    function getIssueStatusCode($issueID)
    {
        $issue = $this->getIssue($issueID);
        
        if (is_null($issue)) {
            return 'unknown';
        }
        
        $status = (string) $issue->status['name'];
        return $status ? $status : 'unknown';
    }
    
    /**
     * Create a new issue in Redmine
     *
     * @param array $issue an array containing the issue parameters
     * @return int|bool the issue ID on success, false on failure
     */
    public function addIssue($summary, $description)
    {
        tLog(__METHOD__ . " Creating new issue");
        
        if (!$this->connected) {
            $this->connect();
        }
        
        // Create XML for issue
        $xmlStr = "<?xml version=\"1.0\"?>\n<issue>\n";
        $xmlStr .= "<subject><![CDATA[" . $summary . "]]></subject>\n";
        $xmlStr .= "<description><![CDATA[" . $description . "]]></description>\n";
        $xmlStr .= "<project_id>" . $this->cfg->projectidentifier . "</project_id>\n";
        $xmlStr .= "</issue>";
        
        // Set URL
        $url = $this->APIUrl . "issues.xml";
        curl_setopt($this->curl, CURLOPT_URL, $url);
        
        // Set POST request
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xmlStr);
        
        // Set headers
        $httpHeaders = array(
            'Content-Type: application/xml',
            'X-Redmine-API-Key: ' . $this->cfg->apikey
        );
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $httpHeaders);
        
        // Execute request
        $response = curl_exec($this->curl);
        $curlInfo = curl_getinfo($this->curl);
        
        tLog(__METHOD__ . " HTTP Status: " . $curlInfo['http_code']);
        
        if ($response === false) {
            tLog(__METHOD__ . " cURL Error: " . curl_error($this->curl), 'ERROR');
            return false;
        }
        
        if ($curlInfo['http_code'] != 201) {
            tLog(__METHOD__ . " Issue creation failed with HTTP status " . 
                 $curlInfo['http_code'], 'ERROR');
            tLog(__METHOD__ . " Response: " . $response, 'ERROR');
            return false;
        }
        
        try {
            $xml = new SimpleXMLElement($response);
            $issueID = (string) $xml->id;
            tLog(__METHOD__ . " Created issue #$issueID");
            return $issueID;
        } catch (Exception $e) {
            tLog(__METHOD__ . " XML Parse Error: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}