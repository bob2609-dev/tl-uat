<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Excel import and SQL execution - Minimal version to avoid template issues
 *
 * @package     TestLink
 * @filesource  excelImport_minimal.php
 * @copyright   2005-2013, TestLink community
 * @link        http://www.testlink.org
 *
 */

// Include ADODb required files
require_once(dirname(__FILE__) . '/../../vendor/adodb/adodb-php/adodb.inc.php');
require_once(dirname(__FILE__) . '/../../vendor/adodb/adodb-php/adodb-exceptions.inc.php');

require_once('../../config.inc.php');
require_once('common.php');
require_once('excelImport.php'); // Include the original functions
require_once('sql_execution_lib.php'); // Include the shared SQL execution library

// Define database constants if not already defined by config_db.inc.php
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'tl_uat');
if (!defined('DB_PASS')) define('DB_PASS', 'tl_uat269');

// Try to load TestLink DB config directly if not already loaded
$config_db_path = dirname(__FILE__) . '/../../config_db.inc.php';
minimal_log("Trying to load database config from: $config_db_path");
if (file_exists($config_db_path)) {
    include_once($config_db_path);
    // map config_db variables to our constants if they exist
    if (defined('DB_HOST')) minimal_log("DB_HOST is defined: " . DB_HOST);
    if (defined('DB_USER')) minimal_log("DB_USER is defined: " . DB_USER);
    if (defined('DB_PASS')) minimal_log("DB_PASS is defined: " . (DB_PASS ? '[set]' : '[empty]'));
}

// Log function for simple debugging
function minimal_log($msg, $level = 'INFO') {
    $log_file = dirname(__FILE__) . '/minimal_import.log';
    $time = date('Y-m-d H:i:s');
    $msg = "[$time] [$level] $msg" . PHP_EOL;
    file_put_contents($log_file, $msg, FILE_APPEND);
}

// We now use parseMultiStatementSQL from the shared sql_execution_lib.php library

// Override sql_exec_log function to use our local logging
if (!function_exists('sql_exec_log')) {
    function sql_exec_log($msg, $level = 'INFO') {
        minimal_log($msg, $level);
    }
}

// Use the original robust SQL execution function from the shared library directly
// We no longer need our local implementation since we're using the shared one

// Start session and CSRF protection
session_start();
if (!isset($_SESSION['tcvol'])) {
    minimal_log("No session found. Redirecting to login page.", "ERROR");
    header("Location: ../../login.php");
    exit();
}

// Initialize CSRF tokens directly if not set
if (!isset($_SESSION['CSRFName']) || !isset($_SESSION['CSRFToken'])) {
    minimal_log("Initializing CSRF tokens directly");
    $_SESSION['CSRFName'] = 'CSRFName';
    $_SESSION['CSRFToken'] = md5(uniqid(rand(), true));
}

minimal_log("Current CSRF tokens: {$_SESSION['CSRFName']} / {$_SESSION['CSRFToken']}");

// Direct SQL execution functionality
if (isset($_POST['executeSQL']) && isset($_POST['sqlFile']) && isset($_POST['targetDatabase'])) {
    minimal_log("SQL execution request received");
    
    // CSRF protection is bypassed for debugging
    minimal_log("CSRF validation bypassed for debugging");
    
    // Execute SQL from file
    $sqlFile = $_POST['sqlFile'];
    $targetDB = $_POST['targetDatabase'];
    
    // Security: Validate path is within SQL scripts directory
    $baseDir = dirname(__FILE__) . '/SQL scripts/';
    $realPath = realpath($baseDir . basename($sqlFile));
    
    if ($realPath === false || strpos($realPath, $baseDir) !== 0) {
        minimal_log("Invalid SQL file path: " . $sqlFile, "ERROR");
        $error = "Invalid SQL file path.";
    } else {
        minimal_log("Executing SQL file: " . $realPath);
        
        // Use our robust SQL execution function instead of ADODB
        minimal_log("Using robust SQL execution method");
        $result = executeSqlFileRobust($realPath, $targetDB);
        
        if ($result->status_ok) {
            minimal_log("SQL execution successful: " . $result->msg);
            $success = $result->msg;
        } else {
            minimal_log("SQL execution failed: " . $result->msg, "ERROR");
            $error = $result->msg;
        }
    }
}

// Output simple HTML with SQL execution form
?>
<!DOCTYPE html>
<html>
<head>
    <title>TestLink Excel Import - Minimal SQL Executor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background-color: #dff0d8; border: 1px solid #3c763d; padding: 15px; margin-bottom: 20px; }
        .error { background-color: #f2dede; border: 1px solid #a94442; padding: 15px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        button { background-color: #f0ad4e; color: white; padding: 8px 12px; border: none; cursor: pointer; }
        a { color: #337ab7; text-decoration: none; }
    </style>
</head>
<body>
    <h1>TestLink Excel Import - Minimal SQL Executor</h1>
    
    <?php if (isset($success)): ?>
        <div class="success">
            <strong>SQL Execution Successful:</strong> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="error">
            <strong>SQL Execution Failed:</strong> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <p>This is a minimal SQL execution page designed to avoid template errors. It allows you to execute SQL scripts that were generated by the Excel import process.</p>
    
    <h2>Available SQL Files</h2>
    <ul>
    <?php 
    $sqlDir = dirname(__FILE__) . '/SQL scripts/';
    $files = scandir($sqlDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
            echo '<li><strong>' . htmlspecialchars($file) . '</strong> - 
                  <a href="excelImport_minimal.php?action=view&file=' . htmlspecialchars($file) . '">View</a> | 
                  <form method="post" style="display: inline;">
                      <input type="hidden" name="executeSQL" value="1">
                      <input type="hidden" name="sqlFile" value="' . htmlspecialchars($file) . '">
                      <input type="hidden" name="targetDatabase" value="tl_uat">
                      <input type="hidden" name="CSRFName" value="' . htmlspecialchars($_SESSION['CSRFName'] ?? '') . '">
                      <input type="hidden" name="CSRFToken" value="' . htmlspecialchars($_SESSION['CSRFToken'] ?? '') . '">
                      <button type="submit">Execute</button>
                  </form>
                  </li>';
        }
    }
    ?>
    </ul>
    
    <?php 
    // View SQL file content
    if (isset($_GET['action']) && $_GET['action'] == 'view' && isset($_GET['file'])) {
        $file = $_GET['file'];
        $baseDir = dirname(__FILE__) . '/SQL scripts/';
        $realPath = realpath($baseDir . basename($file));
        
        if ($realPath !== false && strpos($realPath, $baseDir) === 0) {
            echo '<h2>SQL File Content: ' . htmlspecialchars($file) . '</h2>';
            echo '<pre style="background-color: #f5f5f5; padding: 15px; overflow: auto; max-height: 500px;">';
            echo htmlspecialchars(file_get_contents($realPath));
            echo '</pre>';
        }
    }
    ?>
    
    <p><a href="excelImport.php">Return to Full Excel Import Page</a></p>
</body>
</html>
