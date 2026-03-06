<?php
/**
 * Debug script to investigate attachment handling issues
 */
require_once('config.inc.php');
require_once('lib/functions/common.php');
require_once('lib/functions/attachments.inc.php');

// Initialize TestLink
testlinkInitPage($db, false, true);

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get specific attachment ID from query parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("Please provide a valid attachment ID as 'id' parameter");
}

// Debug output
echo "<h1>Attachment Debug Information</h1>";

// Get attachment information
$attachmentRepository = tlAttachmentRepository::create($db);
$attachmentInfo = $attachmentRepository->getAttachmentInfo($id);

if (!$attachmentInfo) {
    die("Attachment not found with ID: $id");
}

// Display attachment metadata
echo "<h2>Attachment Metadata</h2>";
echo "<pre>";
print_r($attachmentInfo);
echo "</pre>";

// Get repository type
global $g_repositoryType;
echo "<p>Repository Type: " . ($g_repositoryType == TL_REPOSITORY_TYPE_DB ? "Database" : "Filesystem") . "</p>";

// Get raw content without processing
echo "<h2>First 100 bytes of Raw Content</h2>";
$content = $attachmentRepository->getAttachmentContent($id, $attachmentInfo);

if (empty($content)) {
    echo "<p>No content retrieved</p>";
} else {
    echo "<p>Content length: " . strlen($content) . " bytes</p>";
    echo "<p>First 100 bytes (hex):</p>";
    echo "<pre>" . bin2hex(substr($content, 0, 100)) . "</pre>";
    
    echo "<h2>MIME Type Analysis</h2>";
    $storedType = $attachmentInfo['file_type'];
    echo "<p>Stored MIME Type: $storedType</p>";
    
    // Check file extension
    $ext = strtolower(pathinfo($attachmentInfo['file_name'], PATHINFO_EXTENSION));
    echo "<p>File Extension: $ext</p>";
    
    // Use finfo if available
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_buffer($finfo, $g_repositoryType == TL_REPOSITORY_TYPE_DB ? base64_decode($content) : $content);
        finfo_close($finfo);
        echo "<p>Detected MIME Type (finfo): $detectedType</p>";
    } else {
        echo "<p>finfo not available</p>";
    }
    
    // Output download link
    echo "<h2>Test Links</h2>";
    echo "<p><a href='lib/attachments/attachmentdownload.php?id=$id' target='_blank'>Regular Download Link</a></p>";
    
    // Create a direct output with forced MIME type
    if ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') {
        echo "<h2>Direct Output Test</h2>";
        echo "<p>Testing direct output with correct MIME type:</p>";
        echo "<img src='debug_direct.php?id=$id' />";
    }
}
?>
