Diagnostic test to identify the performance bottleneck

-- Test 1: Count users (should be fast)
SELECT 'Test 1: Count users' AS test_name, COUNT(*) AS count, NOW() AS timestamp
FROM users WHERE active = 1;

-- Test 2: Count user_assignments (might be slow)
SELECT 'Test 2: Count user_assignments' AS test_name, COUNT(*) AS count, NOW() AS timestamp
FROM user_assignments WHERE type IN (1, 2) AND status = 1;

-- Test 3: Count executions (this might be the bottleneck)
SELECT 'Test 3: Count executions' AS test_name, COUNT(*) AS count, NOW() AS timestamp
FROM executions;

-- Test 4: Count executions for project 242099 (specific test)
SELECT 'Test 4: Count executions for project 242099' AS test_name, COUNT(*) AS count, NOW() AS timestamp
FROM executions e
JOIN testplans tp ON e.testplan_id = tp.id
WHERE tp.testproject_id = 242099;

-- Test 5: Simple join without aggregations
SELECT 'Test 5: Simple join test' AS test_name, COUNT(*) AS count, NOW() AS timestamp
FROM users u
LEFT JOIN user_assignments ua ON ua.user_id = u.id AND ua.type IN (1, 2) AND ua.status = 1
WHERE u.active = 1;

-- Test 6: Check table sizes
SELECT 
    'executions' AS table_name,
    COUNT(*) AS row_count,
    ROUND(data_length/1024/1024, 2) AS size_mb
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'executions'
UNION ALL
SELECT 
    'user_assignments' AS table_name,
    COUNT(*) AS row_count,
    ROUND(data_length/1024/1024, 2) AS size_mb
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'user_assignments';
