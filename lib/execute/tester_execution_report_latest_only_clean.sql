SET @project_id = NULL;
SET @user_id = NULL;
SET @start_date = NULL;
SET @end_date = NULL;
SET @include_non_assigned = 1;

SELECT 
    u.id AS tester_id,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    IFNULL(a.assigned_cnt, 0) AS assigned_testcases,
    IFNULL(e.executed_cnt, 0) AS executed_testcases,
    IFNULL(e.pass_cnt, 0) AS passed_testcases,
    IFNULL(e.fail_cnt, 0) AS failed_testcases,
    IFNULL(e.blocked_cnt, 0) AS blocked_testcases,
    GREATEST(IFNULL(a.assigned_cnt,0) - IFNULL(e.executed_cnt,0), 0) AS assigned_not_run,
    CASE 
        WHEN (IFNULL(e.pass_cnt, 0) + IFNULL(e.fail_cnt, 0) + IFNULL(e.blocked_cnt, 0)) = 0 THEN 0
        ELSE ROUND((IFNULL(e.pass_cnt, 0) / (IFNULL(e.pass_cnt, 0) + IFNULL(e.fail_cnt, 0) + IFNULL(e.blocked_cnt, 0))) * 100, 2)
    END AS pass_rate_percent,
    e.last_execution_date
FROM users u
LEFT JOIN (
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
    SELECT
        latest_exec.tester_id,
        COUNT(*) AS executed_cnt,
        SUM(latest_exec.status = 'p') AS pass_cnt,
        SUM(latest_exec.status = 'f') AS fail_cnt,
        SUM(latest_exec.status = 'b') AS blocked_cnt,
        MAX(latest_exec.execution_ts) AS last_execution_date
    FROM (
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
    WHERE latest_exec.rn = 1
    GROUP BY latest_exec.tester_id
) e ON e.tester_id = u.id
WHERE u.active = 1
    AND (a.user_id IS NOT NULL OR e.tester_id IS NOT NULL)
    AND (@user_id IS NULL OR u.id = @user_id)
    AND (@include_non_assigned = 1 OR IFNULL(a.assigned_cnt, 0) > 0)
ORDER BY tester_name;

SELECT 
    'TOTAL' AS tester_id,
    'TOTAL' AS tester_name,
    SUM(IFNULL(a.assigned_cnt, 0)) AS assigned_testcases,
    SUM(IFNULL(e.executed_cnt, 0)) AS executed_testcases,
    SUM(IFNULL(e.pass_cnt, 0)) AS passed_testcases,
    SUM(IFNULL(e.fail_cnt, 0)) AS failed_testcases,
    SUM(IFNULL(e.blocked_cnt, 0)) AS blocked_testcases,
    GREATEST(SUM(IFNULL(a.assigned_cnt,0)) - SUM(IFNULL(e.executed_cnt,0)), 0) AS assigned_not_run,
    CASE 
        WHEN (SUM(IFNULL(e.pass_cnt, 0)) + SUM(IFNULL(e.fail_cnt, 0)) + SUM(IFNULL(e.blocked_cnt, 0))) = 0 THEN 0
        ELSE ROUND((SUM(IFNULL(e.pass_cnt, 0)) / (SUM(IFNULL(e.pass_cnt, 0)) + SUM(IFNULL(e.fail_cnt, 0)) + SUM(IFNULL(e.blocked_cnt, 0)))) * 100, 2)
    END AS pass_rate_percent,
    NULL AS last_execution_date
FROM users u
LEFT JOIN (
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
    SELECT
        latest_exec.tester_id,
        COUNT(*) AS executed_cnt,
        SUM(latest_exec.status = 'p') AS pass_cnt,
        SUM(latest_exec.status = 'f') AS fail_cnt,
        SUM(latest_exec.status = 'b') AS blocked_cnt,
        MAX(latest_exec.execution_ts) AS last_execution_date
    FROM (
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
    WHERE latest_exec.rn = 1
    GROUP BY latest_exec.tester_id
) e ON e.tester_id = u.id
WHERE u.active = 1
    AND (a.user_id IS NOT NULL OR e.tester_id IS NOT NULL)
    AND (@user_id IS NULL OR u.id = @user_id)
    AND (@include_non_assigned = 1 OR IFNULL(a.assigned_cnt, 0) > 0);
