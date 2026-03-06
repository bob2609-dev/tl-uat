<?php
require_once('config.inc.php');
require_once('lib/functions/ldap_api.php');

header('Content-Type: text/html; charset=utf-8');
echo "<h1>TestLink LDAP Authentication Test</h1>";

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    echo "<p>Testing LDAP authentication for user: $username</p>";
    
    $ldap = new tlLDAP();
    $result = $ldap->authenticate($username, $password);
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>LDAP Authentication SUCCESS!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>LDAP Authentication FAILED!</p>";
    }
    
    echo "<p><a href='test_ldap_login.php'>Try again</a></p>";
} else {
    // Show login form
    echo "<form method='post' action='test_ldap_login.php'>";
    echo "<p>Username: <input type='text' name='username'></p>";
    echo "<p>Password: <input type='password' name='password'></p>";
    echo "<p><input type='submit' value='Test LDAP Login'></p>";
    echo "</form>";
}

echo "<p><a href='index.php'>Return to TestLink</a></p>";