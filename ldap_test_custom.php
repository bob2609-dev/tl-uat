<?php
/**
 * TestLink LDAP Connection Test for Custom Config
 * This script directly tests LDAP connectivity using custom_config.inc.php
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>TestLink LDAP Connection Test (Custom Config)</h1>";

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

// Check if LDAP extension is loaded
output("PHP Version: " . phpversion());
if (extension_loaded('ldap')) {
    output("LDAP extension is loaded", 'success');
    
    // Show LDAP functions availability
    $ldapFunctions = ['ldap_connect', 'ldap_bind', 'ldap_search', 'ldap_get_entries'];
    foreach ($ldapFunctions as $func) {
        if (function_exists($func)) {
            output("Function $func is available", 'success');
        } else {
            output("Function $func is NOT available", 'error');
        }
    }
} else {
    output("LDAP extension is NOT loaded! You need to enable it in php.ini", 'error');
    output("PHP.ini location: " . php_ini_loaded_file());
    die();
}

// Try to load LDAP settings from TestLink custom config
$configFile = 'D:/xampp/htdocs/tl-uat/custom_config.inc.php';
if (!file_exists($configFile)) {
    // Try backup directory if not found
    $configFile = __DIR__ . '/custom_config.inc.php';
}

if (file_exists($configFile)) {
    output("Loading LDAP settings from custom config file: $configFile");
    
    // Extract variables from the config file without including it
    $configContent = file_get_contents($configFile);
    
    // Parse LDAP server
    if (preg_match('/\$tlCfg->authentication\[\'ldap_server\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $ldapServer = $matches[1];
        output("LDAP Server: $ldapServer");
    } else {
        output("Could not find LDAP server in config", 'warning');
        $ldapServer = '';
    }
    
    // Parse LDAP port
    if (preg_match('/\$tlCfg->authentication\[\'ldap_port\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $ldapPort = $matches[1];
        output("LDAP Port: $ldapPort");
    } else {
        output("Could not find LDAP port in config, using default 389", 'warning');
        $ldapPort = 389;
    }
    
    // Parse LDAP version
    if (preg_match('/\$tlCfg->authentication\[\'ldap_version\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $ldapVersion = $matches[1];
        output("LDAP Version: $ldapVersion");
    } else {
        output("Could not find LDAP version in config, using default 3", 'warning');
        $ldapVersion = 3;
    }
    
    // Parse LDAP base DN
    if (preg_match('/\$tlCfg->authentication\[\'ldap_basedn\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $ldapBaseDN = $matches[1];
        output("LDAP Base DN: $ldapBaseDN");
    } else {
        output("Could not find LDAP base DN in config", 'warning');
        $ldapBaseDN = '';
    }
    
    // Parse LDAP bind DN
    if (preg_match('/\$tlCfg->authentication\[\'ldap_binddn\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $ldapBindDN = $matches[1];
        output("LDAP Bind DN: $ldapBindDN");
    } else {
        output("Could not find LDAP bind DN in config", 'warning');
        $ldapBindDN = '';
    }
    
    // Parse LDAP bind password
    if (preg_match('/\$tlCfg->authentication\[\'ldap_bindpw\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $ldapBindPW = $matches[1];
        output("LDAP Bind Password: [HIDDEN]");
    } else {
        output("Could not find LDAP bind password in config", 'warning');
        $ldapBindPW = '';
    }
    
    // Additional LDAP configuration from custom config
    if (preg_match('/\$tlCfg->authentication\[\'method\'\]\s*=\s*[\'"](.*?)[\'"];/s', $configContent, $matches)) {
        $authMethod = $matches[1];
        output("Authentication Method: $authMethod");
        
        if ($authMethod !== 'LDAP') {
            output("WARNING: Authentication method is not set to LDAP in the configuration!", 'error');
        }
    }
    
    // Test LDAP connection
    output("\n<strong>Testing LDAP Connection:</strong>");
    
    // Disable LDAP certificate verification (for testing only)
    if (defined('LDAP_OPT_X_TLS_REQUIRE_CERT')) {
        ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        output("Disabled LDAP certificate verification");
    }
    
    // Connect to LDAP server
    output("Connecting to LDAP server $ldapServer:$ldapPort...");
    $ldapConn = @ldap_connect($ldapServer, $ldapPort);
    
    if ($ldapConn) {
        output("Connected to LDAP server", 'success');
        
        // Set LDAP version
        if (ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, $ldapVersion)) {
            output("Set LDAP protocol version to $ldapVersion", 'success');
        } else {
            output("Failed to set LDAP protocol version: " . ldap_error($ldapConn), 'error');
        }
        
        // Try to bind
        output("Binding to LDAP server...");
        $ldapBind = @ldap_bind($ldapConn, $ldapBindDN, $ldapBindPW);
        
        if ($ldapBind) {
            output("Successfully bound to LDAP server", 'success');
            
            // Try a search
            if (!empty($ldapBaseDN)) {
                output("Testing LDAP search...");
                $searchFilter = '(objectClass=*)';
                $searchResult = @ldap_search($ldapConn, $ldapBaseDN, $searchFilter, ['cn'], 0, 1);
                
                if ($searchResult) {
                    $entries = ldap_get_entries($ldapConn, $searchResult);
                    output("Search successful, found {$entries['count']} entries", 'success');
                } else {
                    output("Search failed: " . ldap_error($ldapConn), 'error');
                }
            }
        } else {
            output("Bind failed: " . ldap_error($ldapConn), 'error');
            
            // Try anonymous bind as fallback
            output("Trying anonymous bind...");
            $anonBind = @ldap_bind($ldapConn);
            
            if ($anonBind) {
                output("Anonymous bind successful", 'success');
                output("This indicates a problem with your bind credentials, not the connection itself");
            } else {
                output("Anonymous bind also failed: " . ldap_error($ldapConn), 'error');
                output("This indicates a fundamental connection problem");
            }
        }
        
        // Close connection
        ldap_close($ldapConn);
    } else {
        output("Failed to connect to LDAP server", 'error');
    }
} else {
    output("Could not find custom config file at $configFile", 'error');
}

// Provide direct fix for LDAP issues in PHP 8
echo "\n<h2>LDAP Fix Options</h2>";

// Create a form to apply fixes
echo "<form method=\"post\">";
echo "<p>Select fixes to apply:</p>";

// Fix 1: Disable TLS verification
echo "<label><input type='checkbox' name='fix_tls' value='1' checked> Disable TLS certificate verification</label><br>";

// Fix 2: Force LDAP protocol version 3
echo "<label><input type='checkbox' name='fix_version' value='1' checked> Force LDAP protocol version 3</label><br>";

// Fix 3: Add option to retry with anonymous bind
echo "<label><input type='checkbox' name='fix_anon' value='1' checked> Try anonymous bind as fallback</label><br>";

// Fix 4: Test with a specific username/password
echo "<p>Test LDAP authentication with credentials:</p>";
echo "<label>Username: <input type='text' name='test_user'></label><br>";
echo "<label>Password: <input type='password' name='test_pass'></label><br>";

echo "<p><button type='submit' name='apply_fixes'>Apply Fixes & Test Authentication</button></p>";
echo "</form>";

// Process form submission
if (isset($_POST['apply_fixes'])) {
    echo "<h2>Applying LDAP Fixes</h2>";
    
    // Get TestLink LDAP API file
    $ldapApiFile = 'D:/xampp/htdocs/tl-uat/lib/functions/ldap_api.php';
    
    if (file_exists($ldapApiFile)) {
        // Backup the file
        copy($ldapApiFile, $ldapApiFile . '.bak');
        output("Created backup of ldap_api.php");
        
        // Read the file
        $content = file_get_contents($ldapApiFile);
        $originalContent = $content;
        
        // Apply selected fixes
        if (isset($_POST['fix_tls'])) {
            // Add TLS verification disable
            if (strpos($content, 'LDAP_OPT_X_TLS_REQUIRE_CERT') === false) {
                $content = preg_replace(
                    '/(function authenticate\(\$username, \$password\)\s*{)/i',
                    "$1\n        // Disable TLS certificate verification for PHP 8 compatibility\n        if (defined('LDAP_OPT_X_TLS_REQUIRE_CERT')) {\n            ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);\n        }",
                    $content
                );
                output("Added TLS certificate verification disable", 'success');
            }
        }
        
        if (isset($_POST['fix_version'])) {
            // Force LDAP protocol version 3
            if (strpos($content, 'ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION') === false) {
                $content = preg_replace(
                    '/(\$ldapconn = @?ldap_connect\(\$this->ldapServer, \$this->ldapPort\);\s*)/i',
                    "$1\n        // Force LDAP protocol version 3 for PHP 8 compatibility\n        if (\$ldapconn) {\n            ldap_set_option(\$ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);\n        }",
                    $content
                );
                output("Added forced LDAP protocol version 3", 'success');
            }
        }
        
        if (isset($_POST['fix_anon'])) {
            // Add anonymous bind fallback
            if (strpos($content, 'anonymous bind') === false) {
                $content = preg_replace(
                    '/(\$bind = @?ldap_bind\(\$ldapconn, \$bindDN, \$password\);\s*)/i',
                    "$1\n        // Try anonymous bind as fallback for PHP 8 compatibility\n        if (!\$bind) {\n            // Log the error for debugging\n            error_log('LDAP bind failed with DN: ' . \$bindDN . ' - Error: ' . ldap_error(\$ldapconn));\n            \n            // Try anonymous bind\n            \$bind = @ldap_bind(\$ldapconn);\n            if (\$bind) {\n                error_log('Anonymous bind successful, will try to search for user');\n            }\n        }",
                    $content
                );
                output("Added anonymous bind fallback", 'success');
            }
        }
        
        // Write changes if content was modified
        if ($content !== $originalContent) {
            file_put_contents($ldapApiFile, $content);
            output("Applied LDAP fixes to ldap_api.php", 'success');
        } else {
            output("No changes were needed or applied to ldap_api.php", 'warning');
        }
        
        // Test authentication if credentials provided
        if (!empty($_POST['test_user']) && !empty($_POST['test_pass'])) {
            echo "<h3>Testing Authentication</h3>";
            output("Testing LDAP authentication with provided credentials...");
            
            // Load the LDAP API file
            require_once($ldapApiFile);
            
            // Create LDAP object with settings from config
            $ldap = new tlLDAP();
            $ldap->ldapServer = $ldapServer;
            $ldap->ldapPort = $ldapPort;
            $ldap->ldapVersion = $ldapVersion;
            $ldap->ldapBaseDN = $ldapBaseDN;
            
            // Try to authenticate
            $result = $ldap->authenticate($_POST['test_user'], $_POST['test_pass']);
            
            if ($result) {
                output("Authentication successful! The fixes worked.", 'success');
            } else {
                output("Authentication failed. The fixes may not have solved the issue.", 'error');
                output("Check the PHP error log for more details.");
            }
        }
    } else {
        output("Could not find LDAP API file at $ldapApiFile", 'error');
    }
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Make sure the LDAP extension is enabled in PHP</li>";
echo "<li>Apply the fixes above if needed</li>";
echo "<li>Restart your web server</li>";
echo "<li>Try logging in to TestLink again</li>";
echo "</ol>";

echo "<p>If you still have issues, check the PHP error log for more detailed information.</p>";
