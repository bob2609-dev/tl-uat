<?php
// Simple script to download a file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['path']) && isset($_POST['filename'])) {
    $path = $_POST['path'];
    $filename = $_POST['filename'];
    $mimetype = $_POST['mimetype'] ?? 'application/octet-stream';
    
    if (file_exists($path)) {
        header('Content-Type: ' . $mimetype);
        header('Content-Length: ' . filesize($path));
        header('Content-Disposition: inline; filename="' . $filename . '"');
        readfile($path);
        exit;
    } else {
        echo "File not found: " . $path;
    }
} else {
    echo "Invalid request";
}
?>		