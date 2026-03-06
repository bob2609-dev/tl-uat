-- Create the view for tester execution report
-- This will be used by the PHP report

CREATE OR REPLACE VIEW vw_tester_execution_report AS
SELECT 
    u.id AS tester_id,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    COUNT(tptc.id) AS total_assigned,
    COUNT(CASE WHEN e.id IS NOT NULL THEN tptc.id END) AS total_executions,
    COUNT(CASE WHEN e.status = 'p' THEN tptc.id END) AS passed,
    COUNT(CASE WHEN e.status = 'f' THEN tptc.id END) AS failed,
    COUNT(CASE WHEN e.status = 'b' THEN tptc.id END) AS blocked,
    COUNT(CASE WHEN e.id IS NULL THEN tptc.id END) AS not_run,
    MAX(e.execution_ts) AS last_execution,
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
    LEFT JOIN executions e ON e.tcversion_id = tptc.tcversion_id 
        AND e.testplan_id = tptc.testplan_id 
        AND e.platform_id = tptc.platform_id
        AND (ua.build_id = e.build_id OR ua.build_id = 0 OR ua.build_id IS NULL)
WHERE u.active = 1
    AND (ua.id IS NULL OR tp.testproject_id IS NOT NULL)
GROUP BY u.id, u.login, u.first, u.last, tp.testproject_id, tp.id, ua.build_id;
