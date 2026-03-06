<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Attachment MIME type helper functions
 */

/**
 * Get the correct MIME type for a file based on its extension
 * @param string $filename The filename to check
 * @return string The appropriate MIME type
 */
function getMimeType($filename) {
    $mime_types = array(
        // Images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        
        // Documents
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'text/plain',
        'csv' => 'text/csv',
        'html' => 'text/html',
        'htm' => 'text/html',
        'xml' => 'application/xml',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'tar' => 'application/x-tar',
        'gz' => 'application/gzip'
    );
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (array_key_exists($ext, $mime_types)) {
        return $mime_types[$ext];
    } else {
        return 'application/octet-stream';
    }
}
?>