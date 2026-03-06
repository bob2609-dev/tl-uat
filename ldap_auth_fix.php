<?php
/**
 * TestLink LDAP Authentication Fix
 * This script properly fixes LDAP authentication
 */

echo "<h1>TestLink LDAP Authentication Fix</h1>";

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

// Find files to fix
$targetPath = 'D:/xampp/htdocs/tl-uat';
output("Target path: $targetPath");

// Check if directory exists
if (!file_exists($targetPath)) {
    output("Target directory does not exist", 'error');
    die();
}

// Create logs directory if it doesn't exist
$logsDir = $targetPath . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
    output("Created logs directory", 'success');
}

// Fix the doAuthorize.php file
$doAuthFile = $targetPath . '/lib/functions/doAuthorize.php';
if (!file_exists($doAuthFile)) {
    output("Could not find doAuthorize.php file", 'error');
    die();
}

// Create backup of original file
if (!file_exists($doAuthFile . '.original')) {
    copy($doAuthFile, $doAuthFile . '.original');
    output("Created backup of original doAuthorize.php", 'success');
}

// Create the fixed auth_does_password_match function
$fixedFunction = <<<'EOD'
/**
 * @return object
 *         obj->status_ok = true/false
 *         obj->msg = message to explain what has happened to a human being.
 */
function auth_does_password_match(&$db,&$user,$password)
{
  $debugFile = __DIR__ . '/../../logs/auth_debug.txt';
  $time = date('Y-m-d H:i:s');
  file_put_contents($debugFile, "[$time] Auth attempt for user {$user->login}\n", FILE_APPEND);
  
  $authCfg = config_get('authentication');
  file_put_contents($debugFile, "[$time] Auth method: {$authCfg['method']}\n", FILE_APPEND);
  
  $ret = new stdClass();
  $ret->status_ok = false;
  $ret->msg = sprintf(lang_get('unknown_authentication_method'),$authCfg['method']);
  
  switch($authCfg['method']) {
    case 'LDAP':
    case 'LDAP,MD5':
    case 'MD5,LDAP':
      // Try LDAP first if it's part of the method
      if (strpos($authCfg['method'], 'LDAP') !== false) {
        file_put_contents($debugFile, "[$time] Trying LDAP auth for {$user->login}\n", FILE_APPEND);
        require_once(__DIR__ . '/ldap_api.php');
        $ldap = new tlLDAP();
        
        try {
          $ldapResult = $ldap->authenticate($user->login, $password);
          file_put_contents($debugFile, "[$time] LDAP auth result: " . ($ldapResult ? 'success' : 'failure') . "\n", FILE_APPEND);
          
          if ($ldapResult) {
            $ret->status_ok = true;
            $ret->msg = 'ok';
            file_put_contents($debugFile, "[$time] LDAP auth successful\n", FILE_APPEND);
            return $ret; // Success, no need to try MD5
          }
        } catch (Exception $e) {
          file_put_contents($debugFile, "[$time] LDAP auth exception: {$e->getMessage()}\n", FILE_APPEND);
        }
      }
      
      // If LDAP fails or is not in the method, try MD5
      if (strpos($authCfg['method'], 'MD5') !== false) {
        file_put_contents($debugFile, "[$time] Trying MD5 auth\n", FILE_APPEND);
        
        // Check if user has auth property and it's not null
        if (property_exists($user, 'auth') && $user->auth !== null) {
          $user_pwd = $user->auth->getPassword();
          if (!is_null($user_pwd)) {
            $ret->status_ok = ($user_pwd === md5($password));
            $ret->msg = 'ok';
            file_put_contents($debugFile, "[$time] MD5 auth result: " . ($ret->status_ok ? 'success' : 'failure') . "\n", FILE_APPEND);
          } else {
            file_put_contents($debugFile, "[$time] MD5 auth failed: password is null\n", FILE_APPEND);
          }
        } else {
          file_put_contents($debugFile, "[$time] MD5 auth failed: user->auth is null\n", FILE_APPEND);
        }
      }
      break;

    case 'MD5':
    case 'DB':
    default:
      file_put_contents($debugFile, "[$time] Using MD5/DB auth only\n", FILE_APPEND);
      // Handle standard MD5 authentication with proper null checks
      if (property_exists($user, 'auth') && $user->auth !== null) {
        $user_pwd = $user->auth->getPassword();
        if (!is_null($user_pwd)) {
          $ret->status_ok = ($user_pwd === md5($password));
          $ret->msg = 'ok';
          file_put_contents($debugFile, "[$time] MD5 auth result: " . ($ret->status_ok ? 'success' : 'failure') . "\n", FILE_APPEND);
        } else {
          file_put_contents($debugFile, "[$time] MD5 auth failed: password is null\n", FILE_APPEND);
        }
      } else {
        file_put_contents($debugFile, "[$time] MD5 auth failed: user->auth is null\n", FILE_APPEND);
      }
      break;
  }
  
  return $ret;
}
EOD;

// Create a simple pattern to find the function
$pattern = '/function auth_does_password_match[\s\S]*?\n}\s*\n/'; 

// Read the file
$content = file_get_contents($doAuthFile);

// Replace the function
if (preg_match($pattern, $content)) {
    $content = preg_replace($pattern, $fixedFunction . "\n", $content);
    file_put_contents($doAuthFile, $content);
    output("Successfully fixed auth_does_password_match function", 'success');
} else {
    output("Could not find auth_does_password_match function in the file", 'error');
}

// Create custom ldap_api.php file
$ldapApiFile = $targetPath . '/lib/functions/ldap_api.php';
if (!file_exists($ldapApiFile)) {
    output("Could not find ldap_api.php file", 'error');
    die();
}

// Create backup of original file
if (!file_exists($ldapApiFile . '.original')) {
    copy($ldapApiFile, $ldapApiFile . '.original');
    output("Created backup of original ldap_api.php", 'success');
}

// Create fixed LDAP API file
$fixedLdapApi = <<<'EOD'
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
EOD;

// Write the fixed LDAP API file
file_put_contents($ldapApiFile, $fixedLdapApi);
output("Successfully fixed ldap_api.php file", 'success');

// Create auth debug log file
$authLogFile = $logsDir . '/auth_debug.txt';
file_put_contents($authLogFile, "Authentication Debug Log Started: " . date('Y-m-d H:i:s') . "\n");
output("Created authentication debug log file: $authLogFile", 'success');

// Create LDAP debug log file
$ldapLogFile = $logsDir . '/ldap_debug.txt';
file_put_contents($ldapLogFile, "LDAP Debug Log Started: " . date('Y-m-d H:i:s') . "\n");
output("Created LDAP debug log file: $ldapLogFile", 'success');

output("\nLDAP authentication fix complete!", 'success');
echo "<ol>";
echo "<li>Restart your Apache web server in XAMPP Control Panel</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try logging in to TestLink again with your LDAP credentials</li>";
echo "<li>If you still have issues, check the debug logs at:</li>";
echo "<ul>";
echo "<li>$authLogFile - For authentication process details</li>";
echo "<li>$ldapLogFile - For LDAP connection details</li>";
echo "</ul>";
echo "</ol>";
