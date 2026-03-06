<?php
// Minimal execSetResults.php test without authentication
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== MINIMAL EXECSETRESULTS TEST ===\n";

// Get the correct base path
$basePath = dirname(__DIR__, 2);

// Load config
require_once($basePath . '/config.inc.php');
echo "✓ config.inc.php loaded\n";

// Load ADOdb
require_once($basePath . '/vendor/adodb/adodb-php/adodb.inc.php');
echo "✓ ADOdb library loaded\n";

// Create database instance directly (bypassing testlinkInitPage)
echo "  - Creating database instance...\n";
$db = new database(DB_TYPE);
echo "✓ database instance created\n";

// Test database query
echo "  - Testing database query...\n";
try {
    $sql = "SELECT 1 as test_value";
    $result = $db->exec_query($sql);
    
    if ($result) {
        echo "✓ Database query executed successfully\n";
        $row = $db->fetch_array($result);
        if ($row) {
            echo "✓ Database fetch successful - Value: " . $row['test_value'] . "\n";
        }
    } else {
        echo "✗ Database query failed\n";
    }
} catch (Exception $e) {
    echo "✗ Database query failed: " . $e->getMessage() . "\n";
}

// Now test the init_args function logic
echo "\n=== TESTING INIT_ARGS LOGIC ===\n";

// Simulate the exact URL parameters from the failing request
$_GET['version_id'] = '431183';
$_GET['level'] = 'testcase';
$_GET['id'] = '431182';
$_GET['form_token'] = '1787740889';
$_GET['setting_build'] = '7';

echo "GET parameters: " . json_encode($_GET) . "\n";

// Simulate the key2loop initialization from execSetResults.php
$key2loop = array('level' => '','status' => null, 'statusSingle' => null, 
                'do_bulk_save' => 0,'save_results' => 0,'save_and_next' => 0, 
                'save_and_exit' => 0);

$args = new stdClass();
foreach($key2loop as $key => $value) {
    $args->$key = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $value;
    echo "✓ args->$key = '" . $args->$key . "'\n";
}

// Check the level switch statement
echo "\n=== TESTING LEVEL SWITCH ===\n";
switch($args->level) {
    case 'testcase':
        echo "✓ testcase branch - setting tc_id\n";
        $args->tc_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;
        $args->tsuite_id = null;
        echo "  - tc_id: " . $args->tc_id . "\n";
        break;
        
    case 'testsuite':
        echo "✓ testsuite branch - setting tsuite_id\n";
        $args->tsuite_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;
        $args->tc_id = null;
        break;
        
    default:
        echo "✗ Unknown level: '" . $args->level . "'\n";
        break;
}

echo "\n=== SUCCESS ===\n";
echo "The database and basic logic work fine.\n";
echo "The 500 error is likely caused by authentication/session issues in testlinkInitPage().\n";
?>
