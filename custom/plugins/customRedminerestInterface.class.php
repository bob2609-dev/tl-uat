<?php
/**
 * TestLink-Redmine Integration: Custom Redmine Interface Class
 * 
 * This file should be placed at:
 * - Linux/Docker: /custom/plugins/customRedminerestInterface.class.php
 * - Windows/XAMPP: C:\xampp\htdocs\testlink\custom\plugins\customRedminerestInterface.class.php
 * 
 * Modification: Added functionality to include the TestLink username as a custom field
 * in the XML sent to Redmine, with proper null checking and fallback mechanisms.
 */

/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 *
 * @filesource  customRedminerestInterface.class.php
 * @author      [Your Name]
 *
 * @internal revisions
 * @since 1.9.14
 *
 */

require_once(TL_ABS_PATH . 'lib/issuetrackerintegration/redminerestInterface.class.php');

class customRedminerestInterface extends redminerestInterface
{
    /**
     * Constructor
     *
     * @param $type the bug tracker type
     * @param $config the bug tracker configuration
     */
    function __construct($type, $config, $name)
    {
        // Log that we're using the custom Redmine interface
        // For Windows/XAMPP, change this path to: C:\xampp\htdocs\testlink\logs\using_custom_redmine.txt
        file_put_contents('C:\xampp\htdocs\tl-uat\using_custom_redmine.txt', "Using custom Redmine interface at " . date('Y-m-d H:i:s') . " for $type\n", FILE_APPEND);
        // For Windows/XAMPP, change this path to: C:\xampp\htdocs\testlink\logs\custom_interface_loaded.txt
        file_put_contents('C:\xampp\htdocs\tl-uat\custom_interface_loaded.txt', "Custom interface loaded with config: " . print_r($config, true) . "\n", FILE_APPEND);
        
        // Call the parent constructor
        parent::__construct($type, $config, $name);
        
        // Log that the custom class was instantiated
        // For Windows/XAMPP, change this path to: C:\xampp\htdocs\testlink\logs\custom_class_instantiated.txt
        file_put_contents('C:\xampp\htdocs\tl-uat\custom_class_instantiated.txt', "Custom class instantiated with config: " . print_r($config, true) . "\n", FILE_APPEND);
        
        // Also try to log to Apache error log directly
        @error_log("REDMINE INTEGRATION: Custom class instantiated with type=$type, name=$name");
    }
    
    /**
     * Overridden addIssue method to include TestLink username
     * 
     * @param string $summary summary of issue
     * @param string $description description of issue
     * @param array $opt array of optional parameters
     *              
     * @return mixed bug ID on success or error string on failure
     */
    public function addIssue($summary, $description, $opt = NULL)
    {
        try
        {
            // Get current TestLink user with proper null checking
            $username = '';
            if (isset($_SESSION['currentUser'])) {
                $userObj = $_SESSION['currentUser'];
                $username = $userObj->login;
                error_log("TestLink Username: " . $username);
                tLog("TestLink Username: " . $username, 'ERROR'); // Using ERROR level to ensure it's logged
            } else {
                error_log("No TestLink user found in session");
            }
            
            // We'll use the parent class implementation but modify the XML before sending
            // First create the basic issue structure as the parent class would
            $issueXmlObj = new SimpleXMLElement('<?xml version="1.0"?><issue></issue>');
            
            // Add standard fields
            $issueXmlObj->addChild('subject', substr(htmlspecialchars($summary), 0, 255));
            $issueXmlObj->addChild('description', htmlspecialchars($description));
            
            // Add project ID
            $pid = (string)$this->cfg->projectidentifier;
            $issueXmlObj->addChild('project_id', $pid);
            
            // Add tracker ID if configured
            if(property_exists($this->cfg, 'trackerid')) {
                $issueXmlObj->addChild('tracker_id', (string)$this->cfg->trackerid);
            }
            
            // Add other attributes from config
            if(!is_null($this->issueOtherAttr)) {
                foreach($this->issueOtherAttr as $ka => $kv) {
                    $issueXmlObj->addChild((isset($this->translate[$ka]) ? $this->translate[$ka] : $ka), (string)$kv);
                }
            }
            
            // Get custom field ID from config or default to 1
            $customFieldId = 1; // Default value
            if (property_exists($this->cfg, 'reporterCustomFieldId')) {
                $customFieldId = (int)$this->cfg->reporterCustomFieldId;
            }
            
            // Log the custom field ID being used
            error_log("Using Redmine custom field ID: " . $customFieldId);
            
            // Create custom fields directly in the XML object instead of string concatenation
            // This ensures proper XML structure
            $customFieldsNode = $issueXmlObj->addChild('custom_fields');
            $customFieldsNode->addAttribute('type', 'array');
            
            $customField = $customFieldsNode->addChild('custom_field');
            $customField->addAttribute('id', $customFieldId);
            $customField->addAttribute('name', 'TestLink Reporter');
            $customField->addChild('value', htmlspecialchars($username));
            
            // Convert to XML string
            $xml = $issueXmlObj->asXML();
            
            // Log the XML
            // For Windows/XAMPP, change this path to: C:\xampp\htdocs\testlink\logs\redmine_xml.txt
            file_put_contents('C:\xampp\htdocs\tl-uat\redmine_xml.txt', "XML sent at " . date('Y-m-d H:i:s') . ":\n$xml\n\n", FILE_APPEND);
            
            // Check if we have a valid API client
            if (!isset($this->APIClient) || is_null($this->APIClient)) {
                // If not, fall back to parent implementation
                error_log("API Client not available, falling back to parent implementation");
                // For Windows/XAMPP, change this path to: C:\xampp\htdocs\testlink\logs\api_client_null.txt
                file_put_contents('C:\xampp\htdocs\tl-uat\api_client_null.txt', "API Client not available at " . date('Y-m-d H:i:s') . ", falling back to parent implementation\n", FILE_APPEND);
                return parent::addIssue($summary, $description, $opt);
            }
            
            // Call the Redmine API with our XML
            $op = $this->APIClient->addIssueFromXMLString($xml, $reporter);
            
            // Add username info to additional info displayed in TestLink
            $additionalInfo = "TestLink Reporter: " . $username;
            // We'll store this in a class property instead of using methods that don't exist
            $this->additionalInfo = $additionalInfo;
            
            // Return success with issue ID
            $ret = array('status_ok' => true, 'id' => (string)$op->id, 
                       'msg' => sprintf(lang_get('redmine_bug_created'),
                        $summary, $pid),
                       'additional_info' => $this->additionalInfo);
            
            return $ret;
        }
        catch (Exception $e)
        {
            // Handle exceptions
            $msg = "REDMINE INTEGRATION FAILURE: " . $e->getMessage();
            error_log($msg);
            return array('status_ok' => false, 'msg' => $msg);
        }
    }
    
    // $tlCfg->config_check_warning_mode = 'SILENT';

// At the end of your custom_config.inc.php file, add this:

// *******************************************************************************
// Redmine Bug Tracker Integration Configuration
// *******************************************************************************

// Register the custom bug tracker interface
require_once('lib/issuetrackers/redmine_ssl_fix.class.php');

// Define the interface to use for bug tracking
$g_interface_bugs = 'redmineSslFix';
$g_interface_bugs_configuration = array();
}