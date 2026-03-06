-- Test the CTE structure to debug the execution_status issue

-- Test just the latest_executions_historical CTE
WITH latest_executions_historical AS (
    SELECT 
        e.id,
        e.testplan_id,
        e.tcversion_id,
        e.platform_id,
        e.build_id,
        e.tester_id,
        e.execution_ts,
        e.status AS execution_status,  -- Rename to execution_status for consistency
        e.execution_type,
        e.notes,
        -- Use ROW_NUMBER() to get the latest execution per test case/platform/build combination
        ROW_NUMBER() OVER (
            PARTITION BY e.tcversion_id, e.platform_id, e.build_id 
            ORDER BY e.execution_ts DESC
        ) AS rn
    FROM executions e
    WHERE 
        -- Filter by date range
        (DATE('2026-01-28') IS NULL OR e.execution_ts >= DATE('2026-01-28'))
        AND (DATE_ADD(DATE('2026-01-28'), INTERVAL 1 DAY) IS NULL OR e.execution_ts < DATE_ADD(DATE('2026-01-28'), INTERVAL 1 DAY))
)
SELECT 
    id,
    testplan_id,
    tcversion_id,
    platform_id,
    build_id,
    tester_id,
    execution_ts,
    execution_status,
    execution_type,
    notes,
    rn
FROM latest_executions_historical 
WHERE rn = 1
LIMIT 10;

-- If this works, then the issue is in the full stored procedure deployment
