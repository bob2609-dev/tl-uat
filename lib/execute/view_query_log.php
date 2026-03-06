<?php
/**
 * Query Log Viewer
 * Simple script to view the database query log
 */

$log_file = __DIR__ . '/query_log.txt';

if (!file_exists($log_file)) {
    echo "No query log found. Please access execNavigator.php first to generate queries.\n";
    exit;
}

echo "<!DOCTYPE html>\n";
echo "<html><head><title>TestLink Query Log</title></head><body>\n";
echo "<h1>TestLink Database Query Log</h1>\n";
echo "<pre style='background: #f5f5f5; padding: 10px; font-family: monospace;'>\n";

$content = file_get_contents($log_file);

// Highlight slow queries
$content = str_replace('*** SLOW QUERY DETECTED', '<span style="color: red; font-weight: bold;">*** SLOW QUERY DETECTED</span>', $content);

// Format timestamps
$content = preg_replace('/\[(\d{2}:\d{2}:\d{2}\.\d{3})\]/', '<span style="color: blue;">[$1]</span>', $content);

// Format query numbers
$content = preg_replace('/Query #(\d+)/', '<span style="color: green;">Query #$1</span>', $content);

// Format execution times
$content = preg_replace('/- (\d+\.\d{4})s/', '- <span style="color: orange; font-weight: bold;">$1s</span>', $content);

echo $content;

echo "</pre>\n";
echo "<hr>\n";
echo "<small>Log file: " . $log_file . "</small>\n";
echo "</body></html>\n";
?>
