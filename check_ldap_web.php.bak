<?php
echo "LDAP extension loaded: " . (extension_loaded('ldap') ? 'Yes' : 'No') . "<br>";
echo "ldap_connect function exists: " . (function_exists('ldap_connect') ? 'Yes' : 'No') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PHP loaded config file: " . php_ini_loaded_file() . "<br>";

// Show all loaded extensions
echo "<h3>Loaded Extensions:</h3>";
$extensions = get_loaded_extensions();
sort($extensions);
foreach ($extensions as $ext) {
    echo $ext . "<br>";
}
?>