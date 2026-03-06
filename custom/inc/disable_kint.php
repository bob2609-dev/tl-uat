<?php
/**
 * TestLink - Kint Disabler for PHP 8 Compatibility
 * This script disables the Kint debugging tool which is causing fatal errors in PHP 8
 */

// Define path to Kint class file
$kintClassFile = dirname(dirname(dirname(__FILE__))) . '/third_party/kint/Kint.class.php';

// Check if file exists
if (file_exists($kintClassFile)) {
    // Create backup if it doesn't already exist
    $backupFile = $kintClassFile . '.original';
    if (!file_exists($backupFile)) {
        copy($kintClassFile, $backupFile);
        echo "Created backup of original Kint.class.php as Kint.class.php.original\n";
    }
    
    // Replace Kint with a stub class that does nothing
    $stubContent = "<?php\n/**\n * Kint Stub Class for PHP 8 Compatibility\n * This is a replacement for the original Kint that does nothing\n */\n\nclass Kint {\n    public static function dump() { return null; }\n    public static function trace() { return null; }\n    public static function enabled() { return false; }\n    \n    // Add any other static methods that might be called in the code\n    public static function _dump() { return null; }\n    public static function _trace() { return null; }\n}\n\n// Define constants to prevent errors\nif (!defined('KINT_DIR')) {\n    define('KINT_DIR', dirname(__FILE__));
}\n\nif (!defined('KINT_SKIP_HELPERS')) {\n    define('KINT_SKIP_HELPERS', true);\n}\n";
    
    // Write the stub file
    file_put_contents($kintClassFile, $stubContent);
    echo "Successfully disabled Kint by replacing with stub class\n";
    
    // Also create a similar stub for kintParser.class.php
    $kintParserFile = dirname(dirname(dirname(__FILE__))) . '/third_party/kint/inc/kintParser.class.php';
    if (file_exists($kintParserFile)) {
        // Create backup if it doesn't already exist
        $backupParserFile = $kintParserFile . '.original';
        if (!file_exists($backupParserFile)) {
            copy($kintParserFile, $backupParserFile);
            echo "Created backup of original kintParser.class.php\n";
        }
        
        // Create empty parser class
        $parserStub = "<?php\n/**\n * Stub for kintParser to avoid PHP 8 errors\n */\n\nclass kintParser {\n    public static function factory() { return new self(); }\n    public function parse() { return null; }\n}\n";
        
        file_put_contents($kintParserFile, $parserStub);
        echo "Successfully disabled kintParser\n";
    }
    
    echo "\nKint has been completely disabled. TestLink should now work without the PHP 8 fatal errors.\n";
    echo "If you want to restore the original Kint functionality, rename the .original files back to their original names.\n";
} else {
    echo "Could not find Kint.class.php at expected location: $kintClassFile\n";
    echo "Please check the path and try again.\n";
}
