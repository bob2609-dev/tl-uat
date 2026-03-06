<?php
/**
 * TestLink LDAP Authentication Fix - Simple Version
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
    
    // Create a simple debug function to add to the LDAP class
    $debugFunction = "\n    // Debug LDAP connection\n    private function debugLDAP(\$msg, \$data = null) {\n        \$debugFile = __DIR__ . '/../../logs/ldap_debug.txt';\n        \$time = date('Y-m-d H:i:s');\n        \$text = \"[\$time] \$msg\";\n        if (\$data !== null) {\n            \$text .= \" \" . print_r(\$data, true);\n        }\n        file_put_contents(\$debugFile, \$text . \"\\n\", FILE_APPEND);\n    }\n";
    
    // Check if the class definition exists
    if (preg_match('/class tlLDAP\s*\{/i', $content)) {
        // Add the debug function to the class
        $content = preg_replace('/class tlLDAP\s*\{/i', "class tlLDAP {\n$debugFunction", $content);
        
        // Add debug call at the start of authenticate function
        $content = preg_replace(
            '/function authenticate\(\$username, \$password\)/i',
            "function authenticate(\$username, \$password) {\n        \$this->debugLDAP(\"Authentication attempt\", [\"username\" => \$username]);",
            $content
        );
        
        // Add debug call after LDAP connect
        $content = preg_replace(
            '/\$ldapconn = @?ldap_connect\(\$this->ldapServer, \$this->ldapPort\);/i',
            "\$ldapconn = @ldap_connect(\$this->ldapServer, \$this->ldapPort);\n        \$this->debugLDAP(\"LDAP Connect\", [\"server\" => \$this->ldapServer, \"port\" => \$this->ldapPort]);",
            $content
        );
        
        // Add debug call after LDAP bind
        $content = preg_replace(
            '/\$bind = @?ldap_bind\(\$ldapconn, \$bindDN, \$password\);/i',
            "\$bind = @ldap_bind(\$ldapconn, \$bindDN, \$password);\n        \$this->debugLDAP(\"LDAP Bind\", [\"success\" => \$bind ? \"yes\" : \"no\", \"error\" => ldap_error(\$ldapconn)]);",
            $content
        );
        
        // Write updated content
        file_put_contents($ldapAuthFile, $content);
        log_message("Added LDAP debugging to authentication class");
    } else {
        log_message("Could not find tlLDAP class in ldap_api.php");
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

log_message("\nLDAP fix complete! Here's what to do next:");
log_message("1. Make sure the LDAP extension is enabled in PHP");
log_message("2. Restart your web server and try logging in again");
log_message("3. Check the debug log at: $targetPath/logs/ldap_debug.txt to see what's happening");
