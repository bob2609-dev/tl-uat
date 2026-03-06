-- Test the fixed logic - should now show 502
SELECT 
    COUNT(DISTINCT ua.id) as total_assigned
FROM 
    users u
    LEFT JOIN user_assignments ua ON ua.user_id = u.id 
        AND ua.type IN (1, 2) 
        AND ua.status = 1
        AND ua.build_id = 4
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
    LEFT JOIN (
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
    ) e ON e.tcversion_id = tptc.tcversion_id 
        AND e.testplan_id = tptc.testplan_id 
        AND e.platform_id = tptc.platform_id
        AND e.build_id = 4
WHERE 1=1 
    AND u.active = 1
    AND u.id = 111
    AND (ua.id IS NULL OR tp.testproject_id = 242099 OR tptc.id IS NULL);
