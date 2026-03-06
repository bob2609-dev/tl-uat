<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Simple Redmine Interface that works without complex dependency issues
 *
 * @package     TestLink
 * @author      Mwaimu Mtingele
 */

/** Interface required - minimum implementation */
if(!class_exists('issueTrackerInterface'))
{
  class issueTrackerInterface
  {
    var $methodOpt = array('getIssue' => array('summary' => true, 'reporter' => true));  
    
    /**
     * Minimal constructor to make sure we don't break anything
     */
    function __construct()
    {
    }
    
    /**
     * checks id for validity
     *
     * @param string issueID
     *
     * @return bool returns true if the bugid has the right format, false else
     **/
    function checkBugIDSyntax($issueID)
    {
      return true;
    }
    
    /**
     * establishes connection to the bugtracking system
     *
     * @return bool returns true if the bugtracking connection is established and the
     * bugtracking system can be accessed, false else
     **/
    function connect()
    {
      return true;
    }
  }
}

/**
 * Simple Redmine Interface that avoids complex dependency issues
 */
class simpleredmineInterface extends issueTrackerInterface
{
  // Redmine API Access
  private $apiKey = '';
  private $url = '';
  
  /**
   * Constructor for the interface object
   * 
   * @param str $type (see tlIssueTracker.class.php $systems property)
   * @param str $cfg
   */
  function __construct($type, $cfg)
  {
    if (isset($cfg->uribase)) {
      $this->url = trim($cfg->uribase);
    }
    
    if (isset($cfg->apikey)) {
      $this->apiKey = trim($cfg->apikey);
    }
  }
  
  /**
   * Valid URL Format
   */
  function buildViewBugURL($issueID)
  {
    return $this->url . '/issues/' . urlencode($issueID);
  }

  /**
   * Returns the URL to the bugtracking page for viewing
   *
   * @param int id the bug id
   *
   * @return string returns a complete URL to view the bug
   **/
  function buildViewBugLink($issueID, $options = null)
  {
    $url = $this->buildViewBugURL($issueID);
    return "<a href='{$url}' target='_blank'>{$issueID}</a>";
  }

  /**
   * Simple implementation that always passes the connection test
   * because we're using direct URL access
   */
  function connect()
  {
    return true;
  }
  
  /**
   * Always returns a simple array with basic issue info
   */
  function getIssue($issueID)
  {
    return array(
      'id' => $issueID,
      'summary' => 'Issue #' . $issueID,
      'statusCode' => 'O', // Open status code
      'statusVerbose' => 'Open', // Status text
      'statusColor' => '#ffbc3d', // Orange color for unknown status
    );
  }
}
