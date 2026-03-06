<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Downloads the attachment by a given id
 * (Modified to redirect to the fixed direct_image.php script)
 *
 * @filesource attachmentdownload.php
 *
 */

// Get the attachment ID
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

// If we have an ID, redirect to our fixed image handler
if ($id > 0) {
    // Calculate base URL (will work with both http and https)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host;
    
    // Create the redirect URL
    $redirectUrl = $baseUrl . '/direct_image.php?id=' . $id;
    
    // Perform redirect
    header('Location: ' . $redirectUrl);
    exit;
}
@ob_end_clean();
require_once('../../config.inc.php');
require_once('../functions/common.php');
require_once('../functions/attachments.inc.php');
require_once('attachmentfix.php'); // Include our new MIME type helper

// This way can be called without _SESSION, this is useful for reports
testlinkInitPage($db,false,true);

$args = init_args($db);
if ($args->id)
{
  $attachmentRepository = tlAttachmentRepository::create($db);
  $attachmentInfo = $attachmentRepository->getAttachmentInfo($args->id);

  if ($attachmentInfo) {
    switch ($args->opmode) 
    {
      case 'API':
        // want to check if apikey provided is right for attachment context
        // - test project api key:
        //   is needed to get attachments for:
        //   test specifications
        //
        // - test plan api key:
        //   is needed to get attacments for:
        //   test case executions
        //   test specifications  ( access to parent data - OK!)
        //   
        // What kind of attachments I've got ?
        $doIt = false;
        $attContext = $attachmentInfo['fk_table'];
        switch($attContext)
        {
          case 'executions':
            // check apikey
            // 1. has to be a test plan key
            // 2. execution must belong to the test plan.
            $item = getEntityByAPIKey($db,$args->apikey,'testplan');
            if( !is_null($item) )
            {
              $tables = tlObjectWithDB::getDBTables(array('executions'));
              $sql = "SELECT testplan_id FROM {$tables['executions']} " .
                     "WHERE id = " . intval($attachmentInfo['fk_id']);

              $rs = $db->get_recordset($sql);
              if(!is_null($rs))
              {
                if($rs['0']['testplan_id'] == $item['id'])
                {
                  // GOOD !
                  $doIt = true;
                }  
              }       
            }  
          break;
        }
      break;
      
      case 'GUI':
      default:   
        $doIt = true;
      break;
    }


    if( $doIt )
    {
      $content = '';
      $getContent = true;
      if( $args->opmode !== 'API' && $args->skipCheck !== 0 && $args->skipCheck !== false)
      {
        if( $args->skipCheck != hash('sha256',$attachmentInfo['file_name']) )
        {
          $getContent = false;
        }  
      }  

      if($getContent)
      {
        $content = $attachmentRepository->getAttachmentContent($args->id,$attachmentInfo);
      }  

      if ($content != "" )
      {
        @ob_end_clean();
        
        // Ensure no output buffering is active
        while (ob_get_level()) {
          ob_end_clean();
        }
        
        // Reset any previous output
        if (headers_sent()) {
          die("Headers already sent. Cannot serve attachment properly.");
        }
        
        // No caching
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // Get correct MIME type from our helper function instead of using the stored one
        $correctMimeType = getMimeType($attachmentInfo['file_name']);
        
        // Force specific MIME types for common image formats regardless of stored type
        $ext = strtolower(pathinfo($attachmentInfo['file_name'], PATHINFO_EXTENSION));
        switch($ext) {
          case 'jpg':
          case 'jpeg':
          case 'jpe':
            $contentType = 'image/jpeg';
            break;
          case 'png':
            $contentType = 'image/png';
            break;
          case 'gif':
            $contentType = 'image/gif';
            break;
          case 'bmp':
            $contentType = 'image/bmp';
            break;
          case 'svg':
            $contentType = 'image/svg+xml';
            break;
          default:
            // Use our determined MIME type but fall back to stored type if needed
            $contentType = ($correctMimeType != 'application/octet-stream') ? 
                           $correctMimeType : $attachmentInfo['file_type'];
            break;
        }
        
        // Set content type and length
        header('Content-Type: ' . $contentType);
        header('Content-Length: '.$attachmentInfo['file_size']);
        
        // Ensure proper content disposition
        $filename = basename($attachmentInfo['file_name']);
        header("Content-Disposition: inline; filename=\"$filename\"");
        header("Content-Description: File Transfer");

        // Output the file content based on repository type
        global $g_repositoryType;
        if($g_repositoryType == TL_REPOSITORY_TYPE_DB)
        {
          // For database storage - properly decode the base64 content
          $decoded = base64_decode($content, true);
          if($decoded !== false) {
            echo $decoded;
          } else {
            // Fall back to raw content if base64 decode fails
            echo $content;
          }
        }
        else {
          // For filesystem storage
          echo $content;
        }
        exit();
      }
    }  
  }
}

$smarty = new TLSmarty();
$smarty->assign('gui',$args);
$smarty->display('attachment404.tpl');

/**
 * @return object returns the arguments for the page
 */
function init_args(&$dbHandler)
{
  // id (attachments.id) of the attachment to be downloaded
  $iParams = array('id' => array(tlInputParameter::INT_N),
                   'apikey' => array(tlInputParameter::STRING_N,64),  
                   'skipCheck' => array(tlInputParameter::STRING_N,1,64));
  
  $args = new stdClass();
  G_PARAMS($iParams,$args);

  $args->light = 'green';
  $args->opmode = 'GUI';
  if( is_null($args->skipCheck) || $args->skipCheck === 0 )
  {
    $args->skipCheck = false;
  }  

  // var_dump($args->skipCheck);die();
  // using apikey lenght to understand apikey type
  // 32 => user api key
  // other => test project or test plan
  $args->apikey = trim($args->apikey);
  $apikeyLenght = strlen($args->apikey);
  if($apikeyLenght > 0)
  {
    $args->opmode = 'API';
    $args->skipCheck = true;
  } 
  return $args;
}

/**
 * @param $db resource the database connection handle
 * @param $user the current active user
 * @return boolean returns true if the page can be accessed
 */
function checkRights(&$db,&$user)
{
  return (config_get("attachments")->enabled);
}