<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * 
 * @filesource excel_export_handler.php
 * @author TestLink team
 * 
 * Handler for Excel export functionality
 */
require_once('../../config.inc.php');
require_once('common.php');

// Security: Validate that the user is logged in and has appropriate permissions
testlinkInitPage($db);
$user = $_SESSION['currentUser'];
if (!$user->hasRight($db, 'mgt_view_tc')) {
    die('Access denied: Insufficient permissions');
}

// Sanitize and collect parameters from the request
$params = array();
$valid_params = array('project', 'testplan', 'build', 'status', 'startdate', 'enddate');

foreach ($valid_params as $param) {
    if (isset($_GET[$param])) {
        // Simple validation and sanitization
        $params[$param] = htmlspecialchars($_GET[$param], ENT_QUOTES, 'UTF-8');
    }
}

// Set path to executable and config file
$executablePath = dirname(__FILE__) . '/excel_export/test_execution_export.exe';
$configPath = dirname(__FILE__) . '/excel_export/db_config.json';

// Validate that files exist
if (!file_exists($executablePath)) {
    die('Error: Export executable not found');
}

if (!file_exists($configPath)) {
    die('Error: Database configuration file not found');
}

// Build command with parameters
$command = escapeshellcmd($executablePath) . ' --config ' . escapeshellarg($configPath);

// Add filters from parameters if they exist
foreach ($params as $key => $value) {
    if (!empty($value) && $value !== '0') {
        $command .= ' --' . escapeshellarg($key) . ' ' . escapeshellarg($value);
    }
}

// Enable debug output to a log file
$logFile = dirname(__FILE__) . '/excel_export/ui_export_debug.log';
file_put_contents($logFile, "Command: $command\n", FILE_APPEND);

// Execute the command
exec($command . ' 2>&1', $output, $return_var);

// Log all output for debugging
file_put_contents($logFile, "Return code: $return_var\nOutput:\n" . implode("\n", $output) . "\n\n", FILE_APPEND);

// Find the generated file (it should be the last line of output)
$excelFile = null;
foreach ($output as $line) {
    file_put_contents($logFile, "Checking line: $line\n", FILE_APPEND);
    if (strpos($line, 'Excel file generated successfully:') !== false) {
        $excelFile = trim(str_replace('Excel file generated successfully:', '', $line));
        file_put_contents($logFile, "Found file: $excelFile\n", FILE_APPEND);
        break;
    }
}

// If we didn't find the expected message, look for alternate formats
if (!$excelFile) {
    foreach ($output as $line) {
        // Check for any line that might indicate where the file was created
        if (strpos($line, 'testlink_export_') !== false && (strpos($line, '.xlsx') !== false)) {
            // Extract the file path using a simpler approach
            $parts = explode('Excel file generated successfully:', $line);
            if (count($parts) > 1) {
                $excelFile = trim($parts[1]);
                file_put_contents($logFile, "Found file path using alternate method: $excelFile\n", FILE_APPEND);
                break;
            } else {
                // More aggressive pattern matching for mixed output
                if (preg_match('/(output\\\\testlink_export_[0-9_]+\.xlsx)/', $line, $matches)) {
                    $excelFile = $matches[1];
                    file_put_contents($logFile, "Extracted filename using regex: $excelFile\n", FILE_APPEND);
                    break;
                } else if (preg_match('/Generating Excel file: (output\\\\testlink_export_[0-9_]+\.xlsx)/', $line, $matches)) {
                    $excelFile = $matches[1];
                    file_put_contents($logFile, "Extracted filename from generation line: $excelFile\n", FILE_APPEND);
                    break;
                } else if (preg_match('/INFO - (output\\\\testlink_export_[0-9_]+\.xlsx)/', $line, $matches)) {
                    $excelFile = $matches[1];
                    file_put_contents($logFile, "Extracted filename from INFO log: $excelFile\n", FILE_APPEND);
                    break;
                }
            }
        }
    }
}

// Convert relative path to absolute path if needed
if ($excelFile) {
    // Replace backslashes with forward slashes for PHP
    $excelFile = str_replace('\\', '/', $excelFile);
    
    // Check if this is a relative path
    if (!preg_match('~^(?:/|\\\\|[a-z]:)~i', $excelFile)) {
        // It's a relative path - convert to absolute
        $scriptDir = dirname(__FILE__);
        $excelFile = $scriptDir . '/excel_export/' . $excelFile;
        file_put_contents($logFile, "Converted to absolute path: $excelFile\n", FILE_APPEND);
    }
}

// Check if file was generated
if ($return_var !== 0 || !$excelFile || !file_exists($excelFile)) {
    // Enhanced error reporting
    $errorDetails = array(
        'success' => false,
        'message' => 'Failed to generate Excel file. Error code: ' . $return_var,
        'command' => $command,
        'output' => $output
    );
    file_put_contents($logFile, "Error: " . json_encode($errorDetails) . "\n", FILE_APPEND);
    
    header('Content-Type: application/json');
    echo json_encode($errorDetails);
    exit;
}

// Return file for download
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . basename($excelFile) . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($excelFile));
readfile($excelFile);

// Optional: Clean up - remove the file after sending
// Uncomment if you want to delete the file after download
// unlink($excelFile);
