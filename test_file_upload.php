<?php
/**
 * TestLink File Upload Test Script
 * 
 * This script tests file upload functionality and helps diagnose issues
 */

// Include TestLink core files
require_once('config.inc.php');
require_once('lib/functions/common.php');
require_once('lib/functions/files.inc.php');

// Create a simple HTML form for testing
if (!isset($_FILES['uploadedFile'])) {
    // Display the upload form
    echo "<html><head><title>TestLink File Upload Test</title></head><body>";
    echo "<h1>TestLink File Upload Test</h1>";
    echo "<p>This script helps diagnose file upload issues in TestLink.</p>";
    
    // Show current configuration
    echo "<h2>Current Configuration</h2>";
    echo "<pre>";
    echo "allowed_files: " . htmlspecialchars($tlCfg->attachments->allowed_files) . "\n";
    echo "allowed_filenames_regexp: " . htmlspecialchars($tlCfg->attachments->allowed_filenames_regexp) . "\n";
    echo "repository_size_limit: " . $tlCfg->attachments->repository_size_limit . " bytes\n";
    echo "</pre>";
    
    echo "<form action=\"" . $_SERVER['PHP_SELF'] . "\" method=\"post\" enctype=\"multipart/form-data\">";
    echo "<p>Select file to upload: <input type=\"file\" name=\"uploadedFile\"></p>";
    echo "<p><input type=\"submit\" value=\"Upload File\"></p>";
    echo "</form>";
    echo "</body></html>";
} else {
    // Process the uploaded file
    echo "<html><head><title>TestLink File Upload Test - Results</title></head><body>";
    echo "<h1>File Upload Test Results</h1>";
    
    // Display file information
    echo "<h2>File Information</h2>";
    echo "<pre>";
    echo "File name: " . htmlspecialchars($_FILES['uploadedFile']['name']) . "\n";
    echo "File type: " . htmlspecialchars($_FILES['uploadedFile']['type']) . "\n";
    echo "File size: " . $_FILES['uploadedFile']['size'] . " bytes\n";
    echo "Temporary file: " . htmlspecialchars($_FILES['uploadedFile']['tmp_name']) . "\n";
    
    // Get file extension
    $fileName = $_FILES['uploadedFile']['name'];
    $fileExt = getFileExtension($fileName, "");
    echo "File extension: " . $fileExt . "\n";
    
    // Check if extension is allowed
    $allowedFiles = explode(',', $tlCfg->attachments->allowed_files);
    $isAllowed = in_array($fileExt, $allowedFiles);
    echo "Extension in allowed list: " . ($isAllowed ? "Yes" : "No") . "\n";
    
    // Check case-insensitive
    $fileExtLower = strtolower($fileExt);
    $allowedFilesLower = array_map('strtolower', $allowedFiles);
    $isAllowedCaseInsensitive = in_array($fileExtLower, $allowedFilesLower);
    echo "Extension in allowed list (case-insensitive): " . ($isAllowedCaseInsensitive ? "Yes" : "No") . "\n";
    
    // Check filename pattern
    $pattern = trim($tlCfg->attachments->allowed_filenames_regexp);
    $patternMatch = true;
    if ($pattern != '') {
        $patternMatch = preg_match($pattern, $fileName);
    }
    echo "Filename matches pattern: " . ($patternMatch ? "Yes" : "No") . "\n";
    
    // Check file size
    $sizeOK = $_FILES['uploadedFile']['size'] <= $tlCfg->attachments->repository_size_limit;
    echo "File size within limit: " . ($sizeOK ? "Yes" : "No") . "\n";
    echo "</pre>";
    
    // Overall result
    echo "<h2>Result</h2>";
    if ($isAllowed && $patternMatch && $sizeOK) {
        echo "<p style=\"color: green; font-weight: bold;\">This file WOULD be accepted by TestLink.</p>";
    } else {
        echo "<p style=\"color: red; font-weight: bold;\">This file would be REJECTED by TestLink.</p>";
        echo "<p>Reasons:</p><ul>";
        if (!$isAllowed) {
            echo "<li>File extension '" . htmlspecialchars($fileExt) . "' is not in the allowed list.</li>";
        }
        if (!$patternMatch) {
            echo "<li>Filename does not match the required pattern.</li>";
        }
        if (!$sizeOK) {
            echo "<li>File size exceeds the maximum allowed size.</li>";
        }
        echo "</ul>";
    }
    
    // Dump allowed extensions for reference
    echo "<h2>Allowed Extensions</h2>";
    echo "<pre>";
    print_r($allowedFiles);
    echo "</pre>";
    
    echo "<p><a href=\"" . $_SERVER['PHP_SELF'] . "\">Try another file</a></p>";
    echo "</body></html>";
}
