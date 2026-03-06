<?php
// Simple test to verify PHP SQL generation
$projectId = 242099;
$planId = 0;
$buildId = 4;
$testerId = 111;
$reportType = 'all';

// Test the "All Testers" SQL generation
$sql = "
    SELECT 
        ROW_NUMBER() OVER (ORDER BY tester_name) AS serial_no,
        tester_id,
        tester_name,
        total_assigned,
        total_executions,
        passed,
        failed,
        blocked,
        not_run,
        last_execution
    FROM vw_tester_execution_report 
    WHERE (testproject_id = " . intval($projectId) . " OR testproject_id IS NULL)";

// Add tester filter if specified
if ($testerId > 0) {
    $sql .= " AND tester_id = " . intval($testerId);
}

$sql .= "
    UNION ALL
    
    SELECT 
        NULL AS serial_no,
        NULL AS tester_id,
        'TOTAL' AS tester_name,
        SUM(total_assigned) AS total_assigned,
        SUM(total_executions) AS total_executions,
        SUM(passed) AS passed,
        SUM(failed) AS failed,
        SUM(blocked) AS blocked,
        SUM(not_run) AS not_run,
        MAX(last_execution) AS last_execution
    FROM vw_tester_execution_report 
    WHERE (testproject_id = " . intval($projectId) . " OR testproject_id IS NULL)";

// Add tester filter if specified
if ($testerId > 0) {
    $sql .= " AND tester_id = " . intval($testerId);
}

$sql .= "
    ORDER BY 
        CASE WHEN tester_name = 'TOTAL' THEN 2 ELSE 1 END,
        tester_name";

echo "<h3>Generated SQL:</h3>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

echo "<h3>Test this query directly in your database:</h3>";
echo "<textarea rows='10' cols='100'>" . htmlspecialchars($sql) . "</textarea>";
?>
