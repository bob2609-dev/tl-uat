<?php
// Simple attachment proxy that bypasses the logger issues
error_reporting(0); // Disable error reporting for this script

// Get the attachment ID from the URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("No attachment ID specified");
}

// Connect to database
$conn = new mysqli('localhost', 'tl_uat', 'tl_uat269', 'tl_uat');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get attachment info
$sql = "SELECT * FROM attachments WHERE id = $id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $attachment = $result->fetch_assoc();
    
    // Repository path
    $repoPath = 'C:/xampp/htdocs/tl-uat/upload_area';
    
    // Build file path
    $filePath = $repoPath . '/' . $attachment['file_name'];
    
    // Set appropriate headers
    header('Content-Type: ' . $attachment['file_type']);
    header('Content-Length: ' . $attachment['file_size']);
    header('Content-Disposition: inline; filename="' . $attachment['file_name'] . '"');
    
    // Output the file if it exists
    if (file_exists($filePath)) {
        readfile($filePath);
        exit;
    } else {
        die("File not found: " . $filePath);
    }
} else {
    die("Attachment not found");
}

$conn->close();
?>