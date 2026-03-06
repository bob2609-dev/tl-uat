<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * Kint Parser Fix for PHP 8
 * This file fixes the critical error in kintParser.class.php
 *
 * @filesource  kint_fix.php
 * @package     TestLink
 */

// Check if the Kint parser file exists
$kintParserFile = dirname(dirname(dirname(__FILE__))) . '/third_party/kint/inc/kintParser.class.php';

if (file_exists($kintParserFile)) {
    // Read the file content
    $content = file_get_contents($kintParserFile);
    
    // Replace the problematic curly brace syntax with square bracket syntax
    // Line 463: Replace $var{0} with $var[0]
    $content = preg_replace('/\$([a-zA-Z0-9_]+){([0-9]+)}/', '\$$1[$2]', $content);
    
    // Also fix other potential curly brace array/string access issues
    $content = preg_replace('/(\$[a-zA-Z0-9_]+(?:\[[^\]]+\])*){([0-9]+|\$[a-zA-Z0-9_]+)}/', '$1[$2]', $content);
    
    // Write the modified content back to the file
    file_put_contents($kintParserFile, $content);
    
    error_log('Kint parser file fixed for PHP 8 compatibility.');
}

// Fix ADODB Iterator return type issues by adding a .htaccess file with PHP directive
$htaccessPath = dirname(dirname(dirname(__FILE__))) . '/.htaccess';
$htaccessContent = "\n# PHP 8 Compatibility Settings\n<IfModule mod_php.c>\n  php_value error_reporting E_ALL & ~E_DEPRECATED & ~E_NOTICE\n</IfModule>\n";

// Check if .htaccess exists and append our settings if it does
if (file_exists($htaccessPath)) {
    // Add our settings if they don't already exist
    $currentContent = file_get_contents($htaccessPath);
    if (strpos($currentContent, 'PHP 8 Compatibility Settings') === false) {
        file_put_contents($htaccessPath, $currentContent . $htaccessContent);
        error_log('Added PHP 8 compatibility settings to .htaccess');
    }
} else {
    // Create a new .htaccess file with our settings
    file_put_contents($htaccessPath, $htaccessContent);
    error_log('Created .htaccess with PHP 8 compatibility settings');
}
