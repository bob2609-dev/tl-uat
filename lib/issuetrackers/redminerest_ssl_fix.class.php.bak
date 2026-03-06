<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Enhanced Redmine REST API interface with SSL verification disabled
 */

// Include TestLink's native Redmine interface class
require_once('lib/issuetrackerintegration/redminerestInterface.class.php');

/**
 * Class that extends TestLink's native Redmine integration but disables SSL verification
 *
 * @author  Mwaimu Mtingele
 */
class redminerestSslFix extends redminerestInterface
{
    /**
     * Constructor - initializes the object
     * 
     * @param string $type
     * @param object $config
     * @param string $name
     */
    function __construct($type, $config, $name)
    {
        parent::__construct($type, $config, $name);
        $this->interfaceVerbose = true; // Enable logging for troubleshooting
        
        // Add a specific log entry to check if this class is being used
        tLog(__METHOD__ . " SSL verification disabled constructor called for {$name}", 'DEBUG');
    }
    
    /**
     * checks id for validity
     *
     * @param string issueID
     *
     * @return bool returns true if the bugid has the right format, false else
     */
    function checkBugIDSyntax($issueID)
    {
        return $this->checkBugIDSyntaxNumeric($issueID);
    }
    
    /**
     * establishes connection to the bugtracking system
     *
     * @return bool returns true if the soap connection was established and the
     * wsdl could be downloaded, false else
     */
    function connect()
    {
        tLog(__METHOD__ . " Connecting to Redmine with SSL verification disabled");
        return true;
    }
    
    /**
     * Modified method to disable SSL verification in all cURL calls
     */
    public function _buildGetBugURLString($issueID)
    {
        tLog(__METHOD__ . " Building URL for $issueID with SSL verification disabled");
        return parent::_buildGetBugURLString($issueID);
    }
    
    /**
     * Modified method that extends the parent's method to disable SSL verification
     */
    public function getIssue($issueID)
    {
        tLog(__METHOD__ . " Getting issue #$issueID with SSL verification disabled");
        
        try {
            $url = $this->_buildGetBugURLString($issueID);
            $curl = curl_init();
            
            // CRITICAL: Disable SSL verification
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            if(!is_null($this->cfg->apikey)) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, 
                    array("X-Redmine-API-Key: {$this->cfg->apikey}"));
            }
            
            $ret = curl_exec($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            
            tLog(__METHOD__ . " URL:$url - Response code:" . $info['http_code']);
            
            if($info['http_code'] != 200) {
                tLog(__METHOD__ . " URL:$url - Response code:" . 
                     $info['http_code'] . " - Not Found or Access Denied");
                return null;
            }
            
            if($ret === false) {
                tLog(__METHOD__ . " URL:$url - CURL execution failed");
                return null;
            }
            
            $issue = $this->_processIssueData($ret);
            return $issue;
            
        } catch (Exception $e) {
            tLog(__METHOD__ . " URL:$url - Exception: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Checks if bug exists in Redmine with SSL verification disabled
     * 
     * @param int $issueID
     * @return bool
     */
    public function checkBugIDExistence($issueID)
    {
        tLog(__METHOD__ . " Checking bug #$issueID with SSL verification disabled");
        
        if(($status_ok = $this->checkBugIDSyntax($issueID))) {
            $issue = $this->getIssue($issueID);
            $status_ok = !is_null($issue);
        }
        return $status_ok;
    }
    
    /**
     * Returns the status of the bug
     *
     * @param int issueID
     * @return string returns the status of the bug (if found in the system), or null else
     */
    function getBugStatus($issueID)
    {
        tLog(__METHOD__ . " Getting status for bug #$issueID with SSL verification disabled");
        
        if($this->checkBugIDExistence($issueID)) {
            $issue = $this->getIssue($issueID);
            return (!is_null($issue) && isset($issue['status'])) ? $issue['status'] : null;
        }
        return null;
    }
}
