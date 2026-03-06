<?php
// Simple script to directly output an image file
$path = isset($_GET['path']) ? $_GET['path'] : '';

if (!empty($path) && file_exists($path) && is_file($path)) {
    $mimeType = mime_content_type($path);
    header('Content-Type: ' . $mimeType);
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
} else {
    header('HTTP/1.0 404 Not Found');
    echo "File not found or invalid path.";
}
?>