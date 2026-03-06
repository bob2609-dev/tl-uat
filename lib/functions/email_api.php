<?php
/** 
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 *
 * Email API (adapted from third party code)
 *
 */

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require( 'autoload.php' );

require_once( 'lang_api.php' );
require_once( 'common.php');
require_once( 'string_api.php');


/** @var mixed reusable object of class SMTP */
$g_phpMailer = null;


/**
 *
 */
function email_send_wrapper( $mailObj, $opt = null ) 
{
  $prop = array();
  $prop['opt'] = array('cc','attachment');

  $oops = array('cc' => '', 'attachment' => null,
                'exit_on_error' => false, 'htmlFormat' => false,
                'strip_email_links' => true);

  $oops = array_merge($oops,(array)$opt);
  
  // function email_send( 
  // $p_from, $p_recipient, $p_subject, $p_message, $p_cc='',
  // $p_exit_on_error = false, $htmlFormat = false, $opt = null ) 
  return email_send($mailObj->from_address, $mailObj->to_address, 
                    $mailObj->subject, $mailObj->message, $oops['cc'],
                    $oops['attachment'],$oops['exit_on_error'],
                    $oops['htmlFormat'],$opt);
}


/** 
 * sends the actual email 
 * 
 * @param boolean $p_exit_on_error == true - calls exit() on errors, else - returns true 
 *    on success and false on errors
 * @param boolean $htmlFormat specify text type true = html, false (default) = plain text
 */
function email_send( $p_from, $p_recipient, $p_subject, $p_message, $p_cc='',
                     $p_attachment = null,
                     $p_exit_on_error = false, $htmlFormat = false, $opt = null ) 
{

  global $g_phpMailer;

  $op = new stdClass();
  $op->status_ok = true;
  $op->msg = 'ok';

  $options = array('strip_email_links' => true);
  $options = array_merge($options,(array)$opt);

  // Check fatal Error
  $smtp_host = config_get( 'smtp_host' );
  if( is_blank($smtp_host) )
  {
    $op->status_ok=false;
    $op->msg=lang_get('stmp_host_unconfigured');
    return $op;  // >>>---->
  }

  $ot = new stdClass();
  $ot->recipient = trim( $p_recipient );
  $ot->subject   = string_email( trim( $p_subject ) );
  $ot->message = trim($p_message);
  $ot->message   = $options['strip_email_links'] ? string_email_links($p_message) : $p_message;

  # short-circuit if no recipient is defined, or email disabled
  # note that this may cause signup messages not to be sent

  # Visit http://phpmailer.sourceforge.net
  # if you have problems with phpMailer
  # Use exceptions=false to avoid fatal errors on Send(); we'll handle errors via return value and ErrorInfo
  $mail = new PHPMailer(false);

  $mail->SMTPAutoTLS = config_get('SMTPAutoTLS');    
  $mail->PluginDir = PHPMAILER_PATH;

  // Need to get strings file for php mailer
  // To avoid problems I choose ENglish
  $mail->SetLanguage( 'en', PHPMAILER_PATH . 'language' . DIRECTORY_SEPARATOR );

  // Optional SMTP debug to error log (0=off, 1=client msgs, 2=client+server, 3,4=verbose)
  // Control via custom_config: $g_smtp_debug_level
  $smtpDebug = (int)config_get('smtp_debug_level', 0);
  // Prepare local log file in same folder as this file under logs/
  $localLogDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
  if (!is_dir($localLogDir)) {
    @mkdir($localLogDir, 0755, true);
  }
  $localLogFile = $localLogDir . DIRECTORY_SEPARATOR . 'smtp_debug.log';
  if ($smtpDebug > 0) {
    $mail->SMTPDebug = $smtpDebug;
    // Write PHPMailer debug output to local log file via closure
    $debugFile = $localLogFile; // capture for closure
    $mail->Debugoutput = function($str, $level) use ($debugFile) {
      @file_put_contents($debugFile, '['.date('Y-m-d H:i:s')."] [level=$level] " . $str . "\n", FILE_APPEND);
    };
  }

  # Select the method to send mail
  switch ( config_get( 'phpMailer_method' ) ) 
  {
    case PHPMAILER_METHOD_MAIL: $mail->IsMail();
    break;

    case PHPMAILER_METHOD_SENDMAIL: $mail->IsSendmail();
        break;

    case PHPMAILER_METHOD_SMTP: $mail->IsSMTP();
      # SMTP collection is always kept alive
      $mail->SMTPKeepAlive = true;

      # Copied from last mantis version
      if ( !is_blank( config_get( 'smtp_username' ) ) ) {
        # Use SMTP Authentication
        $mail->SMTPAuth = true;
        $mail->Username = config_get( 'smtp_username' );
        $mail->Password = config_get( 'smtp_password' );
      }

      if ( !is_blank( config_get( 'smtp_connection_mode' ) ) ) {
        $mail->SMTPSecure = config_get( 'smtp_connection_mode' );
      }

      $mail->Port = config_get( 'smtp_port' );


      // is not a lot clear why this is useful (franciscom)
      // need to use sometime to understand .
      if( is_null( $g_phpMailer ) )  
      {
        register_shutdown_function( 'email_smtp_close' );
      } 
      else 
      {
        $mail = $g_phpMailer;
      }
    break;
  }

  $mail->IsHTML($htmlFormat);    # set email format to plain text
  $mail->WordWrap = 80;

  # Urgent = 1, Not Urgent = 5, Disable = 0
  $mail->Priority = config_get( 'mail_priority' ); 

  $mail->CharSet = config_get( 'charset');
  $mail->Host = config_get( 'smtp_host' );
  // Provide a Hostname for HELO/EHLO. Some relays are picky.
  if (function_exists('gethostname')) {
    $mail->Hostname = gethostname();
  }
  $mail->Sender  = config_get( 'return_path_email' );
  $mail->FromName = '';

  $mail->From = config_get( 'from_email' );
  if ( !is_blank( $p_from ) )
  {
    $mail->From = $p_from;
  }
 
  $t_debug_to = '';

  # add to the Recipient list
  $t_recipient_list = explode(',', $ot->recipient);

  while ( list( , $t_recipient ) = each( $t_recipient_list ) ) {
    if ( !is_blank( $t_recipient ) ) {
        $mail->AddAddress( $t_recipient, '' );
    }
  }

  $t_cc_list = explode(',', $p_cc);
  while(list(, $t_cc) = each($t_cc_list)) {
    if ( !is_blank( $t_cc ) ) {
        $mail->AddCC( $t_cc, '' );
    }
  }

  $mail->Subject = $ot->subject;
  $mail->Body    = make_lf_crlf( "\n" . $ot->message );

  if( !is_null($p_attachment) )
  {
    $mail->AddAttachment($p_attachment['file'],$p_attachment['newname']);    
  }  

  // Build context string for logging on error
  $ctx_username_set = !is_blank( config_get( 'smtp_username' ) ) ? 'yes' : 'no';
  $smtp_context = sprintf(
    'ctx host=%s port=%s secure=%s autoTLS=%s userSet=%s helo=%s from=%s sender=%s',
    (string)$mail->Host,
    (string)$mail->Port,
    (string)(isset($mail->SMTPSecure) ? $mail->SMTPSecure : ''),
    (string)$mail->SMTPAutoTLS,
    $ctx_username_set,
    (string)(isset($mail->Hostname) ? $mail->Hostname : ''),
    (string)$mail->From,
    (string)$mail->Sender
  );

  try {
    if ( !$mail->Send() ) 
    {
      if ( $p_exit_on_error )  
      {
        PRINT "PROBLEMS SENDING MAIL TO: $p_recipient<br />";
        PRINT 'Mailer Error: '. $mail->ErrorInfo.'<br />';
        exit;
      }
      else
      {
        $op->status_ok = false;
        $op->msg = $mail->ErrorInfo;
        // Log detailed error for diagnostics without exposing to users
        $msg = 'PHPMailer Send() returned false. ' . $smtp_context . ' ErrorInfo=' . $mail->ErrorInfo;
        error_log($msg);
        @file_put_contents($localLogFile, '['.date('Y-m-d H:i:s')."] " . $msg . "\n", FILE_APPEND);
      }
    }
  } catch (Exception $e) {
    // Catch any PHPMailer exceptions just in case and log safely
    $op->status_ok = false;
    $op->msg = $mail->ErrorInfo ? $mail->ErrorInfo : ('Mailer exception: ' . $e->getMessage());
    $msg = 'PHPMailer exception during Send(): ' . $e->getMessage() . ' ' . $smtp_context;
    error_log($msg);
    @file_put_contents($localLogFile, '['.date('Y-m-d H:i:s')."] " . $msg . "\n", FILE_APPEND);
  }
  return $op;
}



/**
 * closes opened kept alive SMTP connection (if it was opened)
 * 
 * @param string 
 * @return null
 */
function email_smtp_close() {
  global $g_phpMailer;

  if( !is_null( $g_phpMailer ) ) {
    if( $g_phpMailer->smtp->Connected() ) {
      $g_phpMailer->smtp->Quit();
      $g_phpMailer->smtp->Close();
    }
    $g_phpMailer = null;
  }
}

# --------------------
# clean up LF to CRLF
function make_lf_crlf( $p_string ) {
  $t_string = str_replace( "\n", "\r\n", $p_string );
  return str_replace( "\r\r\n", "\r\n", $t_string );
}

# --------------------
# Check limit_email_domain option and append the domain name if it is set
function email_append_domain( $p_email ) {
  $t_limit_email_domain = config_get( 'limit_email_domain' );
  if ( $t_limit_email_domain && !is_blank( $p_email ) ) {
    $p_email = "$p_email@$t_limit_email_domain";
  }

  return $p_email;
}