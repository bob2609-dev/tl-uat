-- Create corrected view for tester execution report
-- Key insights:
-- 1. user_assignments table holds the truth about assigned cases (user_id 111 has 776 assignments)
-- 2. Build distribution: 465 for build_id 4, 311 for build_id 2
-- 3. executions table may have multiple executions per test case - count each case only once with latest status

CREATE OR REPLACE VIEW vw_tester_execution_report_corrected AS
WITH 
-- Get all unique test case assignments per user
user_assignments_detail AS (
    SELECT 
        ua.user_id,
        ua.feature_id AS testplan_tcversion_id,
        ua.build_id,
        tptc.tcversion_id,
        tptc.testplan_id,
        tptc.platform_id,
        tp.testproject_id
    FROM user_assignments ua
    JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    JOIN testplans tp ON tptc.testplan_id = tp.id
    WHERE ua.type IN (1, 2) 
        AND ua.status = 1
        AND tp.testproject_id IS NOT NULL
),

-- Get latest execution for each test case (only once per test case)
latest_executions AS (
    SELECT 
        e.tcversion_id,
        e.testplan_id,
        e.platform_id,
        e.build_id,
        e.id AS execution_id,
        e.status AS execution_status,
        e.execution_ts,
        e.tester_id AS executed_by,
        ROW_NUMBER() OVER (
            PARTITION BY e.tcversion_id, e.testplan_id, e.platform_id 
            ORDER BY e.execution_ts DESC, e.id DESC
        ) AS rn
    FROM executions e
),

-- Get unique test cases with their latest execution status
test_cases_with_status AS (
    SELECT 
        uad.user_id,
        uad.build_id,
        uad.testproject_id,
        uad.testplan_id,
        uad.tcversion_id,
        uad.platform_id,
        COALESCE(le.execution_status, 'n') AS execution_status,  -- Default to 'n' (not run) if no execution
        le.execution_ts,
        le.executed_by,
        CASE 
            WHEN le.execution_id IS NOT NULL THEN 1 
            ELSE 1  -- Count all assignments, executed or not
        END AS has_execution
    FROM user_assignments_detail uad
    LEFT JOIN latest_executions le ON 
        le.tcversion_id = uad.tcversion_id 
        AND le.testplan_id = uad.testplan_id 
        AND le.platform_id = uad.platform_id
        AND le.rn = 1  -- Only get the latest execution
        AND (uad.build_id = le.build_id OR uad.build_id = 0 OR uad.build_id IS NULL)
)

-- Final aggregation
SELECT 
    u.id AS tester_id,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    u.login,
    COUNT(*) AS total_assigned,  -- Count each assignment only once
    SUM(has_execution) AS total_executions,
    SUM(CASE WHEN execution_status = 'p' THEN 1 ELSE 0 END) AS passed,
    SUM(CASE WHEN execution_status = 'f' THEN 1 ELSE 0 END) AS failed,
    SUM(CASE WHEN execution_status = 'b' THEN 1 ELSE 0 END) AS blocked,
    SUM(CASE WHEN execution_status = 'n' THEN 1 ELSE 0 END) AS not_run,
    SUM(CASE WHEN execution_status = 's' THEN 1 ELSE 0 END) AS skipped,
    SUM(CASE WHEN execution_status = 'w' THEN 1 ELSE 0 END) AS warning,
    MAX(execution_ts) AS last_execution,
    -- Add filtering columns - use the values from assignments
    MIN(tcws.testproject_id) AS testproject_id,
    MIN(tcws.testplan_id) AS testplan_id,
    MIN(tcws.build_id) AS build_id
FROM users u
JOIN test_cases_with_status tcws ON u.id = tcws.user_id
WHERE u.active = 1
GROUP BY u.id, u.login, u.first, u.last, tcws.testproject_id;
