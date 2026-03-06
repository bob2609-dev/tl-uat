-- Debug query to understand why we get 518 instead of 502
SELECT 
    ua.id as assignment_id,
    ua.feature_id,
    tptc.id as tptc_id,
    tp.id as testplan_id,
    tp.testproject_id,
    CASE 
        WHEN ua.id IS NULL THEN 'NO ASSIGNMENT'
        WHEN tp.testproject_id = 242099 THEN 'CORRECT PROJECT'
        ELSE 'WRONG/NO PROJECT'
    END as project_status
FROM 
    users u
    LEFT JOIN user_assignments ua ON ua.user_id = u.id 
        AND ua.type IN (1, 2) 
        AND ua.status = 1
        AND ua.build_id = 4
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id AND tp.testproject_id = 242099
    LEFT JOIN executions e ON e.tcversion_id = tptc.tcversion_id 
        AND e.testplan_id = tptc.testplan_id 
        AND e.platform_id = tptc.platform_id
        AND e.build_id = 4
WHERE 1=1 
    AND u.active = 1
    AND u.id = 111
    AND (ua.id IS NULL OR tp.testproject_id = 242099)
LIMIT 20;
