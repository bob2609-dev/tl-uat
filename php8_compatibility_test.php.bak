<?php
/**
 * TestLink PHP 8 Compatibility Test
 * This script checks if your PHP environment is compatible with this PHP 8-adapted TestLink
 */

// Include PHP 8 compatibility layer
require_once('custom/inc/php8_compatibility.php');

// Basic page styling
echo "<!DOCTYPE html>\n<html>\n<head>\n";
echo "<title>TestLink PHP 8 Compatibility Test</title>\n";
echo "<style>\n";
echo "body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.5; }\n";
echo "h1, h2, h3 { color: #333; }\n";
echo ".pass { color: green; }\n";
echo ".fail { color: red; }\n";
echo ".warning { color: orange; }\n";
echo "pre { background: #f5f5f5; padding: 10px; overflow: auto; }\n";
echo "table { border-collapse: collapse; width: 100%; }\n";
echo "table, th, td { border: 1px solid #ddd; }\n";
echo "th, td { padding: 8px; text-align: left; }\n";
echo "tr:nth-child(even) { background-color: #f2f2f2; }\n";
echo "</style>\n";
echo "</head>\n<body>\n";

echo "<h1>TestLink PHP 8 Compatibility Test</h1>\n";

// Check PHP version
echo "<h2>PHP Environment</h2>\n";
echo "<p>PHP Version: <b>" . phpversion() . "</b></p>";

if (version_compare(phpversion(), '8.0.0', '>=')) {
    echo "<p class='pass'>✓ Running PHP 8.0 or higher</p>";
} elseif (version_compare(phpversion(), '7.0.0', '>=')) {
    echo "<p class='warning'>⚠ Running PHP 7.x - TestLink has been patched for PHP 8 but should still work on PHP 7</p>";
} else {
    echo "<p class='fail'>✗ Running PHP version less than 7.0 - This version of TestLink requires PHP 7 or PHP 8</p>";
}

// Test compatibility functions
echo "<h2>Compatibility Function Tests</h2>\n";

// Test get_magic_quotes_gpc() compatibility
echo "<h3>get_magic_quotes_gpc()</h3>\n";
if (function_exists('get_magic_quotes_gpc')) {
    echo "<p class='pass'>✓ get_magic_quotes_gpc() is available (compatibility function is working)</p>";
    echo "<p>Return value: " . (get_magic_quotes_gpc() ? "true" : "false") . "</p>";
} else {
    echo "<p class='fail'>✗ get_magic_quotes_gpc() is not available</p>";
}

// Test each_compat compatibility
echo "<h3>each_compat()</h3>\n";
if (function_exists('each_compat')) {
    echo "<p class='pass'>✓ each_compat() is available</p>";
    
    // Test the function with an array
    $test_array = array('a' => 1, 'b' => 2, 'c' => 3);
    $result = each_compat($test_array);
    
    if ($result && $result['key'] === 'a' && $result['value'] === 1) {
        echo "<p class='pass'>✓ each_compat() works correctly</p>";
    } else {
        echo "<p class='fail'>✗ each_compat() did not return the expected result</p>";
    }
} else {
    echo "<p class='fail'>✗ each_compat() is not available</p>";
}

// Test safe_strpos compatibility
echo "<h3>safe_strpos()</h3>\n";
if (function_exists('safe_strpos')) {
    echo "<p class='pass'>✓ safe_strpos() is available</p>";
    
    // Test with normal parameters
    $pos = safe_strpos('TestLink', 'Link');
    if ($pos === 4) {
        echo "<p class='pass'>✓ safe_strpos() works correctly with normal parameters</p>";
    } else {
        echo "<p class='fail'>✗ safe_strpos() did not work correctly with normal parameters</p>";
    }
    
    // Test with null parameters (would cause warning/error in PHP 8)
    $pos = safe_strpos(null, 'test');
    if ($pos === false) {
        echo "<p class='pass'>✓ safe_strpos() safely handles null parameters</p>";
    } else {
        echo "<p class='fail'>✗ safe_strpos() did not safely handle null parameters</p>";
    }
} else {
    echo "<p class='fail'>✗ safe_strpos() is not available</p>";
}

// Test PHP 8 specific features
echo "<h2>PHP 8 Features Check</h2>\n";

// Check if named arguments work (PHP 8 feature)
$php8_named_args_supported = false;
try {
    // This will only work in PHP 8+
    eval('$result = strtoupper(string: "test");');
    $php8_named_args_supported = true;
    echo "<p class='pass'>✓ PHP 8 named arguments are supported</p>";
} catch (Throwable $e) {
    echo "<p class='warning'>⚠ PHP 8 named arguments are not supported (expected in PHP 7)</p>";
}

// Check if constructor property promotion works (PHP 8 feature)
$php8_constructor_promotion_supported = false;
try {
    // This will only parse in PHP 8+
    eval('class TestClass { public function __construct(public $name) {} }');
    $php8_constructor_promotion_supported = true;
    echo "<p class='pass'>✓ PHP 8 constructor property promotion is supported</p>";
} catch (Throwable $e) {
    echo "<p class='warning'>⚠ PHP 8 constructor property promotion is not supported (expected in PHP 7)</p>";
}

// Check PHP extensions
echo "<h2>Required PHP Extensions</h2>\n";
$required_extensions = array(
    'mysqli' => 'Required for database connectivity', 
    'json' => 'Required for API functionality',
    'gd' => 'Required for image processing',
    'curl' => 'Required for external integrations',
    'xml' => 'Required for XML processing',
    'mbstring' => 'Required for multi-byte string handling'
);

echo "<table>\n";
echo "<tr><th>Extension</th><th>Status</th><th>Description</th></tr>\n";

foreach ($required_extensions as $ext => $desc) {
    $loaded = extension_loaded($ext);
    echo "<tr>";
    echo "<td>$ext</td>";
    echo "<td class='" . ($loaded ? 'pass' : 'fail') . "'>" . ($loaded ? "✓ Loaded" : "✗ Not loaded") . "</td>";
    echo "<td>$desc</td>";
    echo "</tr>\n";
}
echo "</table>\n";

// Display additional PHP configuration info
echo "<h2>PHP Configuration</h2>\n";

$config_values = array(
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit'),
    'post_max_size' => ini_get('post_max_size'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'display_errors' => ini_get('display_errors'),
    'date.timezone' => ini_get('date.timezone')
);

echo "<table>\n";
echo "<tr><th>Setting</th><th>Value</th></tr>\n";

foreach ($config_values as $name => $value) {
    echo "<tr><td>$name</td><td>$value</td></tr>\n";
}
echo "</table>\n";

// Summary
echo "<h2>Summary</h2>\n";
if (version_compare(phpversion(), '8.0.0', '>=')) {
    echo "<p class='pass'>✓ Your PHP environment is compatible with the PHP 8 adapted TestLink.</p>";
} elseif (version_compare(phpversion(), '7.0.0', '>=')) {
    echo "<p class='warning'>⚠ You are running PHP 7.x. TestLink has been patched for PHP 8 compatibility but should still work on PHP 7.</p>";
} else {
    echo "<p class='fail'>✗ Your PHP version is too old. Please upgrade to PHP 7 or PHP 8.</p>";
}

echo "<p>If you encounter any issues, please check the PHP error logs for more details.</p>";

// Installation instructions
echo "<h2>Installation Notes</h2>\n";
echo "<p>The TestLink application has been modified to run on PHP 8. The following changes were made:</p>";
echo "<ol>";
echo "<li>Added PHP 8 compatibility layer in <code>custom/inc/php8_compatibility.php</code></li>";
echo "<li>Added initialization script in <code>custom/inc/php8_init.php</code></li>";
echo "<li>Modified main entry points to include the compatibility layer</li>";
echo "</ol>";

echo "<p>If you still encounter PHP 8 compatibility issues, please check the PHP error log for specific errors and adjust the compatibility layer as needed.</p>";

echo "</body>\n</html>";
