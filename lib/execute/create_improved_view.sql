-- Create improved view for tester execution report with proper latest execution logic
-- This handles date filtering correctly by using only the latest execution per test case

CREATE OR REPLACE VIEW vw_tester_execution_report AS
SELECT 
    u.id AS tester_id,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    COUNT(tptc.id) AS total_assigned,
    COUNT(CASE WHEN latest_e.id IS NOT NULL THEN tptc.id END) AS total_executions,
    COUNT(CASE WHEN latest_e.status = 'p' THEN tptc.id END) AS passed,
    COUNT(CASE WHEN latest_e.status = 'f' THEN tptc.id END) AS failed,
    COUNT(CASE WHEN latest_e.status = 'b' THEN tptc.id END) AS blocked,
    COUNT(CASE WHEN latest_e.id IS NULL THEN tptc.id END) AS not_run,
    MAX(latest_e.execution_ts) AS last_execution,
    tp.testproject_id,
    tp.id AS testplan_id,
    ua.build_id
FROM 
    users u
    LEFT JOIN user_assignments ua ON ua.user_id = u.id 
        AND ua.type IN (1, 2) 
        AND ua.status = 1
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
    LEFT JOIN (
        -- Get only the latest execution for each test case per platform/build
        SELECT 
            e1.tcversion_id,
            e1.testplan_id, 
            e1.platform_id,
            e1.build_id,
            e1.id,
            e1.status,
            e1.execution_ts
        FROM executions e1
        INNER JOIN (
            SELECT 
                tcversion_id, 
                testplan_id, 
                platform_id, 
                build_id,
                MAX(execution_ts) as max_execution_ts
            FROM executions 
            GROUP BY tcversion_id, testplan_id, platform_id, build_id
        ) e2 ON e1.tcversion_id = e2.tcversion_id 
            AND e1.testplan_id = e2.testplan_id 
            AND e1.platform_id = e2.platform_id
            AND e1.build_id = e2.build_id
            AND e1.execution_ts = e2.max_execution_ts
    ) latest_e ON latest_e.tcversion_id = tptc.tcversion_id 
        AND latest_e.testplan_id = tptc.testplan_id 
        AND latest_e.platform_id = tptc.platform_id
        AND (ua.build_id = latest_e.build_id OR ua.build_id = 0 OR ua.build_id IS NULL)
WHERE u.active = 1
    AND (ua.id IS NULL OR tp.testproject_id IS NOT NULL)
GROUP BY u.id, u.login, u.first, u.last, tp.testproject_id, tp.id, ua.build_id;
