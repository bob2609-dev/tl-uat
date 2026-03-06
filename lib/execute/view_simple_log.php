<?php
/**
 * Simple Query Log Viewer
 * Clean interface for viewing query performance
 */

$log_file = __DIR__ . '/simple_query_log.txt';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>TestLink Query Performance</title></head><body>\n";
echo "<h1>TestLink Database Query Performance</h1>\n";

if (!file_exists($log_file)) {
    echo "<p>No query log found. Please access execNavigator.php first to generate queries.</p>\n";
    echo "<p><a href='execNavigator.php'>Access execNavigator.php</a></p>\n";
    exit;
}

echo "<div style='display: flex; gap: 20px;'>\n";

// Show analysis summary
echo "<div style='flex: 1; background: #e8f5e8; padding: 15px; border-radius: 5px;'>\n";
echo "<h2>Performance Summary</h2>\n";

$analysis = SimpleQueryLogger::analyzeQueries();
echo "<pre style='font-size: 14px; line-height: 1.4;'>{$analysis}</pre>\n";
echo "</div>\n";

// Show detailed log
echo "<div style='flex: 2; background: #f8f9fa; padding: 15px; border-radius: 5px;'>\n";
echo "<h2>Detailed Query Log</h2>\n";
echo "<pre style='background: white; padding: 10px; font-family: monospace; font-size: 12px; max-height: 500px; overflow-y: auto; border: 1px solid #ddd;'>\n";

$content = file_get_contents($log_file);

// Color coding for better visibility
$content = str_replace('*** SLOW QUERY (>0.5s)', '<span style="color: orange; font-weight: bold;">*** SLOW QUERY (>0.5s)</span>', $content);
$content = str_replace('*** VERY SLOW QUERY (>2s)', '<span style="color: red; font-weight: bold;">*** VERY SLOW QUERY (>2s)</span>', $content);
$content = preg_replace('/\[(\d{2}:\d{2}:\d{2}\.\d{3})\]/', '<span style="color: blue;">[$1]</span>', $content);
$content = preg_replace('/Query #(\d+)/', '<span style="color: green; font-weight: bold;">Query #$1</span>', $content);
$content = preg_replace('/- (\d+\.\d{4})s/', '- <span style="color: purple; font-weight: bold;">$1s</span>', $content);

echo $content;
echo "</pre>\n";
echo "</div>\n";

echo "</div>\n";

echo "<hr>\n";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin-top: 20px;'>\n";
echo "<h3>Quick Actions</h3>\n";
echo "<p><a href='execNavigator.php' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Run execNavigator.php Again</a></p>\n";
echo "<p><a href='clear_tree_cache.php' style='background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Clear Cache</a></p>\n";
echo "<p><small>Log file: " . basename($log_file) . "</small></p>\n";
echo "</div>\n";

echo "</body></html>\n";
?>
