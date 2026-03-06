<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 *
 * Custom configuration for TestLink
 * 
 * SCOPE: Critical configurations, particularly email settings and LDAP authentication
 */

// *******************************************************************************
// Email Configuration Settings
// *******************************************************************************
// Enable verbose LDAP logging
$tlCfg->log_level = 'DEBUG';
// $tlCfg->loggerFilter = array('DEBUG','AUDIT','WARNING','ERROR');
// SMTP server Configuration
$g_smtp_host = '10.200.221.17';  
// Email addresses for notifications
$g_tl_admin_email = 'mwaimu.mtingele@nmbtz.com'; 
$g_from_email = 'projectspace-test@nmbtz.com';  
$g_return_path_email = 'mwaimu.mtingele@nmbtz.com';

$g_smtp_debug_level = 3;
// PHPMailer method - set to SMTP
$g_phpMailer_method = PHPMAILER_METHOD_SMTP;

// Email priority (5 = low, 1 = high, 0 = disabled)
$g_mail_priority = 1;

// SMTP port
$g_smtp_port = 25;

// SMTP connection mode (empty string, 'ssl', or 'tls')
$g_smtp_connection_mode = '';

// SMTP authentication (if required)
$g_smtp_username = '';
$g_smtp_password = '';

// Email API configuration (this is crucial for TestLink to send emails correctly)
$tlCfg->email_api = new stdClass();
$tlCfg->email_api->from_address = $g_from_email;
$tlCfg->email_api->from_label = 'TestLink Notification System';
$tlCfg->email_api->return_path = $g_return_path_email;
$tlCfg->email_api->smtp_host = $g_smtp_host;
$tlCfg->email_api->smtp_port = $g_smtp_port;
$tlCfg->email_api->emailFormat = 'html';

// Disable auto TLS to avoid certificate issues
$g_SMTPAutoTLS = false;

// *******************************************************************************
// Image Display Fix Configuration
// *******************************************************************************
// This section adds custom scripts to fix attachment/image display issues

// Add our image fix script to all pages
$tlCfg->hooks['header'] = isset($tlCfg->hooks['header']) ? $tlCfg->hooks['header'] : array();
$tlCfg->hooks['header'][] = 'custom/inc/image_fix_header.php';

// Enable notifications
$tlCfg->notifications = new stdClass();
$tlCfg->notifications->notify_on_test_assign = true;
$tlCfg->notifications->notify_on_test_execution = true;
$tlCfg->notifications->notify_on_build_creation = true;

// *******************************************************************************
// LDAP Authentication Configuration
// *******************************************************************************
// LDAP Authentication Configuration
$tlCfg->authentication['method'] = 'LDAP,MD5';

// LDAP server settings
$tlCfg->authentication['ldap'] = array(); // Initialize as empty array
$tlCfg->authentication['ldap'][0] = array(); // Add first LDAP server

// Server details
$tlCfg->authentication['ldap'][0]['ldap_server'] = 'ldap://10.200.221.11';
$tlCfg->authentication['ldap'][0]['ldap_port'] = 389;
$tlCfg->authentication['ldap'][0]['ldap_version'] = 3;
$tlCfg->authentication['ldap'][0]['ldap_start_tls'] = false;
$tlCfg->authentication['ldap'][0]['ldap_tls'] = false;

// Connection timeout (in seconds)
$tlCfg->authentication['ldap'][0]['ldap_timeout'] = 5;

// Binding credentials
$tlCfg->authentication['ldap'][0]['ldap_bind_dn'] = 'CN=Service.testauto,OU=Service Accounts,DC=nmbtz,DC=com';
$tlCfg->authentication['ldap'][0]['ldap_bind_passwd'] = 'p@ssw0rd';

// Search settings
$tlCfg->authentication['ldap'][0]['ldap_organization'] = '(objectClass=user)';
$tlCfg->authentication['ldap'][0]['ldap_root_dn'] = 'dc=nmbtz,dc=com';
$tlCfg->authentication['ldap'][0]['ldap_uid_field'] = 'sAMAccountName';





// *******************************************************************************
// Performance Optimization Configuration
// *******************************************************************************
// /** @global boolean Disable execution counters for performance optimization */
$g_disable_execution_counters = false;


// User auto-creation
$tlCfg->authentication['ldap'][0]['ldap_automatic_user_creation'] = true;
$tlCfg->authentication['ldap'][0]['ldap_email_field'] = 'mail';
$tlCfg->authentication['ldap'][0]['ldap_firstname_field'] = 'givenname';
$tlCfg->authentication['ldap'][0]['ldap_surname_field'] = 'sn';
$tlCfg->authentication['ldap'][0]['ldap_user_dn_format'] = '';
$tlCfg->authentication['ldap'][0]['ldap_default_role_id'] = 8;


// $tlCfg->config_check_warning_mode = 'SILENT';

// At the end of your custom_config.inc.php file, add this:

// *******************************************************************************
// Bug Tracker Integration Configuration
// *******************************************************************************
// Custom Redmine Integration with Bug Creation Support
// This uses our special redminecreator class to avoid dependency issues
// --------------------------------------------------------

// Enable bug tracking integration
$tlCfg->interface_bugs = true;
$tlCfg->exec_cfg->user_can_create_bugs = true;
$tlCfg->issue_tracker_enabled = true;

// Direct Redmine Bug Link Configuration - Bypass TestLink's complex integration
// This is similar to our successful approach with image display issues
$g_interface_bugs = array('REDMINE');
$g_interface_bugs_map = array('REDMINE' => 'redmineminimal');
$g_interface_bugs_format = array('REDMINE' => 'https://support.profinch.com/issues/%s');

// Force direct href format for bug links
define('BUG_TRACK_HREF', 'https://support.profinch.com/issues/%s');

// Configure our custom issue tracker
if (!property_exists($tlCfg, 'issueTracker')) {
    $tlCfg->issueTracker = new stdClass();
}

// Define the Redmine tracker configuration
$tlCfg->issueTracker->toolsDefaultValues = array();
$tlCfg->issueTracker->toolsDefaultValues['redmine'] = array();
$tlCfg->issueTracker->toolsDefaultValues['redmine']['urlencode_ctl'] = 0;
$tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] = 'https://support.profinch.com';
$tlCfg->issueTracker->toolsDefaultValues['redmine']['apikey'] = 'c16548f2503932a9ef6d6d8f9a59393436e67f39';
$tlCfg->issueTracker->toolsDefaultValues['redmine']['default_assignee_id'] = 2635; // Set your desired user ID here
// Include our dummy Redmine configuration
// require_once('dummy_redmine_config.php');

// Include our serialization fix for Redmine
require_once('custom/inc/redmine_serialization_fix.php');

// Include our text file upload fix
// require_once('custom/inc/txt_file_fix.php');

// Include our JavaScript fixes for Redmine bug display
// Disabled the problematic file with syntax error
// require_once('custom/inc/redmine_js_fix.php');
// Use our new fixed version instead
// require_once('custom/inc/redmine_js_fix_new.php');
// require_once('custom/inc/redmine_bug_display_fix.php');
// require_once('custom/inc/bug_display_debug.php');

// Use our direct bug display fix instead
require_once('custom/inc/direct_bug_display_fix.php');

// Register our functions to run in the header
if (!isset($tlCfg->hooks)) {
    $tlCfg->hooks = array();
}

if (!isset($tlCfg->hooks['header'])) {
    $tlCfg->hooks['header'] = array();
}

// Use our direct bug display fix function
$tlCfg->hooks['header'][] = 'direct_bug_display_fix';

// Add our custom CSS fix for UI issues
function custom_css_fix() {
    echo '<link rel="stylesheet" type="text/css" href="custom/css/custom_fixes.css">';
}
$tlCfg->hooks['header'][] = 'custom_css_fix';

// Add our Redmine integration JavaScript
function add_redmine_js() {
    echo '<script type="text/javascript" src="redmine_hook.js"></script>';
}
$tlCfg->hooks['header'][] = 'add_redmine_js';



// auto check checkboxes in execution
$tlCfg->exec_cfg->exec_mode->addLinkToTLChecked = true;
$tlCfg->exec_cfg->exec_mode->addLinkToTLPrintViewChecked = true;

// *******************************************************************************
// Production Error Handling Configuration (VAPT Item 11 Fix)
// *******************************************************************************
// SECURITY: Disable error display to prevent path disclosure and sensitive information leakage
// Errors are logged to file instead of being displayed to users

// Disable display of errors to prevent information disclosure
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

// Enable error logging to file
ini_set('log_errors', '1');
ini_set('error_log', TL_ABS_PATH . 'logs/php_errors.log');

// Set appropriate error reporting level (exclude notices and deprecations in production)
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);

// Set custom error handler for production-safe error messages
set_error_handler('tl_production_error_handler');

/**
 * Custom error handler that logs errors without exposing sensitive information
 * 
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where error occurred
 * @param int $errline Line number where error occurred
 * @return bool
 */
function tl_production_error_handler($errno, $errstr, $errfile, $errline) {
    // Don't execute PHP internal error handler
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    // Log the full error details to file
    $error_types = array(
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED'
    );
    
    $error_type = isset($error_types[$errno]) ? $error_types[$errno] : 'UNKNOWN';
    $log_message = sprintf(
        "[%s] %s: %s in %s on line %d",
        date('Y-m-d H:i:s'),
        $error_type,
        $errstr,
        $errfile,
        $errline
    );
    
    error_log($log_message);
    
    // For fatal errors, show a generic message to the user
    if ($errno == E_USER_ERROR || $errno == E_ERROR || $errno == E_RECOVERABLE_ERROR) {
        echo "<div style='padding:20px; background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; margin:20px;'>";
        echo "<h3>An error occurred</h3>";
        echo "<p>We're sorry, but an error has occurred while processing your request. ";
        echo "The error has been logged and will be reviewed by the system administrator.</p>";
        echo "<p>Please try again later or contact support if the problem persists.</p>";
        echo "</div>";
        exit(1);
    }
    
    // Don't execute PHP internal error handler
    return true;
}

// Allow virtually all common file types for attachments
$tlCfg->attachments->allowed_files = 'doc,DOC,xls,XLS,gif,GIF,png,PNG,jpg,JPG,xlsx,XLSX,csv,CSV,jpeg,JPEG,pdf,PDF,xml,XML,ppt,PPT,pptx,PPTX,eml,EML,zip,ZIP,rar,RAR,7z,txt,TXT,log,LOG,sql,SQL,json,JSON,html,HTML,htm,HTM,css,CSS,js,JS,php,PHP,java,JAVA,py,PY,c,C,cpp,CPP,h,H,rb,RB,pl,PL,sh,SH,bat,BAT,cmd,CMD,exe,EXE,dll,DLL,so,SO,jar,JAR,war,WAR,ear,EAR,class,CLASS,mp3,MP3,mp4,MP4,avi,AVI,mov,MOV,wmv,WMV,flv,FLV,wav,WAV,ogg,OGG,docx,DOCX,rtf,RTF,tex,TEX,odt,ODT,ods,ODS,odp,ODP,svg,SVG,bmp,BMP,tif,TIF,tiff,TIFF,ico,ICO';

// Remove filename restrictions to allow any naming convention
$tlCfg->attachments->allowed_filenames_regexp = '';  // Empty string means no restrictions


// Increase execution time for bug submission to prevent timeouts
ini_set('max_execution_time', 300);  // 5 minutes

// Increase memory limit
ini_set('memory_limit', '256M');

// *******************************************************************************
// Session Security Configuration (VAPT Item 12 Fix)
// *******************************************************************************
// SECURITY: Configure secure session cookies with HttpOnly, Secure, and SameSite flags
// This prevents XSS attacks from stealing session cookies and CSRF attacks

// Set session cookie parameters for maximum security
ini_set('session.cookie_httponly', '1');  // Prevent JavaScript access to session cookie
ini_set('session.cookie_secure', '1');     // Only send cookie over HTTPS
ini_set('session.cookie_samesite', 'Strict'); // Strict SameSite policy for CSRF protection
ini_set('session.use_only_cookies', '1');  // Don't allow session ID in URL
ini_set('session.use_strict_mode', '1');   // Reject uninitialized session IDs

// Session timeout and regeneration settings
ini_set('session.gc_maxlifetime', '3600'); // Session expires after 1 hour of inactivity
ini_set('session.cookie_lifetime', '0');   // Cookie expires when browser closes

// Use stronger session ID hashing
ini_set('session.hash_function', 'sha256');
ini_set('session.hash_bits_per_character', '5');

// Prevent session fixation attacks
ini_set('session.use_trans_sid', '0');     // Don't pass session ID in URLs

// Session name (change from default PHPSESSID for additional security)
ini_set('session.name', 'TLSESSID');

/**
 * Enhanced session security handler
 * Regenerates session ID periodically and validates session integrity
 */
function tl_session_security_check() {
    // Check if session exists
    if (!isset($_SESSION)) {
        return;
    }
    
    // Session timeout check (1 hour of inactivity)
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 3600)) {
        // Session expired due to inactivity
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['session_expired'] = true;
        return;
    }
    $_SESSION['LAST_ACTIVITY'] = time();
    
    // Regenerate session ID every 30 minutes to prevent fixation
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } else if (time() - $_SESSION['CREATED'] > 1800) {
        // Regenerate session ID but keep session data
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }
    
    // Validate session fingerprint to detect hijacking
    $current_fingerprint = md5(
        $_SERVER['HTTP_USER_AGENT'] . 
        $_SERVER['REMOTE_ADDR']
    );
    
    if (isset($_SESSION['FINGERPRINT'])) {
        if ($_SESSION['FINGERPRINT'] !== $current_fingerprint) {
            // Possible session hijacking detected
            tLog('Possible session hijacking detected for user: ' . (isset($_SESSION['userID']) ? $_SESSION['userID'] : 'unknown'), 'WARNING');
            session_unset();
            session_destroy();
            session_start();
            $_SESSION['security_violation'] = true;
            return;
        }
    } else {
        $_SESSION['FINGERPRINT'] = $current_fingerprint;
    }
}

// Register the security check to run on every request
// Note: This will be called after session_start() in the application
if (isset($_SESSION)) {
    tl_session_security_check();
}
?>

 