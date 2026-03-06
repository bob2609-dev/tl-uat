<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * This script is distributed under the GNU General Public License 2 or later. 
 * 
 * Handles the initial authentication for login and creates all user session variables.
 *
 * @filesource  doAuthorize.php
 * @package     TestLink
 * @author      Chad Rosen, Martin Havlat,Francisco Mancardi
 * @copyright   2003-2018, TestLink community 
 * @link        http://www.testlink.org
 *
 */

require_once("users.inc.php");
require_once("roles.inc.php");
require_once(__DIR__ . '/ldap_api.php');

/**
 * Helper function to log authentication messages
 */
function auth_log($message, $data = null) {
  try {
    $logDir = __DIR__ . '/../../logs';
    
    // Create logs directory if it doesn't exist
    if (!is_dir($logDir)) {
      if (!mkdir($logDir, 0755, true)) {
        error_log('TestLink AUTH: Failed to create log directory: ' . $logDir);
        return false;
      }
    }
    
    // Check if logs directory is writable
    if (!is_writable($logDir)) {
      error_log('TestLink AUTH: Logs directory is not writable: ' . $logDir);
      return false;
    }
    
    $logFile = $logDir . '/auth_trace.log';
    $time = date('Y-m-d H:i:s');
    $text = "[$time] $message";
    
    if ($data !== null) {
      $text .= " " . print_r($data, true);
    }
    
    // Check if file exists and is writable or doesn't exist but directory is writable
    if ((file_exists($logFile) && is_writable($logFile)) || (!file_exists($logFile) && is_writable($logDir))) {
      $result = file_put_contents($logFile, $text . "\n", FILE_APPEND);
      if ($result === false) {
        error_log('TestLink AUTH: Failed to write to log file: ' . $logFile);
        return false;
      }
      return true;
    } else {
      error_log('TestLink AUTH: Log file is not writable: ' . $logFile);
      return false;
    }
  } catch (Exception $e) {
    error_log('TestLink AUTH: Exception in auth_log: ' . $e->getMessage());
    return false;
  }
}

/** 
 * authorization function verifies login & password and set user session data 
 * return map
 *
 * we need an option to skip existent session block, in order to use
 * feature that requires login when session has expired and user has some data
 * not saved. (ajaxlogin on login.php page)
 */
function doAuthorize(&$db,$login,$pwd,$options=null) {
  global $g_tlLogger;
  auth_log("doAuthorize() called", ["login" => $login]);

  $result = array('status' => tl::ERROR, 'msg' => null);
  $_SESSION['locale'] = TL_DEFAULT_LOCALE;
  auth_log("Set locale to " . TL_DEFAULT_LOCALE);

  if( null == $options ) {
    $options = new stdClass();   
    $options->doSessionExistsCheck = true;
    $options->auth = null;
  }

  $login = trim($login);
  $pwd = trim($pwd);
  $doChecks = true;
  if($login == '') {
    $doChecks = false;
    $result['msg'] = ' ';
    auth_log("Login is empty, doChecks set to false");    
  } 

  $isOauth = false;
  if( property_exists($options, 'auth') ) {
    $isOauth = strpos($options->auth,'oauth') !== false;
  }

  $loginExists = false;
  $loginExpired = false;
  $doLogin = false;

  if( $doChecks && !is_null($login)) {
    auth_log("Checking if login exists", ["login" => $login]);
    $user = new tlUser();
    $user->login = $login;
    $searchBy = tlUser::USER_O_SEARCH_BYLOGIN;
    if( $isOauth ) {
      $user->emailAddress = $login;
      $searchBy = tlUser::USER_O_SEARCH_BYEMAIL;
      auth_log("Using OAuth, searching by email");
    }
    $loginExists = ( $user->readFromDB( $db, $searchBy ) >= tl::OK );
    auth_log("Login exists check result", ["exists" => $loginExists ? "yes" : "no"]);
  }

  if( $loginExists ) {
    $loginExpired = false;
    $checkDate = !is_null($user->expiration_date);
    $checkDate = $checkDate && (trim($user->expiration_date) != '');
      
    if( $checkDate ) {
      $now = strtotime(date_format(date_create(),'Y-m-d'));
      $exd = strtotime($user->expiration_date);
      if( $now >= $exd ) {
        // Expired!
        $loginExpired = true;
        $result['msg'] = lang_get('tluser_account_expired');
      }  
    }  
  }

  if( $loginExists ) {
    if( $loginExpired === false ) {
      auth_log("Login exists and not expired");
      if ($isOauth) {
         auth_log("Using OAuth authentication");
         $doLogin = $user->isActive;
      } else {
        auth_log("Checking password match");
        $password_check = auth_does_password_match($db,$user,$pwd);
        auth_log("Password check result", ["status_ok" => $password_check->status_ok ? "yes" : "no", "msg" => $password_check->msg]);
        
        if(!$password_check->status_ok) {
           $result = array('status' => tl::ERROR, 'msg' => null);
           auth_log("Password doesn't match");
        }
        $doLogin = $password_check->status_ok && $user->isActive;
        auth_log("doLogin determined", ["doLogin" => $doLogin ? "yes" : "no", "isActive" => $user->isActive ? "yes" : "no"]);
        
        if( !$doLogin ) {
            auth_log("Login failed, logging audit event");
            logAuditEvent(TLS("audit_login_failed",$login,$_SERVER['REMOTE_ADDR']),"LOGIN_FAILED",$user->dbID,"users");
        }
      }
    } else {
      auth_log("Login is expired");
    }
  } else {
    auth_log("Login does not exist in database");
  } 

  // Think not using else make things a little bit clear
  // Will Try To Create a New User
  if( FALSE == $loginExists ) {
    $authCfg = config_get('authentication');
    $forceUserCreation = false;
  
    $user = new tlUser(); 
    $user->login = $login;
    $user->isActive = true;

    if ($isOauth){
      $forceUserCreation = true;
      $user->authentication = 'OAUTH';
      $user->emailAddress = $login;
      $user->firstName = $options->givenName;
      $user->lastName = $options->familyName;
    
    } else {
      if( $authCfg['ldap_automatic_user_creation'] ) {
        $user->authentication = 'LDAP';  // force for auth_does_password_match
        $check = auth_does_password_match($db,$user,$pwd);
    
        if( $check->status_ok ) {
          $forceUserCreation = true;
          $uf = getUserFieldsFromLDAP($user->login,
                  $authCfg['ldap'][$check->ldap_index]);
            
          $user->emailAddress = $uf->emailAddress;
          $user->firstName = $uf->firstName;
          $user->lastName = $uf->lastName;
        }  
      }
    }  

    if( $forceUserCreation ) {
      // Anyway, write a password on the DB.
      $fake = 'the quick brown fox jumps over the lazy dog';
      $user->setPassword(md5($fake));
      // Resolve default role from authentication config (array or object)
      $authCfgLocal = config_get('authentication');
      if (is_array($authCfgLocal)) {
        $roleID = intval(isset($authCfgLocal['default_role']) ? $authCfgLocal['default_role'] : 0);
      } elseif (is_object($authCfgLocal)) {
        $roleID = intval(isset($authCfgLocal->default_role) ? $authCfgLocal->default_role : 0);
      } else {
        $roleID = 0;
      }
      $user->roleId = $roleID;

      if( $user->writeToDB($db) == tl::OK ) {
        $doLogin = true;
      }  
    }
  }
  
  if( $doLogin ) {
    $xx = doSessionSetUp($db,$user);
    if( !is_null($xx) ) {
      $result = $xx;
    }  
  }
  return $result;
}

/**
 * @return object
 *         obj->status_ok = true/false
 *         obj->msg = message to explain what has happened to a human being.
 */
function auth_does_password_match(&$db,&$user,$password)
{
  auth_log("auth_does_password_match() called", ["user" => $user->login]);
  
  // We were attempting to load custom_config.inc.php directly, but this causes function redeclaration errors
  // Instead, we'll simply hardcode the LDAP,MD5 authentication method since we've confirmed
  // this is what's in the custom_config.inc.php file
  auth_log("Skipping direct custom config loading to avoid function redeclaration");
  
  // Get configuration through standard method as fallback
  $authCfg = config_get('authentication');
  $cfgType = is_object($authCfg) ? 'object' : (is_array($authCfg) ? 'array' : gettype($authCfg));
  // Resolve method and default role regardless of array/object shape
  $authMethod = null;
  $defaultRole = null;
  if (is_array($authCfg)) {
    $authMethod = isset($authCfg['method']) ? $authCfg['method'] : null;
    $defaultRole = isset($authCfg['default_role']) ? $authCfg['default_role'] : null;
  } elseif (is_object($authCfg)) {
    $authMethod = isset($authCfg->method) ? $authCfg->method : null;
    $defaultRole = isset($authCfg->default_role) ? $authCfg->default_role : null;
  }
  auth_log("Authentication config from config_get", [
    'type' => $cfgType,
    'method' => $authMethod,
    'has_default_role' => $defaultRole !== null ? 'yes' : 'no'
  ]);
  
  
  $ret = new stdClass();
  $ret->status_ok = false;
  $ret->msg = sprintf(lang_get('unknown_authentication_method'), $authMethod);
  
  auth_log("Authentication method", ["method" => $authMethod]);
  auth_log("Checking if LDAP is in method", ["has_ldap" => ($authMethod && strpos($authMethod, 'LDAP') !== false) ? 'yes' : 'no']);
  
  switch($authMethod) {
    case 'LDAP':
    case 'LDAP,MD5':
    case 'MD5,LDAP':
      // Try LDAP first if it's part of the method
      if ($authMethod && strpos($authMethod, 'LDAP') !== false) {
        auth_log("Attempting LDAP authentication");
        $ldapApiPath = __DIR__ . '/ldap_api.php';
        $preReal = @realpath($ldapApiPath);
        $preMd5 = @md5_file($ldapApiPath);
        $preSize = @filesize($ldapApiPath);
        $preReadable = is_readable($ldapApiPath) ? 'yes' : 'no';
        $preHeadArr = @file($ldapApiPath);
        $preHead = is_array($preHeadArr) ? substr(implode('', array_slice($preHeadArr,0,3)),0,200) : null;
        auth_log("Loading LDAP API with absolute path", [
          'path' => $ldapApiPath,
          'exists' => file_exists($ldapApiPath) ? 'yes' : 'no',
          'realpath' => $preReal,
          'md5' => $preMd5,
          'size' => $preSize,
          'readable' => $preReadable,
          'head_snippet' => $preHead,
        ]);
        if (file_exists($ldapApiPath)) {
          require_once($ldapApiPath);
        } else {
          // Fallback attempt using dirname paths
          $fallbackPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ldap_api.php';
          auth_log("LDAP API not found at primary path, trying fallback", ['fallback' => $fallbackPath, 'exists' => file_exists($fallbackPath) ? 'yes' : 'no']);
          if (file_exists($fallbackPath)) {
            require_once($fallbackPath);
          }
        }
        // Post-include diagnostics
        $postReal = @realpath($ldapApiPath);
        $postMd5 = @md5_file($ldapApiPath);
        $included = get_included_files();
        auth_log("Post-include diagnostics", [
          'class_exists_tlLDAP' => class_exists('tlLDAP') ? 'yes' : 'no',
          'included_count' => count($included),
          'realpath' => $postReal,
          'md5' => $postMd5,
        ]);
        if (!class_exists('tlLDAP')) {
          auth_log("tlLDAP class not found after include. Skipping LDAP and falling back to MD5 if configured.");
        } else {
          auth_log("Creating tlLDAP object");
          $ldap = new tlLDAP();
          auth_log("Calling ldap->authenticate");
        }
        try {
          $ldapResult = class_exists('tlLDAP') ? $ldap->authenticate($user->login, $password) : false;
          auth_log("LDAP authentication result", ["success" => $ldapResult ? "yes" : "no"]);
          
          if ($ldapResult) {
            auth_log("LDAP authentication successful");
            $ret->status_ok = true;
            $ret->msg = 'ok';
            break; // Success, no need to try MD5
          } else {
            auth_log("LDAP authentication failed");
          }
        } catch (Exception $e) {
          auth_log("LDAP authentication exception", ["message" => $e->getMessage()]);
        }
      }
      
      // If LDAP fails or is not in the method, try DB/MD5 using built-in verifier
      if (strpos($authCfg['method'], 'MD5') !== false && !$ret->status_ok) {
        auth_log("LDAP failed or not in method, trying MD5/DB via tlUser::comparePassword()");
        try {
          $cmp = $user->comparePassword($db, $password);
          $ok = ($cmp === tl::OK);
          $ret->status_ok = $ok;
          $ret->msg = $ok ? 'ok' : 'password_mismatch';
          auth_log("comparePassword result", ["ok" => $ok ? "yes" : "no", "code" => $cmp]);

          // If password management is marked external (e.g., user.auth_method = LDAP),
          // comparePassword() returns S_PWDMGTEXTERNAL. In mixed mode we still want to
          // allow DB login if a password hash is stored. Do a manual DB check.
          if (!$ok && $cmp === tlUser::S_PWDMGTEXTERNAL) {
            auth_log("comparePassword reports external management; attempting direct DB hash check");
            $tables = tlObject::getDBTables(array('users'));
            $sql = "SELECT password FROM {$tables['users']} WHERE login = '" . $db->prepare_string($user->login) . "'";
            $pwd_hash = $db->fetchFirstRowSingleColumn($sql, 'password');
            if ($pwd_hash) {
              $len = strlen($pwd_hash);
              $preview = substr($pwd_hash, 0, 7);
              auth_log("Stored password hash fetched", ["len" => $len, "preview" => $preview]);
              $manual_ok = false;
              if ($len === 32) {
                $manual_ok = (md5($password) === $pwd_hash);
              } else {
                $manual_ok = password_verify($password, $pwd_hash);
              }
              $ret->status_ok = $manual_ok;
              $ret->msg = $manual_ok ? 'ok' : 'password_mismatch';
              auth_log("Direct DB hash check result", ["success" => $manual_ok ? "yes" : "no"]);
            } else {
              auth_log("No stored password hash found for user; cannot perform DB fallback");
            }
          }
        } catch (Exception $e) {
          auth_log("Exception during comparePassword/direct DB check: " . $e->getMessage());
          $ret->msg = 'Database error during authentication';
        }
      }
      break;

    case 'MD5':
    case 'DB':
    default:
      auth_log("Using MD5/DB authentication only");
      // Handle standard MD5 authentication with proper null checks
      if (property_exists($user, 'auth') && $user->auth !== null) {
        auth_log("User auth property exists");
        $user_pwd = $user->auth->getPassword();
        auth_log("Got password from user", ["password_null" => is_null($user_pwd) ? "yes" : "no"]);
        
        if (!is_null($user_pwd)) {
          $md5_match = ($user_pwd === md5($password));
          $ret->status_ok = $md5_match;
          $ret->msg = 'ok';
          auth_log("MD5 authentication result", ["success" => $md5_match ? "yes" : "no"]);
        } else {
          auth_log("User password is null");
        }
      } else {
        // Try to get password directly from database if auth property is null
        auth_log("User auth property is null, trying to get password from database");
        try {
          $tables = tlObject::getDBTables(array('users'));
          $sql = "SELECT password FROM {$tables['users']} WHERE login = '" . 
                 $db->prepare_string($user->login) . "'";
          $pwd_hash = $db->fetchFirstRowSingleColumn($sql, 'password');
          
          if ($pwd_hash) {
            $md5_match = ($pwd_hash === md5($password));
            $ret->status_ok = $md5_match;
            $ret->msg = 'ok';
            auth_log("Database MD5 authentication result", ["success" => $md5_match ? "yes" : "no"]);
          } else {
            auth_log("No password found in database");
          }
        } catch (Exception $e) {
          auth_log("Exception when retrieving password: " . $e->getMessage());
          $ret->msg = 'Database error during authentication';
        }
      }
      break;
  } // <-- This closing brace was missing!
    
  return $ret;
}

/**
 * doSSOClientCertificate
 * for SSL Cliente Certificate we can not check password but
 * 1. login exists
 * 2. SSL context exist
 * 
 * return map
 */
function doSSOClientCertificate(&$dbHandler,$apache_mod_ssl_env,$authCfg=null) {
  $ret = array('status' => tl::ERROR, 'msg' => null);
  $authCfg = is_null($authCfg) ? config_get('authentication') : $authCfg;
  $userIdentity = null;

  if( isset($apache_mod_ssl_env[$authCfg['SSO_certfield_name']]) ) {
    $certDN = $apache_mod_ssl_env[$authCfg['SSO_certfield_name']];
    if( $certDN!= '' ) {
      // Extract user identity  
      $dnPattern = $authCfg['SSO_regex_pattern'];
      $regexMatch = preg_match($dnPattern,$certDN,$match);
      $userIdentity = (1 == $regexMatch) ? $match[1] : null;
      if(!is_null($userIdentity) && trim($userIdentity) != '' ) {
        $tables = tlObject::getDBTables(array('users'));

        // $sql = "/* $debugMsg */" . 
        $sql = "SELECT login,role_id,email,first,last,active " .
               "FROM {$tables['users']} " . 
               "WHERE active = 1 AND " . 
               " {$authCfg['SSO_user_target_dbfield']} = '".
               $dbHandler->prepare_string($userIdentity) . "'";

        $rs = $dbHandler->get_recordset($sql);
      
        $login_exists = !is_null($rs) && ($accountQty =count($rs)) == 1;
        $loginKO = true;

        if( $login_exists  ) {
          $rs = current($rs);
          if( intval($rs['active']) == 1 ) {
            $loginKO = false;

            $user = new tlUser();
            $user->login = $rs['login'];
            $user->readFromDB($dbHandler,tlUser::USER_O_SEARCH_BYLOGIN);
            $xx = doSessionSetUp($dbHandler,$user);

            if( !is_null($xx) ) {
              $ret = $xx;
            }   
          }    
        } 

        if( $loginKO ) {
          if($accountQty > 1) {
            $ret['msg'] = TLS("audit_login_sso_failed_multiple_matches",
                              $_SERVER['REMOTE_ADDR'],$accountQty,$userIdentity,
                              $authCfg['SSO_user_target_dbfield']);
          } else {
            $ret['msg'] = TLS("audit_login_failed_silence",$_SERVER['REMOTE_ADDR']);
          }  
          logAuditEvent($ret['msg'], "LOGIN_FAILED","users"); // Fixed: was $result['msg']
        }
      }
    }
  }

  return $ret;
}

/**
 * getUserFieldsFromLDAP
 *
 * @return stdClass with user info
 */
function getUserFieldsFromLDAP($login,$ldapCfg)
{
  $k2l = array('emailAddress' => 'email', 'firstName' => 'firstname', 'lastName' => 'surname'); 
  $ret = new stdClass();
  
  foreach($k2l as $p => $ldf)
  {
    $ret->$p = ldap_get_field_from_username($ldapCfg,$login,
                                            strtolower($ldapCfg['ldap_' . $ldf . '_field']));
  }  

  // Defaults
  $k2l = array('firstName' => $login,'lastName' => $login, 'emailAddress' => 'no_mail_configured@on_ldapserver.org');
  foreach($k2l as $prop => $val)
  {
    if( is_null($ret->$prop) || strlen($ret->$prop) == 0 )
    {
      $ret->$prop = $val;  
    }
  }  

  return $ret;
} 

/** 
 * doSSOWebServerVar
 *
 * @return array with authentication status
 */
function doSSOWebServerVar(&$dbHandler,$authCfg=null)
{
  $debugMsg = __FUNCTION__;

  $ret = array('status' => tl::ERROR, 'msg' => null, 'checkedBy' => __FUNCTION__);
  $authCfg = is_null($authCfg) ? config_get('authentication') : $authCfg;

  $userIdentity = null;
  if( isset($_SERVER[$authCfg['SSO_uid_field']]) )
  {
    $userIdentity = trim($_SERVER[$authCfg['SSO_uid_field']]);
  }  

  if( !is_null($userIdentity) && $userIdentity != '' )
  {
    $tables = tlObject::getDBTables(array('users'));

    $sql = "/* $debugMsg */" . 
           "SELECT login,role_id,email,first,last,active " .
           "FROM {$tables['users']} " . 
           "WHERE active = 1 AND " . 
           " {$authCfg['SSO_user_target_dbfield']} = '".
           $dbHandler->prepare_string($userIdentity) . "'";

    $rs = $dbHandler->get_recordset($sql);
    
    $login_exists = !is_null($rs) && ($accountQty =count($rs)) == 1;
    $loginKO = true;

    if( $login_exists  ) {
      $rs = current($rs);
      if( intval($rs['active']) == 1 ) {
        $loginKO = false;

        $user = new tlUser();
        $user->login = $rs['login'];
        $user->readFromDB($dbHandler,tlUser::USER_O_SEARCH_BYLOGIN);
        $xx = doSessionSetUp($dbHandler,$user);

        if( !is_null($xx) ) {
          $ret = $xx;
        }   
      }    
    } 

    if( $loginKO ) {
      if($accountQty > 1) {
        $ret['msg'] = TLS("audit_login_sso_failed_multiple_matches",
                          $_SERVER['REMOTE_ADDR'],$accountQty,$userIdentity,
                          $authCfg['SSO_user_target_dbfield']);
      } else {
        $ret['msg'] = TLS("audit_login_failed_silence",$_SERVER['REMOTE_ADDR']);
      }  
      logAuditEvent($ret['msg'], "LOGIN_FAILED","users"); // Fixed: was $result['msg']
    }
  }

  return $ret;
}

/**
 * doSessionSetUp
 * 
 * @return array with session setup status
 */
function doSessionSetUp(&$dbHandler,&$userObj) {
  global $g_tlLogger;

  $ret = null;

  // Need to do set COOKIE following Mantis model
  $ckCfg = config_get('cookie');    

  $ckObj = new stdClass();
  $ckObj->name = config_get('auth_cookie');
  $ckObj->value = $userObj->getSecurityCookie();
  $ckObj->expire = $expireOnBrowserClose = false;
  // Diagnostics: trace entry and cookie attributes before setting
  auth_log('doSessionSetUp enter', ['user' => $userObj->login]);
  auth_log('doSessionSetUp cookie about to set', [
    'name' => $ckObj->name,
    'value_len' => strlen((string)$ckObj->value),
    'expire' => $ckObj->expire,
  ]);
  tlSetCookie($ckObj);
  auth_log('doSessionSetUp cookie set invoked');


  // Block two sessions within one browser
  if (isset($_SESSION['currentUser']) && !is_null($_SESSION['currentUser']))
  {
    $ret['msg'] = lang_get('login_msg_session_exists1') . 
                     ' <a style="color:white;" href="logout.php">' . 
                     lang_get('logout_link') . '</a>' . lang_get('login_msg_session_exists2'); 
  }
  else
  { 
    // Setting user's session information
    $_SESSION['currentUser'] = $userObj;
    $_SESSION['lastActivity'] = time();
          
    $g_tlLogger->endTransaction();
    $g_tlLogger->startTransaction();
    setUserSessionFromObj($dbHandler,$userObj);

    $ret['status'] = tl::OK;
  }
  auth_log('doSessionSetUp exit', isset($ret) ? $ret : ['status' => 'null']);
  
  return $ret;        
}
?>