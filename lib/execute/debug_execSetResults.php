<?php
// Debug script to check execSetResults.php error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DEBUG execSetResults.php ===\n";

// Check if required files exist
$required_files = [
    '../../config.inc.php',
    'common.php',
    'exec.inc.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists\n";
    } else {
        echo "✗ $file missing\n";
    }
}

// Check database connection
try {
    require_once('../../config.inc.php');
    echo "✓ config.inc.php loaded\n";
    
    // Test database connection
    $db = new database();
    if ($db->isConnected()) {
        echo "✓ Database connected\n";
    } else {
        echo "✗ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Check session
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✓ Session active\n";
    echo "Session ID: " . session_id() . "\n";
} else {
    echo "✗ Session not active\n";
}

// Check required session variables
$required_session_vars = ['testplanID', 'testprojectID', 'currentUser'];
foreach ($required_session_vars as $var) {
    if (isset($_SESSION[$var])) {
        echo "✓ Session variable $var is set\n";
    } else {
        echo "✗ Session variable $var is missing\n";
    }
}

echo "\n=== REQUEST PARAMETERS ===\n";
echo "GET: " . json_encode($_GET) . "\n";
echo "POST: " . json_encode($_POST) . "\n";
echo "REQUEST: " . json_encode($_REQUEST) . "\n";

echo "\n=== DONE ===\n";
?>
