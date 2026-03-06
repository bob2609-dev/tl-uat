<?php
/**
 * TestLink Direct SQL Executor - Standalone Script
 * 
 * This is a completely standalone script for executing SQL from imported Excel files
 * that bypasses TestLink's template system and CSRF validation to provide a 
 * direct execution path when the main UI has issues.
 */

// Enforce CSRF/session protections even for this standalone script
// Provide minimal dependencies used by csrf.php on failure paths
if (!function_exists('redirect')) {
  function redirect($url) {
    header('Location: ' . $url);
    exit();
  }
}
// Ensure basehref is defined for csrf.php redirects
if (!isset($_SESSION['basehref']) || empty($_SESSION['basehref'])) {
  // From lib/admin/ back to app root
  $_SESSION["basehref"] = '../../';
}
require_once(dirname(__FILE__) . '/../functions/csrf.php');
require_once(dirname(__FILE__) . '/../functions/common.php');

// Start secure session and initialize CSRF guard
doSessionStart(false);
csrfguard_start();

// Include our shared SQL execution library
require_once(dirname(__FILE__) . '/sql_execution_lib.php');

// Basic configuration - adjust as needed
define('DB_HOST', 'localhost'); 
define('DB_USER', 'tl_uat');
define('DB_PASS', 'tl_uat269');
define('DEFAULT_DB', 'tl_uat');
define('SQL_SCRIPTS_DIR', dirname(__FILE__) . '/SQL scripts/');

// Simple logging
function direct_log($msg, $level = 'INFO') {
    $log_file = dirname(__FILE__) . '/direct_sql_executor.log';
    $time = date('Y-m-d H:i:s');
    $msg = "[$time] [$level] $msg" . PHP_EOL;
    file_put_contents($log_file, $msg, FILE_APPEND);
}

direct_log("=== Direct SQL Executor Started ===");

// We now use parseMultiStatementSQL from the shared library

// Override sql_exec_log function to use our local logging
if (!function_exists('sql_exec_log')) {
    function sql_exec_log($msg, $level = 'INFO') {
        direct_log($msg, $level);
    }
}

// Wrapper for the shared library function that uses our local logging
function executeSqlFileRobust($file_path, $db_name) {
    direct_log("Starting SQL execution for file: " . $file_path . " using shared library");
    
    // Create a DB config array with our constants
    $db_config = array(
        'host' => DB_HOST,
        'user' => DB_USER,
        'pass' => DB_PASS
    );
    
    // Call the shared library function
    $result = executeSqlFileRobust_shared($file_path, $db_name, $db_config);
    
    // Log the result
    if ($result->status_ok) {
        direct_log("SQL execution successful: " . $result->msg);
    } else {
        direct_log("SQL execution failed: " . $result->msg, "ERROR");
    }
    
    return $result;
}

// Process form submission
$result = null;
$db_name = DEFAULT_DB;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'execute' && isset($_POST['sqlFile'])) {
        $file = basename($_POST['sqlFile']); // Security: only use the filename
        $file_path = SQL_SCRIPTS_DIR . $file;
        
        // Allow specifying database target
        if (isset($_POST['database']) && !empty($_POST['database'])) {
            $db_name = $_POST['database'];
        }
        
        direct_log("Executing SQL file: $file_path on database: $db_name");
        $result = executeSqlFileRobust($file_path, $db_name);
    }
}

// Get list of available SQL files
$sql_files = [];
if (is_dir(SQL_SCRIPTS_DIR)) {
    $files = scandir(SQL_SCRIPTS_DIR);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $sql_files[] = $file;
        }
    }
}

// Prepare CSRF token for the form (script does not use Smarty)
$csrf_form_name = 'direct_sql_execute';
$csrf_token = csrfguard_generate_token($csrf_form_name);

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct SQL Executor</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.6;
        }
        h1, h2 { color: #333; }
        pre { 
            background-color: #f5f5f5; 
            padding: 15px; 
            border: 1px solid #ddd; 
            overflow: auto; 
            max-height: 500px;
        }
        .success { 
            background-color: #d4edda; 
            border: 1px solid #c3e6cb; 
            color: #155724; 
            padding: 15px; 
            margin: 15px 0; 
            border-radius: 4px;
        }
        .error { 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            color: #721c24; 
            padding: 15px; 
            margin: 15px 0;
            border-radius: 4px;
        }
        .file-list {
            list-style-type: none;
            padding: 0;
        }
        .file-list li {
            background: #f9f9f9;
            margin: 5px 0;
            padding: 10px;
            border-radius: 4px;
            border-left: 4px solid #4CAF50;
        }
        .btn {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 5px;
        }
        .btn-view {
            background: #2196F3;
        }
        .btn-execute {
            background: #ff9800;
        }
    </style>
</head>
<body>
    <h1>Direct SQL Executor</h1>
    <p>This is a standalone tool for executing SQL files without TestLink's template system or CSRF validation.</p>
    
    <?php if ($result): ?>
        <?php if ($result->status_ok): ?>
            <div class="success">
                <strong>Success:</strong> <?php echo htmlspecialchars($result->msg); ?>
            </div>
        <?php else: ?>
            <div class="error">
                <strong>Error:</strong> <?php echo htmlspecialchars($result->msg); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <h2>Available SQL Files</h2>
    <?php if (empty($sql_files)): ?>
        <p>No SQL files found in the directory.</p>
    <?php else: ?>
        <ul class="file-list">
            <?php foreach ($sql_files as $file): ?>
                <li>
                    <strong><?php echo htmlspecialchars($file); ?></strong>
                    <div style="margin-top: 8px;">
                        <a href="?action=view&file=<?php echo urlencode($file); ?>" class="btn btn-view">View</a>
                        <form method="post" style="display: inline;">
                            <input type="hidden" name="action" value="execute">
                            <input type="hidden" name="sqlFile" value="<?php echo htmlspecialchars($file); ?>">
                            <input type="hidden" name="database" value="<?php echo htmlspecialchars($db_name); ?>">
                            <!-- CSRF Protection -->
                            <input type="hidden" name="CSRFName" value="<?php echo htmlspecialchars((string)$csrf_form_name); ?>">
                            <input type="hidden" name="CSRFToken" value="<?php echo htmlspecialchars((string)$csrf_token); ?>">
                            <button type="submit" class="btn btn-execute">Execute on <?php echo htmlspecialchars($db_name); ?></button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <?php
    // View file content if requested
    if (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['file'])) {
        $file = basename($_GET['file']); // Security: only use the filename
        $file_path = SQL_SCRIPTS_DIR . $file;
        
        if (file_exists($file_path) && is_readable($file_path) && pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            echo '<h2>SQL File Content: ' . htmlspecialchars($file) . '</h2>';
            echo '<pre>' . htmlspecialchars(file_get_contents($file_path)) . '</pre>';
        } else {
            echo '<div class="error">Invalid file request or file not found.</div>';
        }
    }
    ?>
    
    <p>
        <a href="../admin/index.php" class="btn">Back to Admin</a>
        <a href="../../index.php" class="btn">Back to TestLink</a>
    </p>
</body>
</html>
