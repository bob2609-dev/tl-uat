<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * 
 * @filesource excel_export_handler.php
 * @author TestLink team
 * 
 * Handler for Excel export functionality - FIXED VERSION
 */

// Start output buffering to prevent any accidental output
ob_start();

require_once('../../config.inc.php');
require_once('common.php');

// Security: Validate that the user is logged in and has appropriate permissions
testlinkInitPage($db);
$user = $_SESSION['currentUser'];
if (!$user->hasRight($db, 'mgt_view_tc')) {
    ob_end_clean();
    http_response_code(403);
    die('Access denied: Insufficient permissions');
}

// Sanitize and collect parameters from the request
$params = array();
$valid_params = array('project', 'testplan', 'build', 'status', 'start_date', 'end_date');

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
    ob_end_clean();
    http_response_code(500);
    die('Error: Export executable not found');
}

if (!file_exists($configPath)) {
    ob_end_clean();
    http_response_code(500);
    die('Error: Database configuration file not found');
}

// Build command with parameters
$command = escapeshellcmd($executablePath) . ' --config ' . escapeshellarg($configPath);

// Add filters from parameters if they exist
foreach ($params as $key => $value) {
    if (!empty($value) && $value !== '0') {
        // Fix parameter names to match what the executable expects
        $paramName = str_replace('_', '-', $key); // Convert underscores to hyphens
        $command .= ' --' . $paramName . ' ' . escapeshellarg($value);
    }
}

// Enable debug output to a log file
$logFile = dirname(__FILE__) . '/excel_export/ui_export_debug.log';
$debugLog = "Command: $command\n";

// Execute the command
exec($command . ' 2>&1', $output, $return_var);

// Log all output for debugging
$debugLog .= "Return code: $return_var\nOutput:\n" . implode("\n", $output) . "\n\n";

// Find the generated file
$excelFile = null;
$outputDir = dirname(__FILE__) . '/excel_export/';

// Method 1: Look for various patterns that might indicate the Excel file path
foreach ($output as $line) {
    // Pattern 1: Look for "Excel file generated successfully:" message
    if (strpos($line, 'Excel file generated successfully:') !== false) {
        // Extract the path after the colon
        $relativePath = trim(str_replace('Excel file generated successfully:', '', $line));
        $relativePath = ltrim($relativePath, ' ./'); // Remove leading dots/slashes
        
        // Build the absolute path
        $possibleFile = $outputDir . str_replace('\\', '/', $relativePath);
        if (file_exists($possibleFile)) {
            $excelFile = $possibleFile;
            break;
        }
    }
    
    // Pattern 2: Look for "INFO - Generating Excel file:" message
    if (strpos($line, 'INFO - Generating Excel file:') !== false) {
        preg_match('/INFO - Generating Excel file:\s*(.+)$/i', $line, $matches);
        if (isset($matches[1])) {
            $relativePath = trim($matches[1]);
            $relativePath = ltrim($relativePath, ' ./'); // Remove leading dots/slashes
            
            // Build the absolute path
            $possibleFile = $outputDir . str_replace('\\', '/', $relativePath);
            if (file_exists($possibleFile)) {
                $excelFile = $possibleFile;
                break;
            }
        }
    }
    
    // Pattern 3: Look for any mentions of .xlsx files in the output
    if (preg_match('/(?:output|generating|file|path)[\s:]*([^\s]+?\.xlsx)/i', $line, $matches)) {
        $relativePath = trim($matches[1]);
        $relativePath = ltrim($relativePath, ' ./'); // Remove leading dots/slashes
        
        // Try both with and without output/ directory
        $possibleFile1 = $outputDir . str_replace('\\', '/', $relativePath);
        $possibleFile2 = $outputDir . 'output/' . str_replace('\\', '/', basename($relativePath));
        
        if (file_exists($possibleFile1)) {
            $excelFile = $possibleFile1;
            break;
        } else if (file_exists($possibleFile2)) {
            $excelFile = $possibleFile2;
            break;
        }
    }
    
    // Pattern 4: Direct path match for any .xlsx files
    if (preg_match('/((?:output|[a-z]:)?[\\\\|\/]?[^\s]+?\.xlsx)/i', $line, $matches)) {
        $path = trim($matches[1]);
        
        // Handle relative paths
        if (strpos($path, ':') === false && strpos($path, '/') !== 0 && strpos($path, '\\') !== 0) {
            // It's a relative path, try different combinations
            $possiblePaths = [
                $outputDir . str_replace('\\', '/', $path),
                $outputDir . 'output/' . str_replace('\\', '/', basename($path)),
                dirname($outputDir) . '/' . str_replace('\\', '/', $path)
            ];
            
            foreach ($possiblePaths as $possibleFile) {
                if (file_exists($possibleFile)) {
                    $excelFile = $possibleFile;
                    break 2; // Break out of both loops
                }
            }
        } else {
            // It's an absolute path, normalize it
            $possibleFile = str_replace('\\', '/', $path);
            if (file_exists($possibleFile)) {
                $excelFile = $possibleFile;
                break;
            }
        }
    }
}

// Method 2: Fallback - find the most recent file in various output directories
if (!$excelFile || !file_exists($excelFile)) {
    // Try multiple potential output directories
    $potentialDirs = [
        $outputDir . 'output/',
        $outputDir,
        dirname($outputDir) . '/output/',
        dirname(__FILE__) . '/output/'
    ];
    
    foreach ($potentialDirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . 'testlink_export_*.xlsx');
            
            if (!empty($files)) {
                // Sort by modification time, newest first
                usort($files, function($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                
                // Use the most recent file if created within last 5 minutes
                $newestFile = $files[0];
                $fileAge = time() - filemtime($newestFile);
                
                if ($fileAge < 300) { // Within last 5 minutes
                    $excelFile = $newestFile;
                    break; // Found a valid file, exit the loop
                }
            }
        }
    }
}

// Add fallback for absolute path detection
if (!$excelFile || !file_exists($excelFile)) {
    // Look for specific path pattern in the output - scan entire output again
    foreach ($output as $line) {
        // Look for any path with xlsx extension
        if (preg_match('/output[\\\\|\/]testlink_export_[0-9_]+\.xlsx/', $line, $matches)) {
            $fileName = $matches[0];
            
            // Try multiple base directories
            $potentialBases = [
                $outputDir,
                dirname($outputDir),
                dirname(__FILE__),
                getcwd()
            ];
            
            foreach ($potentialBases as $baseDir) {
                $normalizedPath = str_replace('\\', '/', $baseDir . '/' . $fileName);
                if (file_exists($normalizedPath)) {
                    $excelFile = $normalizedPath;
                    break 2; // Break out of both loops
                }
            }
        }
    }
}

// Add more debug info about file detection
$debugLog .= "\nFile detection:\n";
$debugLog .= "Excel file path: " . ($excelFile ? $excelFile : 'Not found') . "\n";
$debugLog .= "File exists: " . ($excelFile && file_exists($excelFile) ? 'Yes' : 'No') . "\n";

// Try directory listings for debugging
$debugLog .= "\nDirectory contents:\n";
$debugDirs = [
    $outputDir,
    $outputDir . 'output/',
    dirname(__FILE__) . '/output/'
];

foreach ($debugDirs as $dir) {
    $debugLog .= "Directory $dir: ";
    if (is_dir($dir)) {
        $files = scandir($dir);
        $debugLog .= implode(", ", $files) . "\n";
    } else {
        $debugLog .= "(not a directory)\n";
    }
}

// Write debug log
file_put_contents($logFile, $debugLog, FILE_APPEND | LOCK_EX);

// Check if file was generated
if ($return_var !== 0 || !$excelFile || !file_exists($excelFile)) {
    $errorDetails = array(
        'success' => false,
        'message' => 'Failed to generate or find Excel file. Error code: ' . $return_var,
        'command' => $command,
        'output' => $output,
        'expected_file' => $excelFile,
        'file_exists' => $excelFile ? file_exists($excelFile) : false
    );
    
    file_put_contents($logFile, "Error: " . json_encode($errorDetails) . "\n", FILE_APPEND | LOCK_EX);
    
    ob_end_clean();
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode($errorDetails);
    exit;
}

// Verify file is readable
if (!is_readable($excelFile)) {
    ob_end_clean();
    http_response_code(500);
    $error = array('success' => false, 'message' => 'Generated file is not readable');
    header('Content-Type: application/json');
    echo json_encode($error);
    exit;
}

// Clean any output buffer before sending file
ob_end_clean();

// Set headers for file download
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . basename($excelFile) . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($excelFile));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');

// Read and output file
readfile($excelFile);

// Log successful download
file_put_contents($logFile, "File downloaded successfully: $excelFile\n", FILE_APPEND | LOCK_EX);
exit;