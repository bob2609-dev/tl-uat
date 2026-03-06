<?php
// SMTP connectivity and send test script
// Usage (CLI):
//   php smtp_test.php --to you@example.com [--from no-reply@example.com] [--host 10.200.221.58] [--port 25] [--secure none|tls|ssl] [--autotls 0|1] [--user USER] [--pass PASS]
// Usage (Web):
//   http://server/tl-uat/smtp_test.php?to=you@example.com&from=no-reply@example.com&host=10.200.221.58&port=25&secure=&autotls=0
// NOTE: Remove this file after debugging. Do not deploy to production environments.

// Set default timezone to avoid warnings in logs
@date_default_timezone_set('UTC');

$SCRIPT_DIR = __DIR__;
$LOG_DIR = $SCRIPT_DIR . DIRECTORY_SEPARATOR . 'logs';
$LOG_FILE = $LOG_DIR . DIRECTORY_SEPARATOR . 'smtp_test.log';
if (!is_dir($LOG_DIR)) { @mkdir($LOG_DIR, 0755, true); }

function log_line($msg) {
  global $LOG_FILE;
  $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n";
  @file_put_contents($LOG_FILE, $line, FILE_APPEND);
}

function is_cli() { return php_sapi_name() === 'cli'; }

// Parse params from CLI or GET
$params = [
  'to'      => 'mwaimu.mtingele@nmbtz.com',
  'from'    => 'no-reply@nmbtz.com',
  'host'    => '10.200.221.17',
  'port'    => 25,
  'secure'  => '', // '', 'tls', 'ssl'
  'autotls' => 0,
  'user'    => '',
  'pass'    => '',
  'subject' => 'SMTP Test ' . date('Y-m-d H:i:s'),
  'body'    => "This is a test email from smtp_test.php",
];

if (is_cli()) {
  $opts = getopt('', ['to:', 'from::', 'host::', 'port::', 'secure::', 'autotls::', 'user::', 'pass::', 'subject::', 'body::']);
  foreach ($opts as $k => $v) { if (array_key_exists($k, $params) && $v !== false) { $params[$k] = $v; } }
} else {
  foreach ($params as $k => $v) { if (isset($_GET[$k])) { $params[$k] = $_GET[$k]; } }
}

if (empty($params['to'])) {
  $msg = "Missing required parameter 'to'. Provide via --to or ?to=...";
  if (is_cli()) { echo $msg . "\n"; } else { echo nl2br(htmlentities($msg)); }
  exit(1);
}

// Normalize types
$params['port'] = (int)$params['port'];
$params['autotls'] = (int)$params['autotls'] ? 1 : 0;
$params['secure'] = in_array($params['secure'], ['', 'tls', 'ssl'], true) ? $params['secure'] : '';

$ctx_summary = sprintf(
  'ctx host=%s port=%d secure=%s autoTLS=%d userSet=%s from=%s to=%s',
  $params['host'], $params['port'], $params['secure'], $params['autotls'], ($params['user'] !== '' ? 'yes' : 'no'), $params['from'], $params['to']
);

// Output header for web
if (!is_cli()) { echo '<pre>'; }

echo "== SMTP Test ==\n";
echo $ctx_summary . "\n";
log_line('START ' . $ctx_summary);

// 1) Raw socket test (fsockopen) to capture errno/errstr
$errno = 0; $errstr = '';
$start = microtime(true);
$fp = @fsockopen($params['host'], $params['port'], $errno, $errstr, 15);
$elapsed = round((microtime(true) - $start), 3);
if ($fp) {
  stream_set_timeout($fp, 5);
  $banner = @fgets($fp, 1024);
  @fclose($fp);
  $msg = "fsockopen: CONNECTED in {$elapsed}s; banner=" . trim((string)$banner);
  echo $msg . "\n"; log_line($msg);
} else {
  $msg = "fsockopen: FAILED in {$elapsed}s; errno={$errno} errstr=" . trim((string)$errstr);
  echo $msg . "\n"; log_line($msg);
}

// 2) PHPMailer test
try {
  // Use Composer autoload if available
  if (file_exists($SCRIPT_DIR . '/vendor/autoload.php')) {
    require_once $SCRIPT_DIR . '/vendor/autoload.php';
  } elseif (file_exists($SCRIPT_DIR . '/vendor/phpmailer/phpmailer/src/PHPMailer.php')) {
    require_once $SCRIPT_DIR . '/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once $SCRIPT_DIR . '/vendor/phpmailer/phpmailer/src/SMTP.php';
    require_once $SCRIPT_DIR . '/vendor/phpmailer/phpmailer/src/Exception.php';
  } else {
    echo "PHPMailer not found (vendor autoload missing).\n"; log_line('PHPMailer not found');
    exit(1);
  }

  $hostname = function_exists('gethostname') ? gethostname() : '';

  $mail = new \PHPMailer\PHPMailer\PHPMailer(false); // exceptions off
  $mail->isSMTP();
  $mail->SMTPKeepAlive = false;
  $mail->Host = $params['host'];
  $mail->Port = $params['port'];
  $mail->SMTPAutoTLS = (bool)$params['autotls'];
  if ($params['secure'] !== '') { $mail->SMTPSecure = $params['secure']; }
  if ($params['user'] !== '') { $mail->SMTPAuth = true; $mail->Username = $params['user']; $mail->Password = $params['pass']; }
  if ($hostname !== '') { $mail->Hostname = $hostname; }

  $mail->SMTPDebug = 3; // verbose
  $debugFile = $LOG_FILE; // capture for closure
  $mail->Debugoutput = function($str, $level) use ($debugFile) {
    $line = '[' . date('Y-m-d H:i:s') . "] [level=$level] " . $str . "\n";
    echo $line; @file_put_contents($debugFile, $line, FILE_APPEND);
  };

  $mail->CharSet = 'UTF-8';
  $mail->setFrom($params['from']);
  $mail->Sender = $params['from'];
  $mail->addAddress($params['to']);
  $mail->Subject = $params['subject'];
  $mail->Body = $params['body'];
  $mail->isHTML(false);

  $ctx2 = sprintf('PHPMailer ctx host=%s port=%d secure=%s autoTLS=%s userSet=%s helo=%s from=%s sender=%s',
    (string)$mail->Host, (int)$mail->Port, (string)(isset($mail->SMTPSecure) ? $mail->SMTPSecure : ''),
    (string)$mail->SMTPAutoTLS, ($mail->SMTPAuth ? 'yes' : 'no'), (string)(isset($mail->Hostname) ? $mail->Hostname : ''),
    (string)$params['from'], (string)$mail->Sender
  );
  echo $ctx2 . "\n"; log_line($ctx2);

  $ok = $mail->send();
  if ($ok) {
    echo "SEND: OK\n"; log_line('SEND: OK');
  } else {
    echo "SEND: FAILED - " . $mail->ErrorInfo . "\n"; log_line('SEND: FAILED - ' . $mail->ErrorInfo);
  }
} catch (\Throwable $e) {
  $msg = 'EXCEPTION: ' . $e->getMessage();
  echo $msg . "\n"; log_line($msg);
}

log_line('END');
if (!is_cli()) { echo '</pre>'; }
