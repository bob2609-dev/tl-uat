-- Test script to verify users who executed without assignments are included

-- 1. First, let's find users who executed tests without being assigned
SELECT DISTINCT
    e.tester_id,
    u.login,
    u.first,
    u.last,
    COUNT(*) as execution_count
FROM executions e
JOIN users u ON e.tester_id = u.id
LEFT JOIN user_assignments ua ON 
    ua.user_id = e.tester_id 
    AND ua.feature_id IN (
        SELECT id FROM testplan_tcversions 
        WHERE tcversion_id = e.tcversion_id 
        AND testplan_id = e.testplan_id
    )
    AND ua.type IN (1, 2) 
    AND ua.status = 1
WHERE 
    DATE(e.execution_ts) = '2026-01-28'
    AND ua.id IS NULL  -- No assignment found
GROUP BY e.tester_id, u.login, u.first, u.last
ORDER BY execution_count DESC;

-- 2. Test the stored procedure with 'all' report type (should include everyone)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-28', '2026-01-28', FALSE);

-- 3. Test the stored procedure with 'assigned' report type (should include users with assignments OR executions)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'assigned', '2026-01-28', '2026-01-28', FALSE);

-- 4. Test with hide zero executions enabled
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-28', '2026-01-28', TRUE);
