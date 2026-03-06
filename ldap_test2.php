<?php
// Specific test for your user
$ldap_server = 'ldap://10.200.221.11';
$ldap_port = 389;
$ldap_version = 3;
$bind_dn = 'CN=Service.testauto,OU=Service Accounts,DC=nmbtz,DC=com';
$bind_password = 'p@ssw0rd';
$base_dn = 'dc=nmbtz,dc=com';
$search_filter = '(&(objectClass=user)(sAMAccountName=mwaimu.mtingele))';

// Connect to LDAP server
echo "Connecting to LDAP server: $ldap_server:$ldap_port...\n";
$ldap_conn = ldap_connect($ldap_server, $ldap_port);

if (!$ldap_conn) {
    echo "Failed to connect to LDAP server\n";
    exit(1);
}

// Set LDAP options
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);
ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);

// Bind with service account
echo "Binding with service account...\n";
$bind = @ldap_bind($ldap_conn, $bind_dn, $bind_password);

if (!$bind) {
    echo "LDAP bind failed: " . ldap_error($ldap_conn) . "\n";
    exit(1);
}

echo "Bind successful!\n";

// Search for specific user
echo "Searching for user with filter: $search_filter\n";
$search = ldap_search($ldap_conn, $base_dn, $search_filter);

if (!$search) {
    echo "Search failed: " . ldap_error($ldap_conn) . "\n";
    exit(1);
}

$entries = ldap_get_entries($ldap_conn, $search);
echo "Found " . $entries["count"] . " entries\n";

// Display user attributes if found
if ($entries["count"] > 0) {
    echo "User found! Attributes:\n";
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
    
    // Test user authentication
    echo "\nTesting direct user authentication...\n";
    $user_dn = $entries[0]['distinguishedname'][0];
    echo "User DN: $user_dn\n";
    
    // Close the service account connection
    ldap_unbind($ldap_conn);
    
    // Try to bind as the user
    $user_conn = ldap_connect($ldap_server, $ldap_port);
    ldap_set_option($user_conn, LDAP_OPT_PROTOCOL_VERSION, $ldap_version);
    ldap_set_option($user_conn, LDAP_OPT_REFERRALS, 0);
    
    // Ask for password
    echo "Enter password for $user_dn: ";
    // In a real test script, you'd prompt for password
    // For security reasons, we'll just use a placeholder here
    $password = "user_password_here";
    
    $user_bind = @ldap_bind($user_conn, $user_dn, $password);
    if ($user_bind) {
        echo "User authentication successful!\n";
    } else {
        echo "User authentication failed: " . ldap_error($user_conn) . "\n";
    }
    
    ldap_close($user_conn);
} else {
    echo "User not found in LDAP directory\n";
}

// Close connection
ldap_close($ldap_conn);
echo "LDAP test completed\n";
?>