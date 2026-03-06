<?php
// Test the current SQL structure from the PHP file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Simulate the current PHP SQL generation
$projectId = 242099;
$buildId = 4;
$testerId = 111;
$reportType = 'all';

// Generate the same SQL as the current PHP file
$sql = "
    SELECT 
        ROW_NUMBER() OVER (ORDER BY u.first, u.last) AS serial_no,
        u.id AS tester_id,
        CONCAT(u.first, ' ', u.last) AS tester_name,
        COUNT(DISTINCT ua.id) AS total_assigned,
        COUNT(DISTINCT CASE WHEN e.id IS NOT NULL THEN tptc.id END) AS total_executions,
        COUNT(DISTINCT CASE WHEN e.status = 'p' THEN tptc.id END) AS passed,
        COUNT(DISTINCT CASE WHEN e.status = 'f' THEN tptc.id END) AS failed,
        COUNT(DISTINCT CASE WHEN e.status = 'b' THEN tptc.id END) AS blocked,
        COUNT(DISTINCT CASE WHEN e.id IS NULL THEN tptc.id END) AS not_run,
        MAX(e.execution_ts) AS last_execution
    FROM users u
    LEFT JOIN user_assignments ua ON u.id = ua.user_id 
        AND ua.type IN (1, 2) 
        AND ua.status = 1 " . ($buildId > 0 ? " AND ua.build_id = " . intval($buildId) : "") . "
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
    LEFT JOIN (
        SELECT e1.tcversion_id, e1.testplan_id, e1.platform_id, e1.build_id, e1.id, e1.status, e1.execution_ts
        FROM executions e1
        INNER JOIN (
            SELECT tcversion_id, testplan_id, platform_id, build_id, MAX(execution_ts) as max_execution_ts
            FROM executions 
            GROUP BY tcversion_id, testplan_id, platform_id, build_id
        ) e2 ON e1.tcversion_id = e2.tcversion_id 
            AND e1.testplan_id = e2.testplan_id 
            AND e1.platform_id = e2.platform_id
            AND e1.build_id = e2.build_id
            AND e1.execution_ts = e2.max_execution_ts
    ) e ON e.tcversion_id = tptc.tcversion_id 
        AND e.testplan_id = tptc.testplan_id 
        AND e.platform_id = tptc.platform_id " . ($buildId > 0 ? " AND e.build_id = " . intval($buildId) : "") . "
    WHERE u.active = 1
        AND (tp.testproject_id = " . intval($projectId) . " OR tp.testproject_id IS NULL)
    GROUP BY u.id, u.login, u.first, u.last";

echo "<h3>Current PHP SQL Structure:</h3>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

// Compare with our working test query
$workingSql = "
SELECT 
    COUNT(DISTINCT ua.id) as total_assigned,
    COUNT(DISTINCT CASE WHEN e.id IS NOT NULL THEN tptc.id END) as total_executions,
    COUNT(DISTINCT CASE WHEN e.id IS NULL THEN tptc.id END) as not_run_test_cases,
    COUNT(DISTINCT CASE WHEN e.status = 'p' THEN tptc.id END) as passed_test_cases,
    COUNT(DISTINCT CASE WHEN e.status = 'f' THEN tptc.id END) as failed_test_cases,
    COUNT(DISTINCT CASE WHEN e.status = 'b' THEN tptc.id END) as blocked_test_cases
FROM 
    users u
    LEFT JOIN user_assignments ua ON ua.user_id = u.id 
        AND ua.type IN (1, 2) 
        AND ua.status = 1
        AND ua.build_id = 4
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
    LEFT JOIN (
        SELECT 
            e1.tcversion_id,
            e1.testplan_id, 
            e1.platform_id,
            e1.build_id,
            e1.id,
            e1.status,
            e1.execution_ts
        FROM executions e1
        INNER JOIN (
            SELECT 
                tcversion_id, 
                testplan_id, 
                platform_id, 
                build_id,
                MAX(execution_ts) as max_execution_ts
            FROM executions 
            GROUP BY tcversion_id, testplan_id, platform_id, build_id
        ) e2 ON e1.tcversion_id = e2.tcversion_id 
            AND e1.testplan_id = e2.testplan_id 
            AND e1.platform_id = e2.platform_id
            AND e1.build_id = e2.build_id
            AND e1.execution_ts = e2.max_execution_ts
    ) e ON e.tcversion_id = tptc.tcversion_id 
        AND e.testplan_id = tptc.testplan_id 
        AND e.platform_id = tptc.platform_id
        AND e.build_id = 4
WHERE 1=1 
    AND u.active = 1
    AND (u.id = 111 OR ua.id IS NULL OR tp.testproject_id = 242099)
    AND u.id = 111";

echo "<h3>Working Test Query:</h3>";
echo "<pre>" . htmlspecialchars($workingSql) . "</pre>";

echo "<h3>Key Differences:</h3>";
echo "<ul>";
echo "<li><strong>Current PHP:</strong> Uses OR condition for project filtering</li>";
echo "<li><strong>Working Test:</strong> Uses specific user filtering</li>";
echo "<li><strong>Current PHP:</strong> Uses OR tp.testproject_id IS NULL</li>";
echo "<li><strong>Working Test:</strong> Uses exact project matching</li>";
echo "</ul>";
?>
