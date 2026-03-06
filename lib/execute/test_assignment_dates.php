<?php
/**
 * Test if assignment dates are filtering out executions
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

testlinkInitPage($db, false, false, false);

echo "=== Checking Assignment Dates vs Execution Dates ===\n";

// Check executions on 2026-01-29 and their assignment dates
$sql = "SELECT 
    e.id as execution_id,
    e.execution_ts,
    e.tester_id,
    u.first,
    u.last,
    ua.creation_ts as assignment_date,
    DATEDIFF(e.execution_ts, ua.creation_ts) as days_diff
FROM executions e
JOIN users u ON e.tester_id = u.id
LEFT JOIN user_assignments ua ON e.tester_id = ua.user_id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
    AND ua.feature_id IN (
        SELECT tptc.id 
        FROM testplan_tcversions tptc 
        WHERE tptc.tcversion_id = e.tcversion_id 
        AND tptc.testplan_id = e.testplan_id
        LIMIT 1
    )
WHERE DATE(e.execution_ts) = '2026-01-29'
ORDER BY e.execution_ts DESC
LIMIT 10";

$result = $db->exec_query($sql);
if ($result) {
    while ($row = $db->fetch_array($result)) {
        echo "Execution: {$row['execution_id']}, Tester: {$row['first']} {$row['last']}\n";
        echo "  Execution Date: {$row['execution_ts']}\n";
        echo "  Assignment Date: " . ($row['assignment_date'] ?? 'NULL') . "\n";
        echo "  Days Difference: " . ($row['days_diff'] ?? 'NULL') . "\n";
        echo "---\n";
    }
}

// Now test without the assignment date filter
echo "\n=== Testing without assignment date filter ===\n";

// Temporarily modify the stored procedure logic to remove assignment date filtering
$sql = "SELECT 
    u.id AS tester_id,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    COUNT(DISTINCT ua.feature_id) AS total_assigned,
    COUNT(DISTINCT e.id) AS total_executions
FROM users u
JOIN user_assignments ua ON u.id = ua.user_id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
JOIN testplans tp ON tptc.testplan_id = tp.id
LEFT JOIN executions e ON 
    e.tcversion_id = tptc.tcversion_id 
    AND e.testplan_id = tptc.testplan_id 
    AND e.platform_id = tptc.platform_id 
    AND DATE(e.execution_ts) = '2026-01-29'
WHERE tp.testproject_id = 1
    AND u.active = 1
GROUP BY u.id, u.first, u.last
HAVING total_executions > 0
ORDER BY total_executions DESC
LIMIT 10";

$result = $db->exec_query($sql);
if ($result) {
    while ($row = $db->fetch_array($result)) {
        echo "Tester: {$row['tester_name']}, Executions: {$row['total_executions']}\n";
    }
}
?>
