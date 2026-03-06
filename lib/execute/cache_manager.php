<?php
/**
 * Tree Cache Manager
 * View and manage the tree cache
 */

$cache_dir = __DIR__ . '/tree_cache';
$performance_log = __DIR__ . '/cache_performance.log';

echo "<!DOCTYPE html>\n";
echo "<html><head><title>TestLink Tree Cache Manager</title></head><body>\n";
echo "<h1>TestLink Tree Cache Manager</h1>\n";

// Handle cache clearing
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '/*.json');
        $cleared = 0;
        foreach ($files as $file) {
            if (unlink($file)) {
                $cleared++;
            }
        }
        echo "<p style='color: green;'><strong>Cache cleared: {$cleared} files removed</strong></p>\n";
    }
    
    // Also clear performance log
    if (file_exists($performance_log)) {
        unlink($performance_log);
        echo "<p style='color: green;'><strong>Performance log cleared</strong></p>\n";
    }
    
    echo "<p><a href='cache_manager.php'>Back to Cache Manager</a></p>\n";
    exit;
}

// Show cache statistics
echo "<div style='display: flex; gap: 20px;'>\n";

// Cache files info
echo "<div style='flex: 1; background: #e8f5e8; padding: 15px; border-radius: 5px;'>\n";
echo "<h2>Cache Statistics</h2>\n";

if (is_dir($cache_dir)) {
    $files = glob($cache_dir . '/*.json');
    $total_size = 0;
    $cache_count = count($files);
    
    foreach ($files as $file) {
        $total_size += filesize($file);
    }
    
    echo "<p><strong>Cache Files:</strong> {$cache_count}</p>\n";
    echo "<p><strong>Total Size:</strong> " . number_format($total_size / 1024, 2) . " KB</p>\n";
    echo "<p><strong>Cache Directory:</strong> {$cache_dir}</p>\n";
    
    if ($cache_count > 0) {
        echo "<h3>Cache Files:</h3>\n";
        echo "<table border='1' style='border-collapse: collapse; width: 100%; font-size: 12px;'>\n";
        echo "<tr><th>File</th><th>Size</th><th>Age</th><th>Expired</th></tr>\n";
        
        foreach ($files as $file) {
            $filename = basename($file);
            $size = filesize($file);
            $age = time() - filemtime($file);
            $expired = $age > 300 ? 'Yes' : 'No';
            
            $row_style = $expired ? "style='background: #ffcccc;'" : "";
            
            echo "<tr {$row_style}>";
            echo "<td>" . substr($filename, 0, 30) . "...</td>";
            echo "<td>" . number_format($size / 1024, 2) . " KB</td>";
            echo "<td>{$age}s</td>";
            echo "<td>{$expired}</td>";
            echo "</tr>\n";
        }
        
        echo "</table>\n";
    }
} else {
    echo "<p>No cache directory found.</p>\n";
}

echo "</div>\n";

// Performance log info
echo "<div style='flex: 1; background: #f8f9fa; padding: 15px; border-radius: 5px;'>\n";
echo "<h2>Recent Performance</h2>\n";

if (file_exists($performance_log)) {
    $lines = file($performance_log);
    $recent_lines = array_slice($lines, -20); // Last 20 entries
    
    echo "<pre style='background: white; padding: 10px; font-family: monospace; font-size: 11px; max-height: 300px; overflow-y: auto; border: 1px solid #ddd;'>\n";
    
    foreach ($recent_lines as $line) {
        $line = trim($line);
        
        // Color code performance info
        if (strpos($line, 'Cache HIT') !== false) {
            $line = "<span style='color: green; font-weight: bold;'>{$line}</span>";
        } elseif (strpos($line, 'Cache MISS') !== false) {
            $line = "<span style='color: orange; font-weight: bold;'>{$line}</span>";
        } elseif (strpos($line, 'Total execution time') !== false) {
            if (preg_match('/(\d+\.\d+)s$/', $line, $matches)) {
                $time = floatval($matches[1]);
                if ($time < 1.0) {
                    $line = "<span style='color: green;'>{$line}</span>";
                } elseif ($time < 3.0) {
                    $line = "<span style='color: orange;'>{$line}</span>";
                } else {
                    $line = "<span style='color: red;'>{$line}</span>";
                }
            }
        }
        
        echo $line . "\n";
    }
    
    echo "</pre>\n";
} else {
    echo "<p>No performance log found. Access execNavigator_cached.php to generate performance data.</p>\n";
}

echo "</div>\n";
echo "</div>\n";

// Actions
echo "<div style='background: #f0f8ff; padding: 15px; border-radius: 5px; margin-top: 20px;'>\n";
echo "<h3>Actions</h3>\n";
echo "<p><a href='execNavigator_cached.php' style='background: #007bff; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Test Cached execNavigator</a></p>\n";
echo "<p><a href='cache_manager.php?action=clear' style='background: #dc3545; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;' onclick='return confirm(\"Clear all cache files?\")'>Clear Cache</a></p>\n";
echo "<p><a href='performance_test.php' style='background: #6c757d; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Run Performance Test</a></p>\n";
echo "</div>\n";

echo "</body></html>\n";
?>
