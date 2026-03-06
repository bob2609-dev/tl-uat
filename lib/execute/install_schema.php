<?php
/**
 * Install Custom Bug Tracking Integration Schema
 * Run this script to create the required database tables
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type
header('Content-Type: text/plain');

echo "=== Custom Bug Tracking Integration Schema Installation ===\n\n";

try {
    // Include TestLink configuration
    require_once('../config.inc.php');
    require_once('lib/functions/common.php');
    
    // Initialize database using TestLink's standard method
    $db = new database(DB_TYPE);
    doDBConnect($db, database::ONERROREXIT);
    
    echo "✓ Database connection established\n";
    
    // Read and execute schema
    $schemaFile = 'sql/custom_bugtrack_integration_schema_safe.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: $schemaFile");
    }
    
    $schema = file_get_contents($schemaFile);
    if (!$schema) {
        throw new Exception("Failed to read schema file");
    }
    
    echo "✓ Schema file loaded\n";
    
    // Split schema into individual statements
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    
    $executed = 0;
    $errors = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || preg_match('/^--/', $statement)) {
            continue;
        }
        
        try {
            $result = $db->exec_query($statement);
            if ($result) {
                $executed++;
                echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
            }
        } catch (Exception $e) {
            $errors++;
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "   Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
    
    echo "\n=== Installation Summary ===\n";
    echo "Statements executed: $executed\n";
    echo "Errors: $errors\n";
    
    if ($errors === 0) {
        echo "\n✓ Schema installed successfully!\n";
        
        // Verify tables were created
        $tables = array('custom_bugtrack_integrations', 'custom_bugtrack_project_mapping', 'custom_bugtrack_integration_log');
        foreach ($tables as $table) {
            $sql = "SHOW TABLES LIKE '$table'";
            $result = $db->fetchFirstRow($sql);
            if ($result) {
                echo "✓ Table '$table' exists\n";
            } else {
                echo "✗ Table '$table' NOT found\n";
            }
        }
    } else {
        echo "\n✗ Installation completed with errors\n";
    }
    
} catch (Exception $e) {
    echo "\n✗ Installation failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Installation Complete ===\n";
?>
