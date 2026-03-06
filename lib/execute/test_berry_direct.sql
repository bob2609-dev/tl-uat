-- Test Berry Nsanya's executions directly
-- This should help us understand why she's not appearing in the stored procedure

-- 1. Check Berry's user ID
SELECT id, login, first, last FROM users WHERE login LIKE '%berry%' OR first LIKE '%berry%' OR last LIKE '%berry%';

-- 2. Check Berry's executions on 2026-01-28
SELECT id, build_id, tester_id, execution_ts, status, testplan_id, tcversion_id, platform_id
FROM executions 
WHERE tester_id = 15 
AND DATE(execution_ts) = '2026-01-28'
ORDER BY execution_ts;

-- 3. Check if Berry has any assignments for the test cases she executed
SELECT ua.id, ua.user_id, ua.feature_id, ua.type, ua.status, ua.creation_ts,
       tptc.testplan_id, tptc.tcversion_id, tptc.platform_id
FROM user_assignments ua
JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
WHERE ua.user_id = 15
AND tptc.tcversion_id IN (414548, 257380);

-- 4. Test the stored procedure directly for Berry on 2026-01-28
CALL sp_tester_execution_report_historical(1, NULL, NULL, 15, 'all', '2026-01-28', '2026-01-28', FALSE);

-- 5. Test with hide zero executions enabled
CALL sp_tester_execution_report_historical(1, NULL, NULL, 15, 'all', '2026-01-28', '2026-01-28', TRUE);
