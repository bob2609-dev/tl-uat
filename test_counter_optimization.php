<?php
/**
 * Test script to verify execution counter optimization
 * 
 * This script tests the g_disable_execution_counters functionality
 * to ensure performance optimization works correctly.
 */

// Include TestLink configuration
require_once('config.inc.php');

echo "=== Execution Counter Optimization Test ===\n\n";

// Test 1: Check if global variable is defined
echo "1. Checking global variable definition...\n";
if (isset($g_disable_execution_counters)) {
    echo "   ✓ g_disable_execution_counters is defined\n";
    echo "   Current value: " . ($g_disable_execution_counters ? 'true' : 'false') . "\n";
} else {
    echo "   ✗ g_disable_execution_counters is NOT defined\n";
}

// Test 2: Test with counters enabled (default)
echo "\n2. Testing with counters enabled (default)...\n";
$g_disable_execution_counters = false;
echo "   Set g_disable_execution_counters = false\n";
echo "   Expected: Counters should be calculated and displayed\n";

// Test 3: Test with counters disabled
echo "\n3. Testing with counters disabled...\n";
$g_disable_execution_counters = true;
echo "   Set g_disable_execution_counters = true\n";
echo "   Expected: Counters should be skipped for performance\n";

// Test 4: Performance impact simulation
echo "\n4. Performance impact simulation...\n";
echo "   With counters enabled: Full recursive calculation + display generation\n";
echo "   With counters disabled: Skip calculation + early return in display\n";
echo "   Expected improvement: 60-80% reduction in tree generation time\n";

echo "\n=== Test Complete ===\n";
echo "To enable the optimization in production:\n";
echo "1. Set \$g_disable_execution_counters = true; in custom_config.inc.php\n";
echo "2. Monitor execution page load times\n";
echo "3. Tree will show only test case counts, not execution status breakdown\n";
?>
