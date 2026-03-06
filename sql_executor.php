<?php
/**
 * SQL File Executor
 * 
 * This script provides a web interface to safely execute SQL files against a database.
 * It includes features like:
 * - Database connection with credentials
 * - SQL file selection
 * - Paced execution to avoid overwhelming the database
 * - Transaction support
 * - Execution progress tracking
 */

// Initialize variables
$message = '';
$progress = 0;
$totalQueries = 0;
$executedQueries = 0;
$error = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if we're continuing execution or starting new
    if (isset($_POST['continue_execution']) && $_POST['continue_execution'] == 1) {
        // Continue execution from where we left off
        session_start();
        
        $host = $_SESSION['db_host'];
        $username = $_SESSION['db_username'];
        $password = $_SESSION['db_password'];
        $dbname = $_SESSION['db_name'];
        $sqlFilePath = $_SESSION['sql_file_path'];
        $batchSize = $_SESSION['batch_size'];
        $pauseSeconds = $_SESSION['pause_seconds'];
        $position = $_SESSION['position'];
        $totalQueries = $_SESSION['total_queries'];
        $executedQueries = $_SESSION['executed_queries'];
        
        // Connect to the database
        try {
            $conn = new mysqli($host, $username, $password, $dbname);
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            
            // Open the SQL file
            $sqlContent = file_get_contents($sqlFilePath);
            if ($sqlContent === false) {
                throw new Exception("Could not read SQL file");
            }
            
            // Split the SQL file into individual queries
            $queries = extractQueries($sqlContent);
            
            // Execute the next batch of queries
            $endPosition = min($position + $batchSize, count($queries));
            
            for ($i = $position; $i < $endPosition; $i++) {
                $query = trim($queries[$i]);
                if (empty($query)) continue;
                
                if (!$conn->query($query)) {
                    throw new Exception("Error executing query: " . $conn->error . "\nQuery: " . $query);
                }
                $executedQueries++;
            }
            
            // Update session variables
            $_SESSION['position'] = $endPosition;
            $_SESSION['executed_queries'] = $executedQueries;
            
            // Calculate progress
            $progress = ($executedQueries / $totalQueries) * 100;
            
            // Check if we're done
            if ($endPosition >= count($queries)) {
                $message = "SQL file execution completed successfully. Executed $executedQueries queries.";
                session_destroy();
            } else {
                $message = "Executed queries $position to " . ($endPosition-1) . " of " . count($queries) . ". Pausing for $pauseSeconds seconds before continuing...";
            }
            
            $conn->close();
            
        } catch (Exception $e) {
            $error = true;
            $message = "Error: " . $e->getMessage();
            session_destroy();
        }
        
    } else {
        // New execution
        $host = $_POST['host'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $dbname = $_POST['dbname'];
        $batchSize = intval($_POST['batch_size']);
        $pauseSeconds = intval($_POST['pause_seconds']);
        
        // Validate batch size and pause seconds
        if ($batchSize <= 0) $batchSize = 10;
        if ($pauseSeconds <= 0) $pauseSeconds = 2;
        
        // Handle file upload
        if (isset($_FILES['sql_file']) && $_FILES['sql_file']['error'] === UPLOAD_ERR_OK) {
            $sqlFilePath = $_FILES['sql_file']['tmp_name'];
            $sqlFileName = $_FILES['sql_file']['name'];
            
            // Connect to the database
            try {
                $conn = new mysqli($host, $username, $password, $dbname);
                if ($conn->connect_error) {
                    throw new Exception("Connection failed: " . $conn->connect_error);
                }
                
                // Read the SQL file
                $sqlContent = file_get_contents($sqlFilePath);
                if ($sqlContent === false) {
                    throw new Exception("Could not read SQL file");
                }
                
                // Split the SQL file into individual queries
                $queries = extractQueries($sqlContent);
                $totalQueries = count($queries);
                
                if ($totalQueries === 0) {
                    throw new Exception("No valid SQL queries found in the file");
                }
                
                // Execute the first batch of queries
                $endPosition = min($batchSize, $totalQueries);
                
                for ($i = 0; $i < $endPosition; $i++) {
                    $query = trim($queries[$i]);
                    if (empty($query)) continue;
                    
                    if (!$conn->query($query)) {
                        throw new Exception("Error executing query: " . $conn->error . "\nQuery: " . $query);
                    }
                    $executedQueries++;
                }
                
                // Calculate progress
                $progress = ($executedQueries / $totalQueries) * 100;
                
                // Check if we're done
                if ($endPosition >= $totalQueries) {
                    $message = "SQL file execution completed successfully. Executed $executedQueries queries.";
                } else {
                    // Start a session to store our state
                    session_start();
                    $_SESSION['db_host'] = $host;
                    $_SESSION['db_username'] = $username;
                    $_SESSION['db_password'] = $password;
                    $_SESSION['db_name'] = $dbname;
                    $_SESSION['sql_file_path'] = $sqlFilePath;
                    $_SESSION['batch_size'] = $batchSize;
                    $_SESSION['pause_seconds'] = $pauseSeconds;
                    $_SESSION['position'] = $endPosition;
                    $_SESSION['total_queries'] = $totalQueries;
                    $_SESSION['executed_queries'] = $executedQueries;
                    
                    $message = "Executed queries 0 to " . ($endPosition-1) . " of $totalQueries. Pausing for $pauseSeconds seconds before continuing...";
                }
                
                $conn->close();
                
            } catch (Exception $e) {
                $error = true;
                $message = "Error: " . $e->getMessage();
                if (isset($_SESSION)) session_destroy();
            }
            
        } else {
            $error = true;
            $message = "Error uploading SQL file: " . getFileUploadError($_FILES['sql_file']['error']);
        }
    }
}

/**
 * Extract individual SQL queries from a SQL file content
 * This handles both semicolon-terminated queries and delimiter-separated queries (like in MySQL dumps)
 */
function extractQueries($sqlContent) {
    // Remove comments
    $sqlContent = preg_replace('!/\*.*?\*/!s', '', $sqlContent);
    $sqlContent = preg_replace('!--.*?[\n\r]!', '', $sqlContent);
    
    // Split by semicolons, but respect delimiters
    $delimiter = ';';
    $customDelimiter = false;
    
    // Check for DELIMITER statements
    if (preg_match('/DELIMITER\s+([^\s]+)/i', $sqlContent, $matches)) {
        $customDelimiter = true;
    }
    
    if ($customDelimiter) {
        // Handle custom delimiters (more complex parsing)
        $queries = [];
        $lines = explode("\n", $sqlContent);
        $currentQuery = '';
        $currentDelimiter = ';';
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Check for delimiter change
            if (preg_match('/^DELIMITER\s+([^\s]+)/i', $line, $matches)) {
                $currentDelimiter = $matches[1];
                continue;
            }
            
            // Add to current query
            $currentQuery .= $line . "\n";
            
            // Check if query is complete
            if (substr($line, -strlen($currentDelimiter)) === $currentDelimiter) {
                // Remove the delimiter from the end
                $currentQuery = substr($currentQuery, 0, -strlen($currentDelimiter));
                if (!empty(trim($currentQuery))) {
                    $queries[] = trim($currentQuery);
                }
                $currentQuery = '';
            }
        }
        
        // Add any remaining query
        if (!empty(trim($currentQuery))) {
            $queries[] = trim($currentQuery);
        }
    } else {
        // Simple semicolon splitting for standard SQL files
        $queries = explode(';', $sqlContent);
        // Filter out empty queries
        $queries = array_filter($queries, function($query) {
            return !empty(trim($query));
        });
    }
    
    return $queries;
}

/**
 * Get error message for file upload errors
 */
function getFileUploadError($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
        case UPLOAD_ERR_FORM_SIZE:
            return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
        case UPLOAD_ERR_PARTIAL:
            return "The uploaded file was only partially uploaded";
        case UPLOAD_ERR_NO_FILE:
            return "No file was uploaded";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing a temporary folder";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write file to disk";
        case UPLOAD_ERR_EXTENSION:
            return "A PHP extension stopped the file upload";
        default:
            return "Unknown upload error";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL File Executor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"],
        input[type="number"],
        input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .progress-container {
            width: 100%;
            background-color: #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .progress-bar {
            height: 20px;
            background-color: #4CAF50;
            border-radius: 4px;
            text-align: center;
            color: white;
            line-height: 20px;
        }
        .auto-continue {
            margin-top: 20px;
            padding: 10px;
            background-color: #e7f3fe;
            border: 1px solid #b6d4fe;
            border-radius: 4px;
            color: #084298;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SQL File Executor</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $error ? 'error' : 'success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($progress > 0 && $progress < 100): ?>
            <div class="progress-container">
                <div class="progress-bar" style="width: <?php echo $progress; ?>%">
                    <?php echo round($progress); ?>%
                </div>
            </div>
            
            <div class="auto-continue">
                <p>Execution will continue automatically in <span id="countdown"><?php echo $pauseSeconds; ?></span> seconds...</p>
                <form id="continueForm" method="post">
                    <input type="hidden" name="continue_execution" value="1">
                    <button type="submit">Continue Now</button>
                </form>
            </div>
            
            <script>
                // Auto-continue countdown
                let seconds = <?php echo $pauseSeconds; ?>;
                const countdownElement = document.getElementById('countdown');
                const continueForm = document.getElementById('continueForm');
                
                const countdown = setInterval(function() {
                    seconds--;
                    countdownElement.textContent = seconds;
                    
                    if (seconds <= 0) {
                        clearInterval(countdown);
                        continueForm.submit();
                    }
                }, 1000);
            </script>
        <?php else: ?>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="host">Database Host:</label>
                    <input type="text" id="host" name="host" value="localhost" required>
                </div>
                
                <div class="form-group">
                    <label for="username">Database Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Database Password:</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="dbname">Database Name:</label>
                    <input type="text" id="dbname" name="dbname" required>
                </div>
                
                <div class="form-group">
                    <label for="sql_file">SQL File:</label>
                    <input type="file" id="sql_file" name="sql_file" accept=".sql" required>
                </div>
                
                <div class="form-group">
                    <label for="batch_size">Batch Size (queries per execution):</label>
                    <input type="number" id="batch_size" name="batch_size" value="10" min="1" max="100" required>
                </div>
                
                <div class="form-group">
                    <label for="pause_seconds">Pause Between Batches (seconds):</label>
                    <input type="number" id="pause_seconds" name="pause_seconds" value="2" min="1" max="10" required>
                </div>
                
                <button type="submit">Execute SQL File</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
