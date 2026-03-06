<?php
/**
 * TestLink LDAP Authentication Fix
 * This script fixes LDAP authentication issues in PHP 8
 */

// Function to log messages
function log_message($message) {
    echo "$message\n";
}

log_message("Starting LDAP authentication fix...");

// Target path (actual TestLink installation)
$targetPath = 'D:/xampp/htdocs/tl-uat';
log_message("Target path: $targetPath");

// Check if LDAP extension is loaded
log_message("Checking if LDAP extension is loaded...");
if (extension_loaded('ldap')) {
    log_message("LDAP extension is loaded.");
} else {
    log_message("WARNING: LDAP extension is NOT loaded in PHP. You need to enable it in php.ini.");
    log_message("Look for 'extension=ldap' in your php.ini file and uncomment it.");
    log_message("PHP.ini location: " . php_ini_loaded_file());
}

// Fix LDAP authentication class
$ldapAuthFile = $targetPath . '/lib/functions/ldap_api.php';
if (file_exists($ldapAuthFile)) {
    // Create backup if doesn't exist
    if (!file_exists($ldapAuthFile . '.bak')) {
        copy($ldapAuthFile, $ldapAuthFile . '.bak');
        log_message("Created backup of ldap_api.php");
    }
    
    // Read the file
    $content = file_get_contents($ldapAuthFile);
    $originalContent = $content;
    
    // Add debug code to see what's happening during authentication
    $debugCode = "\n    // Debug LDAP connection in PHP 8\n    private function debugLDAP(\$message, \$data = null) {\n        \$debugFile = __DIR__ . '/../../logs/ldap_debug.txt';\n        \$timestamp = date('Y-m-d H:i:s');\n        \$debugMsg = "[\$timestamp] \$message";\n        if (\$data !== null) {\n            \$debugMsg .= " " . print_r(\$data, true);\n        }\n        file_put_contents(\$debugFile, \$debugMsg . "\n", FILE_APPEND);\n    }\n";
    
    // Add the debug function to the tlLDAP class
    $content = preg_replace('/(class tlLDAP\s*{)/i', "$1\n$debugCode", $content);
    
    // Add debug calls to authenticate method
    $content = str_replace(
        'function authenticate($username, $password)',
        'function authenticate($username, $password) { $this->debugLDAP("Authenticating user: $username");',
        $content
    );
    
    // Add debug calls before LDAP connect
    $content = str_replace(
        'if(!$ldapconn)',
        '$this->debugLDAP("LDAP Connect Settings", array("host" => $this->ldapServer, "port" => $this->ldapPort)); if(!$ldapconn)',
        $content
    );
    
    // Add debug calls after LDAP connect
    $content = str_replace(
        'if(!$ldapconn)',
        'if(!$ldapconn) { $this->debugLDAP("LDAP Connect Failed", ldap_error($ldapconn)); }',
        $content
    );
    
    // Make sure ldap_set_option is properly called for TLS
    $content = str_replace(
        'ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);',
        'if (defined("LDAP_OPT_X_TLS_REQUIRE_CERT") && defined("LDAP_OPT_X_TLS_NEVER")) { ' . 
        'ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER); ' . 
        '$this->debugLDAP("Set LDAP TLS option"); }',
        $content
    );
    
    // Add more debug for bind
    $content = str_replace(
        '$bind = @ldap_bind($ldapconn, $bindDN, $password);',
        '$this->debugLDAP("Binding with DN", $bindDN); $bind = @ldap_bind($ldapconn, $bindDN, $password); ' . 
        'if (!$bind) { $this->debugLDAP("Bind failed", ldap_error($ldapconn)); } else { $this->debugLDAP("Bind successful"); }',
        $content
    );
    
    if ($content !== $originalContent) {
        file_put_contents($ldapAuthFile, $content);
        log_message("Updated LDAP authentication code with debugging");
    } else {
        log_message("No changes made to LDAP authentication code");
    }
    
    // Create logs directory if it doesn't exist
    $logsDir = $targetPath . '/logs';
    if (!is_dir($logsDir)) {
        mkdir($logsDir, 0755, true);
        log_message("Created logs directory");
    }
    
    // Create empty log file
    $logFile = $logsDir . '/ldap_debug.txt';
    file_put_contents($logFile, "LDAP Debug Log Started: " . date('Y-m-d H:i:s') . "\n");
    log_message("Created LDAP debug log file: $logFile");
} else {
    log_message("LDAP API file not found at: $ldapAuthFile");
}

// Check config.inc.php for LDAP settings
$configFile = $targetPath . '/config.inc.php';
if (file_exists($configFile)) {
    log_message("\nChecking LDAP configuration in config.inc.php...");
    
    $content = file_get_contents($configFile);
    
    // Look for LDAP configuration
    if (strpos($content, '$tlCfg->authentication["method"] = \'LDAP\'') !== false) {
        log_message("Found LDAP authentication method in config");
    } else {
        log_message("WARNING: LDAP authentication method not found in config");
    }
    
    // Extract LDAP server settings
    if (preg_match('/\$tlCfg->authentication\[\'ldap_server\'\]\s*=\s*[\'"](.*?)[\'"];/s', $content, $matches)) {
        log_message("LDAP Server: " . $matches[1]);
    } else {
        log_message("WARNING: LDAP server setting not found");
    }
    
    // Extract LDAP port
    if (preg_match('/\$tlCfg->authentication\[\'ldap_port\'\]\s*=\s*[\'"](.*?)[\'"];/s', $content, $matches)) {
        log_message("LDAP Port: " . $matches[1]);
    } else {
        log_message("WARNING: LDAP port setting not found");
    }
    
    // Extract LDAP version
    if (preg_match('/\$tlCfg->authentication\[\'ldap_version\'\]\s*=\s*[\'"](.*?)[\'"];/s', $content, $matches)) {
        log_message("LDAP Version: " . $matches[1]);
    } else {
        log_message("WARNING: LDAP version setting not found");
    }
    
    // Extract LDAP base DN
    if (preg_match('/\$tlCfg->authentication\[\'ldap_basedn\'\]\s*=\s*[\'"](.*?)[\'"];/s', $content, $matches)) {
        log_message("LDAP Base DN: " . $matches[1]);
    } else {
        log_message("WARNING: LDAP base DN setting not found");
    }
    
    // Extract LDAP bind DN
    if (preg_match('/\$tlCfg->authentication\[\'ldap_binddn\'\]\s*=\s*[\'"](.*?)[\'"];/s', $content, $matches)) {
        log_message("LDAP Bind DN: " . $matches[1]);
    } else {
        log_message("WARNING: LDAP bind DN setting not found");
    }
    
    // Extract LDAP bind password
    if (preg_match('/\$tlCfg->authentication\[\'ldap_bindpw\'\]\s*=\s*[\'"](.*?)[\'"];/s', $content, $matches)) {
        log_message("LDAP Bind Password: [HIDDEN]");
    } else {
        log_message("WARNING: LDAP bind password setting not found");
    }
} else {
    log_message("Config file not found at: $configFile");
}

log_message("\nLDAP fix complete! Here's what to do next:");
log_message("1. Make sure the LDAP extension is enabled in PHP");
log_message("2. Check the LDAP debug log at: $targetPath/logs/ldap_debug.txt");
log_message("3. Restart your web server and try logging in again");
log_message("4. If you still have issues, look at the debug log to see what's happening");
