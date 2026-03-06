-- Diagnostic query to understand the discrepancy between 502 and 477 assignments
-- User 111, build_id = 4, project_id = 242099

SELECT 
    'Step 1: All user assignments' as step,
    COUNT(*) as count
FROM user_assignments ua
WHERE ua.user_id = 111 
AND ua.type = 1 
AND ua.build_id = 4
AND ua.status = 1

UNION ALL

SELECT 
    'Step 2: Assignments with valid feature_id' as step,
    COUNT(*) as count
FROM user_assignments ua
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
WHERE ua.user_id = 111 
AND ua.type = 1 
AND ua.build_id = 4
AND ua.status = 1
AND tptc.id IS NOT NULL

UNION ALL

SELECT 
    'Step 3: Assignments in correct project' as step,
    COUNT(*) as count
FROM user_assignments ua
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE ua.user_id = 111 
AND ua.type = 1 
AND ua.build_id = 4
AND ua.status = 1
AND tptc.id IS NOT NULL
AND tp.testproject_id = 242099

UNION ALL

SELECT 
    'Step 4: Orphaned assignments (invalid feature_id)' as step,
    COUNT(*) as count
FROM user_assignments ua
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
WHERE ua.user_id = 111 
AND ua.type = 1 
AND ua.build_id = 4
AND ua.status = 1
AND tptc.id IS NULL

UNION ALL

SELECT 
    'Step 5: Wrong project assignments' as step,
    COUNT(*) as count
FROM user_assignments ua
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE ua.user_id = 111 
AND ua.type = 1 
AND ua.build_id = 4
AND ua.status = 1
AND tptc.id IS NOT NULL
AND (tp.testproject_id != 242099 OR tp.testproject_id IS NULL);
