<?php
// Minimal integration test without external dependencies
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "=== Minimal Integration Test ===\n";

try {
    // Test basic function definition
    echo "1. Testing basic function definition...\n";
    
    function getCustomIntegrationForProject($db, $tproject_id) {
        error_log("[MINIMAL_TEST] Called with tproject_id: $tproject_id");
        return null; // Simple return for testing
    }
    
    echo "   ✓ Function defined successfully\n";
    
    // Test function call
    echo "2. Testing function call...\n";
    $result = getCustomIntegrationForProject(null, 1);
    echo "   ✓ Function call successful, result: " . ($result ? 'found' : 'null') . "\n";
    
    echo "=== Test completed successfully ===\n";
    
} catch (Throwable $e) {
    echo "FATAL ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
?>
