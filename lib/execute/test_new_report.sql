-- Test the new report logic with simple queries

-- Test 1: Basic assignments count
SELECT 
    ua.user_id,
    COUNT(DISTINCT ua.feature_id) AS assigned_cnt
FROM user_assignments ua
GROUP BY ua.user_id
LIMIT 5;

-- Test 2: Basic executions count
SELECT 
    e.tester_id,
    COUNT(*) AS executed_cnt,
    SUM(e.status = 'p') AS pass_cnt,
    SUM(e.status = 'f') AS fail_cnt,
    MAX(e.execution_ts) AS last_execution_date
FROM executions e
GROUP BY e.tester_id
LIMIT 5;

-- Test 3: Combined query (simplified version)
SELECT 
    u.id AS tester_id,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    IFNULL(a.assigned_cnt, 0) AS assigned_testcases,
    IFNULL(e.executed_cnt, 0) AS executed_testcases,
    IFNULL(e.pass_cnt, 0) AS passed_testcases,
    IFNULL(e.fail_cnt, 0) AS failed_testcases,
    GREATEST(IFNULL(a.assigned_cnt,0) - IFNULL(e.executed_cnt,0), 0) AS assigned_not_run,
    e.last_execution_date
FROM users u
LEFT JOIN (
    SELECT 
        ua.user_id, 
        COUNT(DISTINCT ua.feature_id) AS assigned_cnt
    FROM user_assignments ua
    GROUP BY ua.user_id
    LIMIT 10
) a ON a.user_id = u.id
LEFT JOIN (
    SELECT
        e.tester_id,
        COUNT(*) AS executed_cnt,
        SUM(e.status = 'p') AS pass_cnt,
        SUM(e.status = 'f') AS fail_cnt,
        MAX(e.execution_ts) AS last_execution_date
    FROM executions e
    GROUP BY e.tester_id
    LIMIT 10
) e ON e.tester_id = u.id
WHERE (a.user_id IS NOT NULL OR e.tester_id IS NOT NULL)
LIMIT 10;
