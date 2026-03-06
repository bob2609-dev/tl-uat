<?php
/**
 * Debug script to check database structure and data
 */

require_once('../../config.inc.php');
require_once('common.php');

// Initialize database
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];

echo "<h1>Database Debug - Test Case Assignments</h1>";

// Check 1: Look at user_assignments table structure
echo "<h2>1. User Assignments Table Structure</h2>";
$sql = "DESCRIBE user_assignments";
$result = $db->exec_query($sql);
echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $db->fetch_array($result)) {
    echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td><td>{$row['Default']}</td><td>{$row['Extra']}</td></tr>";
}
echo "</table>";

// Check 2: Look at sample data in user_assignments
echo "<h2>2. Sample User Assignments Data</h2>";
$sql = "SELECT * FROM user_assignments LIMIT 10";
$result = $db->exec_query($sql);
if ($result && $db->num_rows($result) > 0) {
    echo "<table border='1'><tr>";
    for ($i = 0; $i < $db->num_fields($result); $i++) {
        $field = $db->field_name($result, $i);
        echo "<th>{$field}</th>";
    }
    echo "</tr>";
    while ($row = $db->fetch_array($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found in user_assignments table</p>";
}

// Check 3: Look at testplan_tcversions table
echo "<h2>3. Test Plan TC Versions Sample Data</h2>";
$sql = "SELECT * FROM testplan_tcversions LIMIT 5";
$result = $db->exec_query($sql);
if ($result && $db->num_rows($result) > 0) {
    echo "<table border='1'><tr>";
    for ($i = 0; $i < $db->num_fields($result); $i++) {
        $field = $db->field_name($result, $i);
        echo "<th>{$field}</th>";
    }
    echo "</tr>";
    while ($row = $db->fetch_array($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found in testplan_tcversions table</p>";
}

// Check 4: Look at executions table
echo "<h2>4. Executions Sample Data</h2>";
$sql = "SELECT * FROM executions LIMIT 5";
$result = $db->exec_query($sql);
if ($result && $db->num_rows($result) > 0) {
    echo "<table border='1'><tr>";
    for ($i = 0; $i < $db->num_fields($result); $i++) {
        $field = $db->field_name($result, $i);
        echo "<th>{$field}</th>";
    }
    echo "</tr>";
    while ($row = $db->fetch_array($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data found in executions table</p>";
}

// Check 5: Try the old query (executions-based)
echo "<h2>5. Testers with Executions (Old Method)</h2>";
$sql = "SELECT DISTINCT u.id, u.login, u.first, u.last 
        FROM users u
        JOIN executions e ON u.id = e.tester_id
        JOIN testplans tp ON e.testplan_id = tp.id
        LIMIT 10";
$result = $db->exec_query($sql);
if ($result && $db->num_rows($result) > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Login</th><th>First</th><th>Last</th></tr>";
    while ($row = $db->fetch_array($result)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['login']}</td><td>{$row['first']}</td><td>{$row['last']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No testers found with executions</p>";
}

// Check 6: Try the new query (assignments-based)
echo "<h2>6. Testers with Assignments (New Method)</h2>";
$sql = "SELECT DISTINCT u.id, u.login, u.first, u.last 
        FROM users u
        JOIN user_assignments ua ON u.id = ua.user_id
        JOIN testplan_tcversions tptc ON ua.feature_id = tptc.tcversion_id
        JOIN testplans tp ON tptc.testplan_id = tp.id
        WHERE ua.type = 1
        AND ua.status = 1
        LIMIT 10";
$result = $db->exec_query($sql);
if ($result && $db->num_rows($result) > 0) {
    echo "<table border='1'><tr><th>ID</th><th>Login</th><th>First</th><th>Last</th></tr>";
    while ($row = $db->fetch_array($result)) {
        echo "<tr><td>{$row['id']}</td><td>{$row['login']}</td><td>{$row['first']}</td><td>{$row['last']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No testers found with assignments</p>";
}

// Check 7: Look at assignment types
echo "<h2>7. Assignment Types</h2>";
$sql = "SELECT DISTINCT type FROM user_assignments";
$result = $db->exec_query($sql);
if ($result && $db->num_rows($result) > 0) {
    echo "<table border='1'><tr><th>Type</th></tr>";
    while ($row = $db->fetch_array($result)) {
        echo "<tr><td>{$row['type']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No assignment types found</p>";
}

// Check 8: Look at assignment statuses
echo "<h2>8. Assignment Statuses</h2>";
$sql = "SELECT DISTINCT status FROM user_assignments";
$result = $db->exec_query($sql);
if ($result && $db->num_rows($result) > 0) {
    echo "<table border='1'><tr><th>Status</th></tr>";
    while ($row = $db->fetch_array($result)) {
        echo "<tr><td>{$row['status']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No assignment statuses found</p>";
}

echo "<h2>Debug Complete</h2>";
?>
