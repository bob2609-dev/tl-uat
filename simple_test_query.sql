-- Simplified test query - just count assignments for user 111
SELECT 
    COUNT(*) as total_assigned
FROM 
    user_assignments ua
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE 
    ua.user_id = 111 
    AND ua.type = 1 
    AND ua.build_id = 4
    AND ua.status = 1
    AND tp.testproject_id = 242099;
