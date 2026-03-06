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
require_once("ldap_api.php");

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

  $result = array('status' => tl::ERROR, 'msg' => null);
  $_SESSION['locale'] = TL_DEFAULT_LOCALE; 

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
  } 

  $isOauth = false;
  if( property_exists($options, 'auth') ) {
    $isOauth = strpos($options->auth,'oauth') !== false;
  }

  $loginExists = false;
  $loginExpired = false;
  $doLogin = false;

  if( $doChecks && !is_null($login)) {
    $user = new tlUser();
    $user->login = $login;
    $searchBy = tlUser::USER_O_SEARCH_BYLOGIN;
    if( $isOauth ) {
      $user->emailAddress = $login;
      $searchBy = tlUser::USER_O_SEARCH_BYEMAIL;
    }
    $loginExists = ( $user->readFromDB( $db, $searchBy ) >= tl::OK ); 
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
      if ($isOauth) {
         $doLogin = $user->isActive;
      } else {
        $password_check = auth_does_password_match($db,$user,$pwd);
        if(!$password_check->status_ok) {
           $result = array('status' => tl::ERROR, 'msg' => null);
        }
        $doLogin = $password_check->status_ok && $user->isActive;
        if( !$doLogin ) {
            logAuditEvent(TLS("audit_login_failed",$login,$_SERVER['REMOTE_ADDR']),"LOGIN_FAILED",$user->dbID,"users");
        }
      }
    }
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
      $roleID = intval($authCfg['default_role']);
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
 * @return array
 *         obj->status_ok = true/false
 *         obj->msg = message to explain what has happened to a human being.
 */
function auth_does_password_match(&$db,&$user,$password)
{
    $authCfg = config_get('authentication');
    $ret = new stdClass();
    $ret->status_ok = false;
    $ret->msg = sprintf(lang_get('unknown_authentication_method'),$authCfg['method']);
    
    switch($authCfg['method']) {
        case 'LDAP':
        case 'LDAP,MD5':
        case 'MD5,LDAP':
            // Try LDAP first
            if(strpos($authCfg['method'], 'LDAP') !== false) {
                require_once('ldap_api.php');
                $ldap = new tlLDAP();
                $ldapResult = $ldap->authenticate($user->login, $password);
                
                if($ldapResult) {
                    $ret->status_ok = true;
                    $ret->msg = 'ok';
                    break; // Success, no need to try MD5
                }
            }
            
            // If LDAP fails or is not in the method, try MD5
            if(strpos($authCfg['method'], 'MD5') !== false && !$ret->status_ok) {
                $user_pwd = $user->auth->getPassword();
                if(!is_null($user_pwd)) {
                    $ret->status_ok = ($user_pwd === md5($password));
                    $ret->msg = 'ok';
                }
            }
            break;

        case 'MD5':
        case 'DB':
        default:
            $user_pwd = $user->auth->getPassword();
            if(!is_null($user_pwd)) {
                $ret->status_ok = ($user_pwd === md5($password));
                $ret->msg = 'ok';
            }
            break;
    }
    
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
          logAuditEvent($result['msg'], "LOGIN_FAILED","users");
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
      logAuditEvent($result['msg'], "LOGIN_FAILED","users");
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
  tlSetCookie($ckObj);


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
  
  return $ret;        
}
