-- Debug why stored procedure finds only 4 executions when there should be 20+
-- Let's trace through the stored procedure logic step by step

-- Step 1: Check raw executions on 2026-01-29
SELECT 'Raw executions on 2026-01-29' as debug_step;
SELECT 
    e.id,
    e.tcversion_id,
    e.testplan_id,
    e.platform_id,
    e.build_id,
    e.tester_id,
    e.status,
    e.execution_ts,
    u.first,
    u.last
FROM executions e
JOIN users u ON e.tester_id = u.id
WHERE DATE(e.execution_ts) = '2026-01-29'
ORDER BY e.execution_ts DESC
LIMIT 10;

-- Step 2: Check if these executions have corresponding assignments
SELECT 'Checking assignments for these executions' as debug_step;
SELECT 
    e.id as execution_id,
    e.tcversion_id,
    e.testplan_id,
    e.platform_id,
    e.build_id,
    e.tester_id,
    ua.id as assignment_id,
    ua.feature_id,
    ua.user_id as assigned_user_id,
    ua.creation_ts as assignment_date
FROM executions e
LEFT JOIN user_assignments ua ON e.tester_id = ua.user_id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
WHERE DATE(e.execution_ts) = '2026-01-29'
LIMIT 10;

-- Step 3: Check the testplan_tcversions link
SELECT 'Checking testplan_tcversions link' as debug_step;
SELECT 
    e.id as execution_id,
    e.tcversion_id,
    e.testplan_id,
    ua.feature_id,
    tptc.id as tptc_id,
    tptc.tcversion_id as tptc_tcversion_id,
    tptc.testplan_id as tptc_testplan_id
FROM executions e
LEFT JOIN user_assignments ua ON e.tester_id = ua.user_id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
WHERE DATE(e.execution_ts) = '2026-01-29'
LIMIT 10;
