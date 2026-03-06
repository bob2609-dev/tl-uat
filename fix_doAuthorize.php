<?php
/**
 * Fix for doAuthorize.php to properly use LDAP authentication
 */

echo "<h1>TestLink doAuthorize.php Fix</h1>";

// Function to output message
function output($message, $type = 'info') {
    $style = '';
    switch($type) {
        case 'success': $style = 'color: green; font-weight: bold;'; break;
        case 'error': $style = 'color: red; font-weight: bold;'; break;
        case 'warning': $style = 'color: orange;'; break;
        default: $style = 'color: blue;';
    }
    echo "<p style=\"$style\">$message</p>\n";
}

// Target paths
$targetPath = 'D:/xampp/htdocs/tl-uat';
output("Target path: $targetPath");

// Fix doAuthorize.php file
$doAuthFile = $targetPath . '/lib/functions/doAuthorize.php';
if (!file_exists($doAuthFile)) {
    output("Could not find doAuthorize.php file at: $doAuthFile", 'error');
    die();
}

// Create backup of doAuthorize.php file
if (!file_exists($doAuthFile . '.bak')) {
    copy($doAuthFile, $doAuthFile . '.bak');
    output("Created backup of doAuthorize.php", 'success');
}

// Read the file
$content = file_get_contents($doAuthFile);

// Find and fix the auth_does_password_match function
if (preg_match('/(function auth_does_password_match.*?return\s+\$result;\s*})/s', $content, $matches)) {
    $oldFunction = $matches[1];
    
    // Create new function content
    $newFunction = "function auth_does_password_match(&\$db,&\$user,\$password)
{
    \$auth_method = config_get('authentication');
    \$result = false;
    
    // Debug log for authentication
    \$debugFile = __DIR__ . '/../../logs/auth_debug.txt';
    \$time = date('Y-m-d H:i:s');
    \$logMsg = "[\$time] Auth attempt for user {\$user->login} using method {\$auth_method['method']}\n";
    file_put_contents(\$debugFile, \$logMsg, FILE_APPEND);

    switch(\$auth_method['method'])
    {
        case 'LDAP':
        case 'LDAP,MD5':
        case 'MD5,LDAP':
            // Handle LDAP authentication first if it's part of the method
            if (strpos(\$auth_method['method'], 'LDAP') !== false) {
                require_once(TL_ABS_PATH . 'lib/functions/ldap_api.php');
                \$ldap = new tlLDAP();
                \$ldapResult = \$ldap->authenticate(\$user->login, \$password);
                file_put_contents(\$debugFile, "[\$time] LDAP auth result: " . (\$ldapResult ? 'success' : 'failure') . "\n", FILE_APPEND);
                
                if (\$ldapResult) {
                    \$result = true;
                    break; // Success, no need to try MD5
                }
            }
            
            // If LDAP fails or is not in the method, try MD5
            if (strpos(\$auth_method['method'], 'MD5') !== false && !\$result) {
                // Needed for PHP > 5.6
                // From PHP Manual: md5() function: "As of PHP 8.0.0, the salt parameter is deprecated."
                file_put_contents(\$debugFile, "[\$time] Trying MD5 auth\n", FILE_APPEND);
                \$user_pwd = \$user->auth->getPassword();
                if (!is_null(\$user_pwd)) {
                    \$result = (\$user_pwd === md5(\$password));
                    file_put_contents(\$debugFile, "[\$time] MD5 auth result: " . (\$result ? 'success' : 'failure') . "\n", FILE_APPEND);
                }
            }
            break;

        case 'MD5':
        default:
            // Handle standard MD5 authentication
            file_put_contents(\$debugFile, "[\$time] Using MD5 auth only\n", FILE_APPEND);
            \$user_pwd = \$user->auth->getPassword();
            if (!is_null(\$user_pwd)) {
                \$result = (\$user_pwd === md5(\$password));
                file_put_contents(\$debugFile, "[\$time] MD5 auth result: " . (\$result ? 'success' : 'failure') . "\n", FILE_APPEND);
            }
            break;
    }
    
    return \$result;
}";
    
    // Replace the function
    $content = str_replace($oldFunction, $newFunction, $content);
    
    // Write the updated file
    file_put_contents($doAuthFile, $content);
    output("Fixed auth_does_password_match function to properly handle LDAP authentication", 'success');
} else {
    output("Could not find auth_does_password_match function in the file", 'error');
}

// Create logs directory if it doesn't exist
$logsDir = $targetPath . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
    output("Created logs directory", 'success');
}

// Create auth debug log file
$logFile = $logsDir . '/auth_debug.txt';
file_put_contents($logFile, "Authentication Debug Log Started: " . date('Y-m-d H:i:s') . "\n");
output("Created authentication debug log file: $logFile", 'success');

output("\nFix complete! Here's what to do next:", 'success');
echo "<ol>";
echo "<li>Restart your Apache web server in XAMPP Control Panel</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try logging in to TestLink again</li>";
echo "<li>If you still have issues, check the debug logs in logs/auth_debug.txt and logs/ldap_debug.txt</li>";
echo "</ol>";
