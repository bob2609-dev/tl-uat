<?php
/**
 * Attachment download handler with database/file system storage support
 */
 
// Configure error reporting properly
error_reporting(E_ALL);
ini_set('display_errors', 0);  // Disable display in production
ini_set('log_errors', 1);      // Enable error logging

require_once('../../config.inc.php');

// Validate input
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die("Invalid attachment ID");



// Database connection with security improvements
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($db->connect_errno) throw new Exception("Database connection failed");
    
    // Use prepared statement to prevent SQL injection
    $stmt = $db->prepare("SELECT * FROM attachments WHERE id = ?");
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) throw new Exception("Query failed");
    
    $result = $stmt->get_result();
    if (!$result || $result->num_rows === 0) die("Attachment not found");
    
    $attachmentInfo = $result->fetch_assoc();
    $result->close();
    $stmt->close();

    // Determine storage type
    if (!defined('TL_REPOSITORY_TYPE_DB') || !isset($g_repositoryType)) {
        die("Repository configuration missing");
    }

    if ($g_repositoryType == TL_REPOSITORY_TYPE_DB) {
        // Database-stored content
        if (!isset($attachmentInfo['content'])) die("Missing file content");
        
        header('Content-Type: ' . $attachmentInfo['file_type']);
        header('Content-Length: ' . $attachmentInfo['file_size']);
        header('Content-Disposition: inline; filename="' . basename($attachmentInfo['file_name']) . '"');
        echo base64_decode($attachmentInfo['content']);
        exit;
    }

    // File system storage handling
    if (!isset($g_repositoryPath)) die("Repository path not configured");
    
    $filePath = realpath($g_repositoryPath . '/' . str_replace('\\', '/', $attachmentInfo['file_path']));
    $altPath = realpath($g_repositoryPath . '/' . implode('/', array_slice(explode('/', $attachmentInfo['file_path']), 1)));

    foreach ([$filePath, $altPath] as $path) {
        if ($path && is_readable($path)) {
            header('Content-Type: ' . $attachmentInfo['file_type']);
            header('Content-Length: ' . filesize($path));
            header('Content-Disposition: inline; filename="' . basename($attachmentInfo['file_name']) . '"');
            readfile($path);
            exit;
        }
    }

    // Error handling for missing files
    http_response_code(404);
    echo "<h1>File Not Found</h1>";
    echo "<p>Attachment ID: {$id}</p>";
    echo "<p>Attempted paths:<br>";
    echo htmlspecialchars($filePath) . "<br>";
    echo htmlspecialchars($altPath) . "</p>";
    
    if (is_dir($g_repositoryPath)) {
        echo "<h3>Directory Contents:</h3>";
        listDirectoryContents($g_repositoryPath);
    }

} catch (Exception $e) {
    error_log("Attachment error: " . $e->getMessage());
    die("An error occurred processing your request");
}

function listDirectoryContents($dir, $indent = '') {
    $items = @scandir($dir);
    if (!$items) return;
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $dir . '/' . $item;
        echo htmlspecialchars("$indent- $item") . (is_dir($path) ? "/<br>" : "<br>");
        
        if (is_dir($path)) {
            listDirectoryContents($path, $indent . '&nbsp;&nbsp;&nbsp;&nbsp;');
        }
    }
}

// Cleanup
if (isset($db) && $db instanceof mysqli) {
    $db->close();
}
