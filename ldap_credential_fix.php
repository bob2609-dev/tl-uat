<?php
/**
 * TestLink LDAP Credential Fix and Test
 * This script tests and fixes LDAP authentication issues
 */

echo "<h1>TestLink LDAP Credential Fix</h1>";

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
} else {
    output("LDAP extension is NOT loaded! You need to enable it in php.ini", 'error');
    output("PHP.ini location: " . php_ini_loaded_file());
    die();
}

// Target paths
$targetPath = 'D:/xampp/htdocs/tl-uat';
$sourcePath = __DIR__;
output("Target path: $targetPath");

// Function to test LDAP credentials
function testLdapCredentials($server, $port, $bindDN, $password) {
    global $sourcePath;
    
    // Create log file
    $logFile = $sourcePath . '/ldap_test.log';
    $log = "Testing LDAP connection to $server:$port\n";
    $log .= "Bind DN: $bindDN\n";
    file_put_contents($logFile, $log);
    
    // Disable TLS certificate verification
    ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
    
    // Try to connect
    $ldapconn = @ldap_connect($server, $port);
    if (!$ldapconn) {
        $log = "Failed to connect to LDAP server\n";
        file_put_contents($logFile, $log, FILE_APPEND);
        return [false, "Failed to connect to LDAP server"];
    }
    
    // Set protocol version
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    
    // Try to bind
    $bind = @ldap_bind($ldapconn, $bindDN, $password);
    $error = ldap_error($ldapconn);
    
    $log = "Bind result: " . ($bind ? "SUCCESS" : "FAILED") . "\n";
    $log .= "Error: $error\n";
    file_put_contents($logFile, $log, FILE_APPEND);
    
    if ($ldapconn) {
        ldap_close($ldapconn);
    }
    
    return [$bind, $error];
}

// Test form submission
if (isset($_POST['action']) && $_POST['action'] == 'test_credentials') {
    $server = $_POST['server'];
    $port = $_POST['port'];
    $bindDN = $_POST['bind_dn'];
    $password = $_POST['password'];
    
    list($success, $error) = testLdapCredentials($server, $port, $bindDN, $password);
    
    if ($success) {
        output("✅ LDAP connection successful! Your credentials work.", 'success');
        
        // Show update form
        echo "<h2>Update TestLink Configuration</h2>";
        echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
        echo "<input type='hidden' name='action' value='update_config'>";
        echo "<input type='hidden' name='server' value='$server'>";
        echo "<input type='hidden' name='port' value='$port'>";
        echo "<input type='hidden' name='bind_dn' value='$bindDN'>";
        echo "<input type='hidden' name='password' value='$password'>";
        echo "<button type='submit'>Update TestLink Configuration with Working Credentials</button>";
        echo "</form>";
    } else {
        output("❌ LDAP connection failed: $error", 'error');
    }
}

// Update config form submission
if (isset($_POST['action']) && $_POST['action'] == 'update_config') {
    $server = $_POST['server'];
    $port = $_POST['port'];
    $bindDN = $_POST['bind_dn'];
    $password = $_POST['password'];
    
    // Target custom config file
    $configFile = $targetPath . '/custom_config.inc.php';
    if (!file_exists($configFile)) {
        $configFile = $sourcePath . '/custom_config.inc.php';
    }
    
    if (!file_exists($configFile)) {
        output("Could not find custom_config.inc.php", 'error');
    } else {
        // Create backup
        if (!file_exists($configFile . '.bak')) {
            copy($configFile, $configFile . '.bak');
            output("Created backup of custom_config.inc.php", 'success');
        }
        
        // Read config
        $content = file_get_contents($configFile);
        
        // Update the LDAP bind credentials
        $content = preg_replace(
            "/\\\$tlCfg->authentication\['ldap'\]\[0\]\['ldap_bind_dn'\]\s*=\s*'.*?';/", 
            "\$tlCfg->authentication['ldap'][0]['ldap_bind_dn'] = '$bindDN';", 
            $content
        );
        
        $content = preg_replace(
            "/\\\$tlCfg->authentication\['ldap'\]\[0\]\['ldap_bind_passwd'\]\s*=\s*'.*?';/", 
            "\$tlCfg->authentication['ldap'][0]['ldap_bind_passwd'] = '$password';", 
            $content
        );
        
        // Write updated config
        file_put_contents($configFile, $content);
        output("Updated LDAP credentials in custom_config.inc.php", 'success');
        
        // Update for direct user binding
        echo "<h2>LDAP Authentication Options</h2>";
        echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
        echo "<input type='hidden' name='action' value='switch_to_direct'>";
        echo "<p>If service account authentication isn't working, you can try direct user binding:</p>";
        echo "<button type='submit'>Switch to Direct User Binding</button>";
        echo "</form>";
    }
}

// Switch to direct user binding
if (isset($_POST['action']) && $_POST['action'] == 'switch_to_direct') {
    // Target custom config file
    $configFile = $targetPath . '/custom_config.inc.php';
    if (!file_exists($configFile)) {
        $configFile = $sourcePath . '/custom_config.inc.php';
    }
    
    if (!file_exists($configFile)) {
        output("Could not find custom_config.inc.php", 'error');
    } else {
        // Create backup if not already created
        if (!file_exists($configFile . '.bak2')) {
            copy($configFile, $configFile . '.bak2');
            output("Created backup of custom_config.inc.php", 'success');
        }
        
        // Read config
        $content = file_get_contents($configFile);
        
        // Update to use direct binding with user format
        $content = preg_replace(
            "/\\\$tlCfg->authentication\['ldap'\]\[0\]\['ldap_user_dn_format'\]\s*=\s*'.*?';/", 
            "\$tlCfg->authentication['ldap'][0]['ldap_user_dn_format'] = 'cn=%USERNAME%,dc=nmbtz,dc=com';", 
            $content
        );
        
        if (strpos($content, "ldap_user_dn_format") === false) {
            // Add the format if it doesn't exist
            $pos = strpos($content, "\$tlCfg->authentication['ldap'][0]['ldap_root_dn']");
            if ($pos !== false) {
                $content = substr_replace(
                    $content,
                    "\$tlCfg->authentication['ldap'][0]['ldap_user_dn_format'] = 'cn=%USERNAME%,dc=nmbtz,dc=com';\n",
                    $pos,
                    0
                );
            }
        }
        
        // Write updated config
        file_put_contents($configFile, $content);
        output("Updated LDAP configuration to use direct user binding", 'success');
    }
    
    // Create an updated LDAP api file for direct binding
    $ldapApiFile = $targetPath . '/lib/functions/ldap_api.php';
    if (!file_exists($ldapApiFile)) {
        output("Could not find ldap_api.php", 'error');
    } else {
        // Create backup if not already created
        if (!file_exists($ldapApiFile . '.bak2')) {
            copy($ldapApiFile, $ldapApiFile . '.bak2');
            output("Created backup of ldap_api.php", 'success');
        }
        
        // Create a modified version for direct binding
        $directBindContent = <<<'EOD'
<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * Modified for direct user binding and PHP 8 compatibility
 */

require_once(dirname(__FILE__).'/../../config.inc.php');
require_once(dirname(__FILE__).'/common.php');
require_once(dirname(__FILE__).'/users.inc.php');

/**
 * LDAP Authentication class for TestLink
 * @package TestLink
 */
class tlLDAP extends tlObject
{
    /** LDAP configuration options */
    protected $ldapServer;
    protected $ldapPort;
    protected $ldapVersion;
    protected $ldapBaseDN;
    protected $ldapBindDN;
    protected $ldapBindPasswd;
    protected $ldapUserFilterAttr;
    protected $ldapSearchFilter;
    protected $ldapUserDnFormat;
    
    /** TestLink configuration */
    protected $configuration;
    
    // Debug LDAP connection
    private function debugLDAP($msg, $data = null) {
        $debugFile = __DIR__ . '/../../logs/ldap_debug.txt';
        $time = date('Y-m-d H:i:s');
        $text = "[$time] $msg";
        if ($data !== null) {
            $text .= " " . print_r($data, true);
        }
        file_put_contents($debugFile, $text . "\n", FILE_APPEND);
    }
    
    /**
     * Constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->configuration = config_get('authentication');
        
        // Support array-based LDAP configuration
        $this->debugLDAP("Initializing LDAP with config", $this->configuration);
        
        if(isset($this->configuration["ldap"][0]["ldap_server"])) {
            $this->debugLDAP("Using array-based LDAP config");
            $this->ldapServer = $this->configuration["ldap"][0]["ldap_server"];
            $this->ldapPort = $this->configuration["ldap"][0]["ldap_port"];
            $this->ldapVersion = $this->configuration["ldap"][0]["ldap_version"];
            $this->ldapBindDN = $this->configuration["ldap"][0]["ldap_bind_dn"];
            $this->ldapBindPasswd = $this->configuration["ldap"][0]["ldap_bind_passwd"];
            $this->ldapBaseDN = $this->configuration["ldap"][0]["ldap_root_dn"];
            $this->ldapUserFilterAttr = isset($this->configuration["ldap"][0]["ldap_uid_field"]) ? 
                                        $this->configuration["ldap"][0]["ldap_uid_field"] : "sAMAccountName";
            $this->ldapSearchFilter = $this->configuration["ldap"][0]["ldap_organization"];
            $this->ldapUserDnFormat = isset($this->configuration["ldap"][0]["ldap_user_dn_format"]) ? 
                                     $this->configuration["ldap"][0]["ldap_user_dn_format"] : null;
        } else {
            $this->debugLDAP("Using flat LDAP config");
            $this->ldapServer = $this->configuration["ldap_server"];
            $this->ldapPort = $this->configuration["ldap_port"];
            $this->ldapVersion = $this->configuration["ldap_version"];
            $this->ldapBaseDN = $this->configuration["ldap_base_dn"];
            $this->ldapBindDN = $this->configuration["ldap_bind_dn"];
            $this->ldapBindPasswd = $this->configuration["ldap_bind_passwd"];
            $this->ldapUserFilterAttr = $this->configuration["ldap_uid"];
            $this->ldapSearchFilter = $this->configuration["ldap_organization"];
            $this->ldapUserDnFormat = isset($this->configuration["ldap_user_dn_format"]) ? 
                                     $this->configuration["ldap_user_dn_format"] : null;
        }
    }
    
    /**
     * Connect to LDAP server
     *
     * @return resource LDAP connection handler
     */
    public function connect()
    {
        // Disable TLS certificate verification for PHP 8 compatibility
        if (defined('LDAP_OPT_X_TLS_REQUIRE_CERT') && defined('LDAP_OPT_X_TLS_NEVER')) {
            $this->debugLDAP("Disabling TLS certificate verification");
            ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
        }
        
        $this->debugLDAP("Connecting to LDAP", ["server" => $this->ldapServer, "port" => $this->ldapPort]);
        $ldapconn = @ldap_connect($this->ldapServer, $this->ldapPort);
        
        // Force LDAP protocol version 3 for PHP 8 compatibility
        if ($ldapconn) {
            $this->debugLDAP("Setting LDAP protocol version to {$this->ldapVersion}");
            ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, $this->ldapVersion);
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        } else {
            $this->debugLDAP("LDAP connection failed");
        }
        
        return $ldapconn;
    }
    
    /**
     * Authenticate user against LDAP server
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    function authenticate($username, $password)
    {
        $this->debugLDAP("Authentication attempt", ["username" => $username]);
        
        $result = false;
        if(!extension_loaded('ldap')) {
            $this->debugLDAP("LDAP extension not loaded");
            return $result;
        }
        
        $ldapconn = $this->connect();
        if(!$ldapconn) {
            $this->debugLDAP("LDAP connection failed");
            return $result;
        }
        
        // Try direct user binding first if format is specified
        if ($this->ldapUserDnFormat) {
            $userDN = str_replace('%USERNAME%', $username, $this->ldapUserDnFormat);
            $this->debugLDAP("Trying direct user binding", ["userDN" => $userDN]);
            
            $bind = @ldap_bind($ldapconn, $userDN, $password);
            $this->debugLDAP("Direct bind result", ["success" => $bind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
            
            if ($bind) {
                $result = true;
                ldap_close($ldapconn);
                return $result;
            }
        }
        
        // If direct binding fails or is not configured, try search and bind method
        $searchResult = false;
        if ($this->ldapSearchFilter && $this->ldapBaseDN) {
            // First bind with service account if provided
            if ($this->ldapBindDN && $this->ldapBindPasswd) {
                $this->debugLDAP("Binding with service account", ["bindDN" => $this->ldapBindDN]);
                $serviceBind = @ldap_bind($ldapconn, $this->ldapBindDN, $this->ldapBindPasswd);
                $this->debugLDAP("Service account bind result", ["success" => $serviceBind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
                
                if (!$serviceBind) {
                    // Try anonymous bind if service account fails
                    $this->debugLDAP("Trying anonymous bind");
                    $anonBind = @ldap_bind($ldapconn);
                    $this->debugLDAP("Anonymous bind result", ["success" => $anonBind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
                    
                    if (!$anonBind) {
                        ldap_close($ldapconn);
                        return false;
                    }
                }
            } else {
                // No service account, try anonymous bind
                $this->debugLDAP("No service account, trying anonymous bind");
                $anonBind = @ldap_bind($ldapconn);
                $this->debugLDAP("Anonymous bind result", ["success" => $anonBind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
                
                if (!$anonBind) {
                    ldap_close($ldapconn);
                    return false;
                }
            }
            
            // Now search for the user
            $filter = "(&{$this->ldapSearchFilter}({$this->ldapUserFilterAttr}={$username}))";
            $this->debugLDAP("Performing LDAP search", [
                "baseDN" => $this->ldapBaseDN,
                "filter" => $filter
            ]);
            
            $searchResult = @ldap_search(
                $ldapconn,
                $this->ldapBaseDN,
                $filter
            );
        }
        
        if ($searchResult) {
            $this->debugLDAP("LDAP search succeeded");
            $info = ldap_get_entries($ldapconn, $searchResult);
            if ($info["count"] > 0) {
                $bindDN = $info[0]["dn"];
                $this->debugLDAP("Binding with DN from search", ["bindDN" => $bindDN]);
                $bind = @ldap_bind($ldapconn, $bindDN, $password);
                $this->debugLDAP("Bind result", ["success" => $bind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
                $result = $bind;
            } else {
                $this->debugLDAP("No entries found in search");
            }
        }
        
        if ($ldapconn) {
            ldap_close($ldapconn);
        }
        
        return $result;
    }
}
EOD;

        file_put_contents($ldapApiFile, $directBindContent);
        output("Updated ldap_api.php to support direct user binding", 'success');
    }
    
    output("\nDirectly try logging in to TestLink now!", 'success');
}

// Show the test form
if (!isset($_POST['action'])) {
    echo "<h2>LDAP Connection Test</h2>";
    echo "<p>Your LDAP debug logs show that the service account credentials are failing with 'Invalid credentials'.</p>";
    
    echo "<h3>Option 1: Try with Updated Service Account Credentials</h3>";
    echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
    echo "<input type='hidden' name='action' value='test_credentials'>";
    echo "<p>LDAP Server: <input type='text' name='server' value='ldap://10.200.221.11' style='width: 300px;'></p>";
    echo "<p>LDAP Port: <input type='text' name='port' value='389'></p>";
    echo "<p>Bind DN: <input type='text' name='bind_dn' value='CN=Service.testauto,OU=Service Accounts,DC=nmbtz,DC=com' style='width: 400px;'></p>";
    echo "<p>Password: <input type='password' name='password' value=''></p>";
    echo "<p><button type='submit'>Test Connection</button></p>";
    echo "</form>";
    
    echo "<h3>Option 2: Switch to Direct User Binding</h3>";
    echo "<form method='post' action='{$_SERVER['PHP_SELF']}'>";
    echo "<input type='hidden' name='action' value='switch_to_direct'>";
    echo "<p>This option will modify TestLink to use direct user binding instead of a service account.</p>";
    echo "<p>In direct binding mode, each user's credentials are used to bind directly to LDAP.</p>";
    echo "<p><button type='submit'>Switch to Direct User Binding</button></p>";
    echo "</form>";
    
    echo "<h3>Option 3: Use MD5 Authentication Only</h3>";
    echo "<p>If you can't get LDAP working, you can switch to using only MD5 authentication:</p>";
    echo "<ol>";
    echo "<li>Edit custom_config.inc.php</li>";
    echo "<li>Change \$tlCfg->authentication['method'] = 'LDAP,MD5'; to \$tlCfg->authentication['method'] = 'MD5';</li>";
    echo "<li>Restart your web server</li>";
    echo "<li>Log in with your TestLink database credentials</li>";
    echo "</ol>";
}
