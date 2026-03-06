<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource  txt_file_fix.php
 * @author      Mwaimu Mtingele
 *
 * Special fix to allow text file uploads regardless of configuration
 */

// Simple direct fix for text file uploads
// This modifies the allowed_files configuration to ensure txt is included

// Make sure the configuration object exists
if (!isset($tlCfg)) {
    global $tlCfg;
}

if (isset($tlCfg) && isset($tlCfg->attachments)) {
    // Get the current allowed files setting
    $currentAllowed = isset($tlCfg->attachments->allowed_files) ? $tlCfg->attachments->allowed_files : '';
    
    // Check if txt is already in the allowed list (case insensitive)
    $allowedArray = explode(',', $currentAllowed);
    $hasTxt = false;
    
    foreach ($allowedArray as $ext) {
        if (strtolower(trim($ext)) === 'txt') {
            $hasTxt = true;
            break;
        }
    }
    
    // If txt is not in the list, add it
    if (!$hasTxt) {
        // Add txt to the allowed files list
        if (empty($currentAllowed)) {
            $tlCfg->attachments->allowed_files = 'txt,TXT';
        } else {
            $tlCfg->attachments->allowed_files = $currentAllowed . ',txt,TXT';
        }
        
        // Log that we added txt to the allowed files
        error_log('Added txt to allowed file types: ' . $tlCfg->attachments->allowed_files);
    }
    
    // Remove any filename restrictions
    $tlCfg->attachments->allowed_filenames_regexp = '';
    
    // Log the current configuration
    error_log('Current allowed files: ' . $tlCfg->attachments->allowed_files);
    error_log('Current filename pattern: ' . $tlCfg->attachments->allowed_filenames_regexp);
}

// Add a hook to modify the file extension check at runtime
// This is a more reliable approach than modifying the configuration
function txt_file_upload_hook($hookMethod, &$argsObj, &$fInfo) {
    // Only process for file uploads
    if ($hookMethod === 'uploadFile') {
        // Check if this is a text file
        $fileName = isset($fInfo['name']) ? $fInfo['name'] : '';
        if (!empty($fileName)) {
            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
            if (strtolower($fileExt) === 'txt') {
                // Force allow this file by modifying the allowed_files setting temporarily
                global $tlCfg;
                $originalAllowed = $tlCfg->attachments->allowed_files;
                $tlCfg->attachments->allowed_files = $originalAllowed . ',txt,TXT';
                
                // Log that we're allowing this text file
                error_log('TXT file upload hook: Allowing text file: ' . $fileName);
            }
        }
    }
    return true;
}

// Register our hook if the hooks system is available
if (isset($tlCfg) && isset($tlCfg->hooks)) {
    if (!isset($tlCfg->hooks['uploadFile'])) {
        $tlCfg->hooks['uploadFile'] = array();
    }
    
    // Add our hook to the uploadFile hooks
    $tlCfg->hooks['uploadFile'][] = 'txt_file_upload_hook';
    
    error_log('Registered txt_file_upload_hook');
}
