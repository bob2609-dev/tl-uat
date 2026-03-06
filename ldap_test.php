<?php
/**
 * TestLink LDAP Connection Test Script
 * This script tests the LDAP connection using the same settings as TestLink
 * Save as ldap_test.php and run from command line or browser
 */

// Configuration (use the same values as in your TestLink config)
$ldap_server = 'ldap://10.200.221.11';  // Replace with your actual LDAP server
$ldap_port = 389;
$ldap_version = 3;
$bind_dn = 'CN=Service.testauto,OU=Service Accounts,DC=nmbtz,DC=com';  // Replace with your bind DN
$bind_password = 'p@ssw0rd';  // Replace with your bind password
$base_dn = 'dc=nmbtz,dc=com';  // Replace with your base DN
$search_filter = '(objectClass=user)';
$search_attribute = 'sAMAccountName';  // For Active Directory
$test_username = 'mwaimu.mtingele';  // Replace with a known username to search for

// Connect to LDAP server
echo "Connecting to LDAP server: $ldap_server:$ldap_port...\n";
$ldap_conn = ldap_connect($ldap_server, $ldap_port);

if (!$ldap_conn) {
    echo "Failed to connect to LDAP server\n";
    exit(1);
}

// Set LDAP options
echo "Setting LDAP protocol version to $ldap_version...\n";
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

// Bind with service account
echo "Binding with DN: $bind_dn...\n";
$bind = @ldap_bind($ldap_conn, $bind_dn, $bind_password);

if (!$bind) {
    echo "LDAP bind failed: " . ldap_error($ldap_conn) . "\n";
    exit(1);
}

echo "Bind successful!\n";

// Search for a test user
$user_filter = "(&$search_filter($search_attribute=$test_username))";
echo "Searching with filter: $user_filter\n";

$search = ldap_search($ldap_conn, $base_dn, $user_filter);

if (!$search) {
    echo "Search failed: " . ldap_error($ldap_conn) . "\n";
    exit(1);
}

$entries = ldap_get_entries($ldap_conn, $search);
echo "Found " . $entries["count"] . " entries\n";

// Display first user's attributes if found
if ($entries["count"] > 0) {
    echo "First user details:\n";
    foreach ($entries[0] as $key => $value) {
        if (is_array($value)) {
            if ($key != 'count') {
                echo "$key: ";
                if (isset($value[0])) {
                    echo $value[0] . "\n";
                }
            }
        }
    }
}

// Close connection
ldap_close($ldap_conn);
echo "LDAP test completed successfully\n";
?>