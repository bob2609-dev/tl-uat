<?php
/**
 * Comprehensive Query Log Viewer
 * Analyzes performance bottlenecks from execNavigator and execDashboard
 */

$log_file = __DIR__ . '/comprehensive_query_log.txt';
$performance_log = __DIR__ . '/query_performance_log.txt';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>TestLink Query Performance Analysis</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; }\n";
echo ".header { background: #f0f8f0; padding: 15px; border-radius: 5px; margin-bottom: 20px; }\n";
echo ".stats { display: flex; gap: 20px; margin-bottom: 20px; }\n";
echo ".stat-box { flex: 1; background: #e8f5e8; padding: 15px; border-radius: 5px; text-align: center; }\n";
echo ".stat-number { font-size: 24px; font-weight: bold; color: #2e7d32; }\n";
echo ".stat-label { font-size: 14px; color: #666; margin-top: 5px; }\n";
echo ".log-container { background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; padding: 15px; }\n";
echo ".log-entry { padding: 8px; border-bottom: 1px solid #eee; font-family: monospace; font-size: 12px; }\n";
echo ".query { color: #d63384; font-weight: bold; }\n";
echo ".method { color: #28a745; font-weight: bold; }\n";
echo ".time { color: #dc3545; }\n";
echo ".slow { background: #ffebee; }\n";
echo ".medium { background: #fff3cd; }\n";
echo ".fast { background: #d4edda; }\n";
echo ".controls { margin: 20px 0; }\n";
echo "button { background: #007bff; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-right: 10px; }\n";
echo "button:hover { background: #0056b3; }\n";
echo "</style></head><body>\n";

echo "<div class='header'>\n";
echo "<h1>TestLink Query Performance Analysis</h1>\n";
echo "<p>Analyzing database queries from execNavigator.php and execDashboard.php</p>\n";
echo "</div>\n";

// Display statistics
if (file_exists($performance_log)) {
    $lines = file($performance_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $total_queries = count($lines);
    
    $total_time = 0;
    $slow_queries = 0;
    $medium_queries = 0;
    $fast_queries = 0;
    
    foreach ($lines as $line) {
        if (preg_match('/\[(\d+\.\d+)ms\]/', $line, $matches)) {
            $time = floatval($matches[1]);
            $total_time += $time;
            
            if ($time > 1000) $slow_queries++;
            elseif ($time > 100) $medium_queries++;
            else $fast_queries++;
        }
    }
    
    $avg_time = $total_queries > 0 ? $total_time / $total_queries : 0;
    
    echo "<div class='stats'>\n";
    echo "<div class='stat-box'>\n";
    echo "<div class='stat-number'>" . number_format($total_queries) . "</div>\n";
    echo "<div class='stat-label'>Total Queries</div>\n";
    echo "</div>\n";
    
    echo "<div class='stat-box'>\n";
    echo "<div class='stat-number'>" . number_format($avg_time, 2) . "ms</div>\n";
    echo "<div class='stat-label'>Average Time</div>\n";
    echo "</div>\n";
    
    echo "<div class='stat-box'>\n";
    echo "<div class='stat-number'>" . number_format($slow_queries) . "</div>\n";
    echo "<div class='stat-label'>Slow Queries (>1s)</div>\n";
    echo "</div>\n";
    
    echo "<div class='stat-box'>\n";
    echo "<div class='stat-number'>" . number_format($medium_queries) . "</div>\n";
    echo "<div class='stat-label'>Medium Queries (100ms-1s)</div>\n";
    echo "</div>\n";
    
    echo "<div class='stat-box'>\n";
    echo "<div class='stat-number'>" . number_format($fast_queries) . "</div>\n";
    echo "<div class='stat-label'>Fast Queries (<100ms)</div>\n";
    echo "</div>\n";
    echo "</div>\n";
}

// Display recent log entries
echo "<div class='log-container'>\n";
echo "<h2>Recent Query Log (Last 50 entries)</h2>\n";

if (file_exists($log_file)) {
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $recent_lines = array_slice($lines, -50);
    
    foreach ($recent_lines as $line) {
        $class = '';
        $line = htmlspecialchars($line);
        
        if (strpos($line, '[QUERY]') !== false) {
            $class = 'query';
        } elseif (strpos($line, '[METHOD]') !== false) {
            $class = 'method';
        }
        
        // Color code by execution time
        if (preg_match('/\[(\d+\.\d+)ms\]/', $line, $matches)) {
            $time = floatval($matches[1]);
            if ($time > 1000) $class .= ' slow';
            elseif ($time > 100) $class .= ' medium';
            else $class .= ' fast';
        }
        
        echo "<div class='log-entry {$class}'>{$line}</div>\n";
    }
} else {
    echo "<p>No log file found. Run execNavigator_with_logging.php or execDashboard_with_logging.php to generate logs.</p>\n";
}

echo "</div>\n";

echo "<div class='controls'>\n";
echo "<button onclick='window.location.reload()'>Refresh</button>\n";
echo "<button onclick='if(confirm(\"Clear all logs?\")) { window.location.href=\"?clear=1\"; }'>Clear Logs</button>\n";
echo "<button onclick='window.open(\"view_comprehensive_logs.txt\", \"_blank\")'>Download Raw Log</button>\n";
echo "</div>\n";

// Handle log clearing
if (isset($_GET['clear']) && $_GET['clear'] == 1) {
    file_put_contents($log_file, "# Comprehensive Query Log - Cleared at " . date('Y-m-d H:i:s') . "\n\n");
    file_put_contents($performance_log, "# Performance Log - Cleared at " . date('Y-m-d H:i:s') . "\n\n");
    header("Location: view_comprehensive_logs.php");
    exit;
}

echo "</body></html>\n";
?>
