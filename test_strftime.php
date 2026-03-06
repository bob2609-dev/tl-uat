<?php
// Include PHP 8 compatibility layer
require_once('custom/inc/php8_init.php');

echo '<h1>TestLink strftime() Test</h1>';

echo '<p>PHP Version: ' . phpversion() . '</p>';

// Test if safe_strftime is defined
echo '<p>safe_strftime() defined: ' . (function_exists('safe_strftime') ? 'Yes' : 'No') . '</p>';

// Test safe_strftime
echo '<p>Current date with safe_strftime(): ' . safe_strftime('%Y-%m-%d') . '</p>';

// Test original strftime (which is still available but deprecated)
echo '<p>Current date with original strftime(): ' . @strftime('%Y-%m-%d') . '</p>';

echo '<p>Done testing.</p>';
