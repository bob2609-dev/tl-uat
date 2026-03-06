<?php
// Minimal test to isolate custom integration issue
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== Custom Integration Debug Test ===\n";

try {
    // Test 1: Load config
    echo "1. Loading config...\n";
    require_once('config.inc.php');
    require_once('common.php');
    echo "   ✓ Config loaded\n";
    
    // Test 2: Database connection
    echo "2. Testing database connection...\n";
    $db = new database(DB_TYPE);
    doDBConnect($db, database::ONERROREXIT);
    echo "   ✓ Database connected\n";
    
    // Test 3: Check if file exists and is readable
    echo "3. Checking custom integration file...\n";
    $file = 'lib/execute/custom_issue_integration.php';
    if (file_exists($file)) {
        echo "   ✓ File exists\n";
        if (is_readable($file)) {
            echo "   ✓ File is readable\n";
        } else {
            echo "   ✗ File is not readable\n";
            exit;
        }
    } else {
        echo "   ✗ File does not exist\n";
        exit;
    }
    
    // Test 4: Check syntax without including
    echo "4. Checking file syntax...\n";
    $output = [];
    $return_var = 0;
    exec("php -l $file", $output, $return_var);
    if ($return_var === 0) {
        echo "   ✓ Syntax is valid\n";
    } else {
        echo "   ✗ Syntax error:\n";
        echo "   " . implode("\n   ", $output) . "\n";
        exit;
    }
    
    // Test 5: Try to include SAFE version with error suppression
    echo "5. Attempting to include SAFE file...\n";
    $prev_error = error_get_last();
    $result = include_once('lib/execute/custom_issue_integration_safe.php');
    $current_error = error_get_last();
    
    if ($result === false) {
        echo "   ✗ include_once returned false\n";
    } else {
        echo "   ✓ File included successfully\n";
    }
    
    if ($current_error !== $prev_error && $current_error !== null) {
        echo "   ⚠ Error during include:\n";
        echo "   " . $current_error['message'] . " in " . $current_error['file'] . ":" . $current_error['line'] . "\n";
    }
    
    // Test 6: Check if functions exist
    echo "6. Checking if functions exist...\n";
    if (function_exists('getCustomIntegrationForProject')) {
        echo "   ✓ getCustomIntegrationForProject exists\n";
    } else {
        echo "   ✗ getCustomIntegrationForProject does not exist\n";
    }
    
    if (function_exists('getRedmineIssueData')) {
        echo "   ✓ getRedmineIssueData exists\n";
    } else {
        echo "   ✗ getRedmineIssueData does not exist\n";
    }
    
    echo "\n=== Test completed ===\n";
    
} catch (Throwable $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
?>
