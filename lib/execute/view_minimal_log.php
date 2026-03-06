<?php
/**
 * Minimal Query Log Viewer
 * Shows basic performance timing without complex database interception
 */

$log_file = __DIR__ . '/minimal_query_log.txt';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>TestLink Basic Performance</title></head><body>\n";
echo "<h1>TestLink Basic Performance Analysis</h1>\n";

if (!file_exists($log_file)) {
    echo "<p>No query log found. Please access execNavigator_minimal.php first to generate queries.</p>\n";
    echo "<p><a href='execNavigator_minimal.php'>Access execNavigator_minimal.php</a></p>\n";
    exit;
}

echo "<div style='background: #e8f5e8; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>\n";
echo "<h2>Performance Breakdown</h2>\n";

$content = file_get_contents($log_file);

// Extract timing information
preg_match_all('/\[(\d{2}:\d{2}:\d{2}\.\d{3})\] \[(\d+\.\d+)s\] Query - (\d+\.\d+)s/', $content, $matches);

if (!empty($matches[0])) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>\n";
    echo "<tr style='background: #f0f0f0;'><th>Time</th><th>Elapsed</th><th>Query Time</th><th>Operation</th></tr>\n";
    
    for ($i = 0; $i < count($matches[0]); $i++) {
        $time = $matches[1][$i];
        $elapsed = $matches[2][$i];
        $query_time = $matches[3][$i];
        
        // Extract operation name from next line
        $operation = "Unknown";
        $lines = explode("\n", $content);
        for ($j = 0; $j < count($lines); $j++) {
            if (strpos($lines[$j], $matches[0][$i]) !== false && isset($lines[$j + 1])) {
                if (strpos($lines[$j + 1], 'SQL:') !== false) {
                    $operation = trim(str_replace('SQL:', '', $lines[$j + 1]));
                }
                break;
            }
        }
        
        // Color code slow operations
        $row_style = "";
        if ($query_time > 2.0) {
            $row_style = "style='background: #ffcccc;'";
        } elseif ($query_time > 0.5) {
            $row_style = "style='background: #fff3cd;'";
        }
        
        echo "<tr {$row_style}>";
        echo "<td>{$time}</td>";
        echo "<td>{$elapsed}s</td>";
        echo "<td style='font-weight: bold;'>{$query_time}s</td>";
        echo "<td>{$operation}</td>";
        echo "</tr>\n";
    }
    
    echo "</table>\n";
    
    // Show total execution time
    if (preg_match('/Total Execution Time: (\d+\.\d+)s/', $content, $total_match)) {
        echo "<h3>Total Execution Time: {$total_match[1]}s</h3>\n";
    }
}

echo "</div>\n";

echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>\n";
echo "<h2>Detailed Log</h2>\n";
echo "<pre style='background: white; padding: 10px; font-family: monospace; font-size: 12px; max-height: 400px; overflow-y: auto; border: 1px solid #ddd;'>\n";

// Color coding
$content = str_replace('Total Execution Time', '<span style="color: red; font-weight: bold;">Total Execution Time</span>', $content);
$content = preg_replace('/\[(\d{2}:\d{2}:\d{2}\.\d{3})\]/', '<span style="color: blue;">[$1]</span>', $content);
$content = preg_replace('/\[(\d+\.\d+)s\]/', '<span style="color: green;">[$1s]</span>', $content);
$content = preg_replace('/- (\d+\.\d+)s/', '- <span style="color: purple; font-weight: bold;">$1s</span>', $content);

echo $content;
echo "</pre>\n";
echo "</div>\n";

echo "<hr>\n";
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin-top: 20px;'>\n";
echo "<h3>Quick Actions</h3>\n";
echo "<p><a href='execNavigator_minimal.php' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Run Minimal execNavigator</a></p>\n";
echo "<p><a href='clear_tree_cache.php' style='background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Clear Cache</a></p>\n";
echo "<p><small>Log file: " . basename($log_file) . "</small></p>\n";
echo "</div>\n";

echo "</body></html>\n";
?>
