<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Add or modify a device in inventory list
 * 
 * @package 	TestLink
 * @author 		Martin Havlat
 * @copyright 2009,2019 TestLink community  
 *
 **/

require_once('../../config.inc.php');
require_once('common.php');
testlinkInitPage($db);

$data['userfeedback'] = lang_get('inventory_msg_no_action');
$data['success'] = FALSE;
$args = init_args();

if ($_SESSION['currentUser']->hasRight($db,"project_inventory_management")) {
  	$tproj_id = intval($_SESSION['testprojectID']);
  	$tlIs = new tlInventory($tproj_id, $db);
  
  	// Validate machineOwner maps to an active user
  	$ownerId = isset($args->machineOwner) ? intval($args->machineOwner) : 0;
  	if ($ownerId > 0) {
  		$ownerUser = new tlUser($ownerId);
  		$ownerStatus = $ownerUser->readFromDB($db);
  		$ownerActive = ($ownerStatus >= tl::OK) ? intval($ownerUser->isActive) === 1 : false;
  		if (!$ownerActive) {
  			$data['success'] = false;
  			$data['userfeedback'] = lang_get('inventory_invalid_owner');
  			echo json_encode($data);
  			exit;
  		}
  	} else {
  		// Owner must be specified explicitly and valid
  		$data['success'] = false;
  		$data['userfeedback'] = lang_get('inventory_invalid_owner');
  		echo json_encode($data);
  		exit;
  	}
  	// Convert stdClass to array for setInventory
  	$argsArray = (array)$args;
  	$data['success'] = $tlIs->setInventory($argsArray);
  	$data['success'] = ($data['success'] == 1 /*$tlIs->OK*/) ? true : false;
  	$data['userfeedback'] = $tlIs->getUserFeedback();
	$data['record'] = $tlIs->getCurrentData();
}
else {
	tLog('User has not rights to set a device!','ERROR');
	$data['userfeedback'] = lang_get('inventory_msg_no_rights');
}

echo json_encode($data);

/**
 *
 */
function init_args()
{
  $_REQUEST = strings_stripSlashes($_REQUEST);
	$iParams = 
	  array("machineID" => array(tlInputParameter::INT_N),
					"machineOwner" => array(tlInputParameter::INT_N),
			    "machineName" => array(tlInputParameter::STRING_N,0,255),
			    "machineIp" => array(tlInputParameter::STRING_N,0,50),
			    "machineNotes" => array(tlInputParameter::STRING_N,0,2000),
			    "machinePurpose" => array(tlInputParameter::STRING_N,0,2000),
			    "machineHw" => array(tlInputParameter::STRING_N,0,2000),
	 	);

	$args = new stdClass();
  R_PARAMS($iParams,$args);
  
  // XSS prevention: Apply additional sanitization to all string inputs
  if(property_exists($args, 'machineName')) {
    $args->machineName = htmlspecialchars($args->machineName, ENT_QUOTES, 'UTF-8');
  }
  if(property_exists($args, 'machineIp')) {
    $args->machineIp = htmlspecialchars($args->machineIp, ENT_QUOTES, 'UTF-8');
  }
  if(property_exists($args, 'machineNotes')) {
    $args->machineNotes = htmlspecialchars($args->machineNotes, ENT_QUOTES, 'UTF-8');
  }
  if(property_exists($args, 'machinePurpose')) {
    $args->machinePurpose = htmlspecialchars($args->machinePurpose, ENT_QUOTES, 'UTF-8');
  }
  if(property_exists($args, 'machineHw')) {
    $args->machineHw = htmlspecialchars($args->machineHw, ENT_QUOTES, 'UTF-8');
  }
    
  return $args;
}