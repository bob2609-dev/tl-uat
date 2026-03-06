<?php
/**
 * TestLink LDAP Fix for Array Structure
 * This script fixes LDAP authentication for your specific configuration structure
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TestLink LDAP Fix for Array Configuration</h1>";

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
$sourcePath = __DIR__;
output("Target path: $targetPath");
output("Source path: $sourcePath");

// Check if LDAP extension is loaded
output("PHP Version: " . phpversion());
if (extension_loaded('ldap')) {
    output("LDAP extension is loaded", 'success');
} else {
    output("LDAP extension is NOT loaded! You need to enable it in php.ini", 'error');
    output("PHP.ini location: " . php_ini_loaded_file());
    die();
}

// Load settings from custom config
$configFile = $targetPath . '/custom_config.inc.php';
if (!file_exists($configFile)) {
    // Try backup directory
    $configFile = $sourcePath . '/custom_config.inc.php';
}

if (file_exists($configFile)) {
    output("Found custom config at: $configFile", 'success');
    
    // Read config without including it
    $configContent = file_get_contents($configFile);
    
    // Get authentication method
    if (preg_match('/\$tlCfg->authentication\[\'method\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $authMethod = $matches[1];
        output("Authentication Method: $authMethod");
        
        if (strpos($authMethod, 'LDAP') === false) {
            output("WARNING: LDAP is not in the authentication methods!", 'error');
        }
    }
    
    // Check for LDAP array format
    if (preg_match('/\$tlCfg->authentication\[\'ldap\'\]\[0\]\[\'ldap_server\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        output("Found array-based LDAP configuration", 'success');
        $ldapServer = $matches[1];
        output("LDAP Server: $ldapServer");
    } else {
        output("Could not find array-based LDAP configuration", 'error');
    }
} else {
    output("Could not find custom config file", 'error');
    die();
}

// Fix LDAP authentication class
$ldapApiFile = $targetPath . '/lib/functions/ldap_api.php';
if (!file_exists($ldapApiFile)) {
    output("Could not find LDAP API file at: $ldapApiFile", 'error');
    die();
}

// Create backup of LDAP API file
if (!file_exists($ldapApiFile . '.bak')) {
    copy($ldapApiFile, $ldapApiFile . '.bak');
    output("Created backup of ldap_api.php", 'success');
}

// Read the file
$content = file_get_contents($ldapApiFile);
$originalContent = $content;

// Create logs directory if it doesn't exist
$logsDir = $targetPath . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
    output("Created logs directory", 'success');
}

// Add debug logging to the LDAP class
$debugFunction = "\n    // Debug LDAP connection\n    private function debugLDAP(\$msg, \$data = null) {\n        \$debugFile = __DIR__ . '/../../logs/ldap_debug.txt';\n        \$time = date('Y-m-d H:i:s');\n        \$text = \"[\$time] \$msg\";\n        if (\$data !== null) {\n            \$text .= \" \" . print_r(\$data, true);\n        }\n        file_put_contents(\$debugFile, \$text . \"\\n\", FILE_APPEND);\n    }\n";

// Add the debug function to the tlLDAP class
if (preg_match('/class tlLDAP\s*\{/i', $content)) {
    // Check if debug function already exists
    if (strpos($content, 'debugLDAP') === false) {
        $content = preg_replace('/class tlLDAP\s*\{/i', "class tlLDAP {\n$debugFunction", $content);
        output("Added debug function to LDAP class", 'success');
    }
}

// Fix 1: Add TLS certificate verification disable
if (strpos($content, 'LDAP_OPT_X_TLS_REQUIRE_CERT') === false) {
    $content = preg_replace(
        '/(function authenticate\(\$username, \$password\)\s*{)/i',
        "$1\n        // Disable TLS certificate verification for PHP 8 compatibility\n        \$this->debugLDAP(\"Disabling TLS cert verification\");\n        if (defined('LDAP_OPT_X_TLS_REQUIRE_CERT') && defined('LDAP_OPT_X_TLS_NEVER')) {\n            ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);\n        }",
        $content
    );
    output("Added TLS certificate verification disable", 'success');
}

// Fix 2: Force LDAP protocol version 3
if (strpos($content, 'ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION') === false) {
    $content = preg_replace(
        '/(\$ldapconn = @?ldap_connect\(\$this->ldapServer, \$this->ldapPort\);\s*)/i',
        "$1\n        // Force LDAP protocol version 3 for PHP 8 compatibility\n        if (\$ldapconn) {\n            \$this->debugLDAP(\"Setting LDAP protocol version 3\");\n            ldap_set_option(\$ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);\n        }",
        $content
    );
    output("Added forced LDAP protocol version 3", 'success');
}

// Fix 3: Add debugging for LDAP connection
$content = preg_replace(
    '/(\$ldapconn = @?ldap_connect\(\$this->ldapServer, \$this->ldapPort\);\s*)/i',
    "\$this->debugLDAP(\"Connecting to LDAP\", [\"server\" => \$this->ldapServer, \"port\" => \$this->ldapPort]);\n        $1",
    $content
);

// Fix 4: Add debugging for LDAP binding
$content = preg_replace(
    '/(\$bind = @?ldap_bind\(\$ldapconn, \$bindDN, \$password\);\s*)/i',
    "\$this->debugLDAP(\"Binding to LDAP\", [\"bindDN\" => \$bindDN]);\n        $1\n        \$this->debugLDAP(\"Bind result\", [\"success\" => \$bind ? \"yes\" : \"no\", \"error\" => ldap_error(\$ldapconn)]);",
    $content
);

// Fix 5: Fix initialization from config
// This is a critical fix for the array-based configuration
if (strpos($content, 'if(isset($this->configuration["ldap"][0][')) === false) {
    // Find the constructor
    if (preg_match('/(function __construct\(\)\s*{[^}]+})/s', $content, $matches)) {
        $constructor = $matches[1];
        $newConstructor = str_replace(
            '$this->ldapServer = $this->configuration["ldap_server"];',
            '// Support array-based LDAP configuration\n' .
            '        if(isset($this->configuration["ldap"][0]["ldap_server"])) {\n' .
            '            $this->ldapServer = $this->configuration["ldap"][0]["ldap_server"];\n' .
            '            $this->ldapPort = $this->configuration["ldap"][0]["ldap_port"];\n' .
            '            $this->ldapVersion = $this->configuration["ldap"][0]["ldap_version"];\n' .
            '            $this->ldapBindDN = $this->configuration["ldap"][0]["ldap_bind_dn"];\n' .
            '            $this->ldapBindPasswd = $this->configuration["ldap"][0]["ldap_bind_passwd"];\n' .
            '            $this->ldapBaseDN = $this->configuration["ldap"][0]["ldap_root_dn"];\n' .
            '            $this->ldapUserFilterAttr = "sAMAccountName";\n' .
            '            $this->ldapSearchFilter = $this->configuration["ldap"][0]["ldap_organization"];\n' .
            '            $this->debugLDAP("Using array-based LDAP config");\n' .
            '        } else {\n' .
            '            $this->ldapServer = $this->configuration["ldap_server"];',
            $constructor
        );
        
        $content = str_replace($constructor, $newConstructor, $content);
        output("Added support for array-based LDAP configuration", 'success');
    }
}

// Write changes if content was modified
if ($content !== $originalContent) {
    file_put_contents($ldapApiFile, $content);
    output("Applied LDAP fixes to ldap_api.php", 'success');
} else {
    output("No changes were needed or made to ldap_api.php", 'warning');
}

// Create empty log file
$logFile = $logsDir . '/ldap_debug.txt';
file_put_contents($logFile, "LDAP Debug Log Started: " . date('Y-m-d H:i:s') . "\n");
output("Created LDAP debug log file: $logFile", 'success');

output("\nLDAP fix complete! Here's what to do next:", 'success');
echo "<ol>";
echo "<li>Restart your Apache web server in XAMPP Control Panel</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try logging in to TestLink again</li>";
echo "<li>If you still have issues, check the debug log at: $logFile</li>";
echo "</ol>";
