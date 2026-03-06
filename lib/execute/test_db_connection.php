<?php
// Simple database connectivity test
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== DATABASE CONNECTIVITY TEST ===\n";

// Database parameters from config
$host = 'localhost';
$user = 'tl_uat';
$pass = 'tl_uat269';
$dbname = 'tl_uat';

echo "Testing connection to:\n";
echo "- Host: $host\n";
echo "- Database: $dbname\n";
echo "- User: $user\n\n";

// Test 1: Basic mysqli connection
echo "1. Testing basic mysqli connection...\n";
try {
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    
    if ($mysqli->connect_error) {
        echo "✗ Connection failed: " . $mysqli->connect_error . "\n";
        echo "  Error code: " . $mysqli->connect_errno . "\n";
    } else {
        echo "✓ Mysqli connection successful\n";
        
        // Test a simple query
        $result = $mysqli->query("SELECT 1 as test_value");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "✓ Query successful: " . $row['test_value'] . "\n";
        } else {
            echo "✗ Query failed: " . $mysqli->error . "\n";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Test with PDO if available
echo "2. Testing PDO connection...\n";
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ PDO connection successful\n";
    
    $stmt = $pdo->query("SELECT 1 as test_value");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ PDO Query successful: " . $row['test_value'] . "\n";
    
} catch (PDOException $e) {
    echo "✗ PDO Exception: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
?>
