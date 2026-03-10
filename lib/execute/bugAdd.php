<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * @filesource	bugAdd.php
 * 
 */
require_once('../../config.inc.php');
require_once('common.php');

require_once('exec.inc.php');
testlinkInitPage($db,false,false,"checkRights");

// Include custom issue integration
require_once('custom_issue_integration.php');

// Add comprehensive logging
function logBugAdd($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s') . '.' . substr(microtime(true) * 1000, -3);
    $logMessage = "[{$timestamp}] [{$level}] [BUGADD] {$message}\n";
    error_log($logMessage, 3, 'bugadd_debug.log');
    
    // Also log to PHP error log for immediate visibility
    error_log($logMessage);
}

logBugAdd("=== BUGADD SCRIPT START ===");
logBugAdd("REQUEST METHOD: " . $_SERVER['REQUEST_METHOD']);
logBugAdd("POST DATA: " . json_encode($_POST));
logBugAdd("GET DATA: " . json_encode($_GET));

$templateCfg = templateConfiguration();
list($args,$gui,$its) = initEnv($db);

logBugAdd("Initialized - user_action: " . ($args->user_action ?? 'NULL'));
logBugAdd("Exec ID: " . ($args->exec_id ?? 'NULL'));
logBugAdd("Bug ID: " . ($args->bug_id ?? 'NULL'));

if( ($args->user_action == 'create' || $args->user_action == 'doCreate') && 
    $gui->issueTrackerCfg->tlCanCreateIssue) {
  // get matadata
  $gui->issueTrackerMetaData = getIssueTrackerMetaData($its);
  
  switch($args->user_action) {
    case 'create':
     $dummy = generateIssueText($db,$args,$its); 
     $gui->bug_summary = $dummy->summary;
     
     // Apply consistent formatting to the bug summary
     // Pattern to match: '/CBS UPGRADE Project/Test Suite 2/PS-5.ATM-TC4 - Executed ON (ISO FORMAT): 2025-04-10 20:29:59'
     if (preg_match('~Test Case: /([^/]+)/([^/]+)/(.+?)(?:\s*-\s*Executed\s+ON.*)?$~', $gui->bug_summary, $matches)) {
       $testcasePath = $matches[2] . '/' . $matches[3];
       // Replace slashes with greater-than symbols in the testcase path
       $gui->bug_summary = str_replace('/', ' > ', $testcasePath);
     }
     // If it's a different format, try this pattern
     elseif (preg_match('~/([^/]+)/([^/]+)/(.+?)(?:\s*-\s*Executed\s+ON.*)?$~', $gui->bug_summary, $matches)) {
       $testcasePath = $matches[2] . '/' . $matches[3];
       // Replace slashes with greater-than symbols in the testcase path
       $gui->bug_summary = str_replace('/', ' > ', $testcasePath);
     }
    break;

    case 'doCreate':
     logBugAdd("PROCESSING doCreate ACTION");
     $args->direct_link = getDirectLinkToExec($db,$args->exec_id);

     $dummy = generateIssueText($db,$args,$its); 
     $gui->bug_summary = $dummy->summary;
     
     // Apply consistent formatting to the bug summary
     // Pattern to match: '/CBS UPGRADE Project/Test Suite 2/PS-5.ATM-TC4 - Executed ON (ISO FORMAT): 2025-04-10 20:29:59'
     if (preg_match('~Test Case: /([^/]+)/([^/]+)/(.+?)(?:\s*-\s*Executed\s+ON.*)?$~', $gui->bug_summary, $matches)) {
       $testcasePath = $matches[2] . '/' . $matches[3];
       // Replace slashes with greater-than symbols in the testcase path
       $gui->bug_summary = str_replace('/', ' > ', $testcasePath);
     }
     // If it's a different format, try this pattern
     elseif (preg_match('~/([^/]+)/([^/]+)/(.+?)(?:\s*-\s*Executed\s+ON.*)?$~', $gui->bug_summary, $matches)) {
       $testcasePath = $matches[2] . '/' . $matches[3];
       // Replace slashes with greater-than symbols in the testcase path
       $gui->bug_summary = str_replace('/', ' > ', $testcasePath);
     }

     // Check if we should use custom integration
     if (isset($gui->customIntegrationEnabled) && $gui->customIntegrationEnabled) {
       logBugAdd("Using custom integration for bug creation");
       
       // Create bug using custom integration
       $result = createCustomIssue(
         $db,
         $args->tproject_id,
         $args->tplan_id,
         $args->tcversion_id,
         $args->exec_id,
         $gui->bug_summary,
         $args->bug_notes ?? '',
         $gui->customIntegration['default_priority'] ?? 'Normal'
       );
       
       if ($result['success']) {
         $gui->msg = "Bug created successfully: <a href='{$result['issue_url']}' target='_blank'>{$result['issue_id']}</a>";
         $gui->issueTrackerCfg->tlCanCreateIssue = true;
         
         // Add link to TestLink execution if requested
         if ($args->addLinkToTL || $args->addLinkToTLPrintView) {
           $args->direct_link = getDirectLinkToExec($db,$args->exec_id);
           // Link the bug to the execution
           write_execution_bug($db,$args->exec_id, $result['issue_id'],$args->tcstep_id);
           logAuditEvent(TLS("audit_executionbug_added",$result['issue_id']),"CREATE",$args->exec_id,"executions");
         }
       } else {
         $gui->msg = "Failed to create bug: " . $result['message'];
         $gui->issueTrackerCfg->tlCanCreateIssue = false;
       }
       
     } else {
       logBugAdd("Using default issue tracker for bug creation");
       // Use default TestLink issue tracker
       $aop = array('addLinkToTL' => $args->addLinkToTL,
                    'addLinkToTLPrintView' => $args->addLinkToTLPrintView);

       $ret = addIssue($db,$args,$its,$aop);
       $gui->issueTrackerCfg->tlCanCreateIssue = $ret['status_ok'];
       $gui->msg = $ret['msg'];
     }
    break;

  }
}  
else if($args->user_action == 'link' || $args->user_action == 'add_note') {
  logBugAdd("ENTERING LINK/ADD_NOTE SECTION");
  // Well do not think is very elegant to check for $args->bug_id != ""
  // to understand if user has pressed ADD Button
  if(!is_null($issueT) && $args->bug_id != "") {
  	logBugAdd("Bug ID provided: " . $args->bug_id);
  	$l18n = init_labels(array("error_wrong_BugID_format" => null,"error_bug_does_not_exist_on_bts" => null));

    switch($args->user_action) {
      case 'link':
        logBugAdd("PROCESSING LINK ACTION");
        $gui->msg = $l18n["error_wrong_BugID_format"];
        if ($its->checkBugIDSyntax($args->bug_id)) {
          logBugAdd("Bug ID syntax check passed");
          if ($its->checkBugIDExistence($args->bug_id)) {     
            logBugAdd("Bug ID exists - calling write_execution_bug");
            logBugAdd("BEFORE write_execution_bug - exec_id: " . $args->exec_id . ", bug_id: " . $args->bug_id . ", tcstep_id: " . $args->tcstep_id);
            
            if (write_execution_bug($db,$args->exec_id, $args->bug_id,$args->tcstep_id)) {
              logBugAdd("write_execution_bug SUCCESS");
              $gui->msg = lang_get("bug_added");
              logAuditEvent(TLS("audit_executionbug_added",$args->bug_id),"CREATE",$args->exec_id,"executions");
              logBugAdd("Audit event logged");

              // blank notes will not be added :).
              if($gui->issueTrackerCfg->tlCanAddIssueNote)  {
                $hasNotes = (strlen($gui->bug_notes) > 0);
                logBugAdd("Has notes: " . ($hasNotes ? 'YES' : 'NO'));
                // will do call to update issue Notes
                if($args->addLinkToTL || $args->addLinkToTLPrintView) {
                  logBugAdd("Adding link to TL - addLinkToTL: " . ($args->addLinkToTL ? 'YES' : 'NO') . ", addLinkToTLPrintView: " . ($args->addLinkToTLPrintView ? 'YES' : 'NO'));
                  $args->direct_link = getDirectLinkToExec($db,$args->exec_id);

                  $aop = array('addLinkToTL' => $args->addLinkToTL,
                               'addLinkToTLPrintView' => $args->addLinkToTLPrintView);

                  $dummy = generateIssueText($db,$args,$its,$aop); 
                  $gui->bug_notes = $dummy->description;
                }  

                if( $args->addLinkToTL || $args->addLinkToTLPrintView || 
                    $hasNotes ) {
                  $opt = new stdClass();
                  $opt->reporter = $args->user->login;
                  $opt->reporter_email = trim($args->user->emailAddress);
                  if( '' == $opt->reporter_email ) {
                    $opt->reporter_email = $opt->reporter;
                  }

                  $its->addNote($args->bug_id,$gui->bug_notes,$opt);
                }
              }  
            }
          } else {
            $gui->msg = sprintf($l18n["error_bug_does_not_exist_on_bts"],$gui->bug_id);
          }  
        }
      break;
      
      case 'add_note':
        // blank notes will not be added :).
        $gui->msg = '';
        if($gui->issueTrackerCfg->tlCanAddIssueNote && (strlen($gui->bug_notes) > 0) ) {
          $opt = new stdClass();
          $opt->reporter = $args->user->login;
          $opt->reporter_email = trim($args->user->emailAddress);
          if( '' == $opt->reporter_email ) {
            $opt->reporter_email = $opt->reporter;
          }
          
          $ope = $its->addNote($args->bug_id,$gui->bug_notes,$opt);

          if( !$ope['status_ok'] ) {
            $gui->msg = $ope['msg'];
          }  
        }  
      break;
    }
  }
}
logBugAdd("=== PROCESSING COMPLETE ===");
logBugAdd("Final GUI message: " . ($gui->msg ?? 'NULL'));
logBugAdd("=== BUGADD SCRIPT END ===");

$smarty = new TLSmarty();
$smarty->assign('gui',$gui);

logBugAdd("About to display template: " . $templateCfg->template_dir . $templateCfg->default_template);
$smarty->display($templateCfg->template_dir . $templateCfg->default_template);
logBugAdd("Template displayed");



/**
 * 
 * 
 */
function initEnv(&$dbHandler)
{
  $uaWhiteList = array();
  $uaWhiteList['elements'] = array('link','create','doCreate','add_note');
  $uaWhiteList['lenght'] = array();
  foreach ($uaWhiteList['elements'] as $xmen) {
    $uaWhiteList['lenght'][] = strlen($xmen);
  }  
  $user_action['maxLengh'] = max($uaWhiteList['lenght']);
  $user_action['minLengh'] = min($uaWhiteList['lenght']);

	$iParams = array("exec_id" => array("GET",tlInputParameter::INT_N),
		               "bug_id" => array("REQUEST",tlInputParameter::STRING_N),
		               "tproject_id" => array("REQUEST",tlInputParameter::INT_N),
                   "tplan_id" => array("REQUEST",tlInputParameter::INT_N),
		               "tcversion_id" => array("REQUEST",tlInputParameter::INT_N),
                   "bug_notes" => array("POST",tlInputParameter::STRING_N),
                   "issueType" => array("POST",tlInputParameter::INT_N),
                   "issuePriority" => array("POST",tlInputParameter::INT_N),
                   "artifactComponent" => array("POST",tlInputParameter::ARRAY_INT),
                   "artifactVersion" => array("POST",tlInputParameter::ARRAY_INT),
		               "user_action" => array("REQUEST",tlInputParameter::STRING_N,
                                          $user_action['minLengh'],$user_action['maxLengh']),
                   "addLinkToTL" => array("POST",tlInputParameter::CB_BOOL),
                   "addLinkToTLPrintView" => array("POST",tlInputParameter::CB_BOOL),
                   "tcstep_id" => array("REQUEST",tlInputParameter::INT_N),);
	
	$args = new stdClass();
	I_PARAMS($iParams,$args);
	if ($args->exec_id) {
		$_SESSION['bugAdd_execID'] = intval($args->exec_id);
	}
	else {
		$args->exec_id = intval(isset($_SESSION['bugAdd_execID']) ? $_SESSION['bugAdd_execID'] : 0);
	}	

  // it's a checkbox
  $args->addLinkToTL = isset($_REQUEST['addLinkToTL']);
  $args->addLinkToTLPrintView = isset($_REQUEST['addLinkToTLPrintView']);
  
  $args->user = $_SESSION['currentUser'];

  $gui = new stdClass();
  $cfg = config_get('exec_cfg');
  $gui->addLinkToTLChecked = $cfg->exec_mode->addLinkToTLChecked;
  $gui->addLinkToTLPrintViewChecked = 
    $cfg->exec_mode->addLinkToTLPrintViewChecked;


  switch($args->user_action) {
    case 'create':
    case 'doCreate':
      $gui->pageTitle = lang_get('create_issue');
    break;

    case 'add_note':
      $gui->pageTitle = lang_get('add_issue_note');
    break;

    case 'link':
    default:
      $gui->pageTitle = lang_get('title_bug_add');
    break;
  }

  $gui->msg = '';
  $gui->bug_summary = '';
  $gui->tproject_id = $args->tproject_id;
  $gui->tplan_id = $args->tplan_id;
  $gui->tcversion_id = $args->tcversion_id;
  $gui->tcstep_id = $args->tcstep_id;

  $gui->user_action = $args->user_action;
  $gui->bug_id = $args->bug_id;

  

  // ---------------------------------------------------------------
  // Special processing - Check for custom integration first
  $customIntegration = getCustomIntegrationForProject($dbHandler, $args->tproject_id);
  
  if ($customIntegration) {
    logBugAdd("Using custom integration: " . $customIntegration['name']);
    
    // Override with custom integration
    $itObj = null; // We'll handle this differently
    $itCfg = new stdClass();
    $itCfg->tlCanCreateIssue = true;
    $itCfg->tlCanAddIssueNote = false;
    $itCfg->issuetracker_name = $customIntegration['name'];
    $itCfg->editIssueAttr = true;
    $itCfg->userinteraction = 0; // Use custom form
    
    // Set GUI variables for custom integration
    $gui->issueTrackerIntegrationOn = true;
    $gui->tlCanCreateIssue = true;
    $gui->tlCanAddIssueNote = false;
    $gui->customIntegrationEnabled = true;
    $gui->customIntegration = $customIntegration;
    $gui->accessToIssueTracker = lang_get('link_bts_create_bug') . " ({$customIntegration['name']})";
    $gui->createIssueURL = $customIntegration['url'];
    $gui->issueTrackerMetaData = null;
    
  } else {
    logBugAdd("Using default issue tracker");
    // Use default TestLink issue tracker
    list($itObj,$itCfg) = getIssueTracker($dbHandler,$args,$gui);
  }
  
  // Only process defaults if we have a valid issue tracker object
  if ($itObj) {
    $itsDefaults = $itObj->getCfg();
  } else {
    // Create dummy defaults for custom integration
    $itsDefaults = new stdClass();
    $itsDefaults->userinteraction = 0;
    $itsDefaults->issueType = null;
    $itsDefaults->issuepriority = null;
    $itsDefaults->version = array();
    $itsDefaults->component = array();
  }

  $gui->issueType = $args->issueType;
  $gui->issuePriority = $args->issuePriority;
  $gui->artifactVersion = $args->artifactVersion;
  $gui->artifactComponent = $args->artifactComponent;
  $gui->issueTrackerCfg->editIssueAttr = $itsDefaults->userinteraction;

  // This code has been verified with JIRA REST
  if ($itsDefaults->userinteraction == 0) {
    $singleVal = array('issuetype' => 'issueType',
                       'issuepriority' => 'issuePriority');
    foreach ($singleVal as $kj => $attr) {
      $gui->$attr = $itsDefaults->$kj;  
    }  

    $multiVal = array('version' => 'artifactVersion',
                      'component' => 'artifactComponent');
    foreach ($multiVal as $kj => $attr) {
      $gui->$attr = (array)$itsDefaults->$kj;  
    }  
  } 
  $gui->allIssueAttrOnScreen = 1;

  // Second access to user input
  $bug_summary['minLengh'] = 1; 
  $bug_summary['maxLengh'] = ($itObj && method_exists($itObj, 'getBugSummaryMaxLength')) ? $itObj->getBugSummaryMaxLength() : 100; 

  $inputCfg = array("bug_summary" => array("POST",tlInputParameter::STRING_N,
                                           $bug_summary['minLengh'],$bug_summary['maxLengh']));

  I_PARAMS($inputCfg,$args);

  $args->bug_id = trim($args->bug_id);
  switch ($args->user_action) {
    case 'create':
      if( $args->bug_id == '' && $args->exec_id > 0) {
        $map = get_execution($dbHandler,$args->exec_id);
        
        // Get the execution notes
        $execNotes = $map[0]['notes'];
        
        // Initialize default test case data
        $testCaseData = array(
          'scenario_id' => 'No Scenario ID Available',
          'primary_module' => 'No Primary Module Available',
          'sub_scenario' => 'No Sub-Scenario Available',
          'test_case_description' => 'No Description Available',
          'test_type' => 'No Test Type Available',
          'test_script' => 'No Test Script Available',
          'test_execution_path' => 'No Execution Path Available',
          'expected_results' => 'No Expected Results Available',
          'er_process' => 'No Process Results Available',
          'test_data' => 'No Test Data Available',
          'priority' => 'No Priority Available',
          'executed_by' => 'No Execution Info Available',
          'execution_status' => 'No Status Available',
          'testcase_name' => 'No Test Case Name Available',
          'notes' => $execNotes
        );
        
        // Try to use our custom view to get all the necessary fields
        $sql = "SELECT * FROM vw_testcase_customfields WHERE execution_id = " . intval($args->exec_id);
        $viewData = $dbHandler->get_recordset($sql);
        
        if (!empty($viewData)) {
          // We found data in our custom view
          if (!empty($viewData[0]['Scenario_ID'])) {
            $testCaseData['scenario_id'] = $viewData[0]['Scenario_ID'];
          }
          if (!empty($viewData[0]['Primary_Module'])) {
            $testCaseData['primary_module'] = $viewData[0]['Primary_Module'];
          }
          if (!empty($viewData[0]['Sub_Scenario'])) {
            $testCaseData['sub_scenario'] = $viewData[0]['Sub_Scenario'];
          }
          if (!empty($viewData[0]['Test_Case_Description'])) {
            $testCaseData['test_case_description'] = $viewData[0]['Test_Case_Description'];
          }
          if (!empty($viewData[0]['Test_Type'])) {
            $testCaseData['test_type'] = $viewData[0]['Test_Type'];
          }
          if (!empty($viewData[0]['Test_Script'])) {
            $testCaseData['test_script'] = $viewData[0]['Test_Script'];
          }
          if (!empty($viewData[0]['Test_Execution_Path'])) {
            $testCaseData['test_execution_path'] = $viewData[0]['Test_Execution_Path'];
          }
          if (!empty($viewData[0]['Expected_Results'])) {
            $testCaseData['expected_results'] = $viewData[0]['Expected_Results'];
          }
          if (!empty($viewData[0]['E_R_Process'])) {
            $testCaseData['er_process'] = $viewData[0]['E_R_Process'];
          }
          if (!empty($viewData[0]['Test_Data'])) {
            $testCaseData['test_data'] = $viewData[0]['Test_Data'];
          }
          if (!empty($viewData[0]['Priority'])) {
            $testCaseData['priority'] = $viewData[0]['Priority'];
          }
          if (!empty($viewData[0]['Executed_By'])) {
            $testCaseData['executed_by'] = $viewData[0]['Executed_By'];
          }
          if (!empty($viewData[0]['Execution_Status_CF'])) {
            $testCaseData['execution_status'] = $viewData[0]['Execution_Status_CF'];
          }
          if (!empty($viewData[0]['testcase_name'])) {
            $testCaseData['testcase_name'] = $viewData[0]['testcase_name'];
          }
        } else {
          // Fallback methods if the view doesn't have data
          // First, get the execution details to find the test case version
          $sql = "SELECT tcversion_id FROM executions WHERE id = " . intval($args->exec_id);
          $execData = $dbHandler->get_recordset($sql);
          
          if (!empty($execData)) {
            $tcversion_id = $execData[0]['tcversion_id'];
            
            // Get test case ID from nodes hierarchy
            $sql = "SELECT parent_id FROM nodes_hierarchy WHERE id = " . intval($tcversion_id);
            $tcData = $dbHandler->get_recordset($sql);
            
            if (!empty($tcData)) {
              $testcase_id = $tcData[0]['parent_id'];
              
              // Get test case name
              $sql = "SELECT name FROM nodes_hierarchy WHERE id = " . intval($testcase_id);
              $tcNameData = $dbHandler->get_recordset($sql);
              if (!empty($tcNameData)) {
                $testCaseData['testcase_name'] = $tcNameData[0]['name'];
                $testCaseData['scenario_id'] = $tcNameData[0]['name'];
              }
              
              // Get test case version details
              $sql = "SELECT summary, preconditions FROM tcversions WHERE id = " . intval($tcversion_id);
              $tcvDetails = $dbHandler->get_recordset($sql);
              
              if (!empty($tcvDetails)) {
                // Try to use preconditions for test data if available
                if (!empty($tcvDetails[0]['preconditions'])) {
                  $testCaseData['test_data'] = $tcvDetails[0]['preconditions'];
                }
                
                // Try to extract details from summary
                if (!empty($tcvDetails[0]['summary'])) {
                  // Parse summary for expected results
                  if (preg_match('/Expected\s*(?:Test)?\s*Results\s*(?:\(Functional\))?\s*[:#]?\s*([^\n]+)/i', $tcvDetails[0]['summary'], $matches)) {
                    $testCaseData['expected_results'] = trim($matches[1]);
                  }
                  
                  // Parse summary for sub-scenario
                  if (preg_match('/Sub-Scenario\s*[:/]\s*Action\s*[:#]?\s*([^\n]+)/i', $tcvDetails[0]['summary'], $matches)) {
                    $testCaseData['sub_scenario'] = trim($matches[1]);
                  }
                }
              }
              
              // Try to get custom field values directly from the database
              // First, try to get the field IDs for the relevant custom fields
              $sql = "SELECT id, name FROM custom_fields WHERE name LIKE '%Scenario%' OR name LIKE '%Function%' OR 
                     name LIKE '%Sub-Scenario%' OR name LIKE '%Action%' OR 
                     name LIKE '%Test Data%' OR name LIKE '%Expected Results%'";
              $cfFields = $dbHandler->get_recordset($sql);
              
              $fieldMap = array();
              if (!empty($cfFields)) {
                foreach ($cfFields as $field) {
                  $lcFieldName = strtolower($field['name']);
                  if (stripos($lcFieldName, 'scenario id') !== false || stripos($lcFieldName, 'function') !== false) {
                    $fieldMap['scenario_id'] = $field['id'];
                  } 
                  else if (stripos($lcFieldName, 'sub-scenario') !== false || stripos($lcFieldName, 'action') !== false) {
                    $fieldMap['sub_scenario'] = $field['id'];
                  }
                  else if (stripos($lcFieldName, 'test data') !== false) {
                    $fieldMap['test_data'] = $field['id'];
                  }
                  else if (stripos($lcFieldName, 'expected') !== false && stripos($lcFieldName, 'result') !== false) {
                    $fieldMap['expected_results'] = $field['id'];
                  }
                }
              }
              
              // Now get the values for these fields from the test case
              if (!empty($fieldMap)) {
                // Try to get values from design custom fields
                foreach ($fieldMap as $dataKey => $fieldId) {
                  $sql = "SELECT value FROM cfield_design_values WHERE field_id = " . intval($fieldId) . 
                         " AND node_id = " . intval($testcase_id);
                  $cfValue = $dbHandler->get_recordset($sql);
                  if (!empty($cfValue) && !empty($cfValue[0]['value'])) {
                    $testCaseData[$dataKey] = $cfValue[0]['value'];
                  }
                }
                
                // Try to get values from execution custom fields
                foreach ($fieldMap as $dataKey => $fieldId) {
                  $sql = "SELECT value FROM cfield_execution_values WHERE field_id = " . intval($fieldId) . 
                         " AND execution_id = " . intval($args->exec_id);
                  $cfValue = $dbHandler->get_recordset($sql);
                  if (!empty($cfValue) && !empty($cfValue[0]['value'])) {
                    $testCaseData[$dataKey] = $cfValue[0]['value'];
                  }
                }
              }
            }
          }
        }
        
        // Create a simple bug report template with the exact format requested
        $template = "\n\n";
        
        // New template format without section titles
        $template .= "Function ID: " . $testCaseData['scenario_id'] . "\n";
        $template .= "Action: " . $testCaseData['sub_scenario'] . "\n";
        
        // Use test script as the test scenario
        $template .= "Test scenario: " . $testCaseData['test_script'] . "\n";
        
        // Format test data with brackets
        $formattedTestData = " ";
        if (!empty($testCaseData['test_data'])) {
            $formattedTestData = "[\n" . trim($testCaseData['test_data']) . "\n]";
        }
        $template .= "Test Data: " . $formattedTestData . "\n";
        
        // Expected results without section title
        $template .= "Expected result: " . $testCaseData['expected_results'] . " Process & Business Rules: " . $testCaseData['er_process'] . "\n";
        
        // Test result (from notes) in square brackets
        $notesValue = !empty($testCaseData['notes']) ? trim($testCaseData['notes']) : "";
        $formattedNotes = "[\n" . $notesValue . "\n]";
        $template .= "Test result: " . $formattedNotes . "\n";
        
        // Combine execution notes with the template
        $args->bug_notes = $template;
      }  
    break;
    
    case 'doCreate':
    case 'add_note':
    case 'link':
    default:
    break;
  }
  $gui->bug_notes = $args->bug_notes = trim($args->bug_notes);

  $args->basehref = $_SESSION['basehref'];
  $tables = tlObjectWithDB::getDBTables(array('testplans'));
  $sql = ' SELECT api_key FROM ' . $tables['testplans'] . 
         ' WHERE id=' . intval($args->tplan_id);
      
  $rs = $dbHandler->get_recordset($sql);
  $args->tplan_apikey = $rs[0]['api_key'];

  return array($args,$gui,$itObj,$itCfg);
}


/**
 *
 */
function getIssueTracker(&$dbHandler,$argsObj,&$guiObj)
{
  $its = null;
  $tprojectMgr = new testproject($dbHandler);
  $info = $tprojectMgr->get_by_id($argsObj->tproject_id);

  $guiObj->issueTrackerCfg = new stdClass();
  $guiObj->issueTrackerCfg->createIssueURL = null;
  $guiObj->issueTrackerCfg->VerboseID = '';
  $guiObj->issueTrackerCfg->VerboseType = '';
  $guiObj->issueTrackerCfg->bugIDMaxLength = 0;
  $guiObj->issueTrackerCfg->bugSummaryMaxLength = 100; // MAGIC 
  $guiObj->issueTrackerCfg->tlCanCreateIssue = false;
  $guiObj->issueTrackerCfg->tlCanAddIssueNote = true;

  if($info['issue_tracker_enabled']) {
  	$it_mgr = new tlIssueTracker($dbHandler);
  	$issueTrackerCfg = $it_mgr->getLinkedTo($argsObj->tproject_id);
    
  	if( !is_null($issueTrackerCfg) ) {
  		$its = $it_mgr->getInterfaceObject($argsObj->tproject_id);
    
  		$guiObj->issueTrackerCfg->VerboseType = $issueTrackerCfg['verboseType'];
  		$guiObj->issueTrackerCfg->VerboseID = $issueTrackerCfg['issuetracker_name'];
  		$guiObj->issueTrackerCfg->bugIDMaxLength = $its->getBugIDMaxLength();
  		$guiObj->issueTrackerCfg->createIssueURL = $its->getEnterBugURL();
      $guiObj->issueTrackerCfg->bugSummaryMaxLength = $its->getBugSummaryMaxLength();
          
      $guiObj->issueTrackerCfg->tlCanCreateIssue = method_exists($its,'addIssue');
      $guiObj->issueTrackerCfg->tlCanAddIssueNote = method_exists($its,'addNote');
  	}
  }	              
  return array($its,$issueTrackerCfg); 
}

/**
 *
 */
function getDirectLinkToExec(&$dbHandler,$execID)
{
  $tbk = array('executions','testplan_tcversions');
  $tbl = tlObjectWithDB::getDBTables($tbk);
  $sql = " SELECT EX.id,EX.build_id,EX.testplan_id," .
         " EX.tcversion_id,TPTCV.id AS feature_id " .
         " FROM {$tbl['executions']} EX " .
         " JOIN {$tbl['testplan_tcversions']} TPTCV " .
         " ON TPTCV.testplan_id=EX.testplan_id " .
         " AND TPTCV.tcversion_id=EX.tcversion_id " .
         " AND TPTCV.platform_id=EX.platform_id " .
         " WHERE EX.id=" . intval($execID);

  $rs = $dbHandler->get_recordset($sql);
  $rs = $rs[0];
  $dlk = trim($_SESSION['basehref'],'/') . 
         "/ltx.php?item=exec&feature_id=" . $rs['feature_id'] .
         "&build_id=" . $rs['build_id'];
  
  return $dlk;
}

/**
 * Checks the user rights for viewing the page
 * 
 * @param $db resource the database connection handle
 * @param $user tlUser the object of the current user
 *
 * @return boolean return true if the page can be viewed, false if not
 */
function checkRights(&$db,&$user) {
	$hasRights = $user->hasRight($db,"testplan_execute");
	return $hasRights;
}