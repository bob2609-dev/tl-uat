<?php
// Database configuration file

// Database credentials
$db_host = 'localhost';  // Database host
$db_user = 'root';  // Database username
$db_pass = 'tl_nmb25';  // Database password
$db_name = 'testlink_db';  // Database name

// Log database credentials (without password for security)
file_put_contents(__DIR__ . '/testcase_data_log.txt', "\nDatabase config loaded:\nHost: {$db_host}\nUser: {$db_user}\nDatabase: {$db_name}\n", FILE_APPEND);
