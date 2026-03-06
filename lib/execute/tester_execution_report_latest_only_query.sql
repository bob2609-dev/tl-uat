-- =====================================================
-- Tester Execution Report - Latest Execution Only Query
-- =====================================================
-- 
-- This query replicates the logic from tester_execution_report_latest_only.php
-- It counts each test case only once per tester, considering only the status
-- of the latest execution for each test case.
--
-- Key difference from standard report:
-- - If a test case was executed multiple times by the same tester,
--   only the latest execution status is counted
-- - This prevents double-counting and provides more accurate metrics
--
-- USAGE:
-- Replace the placeholder values below with your desired filters
-- =====================================================

-- =====================================================
-- FILTER PARAMETERS - Replace these values as needed
-- =====================================================
SET @project_id = NULL;        -- NULL for all projects, or specific project ID
SET @user_id = NULL;           -- NULL for all users, or specific user ID  
SET @start_date = NULL;        -- NULL for no start date, or 'YYYY-MM-DD'
SET @end_date = NULL;          -- NULL for no end date, or 'YYYY-MM-DD'
SET @include_non_assigned = 1; -- 1 to include users with zero assignments, 0 to exclude

-- =====================================================
-- MAIN QUERY - Latest Execution Only
-- =====================================================

SELECT 
    u.id AS tester_id,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    
    IFNULL(a.assigned_cnt, 0) AS assigned_testcases,
    IFNULL(e.executed_cnt, 0) AS executed_testcases,
    IFNULL(e.pass_cnt, 0) AS passed_testcases,
    IFNULL(e.fail_cnt, 0) AS failed_testcases,
    IFNULL(e.blocked_cnt, 0) AS blocked_testcases,
    
    GREATEST(IFNULL(a.assigned_cnt,0) - IFNULL(e.executed_cnt,0), 0) AS assigned_not_run,
    
    -- Pass rate calculated in SQL for direct query results
    CASE 
        WHEN (IFNULL(e.pass_cnt, 0) + IFNULL(e.fail_cnt, 0) + IFNULL(e.blocked_cnt, 0)) = 0 THEN 0
        ELSE ROUND((IFNULL(e.pass_cnt, 0) / (IFNULL(e.pass_cnt, 0) + IFNULL(e.fail_cnt, 0) + IFNULL(e.blocked_cnt, 0))) * 100, 2)
    END AS pass_rate_percent,
    
    e.last_execution_date
    
FROM users u

LEFT JOIN (
    -- Assigned test cases per user
    SELECT 
        ua.user_id, 
        COUNT(DISTINCT ua.feature_id) AS assigned_cnt
    FROM user_assignments ua
    JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    JOIN testplans tp ON tptc.testplan_id = tp.id
    WHERE ua.type IN (1, 2) AND ua.status = 1
        AND (@project_id IS NULL OR tp.testproject_id = @project_id)
    GROUP BY ua.user_id
) a ON a.user_id = u.id

LEFT JOIN (
    -- Latest execution only logic
    SELECT
        latest_exec.tester_id,
        COUNT(*) AS executed_cnt,
        SUM(latest_exec.status = 'p') AS pass_cnt,
        SUM(latest_exec.status = 'f') AS fail_cnt,
        SUM(latest_exec.status = 'b') AS blocked_cnt,
        MAX(latest_exec.execution_ts) AS last_execution_date
    FROM (
        -- Get only the latest execution for each test case per tester
        SELECT 
            e1.tester_id,
            e1.tcversion_id,
            e1.status,
            e1.execution_ts,
            ROW_NUMBER() OVER (
                PARTITION BY e1.tester_id, e1.tcversion_id 
                ORDER BY e1.execution_ts DESC
            ) AS rn
        FROM executions e1
        JOIN testplans tp ON e1.testplan_id = tp.id
        WHERE 1=1
            AND (@project_id IS NULL OR tp.testproject_id = @project_id)
            AND (@start_date IS NULL OR e1.execution_ts >= @start_date)
            AND (@end_date IS NULL OR e1.execution_ts <= @end_date)
    ) latest_exec
    WHERE latest_exec.rn = 1 -- Only get the latest execution
    GROUP BY latest_exec.tester_id
) e ON e.tester_id = u.id

WHERE u.active = 1
    AND (a.user_id IS NOT NULL OR e.tester_id IS NOT NULL)
    AND (@user_id IS NULL OR u.id = @user_id)
    AND (@include_non_assigned = 1 OR IFNULL(a.assigned_cnt, 0) > 0)

ORDER BY tester_name;

-- =====================================================
-- TOTALS QUERY - Same logic with totals aggregation
-- =====================================================

SELECT 
    'TOTAL' AS tester_id,
    'TOTAL' AS tester_name,
    
    SUM(IFNULL(a.assigned_cnt, 0)) AS assigned_testcases,
    SUM(IFNULL(e.executed_cnt, 0)) AS executed_testcases,
    SUM(IFNULL(e.pass_cnt, 0)) AS passed_testcases,
    SUM(IFNULL(e.fail_cnt, 0)) AS failed_testcases,
    SUM(IFNULL(e.blocked_cnt, 0)) AS blocked_testcases,
    
    GREATEST(SUM(IFNULL(a.assigned_cnt,0)) - SUM(IFNULL(e.executed_cnt,0)), 0) AS assigned_not_run,
    
    -- Overall pass rate
    CASE 
        WHEN (SUM(IFNULL(e.pass_cnt, 0)) + SUM(IFNULL(e.fail_cnt, 0)) + SUM(IFNULL(e.blocked_cnt, 0))) = 0 THEN 0
        ELSE ROUND((SUM(IFNULL(e.pass_cnt, 0)) / (SUM(IFNULL(e.pass_cnt, 0)) + SUM(IFNULL(e.fail_cnt, 0)) + SUM(IFNULL(e.blocked_cnt, 0)))) * 100, 2)
    END AS pass_rate_percent,
    
    NULL AS last_execution_date
    
FROM users u

LEFT JOIN (
    -- Assigned test cases per user
    SELECT 
        ua.user_id, 
        COUNT(DISTINCT ua.feature_id) AS assigned_cnt
    FROM user_assignments ua
    JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    JOIN testplans tp ON tptc.testplan_id = tp.id
    WHERE ua.type IN (1, 2) AND ua.status = 1
        AND (@project_id IS NULL OR tp.testproject_id = @project_id)
    GROUP BY ua.user_id
) a ON a.user_id = u.id

LEFT JOIN (
    -- Latest execution only logic
    SELECT
        latest_exec.tester_id,
        COUNT(*) AS executed_cnt,
        SUM(latest_exec.status = 'p') AS pass_cnt,
        SUM(latest_exec.status = 'f') AS fail_cnt,
        SUM(latest_exec.status = 'b') AS blocked_cnt,
        MAX(latest_exec.execution_ts) AS last_execution_date
    FROM (
        -- Get only the latest execution for each test case per tester
        SELECT 
            e1.tester_id,
            e1.tcversion_id,
            e1.status,
            e1.execution_ts,
            ROW_NUMBER() OVER (
                PARTITION BY e1.tester_id, e1.tcversion_id 
                ORDER BY e1.execution_ts DESC
            ) AS rn
        FROM executions e1
        JOIN testplans tp ON e1.testplan_id = tp.id
        WHERE 1=1
            AND (@project_id IS NULL OR tp.testproject_id = @project_id)
            AND (@start_date IS NULL OR e1.execution_ts >= @start_date)
            AND (@end_date IS NULL OR e1.execution_ts <= @end_date)
    ) latest_exec
    WHERE latest_exec.rn = 1 -- Only get the latest execution
    GROUP BY latest_exec.tester_id
) e ON e.tester_id = u.id

WHERE u.active = 1
    AND (a.user_id IS NOT NULL OR e.tester_id IS NOT NULL)
    AND (@user_id IS NULL OR u.id = @user_id)
    AND (@include_non_assigned = 1 OR IFNULL(a.assigned_cnt, 0) > 0);

-- =====================================================
-- EXAMPLE USAGE SCENARIOS
-- =====================================================

/*
-- Example 1: All projects, all users, no date filtering
SET @project_id = NULL;
SET @user_id = NULL;
SET @start_date = NULL;
SET @end_date = NULL;
SET @include_non_assigned = 1;

-- Example 2: Specific project (ID 242099), all users
SET @project_id = 242099;
SET @user_id = NULL;
SET @start_date = NULL;
SET @end_date = NULL;
SET @include_non_assigned = 1;

-- Example 3: Specific project and date range
SET @project_id = 242099;
SET @user_id = NULL;
SET @start_date = '2026-01-01';
SET @end_date = '2026-02-02';
SET @include_non_assigned = 1;

-- Example 4: Specific user only (ID 111 - Abdulrahman Mfundo)
SET @project_id = NULL;
SET @user_id = 111;
SET @start_date = NULL;
SET @end_date = NULL;
SET @include_non_assigned = 1;

-- Example 5: Exclude users with zero assignments
SET @project_id = 242099;
SET @user_id = NULL;
SET @start_date = NULL;
SET @end_date = NULL;
SET @include_non_assigned = 0;
*/

-- =====================================================
-- PERFORMANCE NOTES
-- =====================================================
/*
1. The ROW_NUMBER() window function ensures we only get the latest execution
   for each test case per tester, preventing double-counting

2. Indexes that would help performance:
   - executions(tester_id, tcversion_id, execution_ts)
   - user_assignments(user_id, feature_id)
   - testplans(testproject_id)

3. For large datasets, consider adding LIMIT clauses for testing:
   - Add "LIMIT 100" to the main query for quick testing
   - Remove LIMIT for full production use

4. The query handles NULL values gracefully:
   - NULL project_id = all projects
   - NULL user_id = all users  
   - NULL dates = no date filtering
*/
