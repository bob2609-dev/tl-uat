<?php
// Include PHP 8 compatibility layer if it exists
if (file_exists('custom/inc/php8_init.php')) {
    require_once('custom/inc/php8_init.php');
}

// Start output buffering to capture errors
ob_start();

echo "<h1>TestLink Configuration Test</h1>";

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

// Test 1: Check main configuration file
echo "<h2>1. Testing config.inc.php</h2>";
try {
    if (file_exists('config.inc.php')) {
        output("config.inc.php found", 'success');
        require_once('config.inc.php');
        output("config.inc.php loaded successfully", 'success');
    } else {
        output("config.inc.php not found!", 'error');
    }
} catch (Exception $e) {
    output("Error loading config.inc.php: " . $e->getMessage(), 'error');
}

// Test 2: Check custom configuration file
echo "<h2>2. Testing custom_config.inc.php</h2>";
try {
    if (file_exists('custom_config.inc.php')) {
        output("custom_config.inc.php found", 'success');
        // Don't include it again if already included
        if (!isset($tlCfg->authentication)) {
            include('custom_config.inc.php');
        }
        output("custom_config.inc.php loaded successfully", 'success');
    } else {
        output("custom_config.inc.php not found!", 'error');
    }
} catch (Exception $e) {
    output("Error loading custom_config.inc.php: " . $e->getMessage(), 'error');
}

// Test 3: Check authentication configuration
echo "<h2>3. Authentication Configuration</h2>";

if (isset($tlCfg) && isset($tlCfg->authentication)) {
    output("Authentication configuration found in \$tlCfg", 'success');
    
    echo "<h3>Authentication Method:</h3>";
    if (isset($tlCfg->authentication['method'])) {
        output("Method: " . $tlCfg->authentication['method'], 'success');
    } else {
        output("Authentication method not set!", 'error');
    }
    
    echo "<h3>LDAP Configuration:</h3>";
    if (isset($tlCfg->authentication['ldap']) && !empty($tlCfg->authentication['ldap'])) {
        output("LDAP configuration found", 'success');
        
        // Display LDAP server details
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Setting</th><th>Value</th></tr>";
        
        foreach ($tlCfg->authentication['ldap'][0] as $key => $value) {
            // Don't show password in plain text
            if ($key === 'ldap_bind_passwd') {
                $value = '********';
            }
            echo "<tr><td>$key</td><td>$value</td></tr>";
        }
        
        echo "</table>";
    } else {
        output("LDAP configuration not found or empty!", 'error');
    }
} else {
    output("\$tlCfg->authentication not found!", 'error');
}

// Test 4: Test config_get function
echo "<h2>4. Testing config_get() Function</h2>";
try {
    if (function_exists('config_get')) {
        output("config_get() function exists", 'success');
        $authCfg = config_get('authentication');
        
        echo "<h3>Results from config_get('authentication'):</h3>";
        echo "<pre>" . print_r($authCfg, true) . "</pre>";
        
        if (isset($authCfg['method'])) {
            if ($authCfg['method'] === $tlCfg->authentication['method']) {
                output("config_get returns the correct authentication method: " . $authCfg['method'], 'success');
            } else {
                output("MISMATCH: config_get returns '" . $authCfg['method'] . "' but custom_config.inc.php has '" . 
                       $tlCfg->authentication['method'] . "'", 'error');
            }
        } else {
            output("Authentication method not found in config_get result!", 'error');
        }
    } else {
        output("config_get() function does not exist!", 'error');
    }
} catch (Exception $e) {
    output("Error testing config_get: " . $e->getMessage(), 'error');
}

// Test 5: Test LDAP connection if LDAP is configured
echo "<h2>5. LDAP Connection Test</h2>";

if (isset($tlCfg->authentication['ldap']) && !empty($tlCfg->authentication['ldap']) && function_exists('ldap_connect')) {
    output("PHP LDAP extension is installed", 'success');
    
    try {
        $ldapServer = $tlCfg->authentication['ldap'][0]['ldap_server'];
        $ldapPort = $tlCfg->authentication['ldap'][0]['ldap_port'];
        $ldapVersion = $tlCfg->authentication['ldap'][0]['ldap_version'];
        $bindDN = $tlCfg->authentication['ldap'][0]['ldap_bind_dn'];
        $bindPwd = $tlCfg->authentication['ldap'][0]['ldap_bind_passwd'];
        
        output("Attempting to connect to LDAP server: $ldapServer:$ldapPort", 'info');
        
        $ldapConn = ldap_connect($ldapServer, $ldapPort);
        if ($ldapConn) {
            output("LDAP connection established", 'success');
            
            // Set LDAP options
            ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, $ldapVersion);
            ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
            
            // Try binding
            output("Attempting to bind with DN: $bindDN", 'info');
            $ldapBind = @ldap_bind($ldapConn, $bindDN, $bindPwd);
            
            if ($ldapBind) {
                output("LDAP bind successful! Your LDAP connection is working correctly.", 'success');
            } else {
                output("LDAP bind failed: " . ldap_error($ldapConn), 'error');
            }
            
            ldap_close($ldapConn);
        } else {
            output("Failed to connect to LDAP server", 'error');
        }
    } catch (Exception $e) {
        output("LDAP test error: " . $e->getMessage(), 'error');
    }
} else {
    if (!function_exists('ldap_connect')) {
        output("PHP LDAP extension is not installed! This is required for LDAP authentication.", 'error');
    } else {
        output("LDAP configuration not found or empty", 'error');
    }
}

// End output buffering and display any errors
$output = ob_get_clean();
echo $output;

// Display additional PHP info
echo "<h2>6. PHP Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check if LDAP extension is loaded
echo "<p>LDAP Extension: " . (extension_loaded('ldap') ? 'Loaded' : 'Not Loaded') . "</p>";

// List loaded extensions
echo "<h3>Loaded Extensions:</h3>";
echo "<pre>" . implode(", ", get_loaded_extensions()) . "</pre>";
?>
