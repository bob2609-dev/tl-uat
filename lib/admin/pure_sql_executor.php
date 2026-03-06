<?php
/**
 * Pure PHP SQL Executor for TestLink
 * Uses native PHP mysqli functions without TestLink dependencies
 */

// Database configuration - global variables
GLOBAL $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $TL_ABS_PATH;

$DB_HOST = 'localhost';
$DB_NAME = 'tl_uat';  
$DB_USER = 'tl_uat';  
$DB_PASS = 'tl_uat269';  

// Try to load TestLink config if available
$TL_ABS_PATH = realpath(dirname(__FILE__) . '/../../');
if (file_exists($TL_ABS_PATH . '/config_db.inc.php')) {
    include_once($TL_ABS_PATH . '/config_db.inc.php');
    if (isset($dbhost)) $DB_HOST = $dbhost;
    if (isset($dbname)) $DB_NAME = $dbname;
    if (isset($dbuser)) $DB_USER = $dbuser;
    if (isset($dbpassword)) $DB_PASS = $dbpassword;
    
    // Debug credentials
    sql_log("Loaded database config: host=$DB_HOST, db=$DB_NAME, user=$DB_USER");
}

// Log function
function sql_log($msg, $level = 'INFO') {
    $log_file = dirname(__FILE__) . '/pure_sql.log';
    $time = date('Y-m-d H:i:s');
    $msg = "[$time] [$level] $msg" . PHP_EOL;
    file_put_contents($log_file, $msg, FILE_APPEND);
}

// Get list of SQL files
function getSqlFiles() {
    $sql_dir = dirname(__FILE__) . '/SQL scripts/';
    $files = [];
    
    if (is_dir($sql_dir)) {
        $dir_contents = scandir($sql_dir);
        foreach ($dir_contents as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                $file_path = $sql_dir . $file;
                $files[] = [
                    'name' => $file,
                    'path' => $file_path,
                    'size' => filesize($file_path),
                    'modified' => filemtime($file_path)
                ];
            }
        }
    }
    
    return $files;
}

// Execute SQL file using a different approach that preserves variable context
function executeSqlFile($file_path, $db_name, $mysqli, $db_config = []) {
    if (!file_exists($file_path)) {
        return ['status' => false, 'message' => 'SQL file not found: ' . $file_path];
    }
    
    $sql_content = file_get_contents($file_path);
    if (empty($sql_content)) {
        return ['status' => false, 'message' => 'SQL file is empty'];
    }
    
    // Ensure USE statement
    if (stripos($sql_content, 'USE') === false) {
        $sql_content = "USE `$db_name`;\n" . $sql_content;
    }
    
    // Instead of executing statement by statement, we'll use a different approach
    // that preserves variable context between statements
    
    // Method 1: Try using multi_query for the entire script
    try {
        // Log the SQL being executed for debugging
        sql_log("Executing SQL with multi_query");
        
        // Execute the full SQL script
        $success = $mysqli->multi_query($sql_content);
        
        if ($success) {
            // Free all results
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
            
            return ['status' => true, 'message' => 'SQL executed successfully using multi_query'];
        } else {
            return ['status' => false, 'message' => 'SQL execution failed: ' . $mysqli->error];
        }
    } catch (Exception $e) {
        sql_log("multi_query approach failed: " . $e->getMessage(), "ERROR");
        
        // If multi_query fails, try phpMyAdmin-like approach by using DELIMITER
        try {
            // Alternative approach: Save SQL to temp file and execute via command line
            // This is similar to how phpMyAdmin might handle it
            $temp_file = tempnam(sys_get_temp_dir(), 'sql_');
            file_put_contents($temp_file, $sql_content);
            
            // Get MySQL credentials from the passed configuration
            $db_host = $db_config['host'] ?? 'localhost';
            $db_user = $db_config['user'] ?? '';
            $db_pass = $db_config['pass'] ?? '';
            
            $command = sprintf(
                'mysql -h %s -u %s %s %s < %s',
                escapeshellarg($db_host),
                escapeshellarg($db_user),
                !empty($db_pass) ? '-p' . escapeshellarg($db_pass) : '',
                escapeshellarg($db_name),
                escapeshellarg($temp_file)
            );
            
            sql_log("Trying command-line approach");
            $output = [];
            $return_var = 0;
            exec($command . ' 2>&1', $output, $return_var);
            unlink($temp_file); // Clean up
            
            if ($return_var === 0) {
                return [
                    'status' => true, 
                    'message' => 'SQL executed successfully using command-line MySQL client'
                ];
            } else {
                return [
                    'status' => false, 
                    'message' => 'Command-line execution failed: ' . implode("\n", $output)
                ];
            }
        } catch (Exception $e2) {
            sql_log("Command-line approach failed: " . $e2->getMessage(), "ERROR");
            
            // If both methods fail, attempt a simplified statement-by-statement approach
            // with special handling for variable assignments
            sql_log("Trying statement-by-statement approach with variable handling");
            
            // Split SQL into statements while preserving context
            $statements = parseMultiStatementSQL($sql_content);
            
            // Execute each parsed statement
            $mysqli->autocommit(false);
            $success = true;
            $errors = [];
            
            foreach ($statements as $statement) {
                if (empty(trim($statement))) continue;
                
                $result = $mysqli->query($statement);
                if ($result === false) {
                    $errors[] = $mysqli->error . ' [Statement: ' . substr($statement, 0, 100) . '...]';
                    $success = false;
                }
            }
            
            if ($success) {
                $mysqli->commit();
                return [
                    'status' => true, 
                    'message' => 'SQL executed successfully using statement-by-statement approach', 
                    'statement_count' => count($statements)
                ];
            } else {
                $mysqli->rollback();
                return [
                    'status' => false, 
                    'message' => 'SQL execution failed: ' . implode('; ', $errors)
                ];
            }
        }
    }
}

// Better SQL parser that handles multi-statement SQL with variables
function parseMultiStatementSQL($sql) {
    $statements = [];
    $current = '';
    $inString = false;
    $stringChar = '';
    
    // State tracking for handling special cases
    $lines = explode("\n", $sql);
    $lineCount = count($lines);
    
    for ($i = 0; $i < $lineCount; $i++) {
        $line = $lines[$i];
        
        // Skip comments and empty lines
        if (preg_match('/^\s*--/', $line) || trim($line) === '') {
            continue;
        }
        
        // Special handling for SET statements and variables
        if (preg_match('/^\s*SET\s+@\w+/i', $line)) {
            // This is a variable assignment - include it with the next statement
            // to maintain variable context
            $current .= $line . "\n";
            
            // Keep adding lines until we find a non-SET statement with a semicolon
            $j = $i + 1;
            $foundNextStatement = false;
            
            while ($j < $lineCount && !$foundNextStatement) {
                $nextLine = $lines[$j];
                
                // Skip comments and empty lines
                if (preg_match('/^\s*--/', $nextLine) || trim($nextLine) === '') {
                    $j++;
                    continue;
                }
                
                $current .= $nextLine . "\n";
                
                // If this line ends with a semicolon and is not a SET statement,
                // we've found the end of the logical statement block
                if (preg_match('/;\s*$/', $nextLine) && 
                    !preg_match('/^\s*SET\s+@\w+/i', $nextLine)) {
                    $foundNextStatement = true;
                    $statements[] = $current;
                    $current = '';
                    $i = $j;  // Skip these lines in the outer loop
                }
                
                $j++;
            }
        } 
        // Regular statement handling
        else {
            $current .= $line . "\n";
            
            // If this line ends with a semicolon, treat it as a complete statement
            if (preg_match('/;\s*$/', $line)) {
                $statements[] = $current;
                $current = '';
            }
        }
    }
    
    // Add the final statement if any
    if (trim($current) !== '') {
        $statements[] = $current;
    }
    
    return $statements;
}

// Process actions
$action = isset($_GET['action']) ? $_GET['action'] : '';
$mysqli = null;
$error = null;
$success = null;

// Try to connect to database
try {
    $mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    
    if ($mysqli->connect_error) {
        throw new Exception("Database connection failed: " . $mysqli->connect_error);
    }
    
    sql_log("Connected to database: $DB_NAME on $DB_HOST");
} catch (Exception $e) {
    $error = $e->getMessage();
    sql_log("Database error: " . $error, 'ERROR');
}

// Process SQL execution
if ($action == 'execute' && isset($_POST['file']) && $mysqli) {
    $file = $_POST['file'];
    $sql_dir = dirname(__FILE__) . '/SQL scripts/';
    $sql_file = $sql_dir . basename($file); // Security: Only allow files in SQL scripts directory
    
    sql_log("Executing SQL file: " . $sql_file);
    
    // Pass DB configuration for command-line execution
    $db_config = [
        'host' => $DB_HOST,
        'user' => $DB_USER,
        'pass' => $DB_PASS
    ];
    
    $result = executeSqlFile($sql_file, $DB_NAME, $mysqli, $db_config);
    
    if ($result['status']) {
        $success = $result['message'];
        sql_log($success);
    } else {
        $error = $result['message'];
        sql_log($error, 'ERROR');
    }
}

// View SQL file
$view_content = null;
if ($action == 'view' && isset($_GET['file'])) {
    $file = $_GET['file'];
    $sql_dir = dirname(__FILE__) . '/SQL scripts/';
    $sql_file = $sql_dir . basename($file);
    
    if (file_exists($sql_file)) {
        $view_content = file_get_contents($sql_file);
        $view_file = $file;
    } else {
        $error = "File not found: $file";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>SQL Executor</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 1000px; margin: 0 auto; }
        h1, h2 { color: #444; }
        .header { margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .success { background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:hover { background-color: #f1f1f1; }
        .code-block { background-color: #f8f9fa; border: 1px solid #eee; padding: 15px; overflow: auto; max-height: 500px; font-family: monospace; white-space: pre; }
        .btn { display: inline-block; font-weight: 400; text-align: center; white-space: nowrap; vertical-align: middle; user-select: none; border: 1px solid transparent; padding: .375rem .75rem; font-size: 1rem; line-height: 1.5; border-radius: .25rem; transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out; text-decoration: none; cursor: pointer; }
        .btn-primary { color: #fff; background-color: #007bff; border-color: #007bff; }
        .btn-primary:hover { color: #fff; background-color: #0069d9; border-color: #0062cc; }
        .btn-warning { color: #212529; background-color: #ffc107; border-color: #ffc107; }
        .btn-warning:hover { color: #212529; background-color: #e0a800; border-color: #d39e00; }
        .btn-sm { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        form { display: inline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>TestLink SQL Executor</h1>
         </div>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($action == 'view' && $view_content): ?>
            <h2>SQL File: <?php echo htmlspecialchars($view_file); ?></h2>
            <div class="code-block"><?php echo htmlspecialchars($view_content); ?></div>
            
            <form method="post" action="?action=execute" onsubmit="return confirm('Are you sure you want to execute this SQL file?');">
                <input type="hidden" name="file" value="<?php echo htmlspecialchars($view_file); ?>">
                <button type="submit" class="btn btn-warning">Execute This SQL File</button>
            </form>
            <a href="?" class="btn btn-primary">Back to File List</a>
            <hr>
        <?php endif; ?>
        
        <h2>Available SQL Files</h2>
        <?php 
        $files = getSqlFiles();
        if (count($files) > 0): 
        ?>
            <table>
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Size</th>
                        <th>Last Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['name']); ?></td>
                            <td><?php echo number_format($file['size']); ?> bytes</td>
                            <td><?php echo date('Y-m-d H:i:s', $file['modified']); ?></td>
                            <td>
                                <a href="?action=view&file=<?php echo urlencode($file['name']); ?>" class="btn btn-primary btn-sm">View</a>
                                <form method="post" action="?action=execute" onsubmit="return confirm('Are you sure you want to execute this SQL file?');">
                                    <input type="hidden" name="file" value="<?php echo htmlspecialchars($file['name']); ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">Execute</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No SQL files found in the directory.</p>
        <?php endif; ?>
        
        <div class="info">
            <p><strong>Database Connection:</strong> <?php echo htmlspecialchars($DB_NAME); ?> on <?php echo htmlspecialchars($DB_HOST); ?></p>
            <!-- <p><strong>SQL Directory:</strong> <?php echo htmlspecialchars(dirname(__FILE__) . '/SQL scripts/'); ?></p> -->
            <p><a href="excelImport.php">Return to Excel Import</a></p>
        </div>
    </div>
</body>
</html>
