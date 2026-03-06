<?php
// Define paths
$reportedPath = "C:/xampp/htdocs/tl-uat/upload_area";
$testFile = $reportedPath . "/test.txt";

echo "<h1>Path Verification</h1>";
echo "Checking if directories and files exist...<br>";

// Check main directory
echo "Upload area directory: {$reportedPath}<br>";
echo "Exists: " . (file_exists($reportedPath) ? "YES" : "NO") . "<br>";
echo "Is readable: " . (is_readable($reportedPath) ? "YES" : "NO") . "<br>";

// Check test file
echo "<br>Test file: {$testFile}<br>";
echo "Exists: " . (file_exists($testFile) ? "YES" : "NO") . "<br>";
if (file_exists($testFile)) {
    echo "Content: <pre>" . htmlspecialchars(file_get_contents($testFile)) . "</pre>";
}

// List all files in the directory
echo "<br>Files in upload_area directory:<br>";
if (is_readable($reportedPath)) {
    $files = scandir($reportedPath);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo "<li>{$file}</li>";
        }
    }
    echo "</ul>";
} else {
    echo "Cannot read directory contents.";
}