-- Fixed report logic query matching the updated PHP code
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
WHERE 1=1 
    AND u.active = 1
    AND u.id = 111
    AND (ua.id IS NULL OR tp.testproject_id = 242099);
