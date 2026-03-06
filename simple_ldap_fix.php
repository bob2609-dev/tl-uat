<?php
/**
 * Simple TestLink LDAP Fix
 * This script makes a direct edit to the LDAP API file
 */

echo "<h1>Simple TestLink LDAP Fix</h1>";

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

// Create logs directory if it doesn't exist
$logsDir = $targetPath . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
    output("Created logs directory", 'success');
}

// Create a simple replacement LDAP API file
$newContent = <<<'EOD'
<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource  ldap_api.php
 * @package     TestLink
 * @author      Asiel Brumfield
 * @copyright   2005-2020, TestLink community
 * @link        http://www.testlink.org
 *
 **/

require_once(dirname(__FILE__).'/../../config.inc.php');
require_once(dirname(__FILE__).'/common.php');
require_once(dirname(__FILE__).'/users.inc.php');

/**
 * LDAP Authentication class for TestLink
 * @package TestLink
 */
class tlLDAP extends tlObject
{
    /** LDAP servers list */
    protected $ldapServers;
    
    /** LDAP configuration options */
    protected $ldapPort;
    protected $ldapVersion;
    protected $ldapServer;
    protected $ldapBaseDN;
    protected $ldapBindDN;
    protected $ldapBindPasswd;
    protected $ldapUserFilterAttr;
    protected $ldapSearchFilter;
    
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
            $this->ldapUserFilterAttr = "sAMAccountName";
            $this->ldapSearchFilter = $this->configuration["ldap"][0]["ldap_organization"];
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
        
        $searchResult = false;
        if ($this->ldapSearchFilter && $this->ldapBaseDN) {
            $this->debugLDAP("Performing LDAP search", [
                "baseDN" => $this->ldapBaseDN,
                "filter" => "(&{$this->ldapSearchFilter}({$this->ldapUserFilterAttr}={$username}))"
            ]);
            
            $searchResult = @ldap_search(
                $ldapconn,
                $this->ldapBaseDN,
                "(&{$this->ldapSearchFilter}({$this->ldapUserFilterAttr}={$username}))"
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
        } else {
            // Bind with the provided username and password
            $bindDN = str_replace('%USERNAME%', $username, $this->ldapBindDN);
            $this->debugLDAP("Binding with DN", ["bindDN" => $bindDN]);
            $bind = @ldap_bind($ldapconn, $bindDN, $password);
            $this->debugLDAP("Bind result", ["success" => $bind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
            
            // Try anonymous bind as fallback
            if (!$bind) {
                $this->debugLDAP("Trying anonymous bind");
                $anonBind = @ldap_bind($ldapconn);
                $this->debugLDAP("Anonymous bind result", ["success" => $anonBind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
            }
            
            $result = $bind;
        }
        
        if ($ldapconn) {
            ldap_close($ldapconn);
        }
        
        return $result;
    }
}
EOD;

// Write the new content to the file
file_put_contents($ldapApiFile, $newContent);
output("Replaced ldap_api.php with fixed version that supports array-based configuration", 'success');

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
