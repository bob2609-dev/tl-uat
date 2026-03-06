<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @package     TestLink
 * @author      Matteo Bisi - matteo.bisi@email.it
 * @copyright   2014, TestLink community
 * @link        http://www.testlink.org
 *
 **/

require_once(TL_ABS_PATH . '/third_party/redmine-php-api/lib/redmine-rest-api.php');
require_once('interface.php');
require_once('issueTrackerInterface.class.php');

//require_once(TL_ABS_PATH . "/gui/templates/issuetrackers/redminerest.php");

/**
 * Redmine REST API Wrapper
 *
 * @package     TestLink
 * @author      Matteo Bisi
 * @date        10/11/2014
 * @version     1.0
 * @link        http://www.testlink.org/
 *
 **/
class redminerestInterface extends issueTrackerInterface
{

  /**
   * Constants
   */
  const RESOURCE_ISSUE = 'issue';
  const ISSUE_TYPE_BUG = 1;

  /**
   * @var object redmine API client
   */
  private $APIClient;

  /**
   * @var bool flag to enable or not showing of redmine link in the HTML
   */
  private $showRedmineLinks;

  private $defaultResolvedStatus = array(3 => 'resolved', 5 => 'closed');



	/**
	 * Sets configuration for the issue tracker
	 * 
	 * @param string $xmlString
	 * 
	 * @return boolean
	 */
	function setCfg($xmlString) {
    $msg = null;
    $signature = 'Source:' . __METHOD__;

    // check for empty string
    if(strlen(trim($xmlString)) == 0) {
      // Bye,Bye
      $msg = " - Issue tracker:$this->name - XML Configuration seems to be empty - please check";
      tLog(__METHOD__ . $msg, 'ERROR');  
      return false;
    }
      
    $this->xmlCfg = "<?xml version='1.0'?> " . $xmlString;
    libxml_use_internal_errors(true);
    try {
      $this->cfg = simplexml_load_string($this->xmlCfg);
      if (!$this->cfg) {
        $msg = $signature . " - Failure loading XML STRING\n";
        foreach(libxml_get_errors() as $error) 
        {
          $msg .= "\t" . $error->message;
        }
      }
    }
    catch(Exception $e)
    {
      $msg = $signature . " - Exception loading XML STRING\n";
      $msg .= 'Message: ' .$e->getMessage();
    }

    if( !($retval = is_null($msg)) )
    {
      tLog(__METHOD__ . $msg, 'ERROR');  
    }  

    // 
    if( !property_exists($this->cfg,'userinteraction') )
    {
      $this->cfg->userinteraction = 0;  
    }  
    $this->cfg->userinteraction = intval($this->cfg->userinteraction) > 0 ? 1 : 0;

    return $retval;
  }


	/**
	 *
	 * check for configuration attributes than can be provided on
	 * user configuration, but that can be considered standard.
	 * If they are MISSING we will use 'these hardcoded values' or the original value.
	 *
	 *
	 **/
	function completeCfg()
	{
	  $base = trim((string)$this->cfg->uribase,"/") . '/';

	  if( !property_exists($this->cfg,'createissueurlsuffix') ) 
	  {
      // $this->cfg->createissueurlsuffix = 'issues/new?issue[subject]={summary}';
      $this->cfg->createissueurlsuffix = 'issues/new?subject={summary}';
	  }


	  if( !property_exists($this->cfg,'uriwithoutresource') ) 
	  {
    	$this->cfg->uriwithoutresource = $base . 'show/';
	  }

	  if( !property_exists($this->cfg,'uriview') ) 
	  {
    	$this->cfg->uriview = $base . 'show/';
	  }

    if( !property_exists($this->cfg,'showredminelinks') ) 
    {
      $this->cfg->showredminelinks = 0;
    }
    
    $this->showRedmineLinks = (int)($this->cfg->showredminelinks) > 0 ? true : false;

		
	  if( !property_exists($this->cfg,'attributes') ) 
	  {
	  	$this->cfg->attributes = null;
	  }

	  if( !property_exists($this->cfg,'otherinterfaces') ) 
	  {
	  	$this->cfg->otherinterfaces = null;
	  }

    $this->setResolvedStatusCfg();
	}


	/**
	 * useful for testing 
	 *
	 *
	 **/
	function getAPIClient()
	{
		return $this->APIClient;
	}

	/**
	 * Construct and configure the bugtracking interface 
	 *
	 * @param string $type (see tlIssueTracker.class.php $systems property)
	 * @param string $cfg
	 */
	function __construct($type,$config,$name)
	{
    $this->interfaceViaDB = false;
		$this->methodOpt = array( 'buildViewBugLink' => array('addSummary' => true, 'colorByStatus' => false), 
		                          'viewIssue' => array('addSummary' => false));
		
	  $this->defaultResolvedStatus = array();
		parent::__construct($type,$config,$name);
    
		$this->completeCfg();
    
		$this->guiCfg = array('use_decoration' => true); // add [] on summary
    $this->setStatusCfg();
	}

  /**
   * Set status configuration:
   *
   * <issuetracker>
   *    <statusdefinition>    
   *        <status><code>10</code><verbose>New</verbose><color>#FF0000</color></status>
   *        <status><code>20</code><verbose>Feedback</verbose><color>#0000FF</color></status>
   *    </statusdefinition>
   * <issuetracker>
   *
   */
  function setStatusCfg()
  {
    $this->status_labels = array();

    if( property_exists($this->cfg,'statusdefinition') )
    {
      $cfg = $this->cfg->statusdefinition->status;
      foreach ($cfg as $cfx) {
        $e = (array) $cfx;
        $this->status_labels[$e['code']] = array('verbose' => (string)$e['verbose'],
                                             'color' => (string)$e['color']);
      }
    }
  }

  /**
   *
   */
  function getIssue($issueID)
  {
    try
    {
      $issue = $this->APIClient->getIssue($issueID);
      
      if( !is_null($issue) && is_object($issue) )
      {
        // Need to adapt structure
        $ret = array('id' => $issue->id,
                    'summary' => $issue->subject,
                    'statusCode' => $issue->status_id,
                    'statusVerbose' => $issue->status_name,
                    'redmineStatus' => $issue->status);
        return $ret;
      }
      else
      {
        return null;
      }
    }
    catch(Exception $e)
    {
      return null;
    }
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
    return $this->checkBugIDSyntaxNumeric($issueID);
  }

  /**
   * establishes connection to the bugtracking system
   *
   * @return bool 
   *
   **/
  function connect()
  {
    $processCatch = false;

    try
    {
  	  // CRITIC NOTICE for developers
  	  // $this->cfg is a simpleXML Object, then seems very conservative and safe
  	  // to cast properties BEFORE using it.
      $redUrl = (string)trim($this->cfg->uribase);
      $redAK = (string)trim($this->cfg->apikey);
      $pxy = new stdClass();
      $pxy->proxy = config_get('proxy');
      
      // Create Redmine API client
      $this->APIClient = new redmine($redUrl,$redAK,$pxy);
      
      // CRITICAL: Disable SSL verification for self-signed certificates
      // This is needed for connecting to Redmine servers with SSL issues
      if (method_exists($this->APIClient, 'setCurlOpts')) {
        $this->APIClient->setCurlOpts(array(
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_SSL_VERIFYHOST => 0
        ));
      }

      // to undestand if connection is OK, I will ask for projects.
      // I've tried to ask for users but get always ERROR from redmine (not able to understand why).
      try
      {
        $items = $this->APIClient->getProjects();
        $this->connected = !is_null($items);
        unset($items);
      }
      catch(Exception $e)
      {
        $processCatch = true;
      }
    }
  	catch(Exception $e)
  	{
  	  $processCatch = true;
  	}
  	
  	if($processCatch)
  	{
  		$logDetails = '';
  		foreach(array('uribase','apikey') as $v)
  		{
  			$logDetails .= "$v={$this->cfg->$v} / "; 
  		}
  		$logDetails = trim($logDetails,'/ ');
  		$this->connected = false;
      tLog(__METHOD__ . " [$logDetails] " . $e->getMessage(), 'ERROR');
  	}
  }

  /**
   * 
   *
   **/
	function isConnected()
	{
		return $this->connected;
	}

  /**
   * 
   *
   **/
	function getHtmlViewIssueRef(
    $issueID, $summary = null, $opt=null, $withText=true)
	{
    $ref = '';

    $options = array('createRedmineViewIssueLink' => true,
                     'include_icons' => true,
                     'include_table' => true);

    if( !is_null($opt) )
    {
      $options = array_merge($options,$opt); 
    }


    if($options['include_table'])
    {
      $ref = '<table class="simple" style="width: 100%"><tr><td>';
    }

    // always be able to use // if blank create link for search on external tool
    // for follow is not important
    if($issueID == '')
    {
      if($options['include_icons'])
      {
         $ref .= ' <img class="clickable" src="' . $this->iconSet['bug'] . 
         '" onclick="javascript:open_bug_add_window(\'' . $this->createFeatAddLink($summary) . '\');"/>';
      }

      if($options['include_table'])
      {
        $ref .= '</td></tr></table>';
      }
      return $ref;      
    }
    

    $Tid = $issueID;
		if($this->isConnected())
		{
      $op = $this->getIssue($issueID);
      if( !is_null($op) )
      {
        if( isset($this->status_labels[$op['statusCode']]) )
        {
          $color = $this->status_labels[$op['statusCode']]['color']; 
        }  
        else
        {
          // need to manage if test plan has contains test cases that have 
          // bugs not covered by defined status.
          // We will use neutral color for this case
          $color = '#FFFFFF';
          foreach($this->status_labels as $vsc)
          {
            $color = $vsc['color'];
            break;  
          }  
        }  

        $ref ='&nbsp;' . 
            "<span class=\"label \" style=\"background-color: {$color}\">" . 
            $op['statusVerbose'] . " - " . 
            "</span><br/>" . 
            json_encode($op);

        // Add summary on issue row, if available
        if( property_exists($op,'summary') )
        {
          $ref .= "&nbsp;" . $op['summary'] . "<br/>";  
        }  
      }

		}
		if($options['createRedmineViewIssueLink'])
    {
      $useImg = $options['include_icons'] && file_exists($this->iconSet['bug']);
      if($useImg)
      {
        $ref .= ' <img src="' . $this->iconSet['bug'] . " alt=\"{$Tid}\" " . 
                '" title="' . $Tid . '" /> ';
      }

		  if($withText)
		  {
			  $ref .= " <a href=" . $this->buildViewBugLink($issueID) . 
              " target=\"_blank\" > $issueID </a>";
		  }
		  else if(!$useImg) 
      {
        $ref .= " <a href=" . $this->buildViewBugLink($issueID) . 
              " target=\"_blank\" > <img title=\"" . $Tid . 
              "\" src=\"" . TL_THEME_IMG_DIR . "report.png\" /></a>";
		  }
    } 

    if($options['include_table'])
    {
      $ref .= '</td></tr></table>';
    }
		return $ref;
	}


	/**
	 * @param string issueID
	 * @param string bugSummary (will be ignored)
	 * 
	 * @return string returns a href-link which should open the bug in the
	 *      current Issue Tracker.
	 */
	function buildViewBugLink($issueID, $bugSummary = null, $opt=null)
	{
		$cfg = $this->cfg;
		$base = (string) trim($cfg->uriview,"/") . "/";
		return $base.$issueID;
	}

  /**
   * @param string issueNumber
   * 
   * @return string returns url to create an issue
   */
  function buildCreateFeatLink($summary = null)
  {
    $add_link = (string)$this->cfg->uribase;
    $add_link .= (string)$this->cfg->createissueurlsuffix;
 
    if( isset($summary) ) {
      $add_link = str_replace('{summary}', urlencode($summary), $add_link);
    }
    return $add_link;
  }


	/**
	 * @return string returns the URL which should be displayed for entering bugs
	 */
	function getEnterBugURL()
	{
		$cfg = $this->cfg;
		return (string)'';
	}

  /**
   * @return string returns the URL which should be displayed for entering bugs
   */
  public static function getCfgTemplate()  
  {
     $tpl = "<!-- Template " . __CLASS__ . " -->\n" .             
              "<issuetracker>\n" .
              "<apikey>REDMINE API KEY</apikey>\n" .
              "<uribase>http://tl.m.redmine.org/</uribase>\n" .
              "<uriview>http://tl.m.redmine.org/show/</uriview>\n" .
              "<showredminelinks>0</showredminelinks>\n" .
              "<createissueurlsuffix>issues/new?issue[subject]={summary}</createissueurlsuffix>\n" .
              "<projectidentifier>REDMINE PROJECT IDENTIFIER\n" .
              " You can use numeric id or identifier string \n" .
              "</projectidentifier>\n" .
              "\n" .
              "<!--                                       -->\n" .
              "<!-- Configure This if you need to provide other attributes, ATTENTION to REDMINE API Docum. -->\n" .
              "<!-- <attributes> -->\n" .
              "<!--   <targetversion>10100</targetversion>\n" .
              "<!--   <parent_issue_id>10100</parent_issue_id>\n" .
              "<!-- </attributes>  -->\n" .
              "<!--                                       -->\n" .
              "<!-- Custom Fields-->\n" .
              "<!-- Check Redmine API Docs for format -->\n" .
              "<!-- <custom_fields type=\"array\"> -->\n" .
              "<!-- <custom_field id=\"1\" name=\"CF-STRING-OPT\"> -->\n" .
              "<!--   <value>SALT</value> -->\n" .
              "<!-- </custom_field> -->\n" .
              "<!-- <custom_field id=\"3\" name=\"CF-LIST-OPT\" multiple=\"true\"> -->\n" .
              "<!--   <value type=\"array\"> -->\n" .
              "<!--     <value>ALFA</value> -->\n" .
              "<!--   </value> -->\n" .
              "<!-- </custom_field> -->\n" .
              "<!-- </custom_fields> -->\n" .
             "<!-- Configure This if you want NON STANDARD BEHAIVOUR for considered issue resolved -->\n" .
              "<!--  <resolvedstatus>-->\n" .
              "<!--    <status><code>3</code><verbose>Resolved</verbose></status> -->\n" .
              "<!--    <status><code>5</code><verbose>Closed</verbose></status> -->\n" .
              "<!--  </resolvedstatus> -->\n" .
              "</issuetracker>\n";
    return $tpl;
  }

 /**
  *
  **/
  function canCreateViaAPI()
  {
    return (property_exists($this->cfg, 'projectidentifier'));
  }

  /**
   * Check if required PHP extension for this integration is loaded
   * 
   * @return array with keys:  
   *               status: true/false
   *               msg: error message if status==false
   */
  public static function checkEnv()
  {
    $status_ok = true;
    $msg = 'OK';
    
    // Check for required PHP extensions
    if(!extension_loaded('curl'))
    {
      $status_ok = false;
      $msg = "Missing required PHP extension: curl";
    }
    
    // Check for third-party API library
    $redmine_api_path = TL_ABS_PATH . "/third_party/redmine-php-api/lib/redmine-rest-api.php";
    if($status_ok && !file_exists($redmine_api_path)) 
    {
      $status_ok = false;
      $msg = "Redmine API Library not found at {$redmine_api_path}";
    }
    
    return array('status' => $status_ok, 'msg' => $msg);
  }

  /**
   * Sets the status configuration used for marking issues as resolved
   */
  public function setResolvedStatusCfg()
  {
    if (property_exists($this->cfg,'resolvedstatus')) {
      $statusCfg = (array)$this->cfg->resolvedstatus;
    } else {
      $statusCfg['status'] = $this->defaultResolvedStatus;
    }
    $this->resolvedStatus = new stdClass();
    foreach ($statusCfg['status'] as $cfx) {
      $e = (array)$cfx;
      $this->resolvedStatus->byCode[$e['code']] = $e['verbose'];
    }
    $this->resolvedStatus->byName = array_flip($this->resolvedStatus->byCode);
  }
  
  /**
   * Returns the resolved status configuration
   */
  public function getResolvedStatusCfg()
  {
    return $this->resolvedStatus;
  }

}
