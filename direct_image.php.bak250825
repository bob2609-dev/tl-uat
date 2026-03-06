<?php
// Super simple image serving script with minimal dependencies

// Disable all error reporting and output
error_reporting(0);
ini_set('display_errors', 0);

// Start with a clean slate - no output before headers
if (ob_get_level()) ob_end_clean();

// Get the attachment ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header("HTTP/1.0 404 Not Found");
    exit("No ID specified");
}

// Use TestLink's database configuration
require_once('config_db.inc.php');

// These can be changed to match your actual config
$uploadDir = 'upload_area';

// Connect to database using TestLink's config values
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// If connection fails, show a more detailed error to help with debugging
if ($db->connect_error) {
    header("HTTP/1.0 500 Internal Server Error");
    exit("Database connection error: " . $db->connect_error . " (Check your DB settings in config_db.inc.php)");
}

// Get attachment info
$sql = "SELECT * FROM attachments WHERE id = $id";
$result = $db->query($sql);

if (!$result || $result->num_rows == 0) {
    header("HTTP/1.0 404 Not Found");
    exit("Attachment not found");
}

$attachment = $result->fetch_assoc();
$fileName = $attachment['file_name'];
$fileSize = $attachment['file_size'];
$filePath = $attachment['file_path'];

// If file_path doesn't exist or isn't a full path, try to find it
$possiblePaths = array(
    $filePath,
    $uploadDir . '/' . $filePath,
    $uploadDir . '/nodes_hierarchy/' . $attachment['fk_id'] . '/' . $fileName,
    $uploadDir . '/executions/' . $attachment['fk_id'] . '/' . $fileName,
    'upload_area/' . $fileName,
    'upload_area/executions/' . $fileName
);

$found = false;
$foundPath = '';

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $found = true;
        $foundPath = $path;
        break;
    }
}

// If not found by path, try to find by size
if (!$found) {
    $searchDirs = array(
        $uploadDir,
        $uploadDir . '/nodes_hierarchy',
        $uploadDir . '/executions'
    );
    
    foreach ($searchDirs as $dir) {
        if (!is_dir($dir)) continue;
        
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $subfiles = scandir($path);
                foreach ($subfiles as $subfile) {
                    if ($subfile == '.' || $subfile == '..') continue;
                    
                    $subpath = $path . '/' . $subfile;
                    if (is_file($subpath) && filesize($subpath) == $fileSize) {
                        $found = true;
                        $foundPath = $subpath;
                        break 3;
                    }
                }
            } elseif (is_file($path) && filesize($path) == $fileSize) {
                $found = true;
                $foundPath = $path;
                break 2;
            }
        }
    }
}

if (!$found) {
    header("HTTP/1.0 404 Not Found");
    exit("File not found");
}

// Determine correct MIME type based on file extension
$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$mimeType = 'application/octet-stream'; // Default

switch ($ext) {
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
    case 'pdf':
        $mimeType = 'application/pdf';
        break;
    case 'doc':
        $mimeType = 'application/msword';
        break;
    case 'docx':
        $mimeType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        break;
    case 'xls':
        $mimeType = 'application/vnd.ms-excel';
        break;
    case 'xlsx':
        $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        break;
    case 'ppt':
        $mimeType = 'application/vnd.ms-powerpoint';
        break;
    case 'pptx':
        $mimeType = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
        break;
    case 'txt':
        $mimeType = 'text/plain';
        break;
}

// Output the file
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($foundPath));
header('Content-Disposition: inline; filename="' . $fileName . '"');
header('Cache-Control: max-age=86400'); // Cache for one day
header('Pragma: public');

readfile($foundPath);
exit;
?>

