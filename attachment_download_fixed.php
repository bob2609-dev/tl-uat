<?php
/**
 * Standalone attachment download script for TestLink
 */
// Disable error reporting
error_reporting(0);
ini_set('display_errors', 0);

// Include minimal TestLink configuration
require_once('config.inc.php');

// Get attachment ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header("HTTP/1.0 404 Not Found");
    die("No attachment ID specified");
}

// Connect to database
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    header("HTTP/1.0 500 Internal Server Error");
    die("Database connection failed");
}

// Get attachment info
$sql = "SELECT * FROM attachments WHERE id = $id";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    $attachment = $result->fetch_assoc();
    
    // Get the file path from the database
    $dbPath = $attachment['file_path'];
    $dbPath = str_replace('\\', '/', $dbPath);
    
    // Build full path
    $filePath = $g_repositoryPath . '/' . $dbPath;
    
    // Check if file exists
    if (file_exists($filePath)) {
        // Set appropriate headers
        header('Pragma: public');
        header("Cache-Control: ");
        header('Content-Type: ' . $attachment['file_type']);
        header('Content-Length: ' . filesize($filePath));
        header("Content-Disposition: inline; filename=\"{$attachment['file_name']}\"");
        
        // Output the file
        readfile($filePath);
        exit;
    }
    
    // File not found at expected path, try to find it
    $parts = explode('/', $dbPath);
    $fileName = end($parts);
    $dirPath = dirname($filePath);
    
    if (is_dir($dirPath)) {
        // Check this directory for the file
        $files = scandir($dirPath);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file === $fileName) {
                header('Pragma: public');
                header("Cache-Control: ");
                header('Content-Type: ' . $attachment['file_type']);
                header('Content-Length: ' . filesize($dirPath . '/' . $file));
                header("Content-Disposition: inline; filename=\"{$attachment['file_name']}\"");
                readfile($dirPath . '/' . $file);
                exit;
            }
        }
    }
    
    // If still not found, search by size
    $searchDirs = [
        $g_repositoryPath,
        $g_repositoryPath . '/executions',
        $g_repositoryPath . '/executions/' . $id
    ];
    
    foreach ($searchDirs as $dir) {
        if (is_dir($dir)) {
            $it = new RecursiveDirectoryIterator($dir);
            $it = new RecursiveIteratorIterator($it);
            
            foreach ($it as $fileInfo) {
                if ($fileInfo->isFile() && 
                    abs($fileInfo->getSize() - $attachment['file_size']) < 100) {
                    header('Pragma: public');
                    header("Cache-Control: ");
                    header('Content-Type: ' . $attachment['file_type']);
                    header('Content-Length: ' . $fileInfo->getSize());
                    header("Content-Disposition: inline; filename=\"{$attachment['file_name']}\"");
                    readfile($fileInfo->getPathname());
                    exit;
                }
            }
        }
    }
    
    header("HTTP/1.0 404 Not Found");
    die("File not found");
} else {
    header("HTTP/1.0 404 Not Found");
    die("Attachment not found with ID: " . $id);
}

$db->close();
?>