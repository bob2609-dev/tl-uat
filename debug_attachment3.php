<?php
/**
 * Comprehensive debug and display script for TestLink attachments
 */
// Allow error reporting for troubleshooting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config to get database connection info
require_once('../../config.inc.php');

// Get attachment ID from request
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("No attachment ID specified");
}

// Connect to database directly
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get attachment info
$sql = "SELECT * FROM attachments WHERE id = " . $id;
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    $attachmentInfo = $result->fetch_assoc();
    
    // Display debugging information
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; font-family: monospace;'>";
    echo "<h2>Attachment Debug Info</h2>";
    echo "<strong>ID:</strong> " . $id . "<br>";
    echo "<strong>File Name:</strong> " . $attachmentInfo['file_name'] . "<br>";
    echo "<strong>File Path in DB:</strong> " . $attachmentInfo['file_path'] . "<br>";
    echo "<strong>File Size:</strong> " . $attachmentInfo['file_size'] . " bytes<br>";
    echo "<strong>File Type:</strong> " . $attachmentInfo['file_type'] . "<br>";
    echo "<strong>Repository Type:</strong> " . ($g_repositoryType == TL_REPOSITORY_TYPE_DB ? "Database" : "Filesystem") . "<br>";
    echo "<strong>Repository Path:</strong> " . $g_repositoryPath . "<br>";
    
    // Construct the full path based on repository path and file path from DB
    $dbPath = $attachmentInfo['file_path'];
    $dbPath = str_replace('\\', '/', $dbPath);
    $filePath = $g_repositoryPath . '/' . $dbPath;
    echo "<strong>Constructed Full Path:</strong> " . $filePath . "<br>";
    echo "<strong>File Exists at Path:</strong> " . (file_exists($filePath) ? "Yes" : "No") . "<br>";
    
    // Try without 'executions' prefix if it's already in the repository path
    $parts = explode('/', $dbPath);
    if ($parts[0] == 'executions') {
        array_shift($parts); // Remove first part (executions)
    }
    $simplifiedPath = implode('/', $parts);
    $alternatePath = $g_repositoryPath . '/' . $simplifiedPath;
    echo "<strong>Alternate Path:</strong> " . $alternatePath . "<br>";
    echo "<strong>File Exists at Alternate Path:</strong> " . (file_exists($alternatePath) ? "Yes" : "No") . "<br>";
    
    // Check parent directories
    $parentDir = dirname($filePath);
    echo "<strong>Parent Directory:</strong> " . $parentDir . "<br>";
    echo "<strong>Parent Directory Exists:</strong> " . (is_dir($parentDir) ? "Yes" : "No") . "<br>";
    
    if (is_dir($parentDir)) {
        echo "<strong>Parent Directory Contents:</strong><br>";
        $files = scandir($parentDir);
        echo "<ul>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li>" . $file . " (" . filesize($parentDir . '/' . $file) . " bytes)</li>";
            }
        }
        echo "</ul>";
    }
    
    // Look for files with similar size
    echo "<strong>Looking for files with similar size (" . $attachmentInfo['file_size'] . " bytes):</strong><br>";
    findFilesBySize($g_repositoryPath, $attachmentInfo['file_size']);
    
    echo "</div>";
    
    // Now try to display the image
    echo "<h2>Attempting to display the image:</h2>";
    
    // Try various possible paths
    $possiblePaths = [
        $filePath,
        $alternatePath,
        // Try with basename only
        $g_repositoryPath . '/' . basename($dbPath),
        $g_repositoryPath . '/executions/' . $id . '/' . basename($dbPath),
    ];
    
    $imageFound = false;
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid green;'>";
            echo "<p>Image found at: " . $path . "</p>";
            
            // Create a base64 data URL to display the image
            $imageData = file_get_contents($path);
            $base64 = base64_encode($imageData);
            $mime = $attachmentInfo['file_type'];
            
            echo "<img src='data:$mime;base64,$base64' style='max-width: 800px; border: 2px solid blue;'>";
            echo "</div>";
            
            $imageFound = true;
            break;
        }
    }
    
    if (!$imageFound) {
        echo "<div style='color: red; padding: 10px; border: 1px solid red;'>";
        echo "Could not find the image at any of the attempted paths.";
        echo "</div>";
    }
    
    // Provide a direct download link using inline PHP
    echo "<div style='margin-top: 20px;'>";
    echo "<h2>Direct File Access</h2>";
    
    foreach ($possiblePaths as $index => $path) {
        if (file_exists($path)) {
            echo "<a href='#' onclick='downloadFile($index); return false;'>Download file from path " . ($index + 1) . "</a><br>";
        }
    }
    
    echo "</div>";
    
    // JavaScript to download the file
    echo "<script>
    function downloadFile(index) {
        var paths = " . json_encode($possiblePaths) . ";
        var fileName = '" . $attachmentInfo['file_name'] . "';
        
        // Create a form to POST to a temporary download script
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', 'temp_download.php');
        
        var hiddenField = document.createElement('input');
        hiddenField.setAttribute('type', 'hidden');
        hiddenField.setAttribute('name', 'path');
        hiddenField.setAttribute('value', paths[index]);
        form.appendChild(hiddenField);
        
        var hiddenField2 = document.createElement('input');
        hiddenField2.setAttribute('type', 'hidden');
        hiddenField2.setAttribute('name', 'filename');
        hiddenField2.setAttribute('value', fileName);
        form.appendChild(hiddenField2);
        
        var hiddenField3 = document.createElement('input');
        hiddenField3.setAttribute('type', 'hidden');
        hiddenField3.setAttribute('name', 'mimetype');
        hiddenField3.setAttribute('value', '" . $attachmentInfo['file_type'] . "');
        form.appendChild(hiddenField3);
        
        document.body.appendChild(form);
        form.submit();
    }
    </script>";
    
} else {
    die("Attachment not found with ID: " . $id);
}

$db->close();

// Function to find files with a specific size
function findFilesBySize($directory, $targetSize, $indent = '') {
    if (!is_dir($directory)) {
        return;
    }
    
    $files = scandir($directory);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $directory . '/' . $file;
        if (is_dir($path)) {
            echo $indent . "Searching in directory: " . $file . "<br>";
            findFilesBySize($path, $targetSize, $indent . '&nbsp;&nbsp;&nbsp;&nbsp;');
        } else {
            $size = filesize($path);
            if (abs($size - $targetSize) < 100) { // Allow small difference
                echo $indent . "<span style='color: green; font-weight: bold;'>MATCH:</span> " . $path . " (" . $size . " bytes)<br>";
            }
        }
    }
}
?>