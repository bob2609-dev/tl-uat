<?php
/**
 * Simple Fix for TestLink Authentication
 */

echo "<h1>TestLink Authentication Fix</h1>";

// Function to output message
function output($message, $type = 'info') {
    $style = '';
    switch($type) {
        case 'success': $style = 'color: green; font-weight: bold;'; break;
        case 'error': $style = 'color: red; font-weight: bold;'; break;
        case 'warning': $style = 'color: orange;'; break;
        default: $style = 'color: blue;';
    }
    echo "<p style=\"$style\">$message</p>\n";
}

// Target paths
$targetPath = 'D:/xampp/htdocs/tl-uat';
output("Target path: $targetPath");

// Create logs directory if it doesn't exist
$logsDir = $targetPath . '/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
    output("Created logs directory", 'success');
}

// Fix doAuthorize.php file directly
$doAuthFile = $targetPath . '/lib/functions/doAuthorize.php';
if (!file_exists($doAuthFile)) {
    output("Could not find doAuthorize.php at: $doAuthFile", 'error');
    die();
}

// Create backup of file
if (!file_exists($doAuthFile . '.bak')) {
    copy($doAuthFile, $doAuthFile . '.bak');
    output("Created backup of doAuthorize.php", 'success');
}

// Read the file
$content = file_get_contents($doAuthFile);

// Replace calls to ldap_authenticate with the proper LDAP class usage
if (strpos($content, 'ldap_authenticate') !== false) {
    // Find the problematic code
    $oldCode = "if (ldap_authenticate($user->login, $password)) {";
    $newCode = "require_once(TL_ABS_PATH . 'lib/functions/ldap_api.php');
            \$ldap = new tlLDAP();
            if (\$ldap->authenticate(\$user->login, \$password)) {";
    
    // Replace the code
    $content = str_replace($oldCode, $newCode, $content);
    
    // Write the updated file
    file_put_contents($doAuthFile, $content);
    output("Fixed LDAP authentication code in doAuthorize.php", 'success');
} else {
    output("Could not find ldap_authenticate in doAuthorize.php - it may already be fixed", 'warning');
}

// Create a simple LDAP test script
$testLdapFile = $targetPath . '/test_ldap_login.php';
$testLdapContent = <<<'EOD'
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
EOD;

file_put_contents($testLdapFile, $testLdapContent);
output("Created LDAP test script at test_ldap_login.php", 'success');

output("\nFix complete! Here's what to do next:", 'success');
echo "<ol>";
echo "<li>Restart your Apache web server in XAMPP Control Panel</li>";
echo "<li>Try the test script: <a href='http://localhost/tl-uat/test_ldap_login.php' target='_blank'>http://localhost/tl-uat/test_ldap_login.php</a></li>";
echo "<li>If the test script works but TestLink login still fails, there may be other issues</li>";
echo "<li>Check the logs in the logs/ directory for more information</li>";
echo "</ol>";
