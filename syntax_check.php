<?php
/**
 * Simple syntax check for the modified files
 */

echo "Checking syntax of modified files...\n\n";

// Check execTreeMenu.inc.php
echo "1. Checking execTreeMenu.inc.php...\n";
$output = [];
$return_var = 0;
exec('php -l "lib/functions/execTreeMenu.inc.php" 2>&1', $output, $return_var);
if ($return_var === 0) {
    echo "   ✓ Syntax OK\n";
} else {
    echo "   ✗ Syntax Error:\n";
    foreach ($output as $line) {
        echo "     $line\n";
    }
}

// Check treeMenu.inc.php  
echo "\n2. Checking treeMenu.inc.php...\n";
$output = [];
$return_var = 0;
exec('php -l "lib/functions/treeMenu.inc.php" 2>&1', $output, $return_var);
if ($return_var === 0) {
    echo "   ✓ Syntax OK\n";
} else {
    echo "   ✗ Syntax Error:\n";
    foreach ($output as $line) {
        echo "     $line\n";
    }
}

// Check custom_config.inc.php
echo "\n3. Checking custom_config.inc.php...\n";
$output = [];
$return_var = 0;
exec('php -l "custom_config.inc.php" 2>&1', $output, $return_var);
if ($return_var === 0) {
    echo "   ✓ Syntax OK\n";
} else {
    echo "   ✗ Syntax Error:\n";
    foreach ($output as $line) {
        echo "     $line\n";
    }
}

echo "\n=== Syntax Check Complete ===\n";
?>
