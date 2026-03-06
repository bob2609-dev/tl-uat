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
    protected $ldapPort = 389;
    protected $ldapVersion = 3;
    protected $ldapServer = '';
    protected $ldapBaseDN = '';
    protected $ldapBindDN = '';
    protected $ldapBindPasswd = '';
    protected $ldapUserFilterAttr = 'uid';
    protected $ldapSearchFilter = '';
    
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
        
        $this->debugLDAP("Initializing LDAP with config", $this->configuration);
        
        // Support array-based LDAP configuration
        if(isset($this->configuration["ldap"][0]["ldap_server"])) {
            $this->debugLDAP("Using array-based LDAP config");
            $this->ldapServer = $this->configuration["ldap"][0]["ldap_server"];
            $this->ldapPort = isset($this->configuration["ldap"][0]["ldap_port"]) ? 
                             $this->configuration["ldap"][0]["ldap_port"] : 389;
            $this->ldapVersion = isset($this->configuration["ldap"][0]["ldap_version"]) ? 
                               $this->configuration["ldap"][0]["ldap_version"] : 3;
            $this->ldapBindDN = isset($this->configuration["ldap"][0]["ldap_bind_dn"]) ? 
                              $this->configuration["ldap"][0]["ldap_bind_dn"] : '';
            $this->ldapBindPasswd = isset($this->configuration["ldap"][0]["ldap_bind_passwd"]) ? 
                                  $this->configuration["ldap"][0]["ldap_bind_passwd"] : '';
            $this->ldapBaseDN = isset($this->configuration["ldap"][0]["ldap_root_dn"]) ? 
                              $this->configuration["ldap"][0]["ldap_root_dn"] : '';
            $this->ldapUserFilterAttr = isset($this->configuration["ldap"][0]["ldap_uid_field"]) ? 
                                       $this->configuration["ldap"][0]["ldap_uid_field"] : 'sAMAccountName';
            $this->ldapSearchFilter = isset($this->configuration["ldap"][0]["ldap_organization"]) ? 
                                    $this->configuration["ldap"][0]["ldap_organization"] : '';
        } else {
            $this->debugLDAP("Using flat LDAP config");
            $this->ldapServer = isset($this->configuration["ldap_server"]) ? 
                              $this->configuration["ldap_server"] : '';
            $this->ldapPort = isset($this->configuration["ldap_port"]) ? 
                             $this->configuration["ldap_port"] : 389;
            $this->ldapVersion = isset($this->configuration["ldap_version"]) ? 
                               $this->configuration["ldap_version"] : 3;
            $this->ldapBaseDN = isset($this->configuration["ldap_base_dn"]) ? 
                              $this->configuration["ldap_base_dn"] : '';
            $this->ldapBindDN = isset($this->configuration["ldap_bind_dn"]) ? 
                              $this->configuration["ldap_bind_dn"] : '';
            $this->ldapBindPasswd = isset($this->configuration["ldap_bind_passwd"]) ? 
                                  $this->configuration["ldap_bind_passwd"] : '';
            $this->ldapUserFilterAttr = isset($this->configuration["ldap_uid"]) ? 
                                       $this->configuration["ldap_uid"] : 'uid';
            $this->ldapSearchFilter = isset($this->configuration["ldap_organization"]) ? 
                                    $this->configuration["ldap_organization"] : '';
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
        
        // First try to bind with the service account if provided
        if ($this->ldapBindDN && $this->ldapBindPasswd) {
            $this->debugLDAP("Binding with service account", ["bindDN" => $this->ldapBindDN]);
            $serviceBind = @ldap_bind($ldapconn, $this->ldapBindDN, $this->ldapBindPasswd);
            $this->debugLDAP("Service account bind result", ["success" => $serviceBind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
            
            if ($serviceBind) {
                // Now search for the user
                if ($this->ldapSearchFilter && $this->ldapBaseDN) {
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
                        $this->debugLDAP("LDAP search failed", ["error" => ldap_error($ldapconn)]);
                    }
                } else {
                    $this->debugLDAP("No search filter or base DN configured");
                }
            } else {
                $this->debugLDAP("Service account bind failed");
                
                // Try direct user bind as a fallback
                $userDN = "cn=$username," . $this->ldapBaseDN;
                $this->debugLDAP("Trying direct user bind", ["userDN" => $userDN]);
                $bind = @ldap_bind($ldapconn, $userDN, $password);
                $this->debugLDAP("Direct bind result", ["success" => $bind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
                $result = $bind;
            }
        } else {
            // No service account, try direct user bind
            $userDN = "cn=$username," . $this->ldapBaseDN;
            $this->debugLDAP("No service account, trying direct user bind", ["userDN" => $userDN]);
            $bind = @ldap_bind($ldapconn, $userDN, $password);
            $this->debugLDAP("Direct bind result", ["success" => $bind ? "yes" : "no", "error" => ldap_error($ldapconn)]);
            $result = $bind;
        }
        
        if ($ldapconn) {
            ldap_close($ldapconn);
        }
        
        return $result;
    }
}

// Diagnostics: confirm class definition reached and tlLDAP is declared
try {
    $authLog = __DIR__ . '/../../logs/auth_trace.log';
    $stamp = date('Y-m-d H:i:s');
    $msg = "[$stamp] ldap_api.php loaded; tlLDAP declared=" . (class_exists('tlLDAP') ? 'yes' : 'no');
    @file_put_contents($authLog, $msg . "\n", FILE_APPEND);
} catch (Exception $e) {
    // ignore
}

/**
 * Utility function to get a field from LDAP for a username
 * 
 * @param array $ldapCfg LDAP configuration
 * @param string $username Username to look up
 * @param string $field Field to retrieve
 * @return string|null Field value or null if not found
 */
function ldap_get_field_from_username($ldapCfg, $username, $field)
{
    if(!extension_loaded('ldap')) {
        return null;
    }
    
    // Get server and port
    $server = isset($ldapCfg['ldap_server']) ? $ldapCfg['ldap_server'] : '';
    $port = isset($ldapCfg['ldap_port']) ? $ldapCfg['ldap_port'] : 389;
    
    if (!$server) {
        return null;
    }
    
    // Disable TLS certificate verification for PHP 8 compatibility
    if (defined('LDAP_OPT_X_TLS_REQUIRE_CERT') && defined('LDAP_OPT_X_TLS_NEVER')) {
        ldap_set_option(NULL, LDAP_OPT_X_TLS_REQUIRE_CERT, LDAP_OPT_X_TLS_NEVER);
    }
    
    $ldapconn = @ldap_connect($server, $port);
    if (!$ldapconn) {
        return null;
    }
    
    // Set options
    $version = isset($ldapCfg['ldap_version']) ? $ldapCfg['ldap_version'] : 3;
    ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, $version);
    ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
    
    // Bind with service account
    $bindDN = isset($ldapCfg['ldap_bind_dn']) ? $ldapCfg['ldap_bind_dn'] : '';
    $bindPwd = isset($ldapCfg['ldap_bind_passwd']) ? $ldapCfg['ldap_bind_passwd'] : '';
    
    if ($bindDN && $bindPwd) {
        $bind = @ldap_bind($ldapconn, $bindDN, $bindPwd);
        if (!$bind) {
            ldap_close($ldapconn);
            return null;
        }
    } else {
        // Anonymous bind
        $bind = @ldap_bind($ldapconn);
        if (!$bind) {
            ldap_close($ldapconn);
            return null;
        }
    }
    
    // Search for user
    $baseDN = isset($ldapCfg['ldap_root_dn']) ? $ldapCfg['ldap_root_dn'] : '';
    $filter = isset($ldapCfg['ldap_organization']) ? $ldapCfg['ldap_organization'] : '';
    $userAttr = isset($ldapCfg['ldap_uid_field']) ? $ldapCfg['ldap_uid_field'] : 'sAMAccountName';
    
    if (!$baseDN || !$filter) {
        ldap_close($ldapconn);
        return null;
    }
    
    $searchFilter = "(&$filter($userAttr=$username))";
    $searchResult = @ldap_search($ldapconn, $baseDN, $searchFilter);
    
    if (!$searchResult) {
        ldap_close($ldapconn);
        return null;
    }
    
    $entries = ldap_get_entries($ldapconn, $searchResult);
    
    if ($entries['count'] == 0) {
        ldap_close($ldapconn);
        return null;
    }
    
    // Get the requested field
    $value = null;
    if (isset($entries[0][$field]) && $entries[0][$field]['count'] > 0) {
        $value = $entries[0][$field][0];
    }
    
    ldap_close($ldapconn);
    return $value;
}