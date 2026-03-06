<?php
// Force complete cache clearing
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Opcache reset successfully<br>";
}

if (function_exists('opcache_invalidate')) {
    // Invalidate the specific file
    $file = __DIR__ . '/lib/execute/other_custom_reports.php';
    opcache_invalidate($file, true);
    echo "File invalidated: $file<br>";
}

// Clear all possible caches
clearstatcache();
echo "Stat cache cleared<br>";

// Touch the file to update its modification time
$file = __DIR__ . '/lib/execute/other_custom_reports.php';
touch($file);
echo "File touched to update timestamp<br>";

echo "<br>All caches cleared. Now try the report again.";
?>
