<?php
/**
 * Simple image proxy for TestLink attachments
 */
// Include config to get database connection info
require_once('config.inc.php');

// Disable error reporting for security
error_reporting(0);
ini_set('display_errors', 0);

// Get attachment ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    die("No attachment ID specified");
}

// Connect to database
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Database connection failed: " . $db->connect_error);
}

// Get attachment info
$sql = "SELECT * FROM attachments WHERE id = $id";
$result = $db->query($sql);

if ($result && $result->num_rows > 0) {
    $attachment = $result->fetch_assoc();
    
    // Construct paths
    $dbPath = $attachment['file_path'];
    $dbPath = str_replace('\\', '/', $dbPath);
    $filePath = $g_repositoryPath . '/' . $dbPath;
    
    // Try alternative path without 'executions' prefix
    $parts = explode('/', $dbPath);
    if ($parts[0] == 'executions') {
        array_shift($parts); // Remove first part (executions)
    }
    $simplifiedPath = implode('/', $parts);
    $alternatePath = $g_repositoryPath . '/' . $simplifiedPath;
    
    // Look for files with similar size
    $found = false;
    $searchDirs = [
        $g_repositoryPath,
        $g_repositoryPath . '/executions',
        $g_repositoryPath . '/executions/' . $attachment['fk_id']
    ];
    
    foreach ($searchDirs as $dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;
                
                $path = $dir . '/' . $file;
                if (is_dir($path)) {
                    $subdir = $path;
                    $subfiles = scandir($subdir);
                    foreach ($subfiles as $subfile) {
                        if ($subfile === '.' || $subfile === '..') continue;
                        
                        $subpath = $subdir . '/' . $subfile;
                        if (is_file($subpath) && abs(filesize($subpath) - $attachment['file_size']) < 100) {
                            $found = true;
                            $foundPath = $subpath;
                            break 3;
                        }
                    }
                } elseif (is_file($path) && abs(filesize($path) - $attachment['file_size']) < 100) {
                    $found = true;
                    $foundPath = $path;
                    break 2;
                }
            }
        }
    }
    
    // Try various paths
    $possiblePaths = [
        $filePath,
        $alternatePath
    ];
    
    // Add found path if any
    if ($found) {
        $possiblePaths[] = $foundPath;
    }
    
    // Try to find and display the image
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            // Determine correct MIME type based on file extension
            $ext = strtolower(pathinfo($attachment['file_name'], PATHINFO_EXTENSION));
            $mime = $attachment['file_type']; // Default to stored type
            
            // Override with correct type for common images
            switch($ext) {
                case 'jpg':
                case 'jpeg':
                case 'jpe':
                    $mime = 'image/jpeg';
                    break;
                case 'png':
                    $mime = 'image/png';
                    break;
                case 'gif':
                    $mime = 'image/gif';
                    break;
                case 'bmp':
                    $mime = 'image/bmp';
                    break;
                case 'svg':
                    $mime = 'image/svg+xml';
                    break;
            }
            
            // Create a base64 data URL to display the image
            $imageData = file_get_contents($path);
            $base64 = base64_encode($imageData);
            
            // Set content type to HTML with proper doctype and viewport
            header('Content-Type: text/html');
            echo "<!DOCTYPE html>\n";
            echo "<html>\n";
            echo "<head>\n";
            echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n";
            echo "<style>\n";
            echo "body { margin: 0; padding: 0; overflow: hidden; }\n";
            echo "img { max-width: 100%; height: auto; display: block; margin: 0 auto; }\n";
            echo "</style>\n";
            echo "</head>\n";
            echo "<body>\n";
            
            // Output the image with base64 encoding
            echo "<img src='data:$mime;base64,$base64'>\n";
            
            echo "</body>\n";
            echo "</html>\n";
            exit;
        }
    }
    
    // No image found
    header('Content-Type: text/html');
    echo "<html><body style='color:red;font-family:Arial;'>Image not found</body></html>";
} else {
    header('Content-Type: text/html');
    echo "<html><body style='color:red;font-family:Arial;'>Attachment not found with ID: $id</body></html>";
}

$db->close();
?>
