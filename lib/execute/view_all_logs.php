<?php
/**
 * Comprehensive Query Log Viewer
 * Shows both PHP-intercepted queries and MySQL general log
 */

$php_log_file = __DIR__ . '/query_log.txt';
$mysql_log_file = __DIR__ . '/mysql_queries.txt';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>TestLink Complete Query Analysis</title></head><body>\n";
echo "<h1>TestLink Database Query Analysis</h1>\n";

// PHP Intercepted Queries
echo "<h2>PHP Intercepted Queries</h2>\n";
if (file_exists($php_log_file)) {
    echo "<pre style='background: #f0f8ff; padding: 10px; font-family: monospace; max-height: 400px; overflow-y: auto;'>\n";
    $content = file_get_contents($php_log_file);
    
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
} else {
    echo "<p>No PHP query log found. Access execNavigator.php first.</p>\n";
}

// MySQL General Log
echo "<h2>MySQL General Log (Raw Queries)</h2>\n";
if (file_exists($mysql_log_file)) {
    echo "<pre style='background: #fff5f5; padding: 10px; font-family: monospace; max-height: 400px; overflow-y: auto;'>\n";
    $content = file_get_contents($mysql_log_file);
    
    // Highlight different query types
    $content = preg_replace('/(SELECT|INSERT|UPDATE|DELETE|CREATE|DROP|ALTER)/i', '<span style="color: purple; font-weight: bold;">$1</span>', $content);
    
    // Highlight timestamps
    $content = preg_replace('/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', '<span style="color: blue;">$1</span>', $content);
    
    echo $content;
    echo "</pre>\n";
} else {
    echo "<p>No MySQL general log found (may require database permissions).</p>\n";
}

// Summary and Analysis
echo "<h2>Performance Analysis</h2>\n";
echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>\n";

if (file_exists($php_log_file)) {
    $content = file_get_contents($php_log_file);
    
    // Extract summary information
    if (preg_match('/Total Queries: (\d+)/', $content, $matches)) {
        echo "<p><strong>Total Queries:</strong> " . $matches[1] . "</p>\n";
    }
    
    if (preg_match('/Total Query Time: (\d+\.\d+)s/', $content, $matches)) {
        echo "<p><strong>Total Query Time:</strong> " . $matches[1] . "s</p>\n";
    }
    
    if (preg_match('/Average Query Time: (\d+\.\d+)s/', $content, $matches)) {
        echo "<p><strong>Average Query Time:</strong> " . $matches[1] . "s</p>\n";
    }
    
    // Count slow queries
    $slow_count = substr_count($content, 'SLOW QUERY DETECTED');
    if ($slow_count > 0) {
        echo "<p style='color: red;'><strong>Slow Queries (>1s):</strong> " . $slow_count . "</p>\n";
    }
    
    // Extract slowest queries
    preg_match_all('/\[\d{2}:\d{2}:\d{2}\.\d{3}\] Query #\d+ - (\d+\.\d{4})s\nSQL: (.+?)\n/', $content, $matches);
    if (!empty($matches[1])) {
        echo "<h3>Top 5 Slowest Queries:</h3>\n";
        echo "<ol>\n";
        array_multisort($matches[1], SORT_DESC, $matches[2]);
        for ($i = 0; $i < min(5, count($matches[1])); $i++) {
            echo "<li><strong>" . $matches[1][$i] . "s</strong> - " . substr($matches[2][$i], 0, 100) . "...</li>\n";
        }
        echo "</ol>\n";
    }
}

echo "</div>\n";

echo "<hr>\n";
echo "<p><small>Log files: " . basename($php_log_file) . " and " . basename($mysql_log_file) . "</small></p>\n";
echo "<p><a href='view_query_log.php'>View PHP Log Only</a> | <a href='clear_tree_cache.php'>Clear Cache</a></p>\n";
echo "</body></html>\n";
?>
