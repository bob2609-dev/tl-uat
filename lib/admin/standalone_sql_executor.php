<?php
/**
 * Standalone SQL Executor for TestLink
 * This script works independently of TestLink's templating system to execute SQL files
 */

// Simple configuration (adjust as needed)
$DB_TYPE = 'mysqli'; // Database type (mysqli, postgres, etc.)
$DB_HOST = 'localhost'; // Database host
$DB_NAME = 'tl_uat'; // Database name
$DB_USER = ''; // Set your database username here
$DB_PASS = ''; // Set your database password here

// Load only minimal TestLink core to get the database functions
$TL_ABS_PATH = realpath(dirname(__FILE__) . '/../../');
require_once($TL_ABS_PATH . '/lib/functions/database.class.php');

// Log function
function sql_log($msg, $level = 'INFO') {
    $log_file = dirname(__FILE__) . '/sql_executor.log';
    $time = date('Y-m-d H:i:s');
    $msg = "[$time] [$level] $msg" . PHP_EOL;
    file_put_contents($log_file, $msg, FILE_APPEND);
}

// SQL execution function
function executeSQLFile($db_connection, $sql_file, $db_name) {
    if (!file_exists($sql_file)) {
        return ['status' => false, 'message' => 'SQL file not found'];
    }
    
    $sql = file_get_contents($sql_file);
    if (empty($sql)) {
        return ['status' => false, 'message' => 'SQL file is empty'];
    }
    
    // Add USE statement if not present
    if (stripos($sql, 'USE') === false) {
        $sql = "USE `{$db_name}`;\n" . $sql;
    }
    
    // Split SQL into individual statements
    $statements = [];
    $current = '';
    $delimiter = ';';
    
    // Simple SQL parser
    foreach (explode("\n", $sql) as $line) {
        $line = trim($line);
        if (empty($line) || substr($line, 0, 2) == '--') {
            continue; // Skip empty lines and comments
        }
        
        $current .= $line . "\n";
        if (substr($line, -strlen($delimiter)) == $delimiter) {
            $statements[] = $current;
            $current = '';
        }
    }
    
    // Add the last statement if any
    if (!empty(trim($current))) {
        $statements[] = $current;
    }
    
    try {
        // Execute each statement in a transaction
        $db_connection->db->StartTrans();
        $errors = [];
        
        foreach ($statements as $statement) {
            if (empty(trim($statement))) continue;
            
            $result = $db_connection->exec_query($statement);
            if ($result === false) {
                $errors[] = $db_connection->db->ErrorMsg();
            }
        }
        
        if (empty($errors)) {
            $db_connection->db->CompleteTrans();
            return ['status' => true, 'message' => 'SQL executed successfully'];
        } else {
            $db_connection->db->FailTrans();
            $db_connection->db->CompleteTrans();
            return ['status' => false, 'message' => 'SQL execution failed: ' . implode('; ', $errors)];
        }
    } catch (Exception $e) {
        return ['status' => false, 'message' => 'Exception: ' . $e->getMessage()];
    }
}

// Main code
$action = isset($_GET['action']) ? $_GET['action'] : '';
$sql_dir = dirname(__FILE__) . '/SQL scripts/';
$error = null;
$success = null;

// Create database connection
try {
    // Try to use TestLink's config if available
    if (file_exists($TL_ABS_PATH . '/config_db.inc.php')) {
        include_once($TL_ABS_PATH . '/config_db.inc.php');
        if (isset($dbhost)) $DB_HOST = $dbhost;
        if (isset($dbname)) $DB_NAME = $dbname;
        if (isset($dbtype)) $DB_TYPE = $dbtype;
        if (isset($dbuser)) $DB_USER = $dbuser;
        if (isset($dbpassword)) $DB_PASS = $dbpassword;
    }
    
    // Connect to database
    $db = new database($DB_TYPE);
    $connected = $db->connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if (!$connected) {
        $error = "Could not connect to database: " . $db->db->ErrorMsg();
        sql_log($error, 'ERROR');
    }
} catch (Exception $e) {
    $error = "Database connection error: " . $e->getMessage();
    sql_log($error, 'ERROR');
    $db = null;
}

// Process SQL execution
if ($action == 'execute' && isset($_POST['file']) && $db) {
    $file = $_POST['file'];
    $sql_file = $sql_dir . basename($file); // Security: Only allow files in SQL scripts directory
    
    sql_log("Executing SQL file: " . $sql_file);
    $result = executeSQLFile($db, $sql_file, $DB_NAME);
    
    if ($result['status']) {
        $success = $result['message'];
        sql_log($success);
    } else {
        $error = $result['message'];
        sql_log($error, 'ERROR');
    }
}

// HTML output - completely self-contained, no TestLink dependencies
?>
<!DOCTYPE html>
<html>
<head>
    <title>Standalone SQL Executor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { background-color: #f8f8f8; padding: 15px; border-bottom: 2px solid #ddd; margin-bottom: 20px; }
        .success { background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-left: 5px solid #3c763d; }
        .error { background-color: #f2dede; color: #a94442; padding: 15px; margin-bottom: 20px; border-left: 5px solid #a94442; }
        .footer { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; font-size: 0.8em; color: #777; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f5f5f5; }
        pre { background-color: #f5f5f5; padding: 15px; overflow: auto; }
        .button { display: inline-block; background-color: #4CAF50; color: white; padding: 8px 15px; text-decoration: none; cursor: pointer; border: none; }
        .button.warning { background-color: #f0ad4e; }
        .button:hover { opacity: 0.8; }
        form { display: inline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Standalone SQL Executor</h1>
            <p>This tool executes SQL files directly, bypassing TestLink's templating system.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <strong>Success:</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <h2>Available SQL Files</h2>
        <?php 
        if (is_dir($sql_dir)) {
            $files = array_diff(scandir($sql_dir), ['.', '..']);
            if (count($files) > 0) {
                echo '<table>';
                echo '<tr><th>File Name</th><th>Size</th><th>Modified</th><th>Actions</th></tr>';
                
                foreach ($files as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                        $file_path = $sql_dir . $file;
                        $size = filesize($file_path);
                        $modified = date('Y-m-d H:i:s', filemtime($file_path));
                        
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($file) . '</td>';
                        echo '<td>' . number_format($size) . ' bytes</td>';
                        echo '<td>' . $modified . '</td>';
                        echo '<td>';
                        echo '<a class="button" href="?action=view&file=' . urlencode($file) . '">View</a> ';
                        
                        // SQL execution form
                        echo '<form method="post" action="?action=execute" onsubmit="return confirm(\'Are you sure you want to execute this SQL file?\');">';
                        echo '<input type="hidden" name="file" value="' . htmlspecialchars($file) . '">';
                        echo '<button type="submit" class="button warning">Execute</button>';
                        echo '</form>';
                        
                        echo '</td>';
                        echo '</tr>';
                    }
                }
                
                echo '</table>';
            } else {
                echo '<p>No SQL files found in the directory.</p>';
            }
        } else {
            echo '<p>SQL directory not found.</p>';
        }
        ?>
        
        <?php 
        // View SQL file content
        if ($action == 'view' && isset($_GET['file'])) {
            $file = $_GET['file'];
            $file_path = $sql_dir . basename($file); // Security: Only allow files in SQL scripts directory
            
            if (file_exists($file_path)) {
                echo '<h2>SQL File Content: ' . htmlspecialchars($file) . '</h2>';
                echo '<pre>' . htmlspecialchars(file_get_contents($file_path)) . '</pre>';
            } else {
                echo '<div class="error">File not found.</div>';
            }
        }
        ?>
        
        <div class="footer">
            <p>This is a standalone tool for executing SQL files. It bypasses TestLink's templating system to avoid errors.</p>
            <p><a href="excelImport.php">Return to TestLink Excel Import</a></p>
        </div>
    </div>
</body>
</html>
