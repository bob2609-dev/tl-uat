<?php
/**
 * Simple log writer for frontend debugging
 */

header('Content-Type: text/plain');

$logFile = __DIR__ . '/custom_bugtrack_integration_frontend.log';
$timestamp = date('Y-m-d H:i:s');
$logEntry = "[$timestamp] " . ($_POST['log_entry'] ?? 'No message') . "\n";

file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

echo "Log written";
?>
