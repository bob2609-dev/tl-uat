-- Find recent executions that belong to testproject_id = 1
SELECT 
    e.id,
    e.execution_ts,
    e.status,
    u.first,
    u.last,
    tp.id as testplan_id,
    tp.notes as testplan_name,
    tp.testproject_id,
    tproj.notes as testproject_name
FROM executions e
JOIN users u ON e.tester_id = u.id
JOIN testplans tp ON e.testplan_id = tp.id
JOIN testprojects tproj ON tp.testproject_id = tproj.id
WHERE tp.testproject_id = 1
    AND DATE(e.execution_ts) >= '2026-01-01'
ORDER BY e.execution_ts DESC
LIMIT 20;

-- Also check the most recent execution date for project_id = 1
SELECT 
    MAX(DATE(e.execution_ts)) as latest_execution_date,
    COUNT(*) as total_executions_2026
FROM executions e
JOIN testplans tp ON e.testplan_id = tp.id
WHERE tp.testproject_id = 1
    AND YEAR(e.execution_ts) = 2026;
