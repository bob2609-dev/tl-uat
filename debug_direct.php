<?php
/**
 * Direct image output with forced MIME type
 */
require_once('config.inc.php');
require_once('lib/functions/common.php');
require_once('lib/functions/attachments.inc.php');

// Initialize TestLink
testlinkInitPage($db, false, true);

// Clear all output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Get specific attachment ID from query parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Please provide a valid attachment ID");
}

// Get attachment information
$attachmentRepository = tlAttachmentRepository::create($db);
$attachmentInfo = $attachmentRepository->getAttachmentInfo($id);

if (!$attachmentInfo) {
    die("Attachment not found");
}

// Get content
$content = $attachmentRepository->getAttachmentContent($id, $attachmentInfo);

if (empty($content)) {
    die("No content available");
}

// Determine correct MIME type based on extension
$ext = strtolower(pathinfo($attachmentInfo['file_name'], PATHINFO_EXTENSION));
switch($ext) {
    case 'jpg':
    case 'jpeg':
    case 'jpe':
        $mimeType = 'image/jpeg';
        break;
    case 'png':
        $mimeType = 'image/png';
        break;
    case 'gif':
        $mimeType = 'image/gif';
        break;
    case 'bmp':
        $mimeType = 'image/bmp';
        break;
    case 'svg':
        $mimeType = 'image/svg+xml';
        break;
    default:
        $mimeType = 'application/octet-stream';
        break;
}

// Set appropriate headers
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . $attachmentInfo['file_size']);
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Output the content
global $g_repositoryType;
if ($g_repositoryType == TL_REPOSITORY_TYPE_DB) {
    // For DB storage, test both with and without base64 decoding
    $outputContent = base64_decode($content, true);
    if ($outputContent === false) {
        // If base64 decode fails, output the raw content as fallback
        echo $content;
    } else {
        echo $outputContent;
    }
} else {
    // For filesystem storage
    echo $content;
}

exit();
