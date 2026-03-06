<?php
/**
 * Generate SQL Query for Tester Report - Project ID 242099
 */

$projectId = 242099;
$planId = 0; // All test plans
$buildId = 0; // All builds
$testerId = 0; // All testers
$startDate = ''; // No date filter
$endDate = ''; // No date filter

// This is the exact query from runTesterReport function in tester_execution_report.php
$sql = "SELECT 
            u.id AS tester_id,
            u.login AS tester_login,
            u.first AS tester_first,
            u.last AS tester_last,
            CONCAT(u.first, ' ', u.last) AS tester_name,
            COUNT(DISTINCT tptc.tcversion_id) AS total_assigned,
            COUNT(DISTINCT CASE WHEN e.id IS NOT NULL THEN tptc.tcversion_id END) AS total_executions,
            COUNT(DISTINCT CASE WHEN e.status = 'p' THEN tptc.tcversion_id END) AS passed,
            COUNT(DISTINCT CASE WHEN e.status = 'f' THEN tptc.tcversion_id END) AS failed,
            COUNT(DISTINCT CASE WHEN e.status = 'b' THEN tptc.tcversion_id END) AS blocked,
            COUNT(DISTINCT CASE WHEN e.id IS NULL THEN tptc.tcversion_id END) AS not_run,
            MAX(e.execution_ts) AS last_execution
        FROM 
            -- Start with all test case assignments to test plans
            testplan_tcversions tptc
            JOIN testplans tp ON tptc.testplan_id = tp.id
            JOIN testprojects tproj ON tp.testproject_id = tproj.id
            -- LEFT JOIN with user assignments to get assigned testers
            LEFT JOIN user_assignments ua ON ua.feature_id = tptc.tcversion_id 
                AND ua.type IN (1, 2) 
                AND ua.status = 1
            -- LEFT JOIN with users to get tester details
            LEFT JOIN users u ON ua.user_id = u.id
            -- LEFT JOIN with executions to find actual executions
            LEFT JOIN executions e ON e.tcversion_id = tptc.tcversion_id 
                AND e.testplan_id = tptc.testplan_id 
                AND e.platform_id = tptc.platform_id
                AND (ua.build_id = e.build_id OR ua.build_id = 0 OR ua.build_id IS NULL)
        WHERE 1=1";

// Apply filters
if ($projectId > 0) {
    $sql .= " AND tp.testproject_id = " . intval($projectId);
}

if ($planId > 0) {
    $sql .= " AND tptc.testplan_id = " . intval($planId);
}

if ($buildId > 0) {
    $sql .= " AND (ua.build_id = " . intval($buildId) . " OR ua.build_id = 0 OR ua.build_id IS NULL)";
}

if ($testerId > 0) {
    $sql .= " AND ua.user_id = " . intval($testerId);
}

// Apply date filters to executions only
if (!empty($startDate)) {
    $sql .= " AND (e.execution_ts IS NULL OR e.execution_ts >= '" . addslashes($startDate . ' 00:00:00') . "')";
}

if (!empty($endDate)) {
    $sql .= " AND (e.execution_ts IS NULL OR e.execution_ts <= '" . addslashes($endDate . ' 23:59:59') . "')";
}

// Only include testers that have assignments
$sql .= " AND u.id IS NOT NULL";

$sql .= " GROUP BY u.id, u.login, u.first, u.last
          HAVING total_assigned > 0
          ORDER BY u.first, u.last";

echo "<h2>SQL Query for Project ID 242099</h2>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; word-wrap: break-word;'>";
echo htmlspecialchars($sql);
echo "</pre>";

echo "<h2>Query Explanation</h2>";
echo "<ul>";
echo "<li><strong>Starts from testplan_tcversions</strong>: All test cases assigned to test plans in project 242099</li>";
echo "<li><strong>LEFT JOIN user_assignments</strong>: Finds users assigned to those test cases (type 1 or 2)</li>";
echo "<li><strong>LEFT JOIN users</strong>: Gets user details for assigned users</li>";
echo "<li><strong>LEFT JOIN executions</strong>: Finds actual executions for those test cases</li>";
echo "<li><strong>COUNT conditions</strong>: Counts passed, failed, blocked, and not run (NULL executions)</li>";
echo "<li><strong>FILTERS</strong>: Only includes users with assignments (u.id IS NOT NULL) and at least one assignment (HAVING total_assigned > 0)</li>";
echo "</ul>";

echo "<h2>Test the Query</h2>";
echo "<p>You can run this query directly in your database to see what data it returns.</p>";
echo "<p>If it returns no results, the issue might be:</p>";
echo "<ul>";
echo "<li>No test case assignments in project 242099</li>";
echo "<li>No user assignments with type 1 or 2</li>";
echo "<li>No users matching the assignment criteria</li>";
echo "</ul>";
?>
