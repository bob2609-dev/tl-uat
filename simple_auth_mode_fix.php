<?php
/**
 * Simple Auth Mode Switch
 * This script switches TestLink to MD5-only authentication
 */

echo "<h1>TestLink Authentication Mode Switch</h1>";

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

// Find and update custom_config.inc.php
$targetPath = 'D:/xampp/htdocs/tl-uat';
$configFile = $targetPath . '/custom_config.inc.php';

if (!file_exists($configFile)) {
    $configFile = __DIR__ . '/custom_config.inc.php';
    output("Using local config file: $configFile", 'warning');
}

if (!file_exists($configFile)) {
    output("Could not find custom_config.inc.php", 'error');
    die();
}

// Create backup
if (!file_exists($configFile . '.bak_md5')) {
    copy($configFile, $configFile . '.bak_md5');
    output("Created backup of custom_config.inc.php", 'success');
}

// Read config
$content = file_get_contents($configFile);

// Check if authentication method is already set to MD5
if (preg_match('/\$tlCfg->authentication\[\'method\'\]\s*=\s*\'MD5\';/', $content)) {
    output("Authentication method is already set to MD5", 'warning');
} else {
    // Replace authentication method
    $content = preg_replace(
        '/\$tlCfg->authentication\[\'method\'\]\s*=\s*\'.*?\';/', 
        "\$tlCfg->authentication['method'] = 'MD5';", 
        $content
    );
    
    // Write updated config
    file_put_contents($configFile, $content);
    output("Updated authentication method to MD5 only", 'success');
}

output("\nAuthentication mode change complete!", 'success');
echo "<ol>";
echo "<li>Restart your Apache web server in XAMPP Control Panel</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try logging in to TestLink again using your database credentials (not LDAP)</li>";
echo "</ol>";

echo "<p>If you don't have a TestLink database user account, you will need to create one:</p>";
echo "<ol>";
echo "<li>Look for the TestLink database in your MySQL server</li>";
echo "<li>Locate the users table</li>";
echo "<li>Create a new user with MD5 password using phpMyAdmin or similar tool</li>";
echo "</ol>";
