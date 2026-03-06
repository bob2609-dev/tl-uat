-- Test query using exact same logic as our report
-- This should match the 477 count from the UI

SELECT 
    COUNT(*) as total_assigned
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
    AND (ua.id IS NULL OR tp.testproject_id = 242099)
    AND u.id = 111;
