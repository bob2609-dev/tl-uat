<?php
/**
 * Direct Fix for doAuthorize.php
 * This script directly edits the file causing the error
 */

echo "<h1>TestLink Authentication Direct Fix</h1>";

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

// Target file
$targetPath = 'D:/xampp/htdocs/tl-uat';
$doAuthFile = $targetPath . '/lib/functions/doAuthorize.php';
output("Target file: $doAuthFile");

if (!file_exists($doAuthFile)) {
    output("Could not find doAuthorize.php file", 'error');
    die();
}

// Create backup
if (!file_exists($doAuthFile . '.original')) {
    copy($doAuthFile, $doAuthFile . '.original');
    output("Created backup of original doAuthorize.php", 'success');
}

// Read the file content
$content = file_get_contents($doAuthFile);

// Check if it contains the problematic function call
if (strpos($content, 'ldap_authenticate') !== false) {
    // Find and replace the problematic code - Option 1: Fix LDAP auth
    $content = str_replace(
        "if (ldap_authenticate($user->login, $password)) {", 
        "require_once('ldap_api.php');\n      \$ldap = new tlLDAP();\n      if (\$ldap->authenticate(\$user->login, \$password)) {", 
        $content
    );
    
    // Option 2: Completely replace the auth_does_password_match function
    if (preg_match('/function auth_does_password_match[^}]+}/s', $content, $matches)) {
        $oldFunction = $matches[0];
        
        $newFunction = "function auth_does_password_match(&\$db,&\$user,\$password)
{\n    \$auth_method = config_get('authentication');\n    \$result = false;\n    switch(\$auth_method['method']) {\n        case 'LDAP':\n        case 'LDAP,MD5':\n        case 'MD5,LDAP':\n            // Handle LDAP authentication first if it's part of the method\n            if (strpos(\$auth_method['method'], 'LDAP') !== false) {\n                require_once('ldap_api.php');\n                \$ldap = new tlLDAP();\n                \$result = \$ldap->authenticate(\$user->login, \$password);\n                \n                if (\$result) {\n                    break; // Success, no need to try MD5\n                }\n            }\n            \n            // If LDAP fails or is not in the method, try MD5\n            if (strpos(\$auth_method['method'], 'MD5') !== false && !\$result) {\n                \$user_pwd = \$user->auth->getPassword();\n                if (!is_null(\$user_pwd)) {\n                    \$result = (\$user_pwd === md5(\$password));\n                }\n            }\n            break;\n\n        case 'MD5':\n        default:\n            // Handle standard MD5 authentication\n            \$user_pwd = \$user->auth->getPassword();\n            if (!is_null(\$user_pwd)) {\n                \$result = (\$user_pwd === md5(\$password));\n            }\n            break;\n    }\n    \n    return \$result;\n}";
        
        $content = str_replace($oldFunction, $newFunction, $content);
    }
    
    // Write the updated content back to the file
    file_put_contents($doAuthFile, $content);
    output("Fixed the undefined function ldap_authenticate() in doAuthorize.php", 'success');
    
    // Option 3: If all else fails, switch to MD5 only authentication
    echo "<h2>Alternative Option: Switch to MD5 Authentication Only</h2>";
    echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
    echo "<input type='hidden' name='action' value='switch_to_md5'>";
    echo "<p>If LDAP authentication continues to fail, you can switch to MD5 authentication only:</p>";
    echo "<button type='submit'>Switch to MD5 Authentication Only</button>";
    echo "</form>";
} else {
    output("Could not find 'ldap_authenticate' function call in the file", 'warning');
}

// Handle switch to MD5 only
if (isset($_POST['action']) && $_POST['action'] == 'switch_to_md5') {
    $configFile = $targetPath . '/custom_config.inc.php';
    if (file_exists($configFile)) {
        // Create backup
        if (!file_exists($configFile . '.md5.bak')) {
            copy($configFile, $configFile . '.md5.bak');
            output("Created backup of custom_config.inc.php", 'success');
        }
        
        // Read config
        $configContent = file_get_contents($configFile);
        
        // Replace authentication method
        $configContent = preg_replace(
            "/\\\$tlCfg->authentication\['method'\]\s*=\s*'.*?';/", 
            "\$tlCfg->authentication['method'] = 'MD5';", 
            $configContent
        );
        
        // Write updated config
        file_put_contents($configFile, $configContent);
        output("Updated authentication method to MD5 only", 'success');
    } else {
        output("Could not find custom_config.inc.php", 'error');
    }
}

output("\nAction complete! Here's what to do next:", 'success');
echo "<ol>";
echo "<li>Restart your Apache web server in XAMPP Control Panel</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try logging in to TestLink again</li>";
echo "</ol>";
