<?php
/**
 * Clear TestLink Tree Cache
 * Run this script to clear the performance cache
 */

echo "Clearing TestLink tree cache...\n";

// Clear tree cache files
$cache_dir = sys_get_temp_dir();
$files = glob($cache_dir . '/testlink_tree_cache_*');

$cleared = 0;
foreach ($files as $file) {
    if (unlink($file)) {
        $cleared++;
        echo "Cleared: " . basename($file) . "\n";
    }
}

echo "Cleared $cleared cache files.\n";
echo "Done.\n";
?>
