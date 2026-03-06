<?php
// PHP script to generate SQL from CSV
// This script will read Userlist.csv and output a SQL file with INSERT statements for all users

// Define file paths
$inputCsv = 'Userlist.csv';
$outputSql = 'complete_insert_users.sql';

// Check if input file exists
if (!file_exists($inputCsv)) {
    die("Error: Cannot find input file $inputCsv\n");
}

// Open input file
$csvHandle = fopen($inputCsv, 'r');
if (!$csvHandle) {
    die("Error: Cannot open input file $inputCsv\n");
}

// Open output file
$sqlHandle = fopen($outputSql, 'w');
if (!$sqlHandle) {
    fclose($csvHandle);
    die("Error: Cannot open output file $outputSql for writing\n");
}

// Write SQL header
$header = "-- SQL Script to insert users from Userlist.csv\n";
$header .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
$header .= "-- Sets role_id to 7 for all users, leaves password empty, and sets auth_method to LDAP\n";
$header .= "-- Checks if users exist via email or login fields (to avoid duplicate key errors)\n\n";

fwrite($sqlHandle, $header);

// Skip header row in CSV
fgetcsv($csvHandle);

// Process each user in CSV
$userCount = 0;
while (($data = fgetcsv($csvHandle)) !== FALSE) {
    $userCount++;
    
    // Extract values
    $firstName = addslashes($data[0]);
    $lastName = addslashes($data[1]);
    $email = addslashes($data[2]);
    $login = substr($email, 0, strpos($email, '@'));
    
    // Create SQL for this user - MODIFIED to check for existing login values too
    $sql = "-- $firstName $lastName\n";
    $sql .= "INSERT INTO users (login, password, role_id, email, first, last, locale, default_testproject_id, active, script_key, cookie_string, auth_method, creation_ts, expiration_date)\n";
    $sql .= "SELECT \n";
    $sql .= "    '$login', \n";
    $sql .= "    '', \n";
    $sql .= "    7, \n";
    $sql .= "    '$email', \n";
    $sql .= "    '$firstName', \n";
    $sql .= "    '$lastName', \n";
    $sql .= "    'en_GB', \n";
    $sql .= "    NULL, \n";
    $sql .= "    1, \n";
    $sql .= "    NULL, \n";
    $sql .= "    MD5(CONCAT('$email', NOW(), RAND())), \n";
    $sql .= "    'LDAP', \n";
    $sql .= "    NOW(), \n";
    $sql .= "    NULL\n";
    $sql .= "WHERE NOT EXISTS (\n";
    $sql .= "    SELECT 1 FROM users \n";
    $sql .= "    WHERE \n";
    $sql .= "        LOWER(email) = LOWER('$email')\n";
    $sql .= "        OR LOWER(REPLACE(email, '@nmbtz.com', '@nmbbank.co.tz')) = LOWER(REPLACE('$email', '@nmbtz.com', '@nmbbank.co.tz'))\n";
    $sql .= "        OR LOWER(login) = LOWER('$login')\n";
    $sql .= ");\n\n";
    
    fwrite($sqlHandle, $sql);
}

// Close files
fclose($csvHandle);
fclose($sqlHandle);

echo "Successfully generated SQL for $userCount users in $outputSql\n";
?>
