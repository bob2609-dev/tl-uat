-- Test to find Berry Nsanya with the optimized procedure

-- 1. Test specifically for Berry (user ID 15)
CALL sp_tester_execution_report_historical(1, NULL, NULL, 15, 'all', '2026-01-28', '2026-01-28', FALSE);

-- 2. Test without project filter to see if Berry appears
CALL sp_tester_execution_report_historical(NULL, NULL, NULL, 15, 'all', '2026-01-28', '2026-01-28', FALSE);

-- 3. Check Berry's assignments and executions directly
SELECT 
    u.id,
    u.login,
    u.first,
    u.last,
    COUNT(DISTINCT ua.id) as assignment_count,
    COUNT(DISTINCT e.id) as execution_count
FROM users u
LEFT JOIN user_assignments ua ON ua.user_id = u.id AND ua.type IN (1, 2) AND ua.status = 1
LEFT JOIN executions e ON e.tester_id = u.id AND DATE(e.execution_ts) = '2026-01-28'
WHERE u.id = 15
GROUP BY u.id, u.login, u.first, u.last;
