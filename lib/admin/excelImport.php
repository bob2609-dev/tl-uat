<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource excelImport.php
 * @package TestLink
 * @copyright 2025
 *
 * Excel Test Case Import functionality
 */

// Include ADODb required files
require_once(dirname(__FILE__) . '/../../vendor/adodb/adodb-php/adodb.inc.php');
require_once(dirname(__FILE__) . '/../../vendor/adodb/adodb-php/adodb-exceptions.inc.php');

// Include our robust SQL execution library
require_once(dirname(__FILE__) . '/sql_execution_lib.php');
require_once('../../config.inc.php');
require_once("../functions/common.php");
require_once("../functions/xml.inc.php");
require_once('web_editor.php');
require_once("../functions/csrf.php"); // Include CSRF protection functions
require_once('users.inc.php');
require_once('excel_import_log.php'); // Include our logging functions

// Start logging session
excel_import_log('=== Excel Import Session Started ===');

testlinkInitPage($db);

// Check user permissions - only admins can access this page
$userHasRights = $_SESSION['currentUser']->hasRight($db, 'mgt_modify_tc');
if (!$userHasRights) {
    $smarty = new TLSmarty();
    $smarty->assign('title', lang_get('access_denied'));
    $smarty->assign('content', lang_get('access_denied_feedback'));
    $smarty->display('workAreaSimple.tpl');
    exit();
}

$templateCfg = excelImportTemplateConfiguration();
$args = init_args($db);
$gui = initializeGui($db, $args);

// Initialize CSRF protection at the beginning of the script
csrfguard_start();
excel_import_log('Excel import script started - CSRF protection initialized');

// We no longer execute SQL directly in this script
// Instead, we only generate SQL files and provide links to pure_sql_executor.php
$gui->currentDatabase = $tlCfg->db->database; // Store current DB for template display only
echo $gui->currentDatabase;
// Check if we should process file import
if ($args->doImport){
    excel_import_log('Processing file import request');
    
    // Always initialize importResults and file_check as objects to prevent template errors
    $gui->importResults = new stdClass();
    $gui->importResults->status_ok = false;
    $gui->importResults->msg = '';
    $gui->importResults->sql_file = '';
    $gui->importResults->sql_content = '';
    $gui->importResults->sql_file_path = '';
    $gui->importResults->num_imported = 0;
    
    $gui->file_check = new stdClass();
    $gui->file_check->status_ok = false;
    $gui->file_check->msg = '';
    
    // Skip the "Import failed" message if this is a fresh page load with no file upload attempt
    // This prevents showing the error message when the page is first loaded
    if (!isset($_FILES['uploadedFile'])) {
        $gui->doImport = false; // Don't show the error message
        excel_import_log('No file uploaded yet - initial page load');
    }
    
    // Check if an SQL file was generated in this session first
    $sql_file_already_generated = false;
    
    // Check for SQL files in the SQL scripts directory with today's date
    $sql_dir = dirname(__FILE__) . '/SQL scripts/';
    $today_date = date('Ymd');
    
    // Look for SQL files in the directory
    if (file_exists($sql_dir) && is_dir($sql_dir)) {
        $files = scandir($sql_dir);
        $recent_sql_files = array();
        
        // Filter for SQL files created today
        foreach ($files as $file) {
            if (strpos($file, $today_date) !== false && strpos($file, '.sql') !== false) {
                $recent_sql_files[] = $file;
                excel_import_log('Found recent SQL file: ' . $file);
            }
        }
        
        // If we found recent SQL files, use the most recent one
        if (!empty($recent_sql_files)) {
            // Sort by name (which includes timestamp) to get the latest
            rsort($recent_sql_files);
            $most_recent_sql = $recent_sql_files[0];
            $sql_filepath = $sql_dir . $most_recent_sql;
            
            // Create importResults from the found SQL file
            $gui->importResults->status_ok = true;
            $gui->importResults->sql_file = $most_recent_sql;
            $gui->importResults->sql_file_path = $sql_filepath;
            
            // Count test cases in the SQL file
            if (file_exists($sql_filepath)) {
                $sql_content = file_get_contents($sql_filepath);
                preg_match_all("/-- Test Case \d+:/i", $sql_content, $matches);
                $num_imported = count($matches[0]);
                $gui->importResults->num_imported = $num_imported;
                $gui->importResults->sql_content = $sql_content;
                
                excel_import_log('Found existing SQL file with ' . $num_imported . ' test cases');
                
                // Set success flags
                $gui->file_check->status_ok = true;
                $gui->file_check->msg = 'SQL file found in directory';
                $sql_file_already_generated = true;
                $gui->import_status_ok = true;
            }
        }
    }
    
    // Also check session for previously stored results
    if (!$sql_file_already_generated && isset($_SESSION['last_sql_file_generated']) && $_SESSION['last_sql_file_generated']) {
        excel_import_log('SQL file was already generated in this session, skipping file checks');
        $gui->file_check->status_ok = true;
        $gui->file_check->msg = 'File processed successfully';
        $sql_file_already_generated = true;
        $gui->import_status_ok = true;
        
        // Check if we have stored importResults in session
        if (isset($_SESSION['last_import_results'])) {
            $gui->importResults = $_SESSION['last_import_results'];
            excel_import_log('Retrieved previous import results from session');
        }
    }

    // Only check file status if doImport flag is set, we haven't already processed the file,
    // and there's no SQL file already generated from this session
    if ($args->doImport && isset($_FILES['uploadedFile']) && !$sql_file_already_generated) {
        // Check upload status directly from $_FILES
        if (!isset($_FILES['uploadedFile']) || $_FILES['uploadedFile']['error'] !== UPLOAD_ERR_OK) {
            $error = isset($_FILES['uploadedFile']['error']) ? $_FILES['uploadedFile']['error'] : 'Unknown error';
            excel_import_log('File upload error: ' . $error, 'ERROR');
            $gui->file_check->msg = 'File upload error: ' . $error;
            $gui->file_check->status_ok = false;
        }
        else if (empty($_FILES['uploadedFile']['tmp_name']) || !file_exists($_FILES['uploadedFile']['tmp_name'])) {
            // Check if the uploaded file actually exists in the temp location
            excel_import_log('Uploaded file not found in temp location: ' . ($_FILES['uploadedFile']['tmp_name'] ?? 'null'), 'ERROR');
            $gui->file_check->msg = 'Uploaded file not found in temp location. File may have been moved or deleted.';
            $gui->file_check->status_ok = false;
        }
        else {
        // Update args with the latest file data
        $args->fileName = $_FILES['uploadedFile']['name'];
        $args->fileTmpName = $_FILES['uploadedFile']['tmp_name'];
        excel_import_log('File found, checking type: ' . $args->fileName);
        
        // Check file type
        $file_check = checkUploadedFile($args);
        $gui->file_check = $file_check;
        
        if ($file_check->status_ok) {
            excel_import_log('File check passed, processing import');
            // Store filename in session to avoid temp file issues after processing
            $_SESSION['last_excel_file'] = $args->fileName;
            $_SESSION['last_import_time'] = date('YmdHis');
            
            $importResults = doExcelImport($args, $db);
            $gui->importResults = $importResults; // This will override the initialization
            
            // If SQL file was successfully generated, set file_check to success
            // This prevents the "file not found" error after successful processing
            if (!empty($importResults->sql_file) && file_exists($importResults->sql_file_path)) {
                $gui->file_check->status_ok = true;
                $gui->file_check->msg = 'File processed successfully';
                
                // Set import_status_ok to trigger the success message in the template
                $gui->import_status_ok = true;
                
                // Store the success state and results in session to prevent duplicate checks
                $_SESSION['last_sql_file_generated'] = true;
                $_SESSION['last_import_results'] = $importResults;
                excel_import_log('Stored SQL generation success in session');
            }
        }
        else {
            excel_import_log('File check failed: ' . $file_check->msg, 'ERROR');
            // Make sure importResults is an object before assigning properties
            if (!is_object($gui->importResults)) {
                $gui->importResults = new stdClass();
            }
            $gui->importResults->msg = $file_check->msg;
        }
    }
}}

// CSRF protection already initialized at the beginning of the script

// Move key properties up to the root level - this is crucial for template compatibility
if (isset($gui->file_check)) {
    $gui->file_check_status_ok = $gui->file_check->status_ok ?? false;
    $gui->file_check_msg = $gui->file_check->msg ?? '';
}

if (isset($gui->importResults)) {
    $gui->import_status_ok = $gui->importResults->status_ok ?? false;
    $gui->import_msg = $gui->importResults->msg ?? '';
    $gui->sql_file = $gui->importResults->sql_file ?? '';
    $gui->sql_content = $gui->importResults->sql_content ?? '';
    $gui->num_imported = $gui->importResults->num_imported ?? 0;
}

// Debug: Log what $gui object looks like
excel_import_log('Debug - GUI object before Smarty: ' . print_r($gui, true));

// Add a debug mode option
$debug_mode = isset($_REQUEST['debug']) ? (bool)$_REQUEST['debug'] : false;

if ($debug_mode) {
    // In debug mode, output directly without using Smarty
    header('Content-Type: text/html');
    echo "<html><head><title>Debug Mode: Excel Import</title></head><body>";
    echo "<h1>Debug Mode: Excel Import</h1>";
    echo "<h2>GUI Object Structure:</h2>";
    echo "<pre>" . htmlspecialchars(print_r($gui, true)) . "</pre>";
    echo "<h2>FILES Array:</h2>";
    echo "<pre>" . htmlspecialchars(print_r($_FILES, true)) . "</pre>";
    echo "<p><a href='lib/admin/excelImport.php'>Return to normal mode</a></p>";
    echo "</body></html>";
} else {
    // Normal mode - use Smarty with simplified template
    $smarty = new TLSmarty();
    
    // Get CSRF tokens directly from active session
    $gui->CSRFName = $_SESSION['CSRFName'] ?? 'CSRFName';
    $gui->CSRFToken = $_SESSION['CSRFToken'] ?? 'CSRFToken';
    
    // Log the CSRF token values for debugging
    excel_import_log('CSRF Name: ' . $gui->CSRFName . ', Token: ' . $gui->CSRFToken);
    
    // Assign GUI to Smarty with tokens embedded
    $smarty->assign('gui', $gui);
    
    // Use the simplified template directly (no replacement needed)
    $smarty->display($templateCfg->template_dir . 'excelImport_simple.tpl');
}

/**
 * Initialize user arguments
 */
function init_args(&$dbHandler)
{
    excel_import_log('Initializing arguments');    
    excel_import_debug($_REQUEST, 'REQUEST data');
    excel_import_debug($_FILES, 'FILES data');
    
    $args = new stdClass();
    $args->doImport = isset($_REQUEST['doImport']) ? (bool)$_REQUEST['doImport'] : false;
    $args->importType = isset($_REQUEST['importType']) ? $_REQUEST['importType'] : null;
    $args->fileName = isset($_FILES['uploadedFile']['name']) ? $_FILES['uploadedFile']['name'] : null;
    $args->fileTmpName = isset($_FILES['uploadedFile']['tmp_name']) ? $_FILES['uploadedFile']['tmp_name'] : null;
    $args->testsuiteID = isset($_REQUEST['testsuiteID']) ? intval($_REQUEST['testsuiteID']) : 0;
    $args->authorID = isset($_REQUEST['authorID']) ? intval($_REQUEST['authorID']) : 0;
    $args->nodeOrder = isset($_REQUEST['nodeOrder']) ? intval($_REQUEST['nodeOrder']) : 100; // Default node order
    $args->targetDatabase = isset($_REQUEST['databaseName']) ? $_REQUEST['databaseName'] : '';
    $args->sheetName = isset($_REQUEST['sheetName']) ? $_REQUEST['sheetName'] : ''; // Sheet name for Excel files
    $args->inspectExcel = isset($_REQUEST['inspectExcel']) ? (bool)$_REQUEST['inspectExcel'] : false; // Option to inspect Excel
    $args->cleanupExcel = isset($_REQUEST['cleanupExcel']) ? (bool)$_REQUEST['cleanupExcel'] : false; // Option to clean up Excel
    
    excel_import_log('Arguments initialized: doImport=' . ($args->doImport ? 'true' : 'false') . 
                    ', fileName=' . ($args->fileName ?? 'null') . 
                    ', tmp_name=' . ($args->fileTmpName ?? 'null'));
    
    return $args;
}

/**
 * Initialize GUI
 */
function initializeGui(&$dbHandler, &$argsObj)
{
    $gui = new stdClass();
    $gui->tproject_id = isset($_SESSION['testprojectID']) ? intval($_SESSION['testprojectID']) : 0;
    $gui->tproject_name = isset($_SESSION['testprojectName']) ? $_SESSION['testprojectName'] : '';
    $gui->page_title = lang_get('import_excel_testcases');
    $gui->file_check = array('status_ok' => 0, 'msg' => 'No File Uploaded');
    // Always initialize importResults as an object to avoid type errors
    $gui->importResults = new stdClass();
    $gui->doImport = isset($_REQUEST['doImport']) ? true : false;
    $gui->import_status_ok = false; // Initialize to false by default
    
    // For drop-downs
    $gui->testcaseStatus = getTestCaseStatusList();
    $gui->importanceOptions = getImportanceOptions();
    $gui->executionOptions = getExecutionOptions();
    
    // Add CSRF tokens to GUI
    $gui->CSRFName = isset($_SESSION['CSRFName']) ? $_SESSION['CSRFName'] : 'CSRFName';
    $gui->CSRFToken = isset($_SESSION['CSRFToken']) ? $_SESSION['CSRFToken'] : 'CSRFToken';
    excel_import_log('CSRF tokens assigned: ' . $gui->CSRFName . ' / Token length: ' . strlen($gui->CSRFToken));
    
    // Get current database name
    $sql = "SELECT DATABASE() AS db_name";
    $result = $dbHandler->exec_query($sql);
    $row = $dbHandler->fetch_array($result);
    $gui->currentDatabase = $row['db_name'];
    
    // Get available databases
    $sql = "SHOW DATABASES";
    $result = $dbHandler->exec_query($sql);
    $gui->databases = array();
    while ($row = $dbHandler->fetch_array($result)) {
        // Skip system databases
        if (!in_array($row['Database'], array('information_schema', 'mysql', 'performance_schema', 'sys'))) {
            $gui->databases[] = $row['Database'];
        }
    }
    
    // We no longer need to get all test suites since we're using direct ID input
    // We'll validate the input test suite ID when form is submitted
    if ($argsObj->doImport && $argsObj->testsuiteID) {
        // Check if the provided test suite ID exists in the system
        // Create testsuite object to validate
        $testsuite_mgr = new testsuite($dbHandler);
        $check = $testsuite_mgr->get_by_id($argsObj->testsuiteID);
        
        if (!$check) {
            $gui->import_result = false;
            $gui->user_feedback = 'Error: Test Suite ID ' . intval($argsObj->testsuiteID) . ' not found or is not a valid test suite.';
        }
    }
    
    // We no longer need to get users for dropdown as we're using direct text input for author
    
    // Check if a file was uploaded
    $argsObj->file_check = array('status_ok' => false, 'msg' => 'No File Uploaded');
    
    excel_import_log('Checking file upload status');
    excel_import_debug($_FILES, 'FILES array details');
    
    if ($argsObj->doImport && isset($_FILES['uploadedFile'])) {
        if (isset($_FILES['uploadedFile']['error']) && $_FILES['uploadedFile']['error'] != 0) {
            // Log the specific upload error
            $errorMessages = array(
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk',
                8 => 'A PHP extension stopped the file upload'
            );
            $errorCode = $_FILES['uploadedFile']['error'];
            $errorMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : 'Unknown upload error';
            
            excel_import_log('File upload error: ' . $errorMessage, 'ERROR');
            $argsObj->file_check = array('status_ok' => false, 'msg' => 'File upload error: ' . $errorMessage);
        } 
        elseif (empty($_FILES['uploadedFile']['tmp_name']) || $_FILES['uploadedFile']['tmp_name'] == 'none') {
            excel_import_log('No file was uploaded or empty tmp_name', 'ERROR');
            $argsObj->file_check = array('status_ok' => false, 'msg' => 'No file was uploaded');
        }
        elseif (!is_uploaded_file($_FILES['uploadedFile']['tmp_name'])) {
            excel_import_log('Security issue: Not an uploaded file', 'ERROR');
            $argsObj->file_check = array('status_ok' => false, 'msg' => 'Security issue: Not a valid uploaded file');
        }
        else {
            excel_import_log('File uploaded successfully: ' . $_FILES['uploadedFile']['name']);
            $argsObj->file_check = array('status_ok' => true, 'msg' => 'File uploaded successfully');
        }
    }
    else {
        excel_import_log('No file upload attempt detected');
    }
    
    // Handle import process
    if ($argsObj->doImport) {
        // Attempt Excel import even if file check had errors
        // This allows us to handle cases where temp file issues might occur but SQL generation can still succeed
        $gui->importResults = doExcelImport($argsObj, $dbHandler);
        
        // If SQL file was successfully generated, consider the import successful
        // regardless of the initial file check status
        if (isset($gui->importResults->sql_file) && !empty($gui->importResults->sql_file)) {
            excel_import_log('SQL file was generated successfully: ' . $gui->importResults->sql_file);
            $gui->file_check['status_ok'] = true;
            $gui->file_check['msg'] = 'File processed successfully';
        }
    }
    
    return $gui;
}

/**
 * Process Excel file and generate SQL for import
 */
function doExcelImport($argsObj, &$dbHandler) {
    excel_import_log('=== Starting Excel import process ===');
    excel_import_debug($argsObj, 'Import arguments');
    
    $results = new stdClass();
    $results->status_ok = false;
    $results->msg = '';
    $results->num_imported = 0;
    $results->sql_file = '';
    $results->sql_content = '';
    $results->sql_file_path = '';
    $results->tool_output = '';
    
    // Check if file name is set
    if (empty($argsObj->fileName)) {
        $msg = "Error: No file name provided";
        excel_import_log($msg, 'ERROR');
        $results->msg = $msg;
        return $results;
    }
    
    excel_import_log('Checking file extension for: ' . $argsObj->fileName);
    
    // Security: Make sure the file extension is allowed
    $allowed_extensions = array('xls', 'xlsx', 'csv');
    $file_info = pathinfo($argsObj->fileName);
    $file_extension = isset($file_info['extension']) ? strtolower($file_info['extension']) : '';
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $msg = "Invalid file type. Only Excel files (.xls, .xlsx, .csv) are allowed. Got: {$file_extension}";
        excel_import_log($msg, 'ERROR');
        $results->msg = $msg;
        return $results;
    }
    
    excel_import_log('File extension check passed: ' . $file_extension);
    
    try {
        // Create a directory for SQL scripts if it doesn't exist
        $sql_dir = dirname(__FILE__) . '/SQL scripts/';
        if (!file_exists($sql_dir)) {
            mkdir($sql_dir, 0755, true);
        }
        
        // Generate a unique filename for the SQL output based on Excel filename + timestamp
        $timestamp = date('YmdHis');
        $excel_filename_no_ext = pathinfo($argsObj->fileName, PATHINFO_FILENAME);
        $sql_filename = "{$excel_filename_no_ext}_{$timestamp}.sql";
        $sql_filepath = $sql_dir . $sql_filename;
        
        // Move uploaded file to temp directory for processing
        $upload_tmp_name = $argsObj->fileTmpName;
        $temp_excel_file = TL_TEMP_PATH . basename($argsObj->fileName);
        
        excel_import_log('Attempting to move uploaded file from: ' . $upload_tmp_name . ' to: ' . $temp_excel_file);
        
        // Make sure TL_TEMP_PATH exists
        if (!file_exists(TL_TEMP_PATH)) {
            excel_import_log('Creating temp directory: ' . TL_TEMP_PATH);
            mkdir(TL_TEMP_PATH, 0755, true);
        }
        
        if (!is_writable(TL_TEMP_PATH)) {
            $msg = "Error: Temporary directory is not writable: " . TL_TEMP_PATH;
            excel_import_log($msg, 'ERROR');
            $results->msg = $msg;
            return $results;
        }
        
        if (empty($upload_tmp_name) || $upload_tmp_name == 'none' || !file_exists($upload_tmp_name)) {
            $msg = "Error: No valid temp file found at: {$upload_tmp_name}";
            excel_import_log($msg, 'ERROR');
            $results->msg = $msg;
            return $results;
        }
        
        if (!move_uploaded_file($upload_tmp_name, $temp_excel_file)) {
            $msg = "Error moving uploaded file.";
            excel_import_log($msg, 'ERROR');
            $results->msg = $msg;
            return $results;
        }
        
        excel_import_log('Successfully moved uploaded file to: ' . $temp_excel_file);
        
        // Path to the TestLink_Excel_Import_Tool.exe in the same directory as this script
        $exePath = realpath(dirname(__FILE__) . '/TestLink_Excel_Import_Tool_V3.exe');
        
        if (!file_exists($exePath)) {
            $msg = "Error: TestLink_Excel_Import_Tool.exe not found at {$exePath}";
            excel_import_log($msg, 'ERROR');
            $results->msg = $msg;
            return $results;
        }
        
        excel_import_log('Found Excel Import Tool at: ' . $exePath);
        
        // Build the command arguments to match example format:
        // TestLink_Excel_Import_Tool_V2.exe -f "file.xlsx" -o "output.sql" -a 1 -e 2 -s 106954 -i 2 -n 1020 -t 7 --preprocess
        $cmd_args = array();
        
        // Required parameters
        $cmd_args[] = '-f "' . $temp_excel_file . '"'; // Excel file path
        $cmd_args[] = '-o "' . $sql_filepath . '"';    // Output SQL file path
        $cmd_args[] = '-s ' . intval($argsObj->testsuiteID); // Test suite ID
        $cmd_args[] = '-a ' . intval($argsObj->authorID);  // Author ID
        $cmd_args[] = '-n ' . intval($argsObj->nodeOrder);  // Node order
        
        // Optional parameters with defaults
        
        // Test case status (Draft=1, Final=7, etc.)
        if (isset($argsObj->importStatus) && !empty($argsObj->importStatus)) {
            $cmd_args[] = '-t ' . intval($argsObj->importStatus);
        } else {
            $cmd_args[] = '-t 7'; // Default to 7 (Final) if not specified
        }
        
        // Importance (Low=1, Medium=2, High=3)
        if (isset($argsObj->importance) && !empty($argsObj->importance)) {
            $cmd_args[] = '-i ' . intval($argsObj->importance);
        } else {
            $cmd_args[] = '-i 2'; // Default to 2 (Medium) if not specified
        }
        
        // Execution type (Manual=1, Automated=2)
        if (isset($argsObj->executionType) && !empty($argsObj->executionType)) {
            $cmd_args[] = '-e ' . intval($argsObj->executionType);
        } else {
            $cmd_args[] = '-e 1'; // Default to 1 (Manual) if not specified
        }
        
        // Add sheet name if specified (this format is correct per help output)
        if (!empty($argsObj->sheetName)) {
            $cmd_args[] = '--sheet-name';
            $cmd_args[] = escapeshellarg($argsObj->sheetName);
        }
        
        // Add the non-interactive mode argument for automatic preprocessing
        // --preprocess: Enable preprocessing (we don't add --clean-only to allow column name fixes)
        $cmd_args[] = '--preprocess';
        
        // Log a note that we're ignoring some options that aren't supported
        if (isset($argsObj->inspectExcel) && $argsObj->inspectExcel) {
            excel_import_log('Note: Ignoring "inspect" option as it is not supported by the tool');
        }
        
        if (isset($argsObj->cleanupExcel) && $argsObj->cleanupExcel) {
            excel_import_log('Note: Ignoring "cleanup" option as it is not supported by the tool');
        }
        
        if (!empty($argsObj->targetDatabase)) {
            excel_import_log('Note: Ignoring "database" option as it is not supported by the tool');
        }
        
        // Build the final command with the executable path and all arguments
        $cmd = '"' . $exePath . '"';
        foreach ($cmd_args as $arg) {
            $cmd .= ' ' . $arg;
        }
        
        // Capture stderr as well as stdout
        $cmd_full = $cmd . ' 2>&1';
        
        // Log the final command for debugging
        excel_import_log('Executing command: ' . $cmd_full);
        
        // Execute the command and capture output
        $output = array();
        $return_var = 0;
        exec($cmd_full, $output, $return_var);
        
        excel_import_log('Command execution complete. Return code: ' . $return_var);
        excel_import_debug($output, 'Command output');
        
        // No temporary files to clean up with the non-interactive argument approach
        
        // Check if the command was successful
        if ($return_var !== 0) {
            $msg = "Error executing Excel import tool. Return code: {$return_var}";
            excel_import_log($msg, 'ERROR');
            excel_import_debug($output, 'Error output');
            $results->msg = $msg;
            $results->tool_output = implode("\n", $output);
            return $results;
        }
        
        excel_import_log('Excel Import Tool executed successfully');
        

        excel_import_log("MY LOG ------------------------------------------<<<>>>>>");
        // Check if SQL file exists
        if (!file_exists($sql_filepath)) {
            $msg = "Error: SQL file was not generated at expected location: {$sql_filepath}";
            excel_import_log($msg, 'ERROR');
            $results->msg = $msg;
            $results->tool_output = implode("\n", $output);
            return $results;
        }
        
        // Read the SQL file contents
        excel_import_log('Reading generated SQL file content');
        $sql_content = file_get_contents($sql_filepath);
        
        // Add the USE database statement at the beginning of the SQL file
        if (!empty($argsObj->targetDatabase)) {
            excel_import_log('Adding USE statement for database: ' . $argsObj->targetDatabase);
            $database_name = $argsObj->targetDatabase;
            $use_statement = "USE `{$database_name}`;

";
            $sql_content = $use_statement . $sql_content;
            
            // Write the updated SQL content back to the file
            file_put_contents($sql_filepath, $sql_content);
            excel_import_log('Updated SQL file with USE statement');
        }
        
        // Parse the SQL to count test case imports
        excel_import_log('Parsing SQL to count imported test cases');
        // Look for INSERT INTO nodes_hierarchy statements with node_type_id = 3 (test cases)
        // The pattern needs to be more flexible to match various SQL formats
        preg_match_all("/INSERT INTO `?nodes_hierarchy`?.*node_type_id'?,\s*3/i", $sql_content, $matches);
        
        // If the above pattern doesn't find any matches, try an alternative pattern
        if (empty($matches[0])) {
            preg_match_all("/-- Test Case \d+:/i", $sql_content, $alt_matches);
            $num_imported = count($alt_matches[0]);
            excel_import_log('Used alternative pattern, found ' . $num_imported . ' test cases');
        } else {
            $num_imported = count($matches[0]);
            excel_import_log('Found ' . $num_imported . ' test cases using primary pattern');
        }
        excel_import_log('Counted ' . $num_imported . ' test cases imported');
            
        $results->status_ok = true;
        $results->num_imported = $num_imported;
        $results->sql_file = $sql_filename;
        $results->sql_content = $sql_content;
        $results->sql_file_path = $sql_filepath;
        $results->tool_output = implode("\n", $output); // Store the tool's output
        $results->msg = "SQL file generated with {$num_imported} test cases: {$sql_filename}. File location: {$sql_filepath}";
        
    } catch (Exception $e) {
        $msg = "Error processing Excel file: " . $e->getMessage();
        excel_import_log($msg, 'ERROR');
        $results->msg = $msg;
    }
    
    return $results;
}

/**
 * Get test case status list
 */
function getTestCaseStatusList()
{
    return array(
        1 => 'Draft',
        2 => 'Ready for review',
        3 => 'Review in progress',
        4 => 'Rework',
        5 => 'Obsolete',
        6 => 'Future',
        7 => 'Final'
    );
}

/**
 * Get importance levels
 */
function getImportanceOptions()
{
    return array(
        1 => 'Low',
        2 => 'Medium',
        3 => 'High'
    );
}

/**
 * Get execution options
 */
function getExecutionOptions()
{
    return array(
        1 => 'Manual',
        2 => 'Automated'
    );
}

/**
 * Check for file upload errors
 * 
 * @param object $argsObj Arguments object containing file info
 * @return object Result object with status and message
 */
function checkUploadedFile($argsObj)
{
    $result = new stdClass();
    $result->status_ok = false;
    $result->msg = '';
    
    excel_import_log('Checking uploaded file');
    
    // Check if a file was uploaded
    if (empty($argsObj->fileName) || empty($argsObj->fileTmpName)) {
        $result->msg = 'No file was uploaded';
        excel_import_log($result->msg, 'ERROR');
        return $result;
    }
    
    // Check if the uploaded file exists
    if (!file_exists($argsObj->fileTmpName)) {
        $result->msg = 'Uploaded file not found in temp location';
        excel_import_log($result->msg, 'ERROR');
        return $result;
    }
    
    // Check file type
    $allowed_extensions = array('xls', 'xlsx', 'csv');
    $file_info = pathinfo($argsObj->fileName);
    $file_extension = isset($file_info['extension']) ? strtolower($file_info['extension']) : '';
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $result->msg = "Invalid file type. Only Excel files (.xls, .xlsx, .csv) are allowed. Got: {$file_extension}";
        excel_import_log($result->msg, 'ERROR');
        return $result;
    }
    
    // All checks passed
    $result->status_ok = true;
    excel_import_log('File upload check passed successfully');
    
    return $result;
}

/**
 * Execute SQL file on the database
 * 
 * @param string $sqlFilePath Full path to the SQL file to execute
 * @param string $dbName Database name to use 
 * @return object Result object with execution status and messages
 */
function executeImportedSQL($sqlFilePath, $dbName) {
    $result = new stdClass();
    $result->status_ok = false;
    $result->msg = '';
    $result->details = array(); // Initialize as array
    
    excel_import_log('Attempting to execute SQL file: ' . $sqlFilePath);
    
    if (!file_exists($sqlFilePath)) {
        $result->msg = "Error: SQL file not found at {$sqlFilePath}";
        excel_import_log($result->msg, 'ERROR');
        return $result;
    }
    
    // Read SQL file content
    $sqlContent = file_get_contents($sqlFilePath);
    if (!$sqlContent) {
        $result->msg = "Error: Could not read SQL file content";
        excel_import_log($result->msg, 'ERROR');
        return $result;
    }
    
    // Connect to the database
    $dbHandler = new database($dbName);
    if (!$dbHandler->db->isConnected()) {
        $result->msg = "Error: Could not connect to the database {$dbName}";
        excel_import_log($result->msg, 'ERROR');
        return $result;
    }
    
    excel_import_log('Connected to database: ' . $dbName);
    
    // Split the SQL into separate statements
    $statements = explode(';', $sqlContent);
    $executedCount = 0;
    $errorCount = 0;
    
    excel_import_log('Found ' . count($statements) . ' SQL statements to execute');
    
    // Begin transaction
    $dbHandler->db->exec_query('START TRANSACTION');
    
    // Execute each statement
    foreach ($statements as $statement) {
        $statement = trim($statement);
        
        // Skip empty statements and comments
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue;
        }
        
        // Skip USE statements as we're already connected to the right database
        if (stripos($statement, 'USE') === 0) {
            continue;
        }
        
        // Execute the statement
        $execResult = $dbHandler->db->exec_query($statement);
        
        if ($execResult) {
            $executedCount++;
            // Use array_push instead of [] notation to avoid lint errors
            array_push($result->details, "Success: " . substr($statement, 0, 50) . "...");
        } else {
            $errorCount++;
            $errorMsg = $dbHandler->db->error_msg();
            excel_import_log("SQL Error: {$errorMsg} in statement: {$statement}", 'ERROR');
            array_push($result->details, "Error: {$errorMsg} in: " . substr($statement, 0, 50) . "...");
        }
    }
    
    // If no errors occurred, commit the transaction; otherwise, roll back
    if ($errorCount === 0) {
        $dbHandler->db->exec_query('COMMIT');
        excel_import_log('Transaction committed. All SQL statements executed successfully.');
        $result->status_ok = true;
        $result->msg = "Successfully executed {$executedCount} SQL statements";
    } else {
        $dbHandler->db->exec_query('ROLLBACK');
        excel_import_log('Transaction rolled back due to errors.', 'ERROR');
        $result->msg = "Execution failed. Encountered {$errorCount} errors. Transaction was rolled back.";
    }
    
    return $result;
}

/**
 * Template configuration
 */
function excelImportTemplateConfiguration()
{
    $template_cfg = new stdClass();
    $template_cfg->template_dir = 'admin/';
    $template_cfg->default_template = 'excelImport_simple.tpl';
    return $template_cfg;
}