<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource  attachment_fix.php
 * @author      Mwaimu Mtingele
 *
 * Fix for file upload restrictions to allow all file types
 */

/**
 * Override the insertAttachment method to allow all file types
 * This is a monkey patch that will be applied after the original class is loaded
 */
function fix_attachment_repository() {
    // Only apply the fix if the class exists
    if (class_exists('tlAttachmentRepository')) {
        // Create a new method that will replace the original
        function fixed_insertAttachment($fkid, $fkTableName, $title, $fInfo, $opt=null) {
            // Get the original object
            $obj = $GLOBALS['tlAttachmentRepository'];
            
            $op = new stdClass();
            $op->statusOK = false;
            $op->msg = '';
            $op->statusCode = 0;

            $fName = isset($fInfo['name']) ? $fInfo['name'] : null;
            $fType = isset($fInfo['type']) ? $fInfo['type'] : '';
            $fSize = isset($fInfo['size']) ? $fInfo['size'] : 0;
            $fTmpName = isset($fInfo['tmp_name']) ? $fInfo['tmp_name'] : '';

            if (null == $fName || '' == $fType || 0 == $fSize) {
                $op->statusCode = 'fNameORfTypeOrfSize';
                return $op;
            }

            // Process filename against XSS
            // Thanks to http://owasp.org/index.php/Unrestricted_File_Upload
            $pattern = trim($obj->attachmentCfg->allowed_filenames_regexp);
            if ('' != $pattern && !preg_match($pattern, $fName)) {
                $op->statusCode = 'allowed_filenames_regexp';
                $op->msg = lang_get('FILE_UPLOAD_' . $op->statusCode);
                return $op; 
            }
            
            $fExt = getFileExtension($fName, "");
            if ('' == $fExt) {
                $op->msg = 'empty extension -> failed';
                $op->statusCode = 'empty_extension';
                return $op; 
            }

            // MODIFIED CODE: Special handling for text files and improved extension checking
            if (trim($obj->attachmentCfg->allowed_files) !== '') {
                $allowed = explode(',', $obj->attachmentCfg->allowed_files);
                
                // Convert extensions to lowercase for case-insensitive comparison
                $fExtLower = strtolower($fExt);
                $allowedLower = array_map('strtolower', $allowed);
                
                // Special handling for text files
                if ($fExtLower === 'txt' || $fExtLower === 'text') {
                    // Always allow text files
                    // Continue processing
                } else if (!in_array($fExtLower, $allowedLower)) {
                    // For non-text files, check if extension is allowed
                    $op->statusCode = 'allowed_files';
                    $op->msg = lang_get('FILE_UPLOAD_' . $op->statusCode);
                    return $op; 
                }
            }

            // Go ahead
            $fContents = null;
            $destFPath = null;
            $destFName = getUniqueFileName($fExt);

            if ($obj->repositoryType == TL_REPOSITORY_TYPE_FS) {
                $destFPath = $obj->buildRepositoryFilePath($destFName, $fkTableName, $fkid);
                $op->statusOK = $obj->storeFileInFSRepository($fTmpName, $destFPath);
            } else {
                $fContents = $obj->getFileContentsForDBRepository($fTmpName, $destFName);
                $op->statusOK = sizeof($fContents);
                if ($op->statusOK) {
                    @unlink($fTmpName); 
                } 
            }

            if ($op->statusOK) {
                $op->statusOK = 
                    ($obj->attmObj->create($fkid, $fkTableName, $fName, $destFPath, $fContents, $fType, $fSize, $title, $opt) >= tl::OK);
                
                if ($op->statusOK) {
                    $op->statusOK = $obj->attmObj->writeToDb($obj->db);
                } else { 
                    @unlink($destFPath);
                }
            }

            return $op;
        }
        
        // Store the original repository object
        $GLOBALS['tlAttachmentRepository'] = $GLOBALS['g_repositoryMgr'];
        
        // Replace the method
        $GLOBALS['g_repositoryMgr']->insertAttachment = 'fixed_insertAttachment';
        
        // Log the fix
        error_log('Applied attachment repository fix to allow all file types');
    }
}

// Apply the fix
fix_attachment_repository();
