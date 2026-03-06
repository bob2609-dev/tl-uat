<?php
/**
 * TestLink SQL Execution Library
 *
 * Shared functions for SQL execution that can be used by both
 * the main TestLink UI and standalone scripts.
 */

// Logging function that works with both TestLink logging and standalone logging
function sql_exec_log($msg, $level = 'INFO') {
    // If TestLink logging function exists, use it
    if (function_exists('excel_import_log')) {
        excel_import_log($msg, $level);
    } else {
        // Otherwise use standalone logging
        $log_file = dirname(__FILE__) . '/sql_execution.log';
        $time = date('Y-m-d H:i:s');
        $msg = "[$time] [$level] $msg" . PHP_EOL;
        file_put_contents($log_file, $msg, FILE_APPEND);
    }
}

// Better SQL parser that handles multi-statement SQL with variables
function parseMultiStatementSQL($sql) {
    $statements = [];
    $current = '';
    
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
    
    // Handle any remaining content
    if (!empty(trim($current))) {
        $statements[] = $current;
    }
    
    return $statements;
}

/**
 * Robust SQL file execution function
 * 
 * @param string $file_path Path to SQL file
 * @param string $db_name Database name
 * @param array $db_config Optional DB connection config (host, user, pass)
 * @return stdClass Result object with status_ok, msg, and details
 */
function executeSqlFileRobust_shared($file_path, $db_name, $db_config = null) {
    sql_exec_log("Starting robust SQL execution for file: " . $file_path);
    
    $result = new stdClass();
    $result->status_ok = false;
    $result->msg = '';
    $result->details = [];
    
    if (!file_exists($file_path)) {
        $result->msg = 'SQL file not found: ' . $file_path;
        sql_exec_log($result->msg, "ERROR");
        return $result;
    }
    
    $sql_content = file_get_contents($file_path);
    if (empty($sql_content)) {
        $result->msg = 'SQL file is empty';
        sql_exec_log($result->msg, "ERROR");
        return $result;
    }
    
    // Ensure USE statement
    if (stripos($sql_content, 'USE') === false) {
        $sql_content = "USE `$db_name`;\n" . $sql_content;
    }
    
    // Get DB connection parameters - try to use provided config first
    $db_host = isset($db_config['host']) ? $db_config['host'] : (defined('DB_HOST') ? DB_HOST : 'localhost');
    $db_user = isset($db_config['user']) ? $db_config['user'] : (defined('DB_USER') ? DB_USER : 'tl_uat');
    $db_pass = isset($db_config['pass']) ? $db_config['pass'] : (defined('DB_PASS') ? DB_PASS : 'tl_uat269');
    
    // Create native mysqli connection for better multi-query support
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($mysqli->connect_error) {
        $result->msg = 'Connection failed: ' . $mysqli->connect_error;
        sql_exec_log($result->msg, "ERROR");
        return $result;
    }
    
    // Method 1: Try using multi_query for the entire script
    try {
        sql_exec_log("Trying multi_query approach");
        
        // Execute the full SQL script
        $success = $mysqli->multi_query($sql_content);
        
        if ($success) {
            // Free all results
            do {
                if ($result_set = $mysqli->store_result()) {
                    $result_set->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());
            
            $result->status_ok = true;
            $result->msg = 'SQL executed successfully using multi_query';
            sql_exec_log($result->msg);
            return $result;
        } else {
            $result->msg = 'SQL execution failed: ' . $mysqli->error;
            sql_exec_log($result->msg, "ERROR");
        }
    } catch (Exception $e) {
        sql_exec_log("multi_query approach failed: " . $e->getMessage(), "ERROR");
        
        // If multi_query fails, try command line approach
        try {
            sql_exec_log("Trying command-line approach");
            $temp_file = tempnam(sys_get_temp_dir(), 'sql_');
            file_put_contents($temp_file, $sql_content);
            
            $command = sprintf(
                'mysql -h %s -u %s %s %s < %s',
                escapeshellarg($db_host),
                escapeshellarg($db_user),
                !empty($db_pass) ? '-p' . escapeshellarg($db_pass) : '',
                escapeshellarg($db_name),
                escapeshellarg($temp_file)
            );
            
            $output = [];
            $return_var = 0;
            exec($command . ' 2>&1', $output, $return_var);
            unlink($temp_file); // Clean up
            
            if ($return_var === 0) {
                $result->status_ok = true;
                $result->msg = 'SQL executed successfully using command-line MySQL client';
                sql_exec_log($result->msg);
                return $result;
            } else {
                $result->msg = 'Command-line execution failed: ' . implode("\n", $output);
                sql_exec_log($result->msg, "ERROR");
            }
        } catch (Exception $e2) {
            sql_exec_log("Command-line approach failed: " . $e2->getMessage(), "ERROR");
            
            // If both methods fail, attempt a simplified statement-by-statement approach
            sql_exec_log("Trying statement-by-statement approach with variable handling");
            
            // Split SQL into statements while preserving context
            $statements = parseMultiStatementSQL($sql_content);
            
            // Execute each parsed statement
            $mysqli->autocommit(false);
            $success = true;
            $errors = [];
            $executed_count = 0;
            
            foreach ($statements as $statement) {
                if (empty(trim($statement))) continue;
                
                $result_set = $mysqli->query($statement);
                if ($result_set === false) {
                    $errors[] = $mysqli->error . ' [Statement: ' . substr($statement, 0, 100) . '...]';
                    $success = false;
                } else {
                    $executed_count++;
                    if ($result_set !== true) {
                        $result_set->free();
                    }
                }
            }
            
            if ($success) {
                $mysqli->commit();
                $result->status_ok = true;
                $result->msg = "SQL executed successfully using statement-by-statement approach. {$executed_count} statements executed.";
                sql_exec_log($result->msg);
                return $result;
            } else {
                $mysqli->rollback();
                $result->msg = 'SQL execution failed: ' . implode('; ', $errors);
                sql_exec_log($result->msg, "ERROR");
            }
        }
    }
    
    return $result;
}
