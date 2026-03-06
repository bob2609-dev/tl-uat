-- Test step by step - see where the 37 assignments are being lost
SELECT 
    'Step 1: All assignments' as step,
    COUNT(*) as count
FROM user_assignments ua
WHERE ua.user_id = 111 
AND ua.type = 1 
AND ua.build_id = 4
AND ua.status = 1

UNION ALL

SELECT 
    'Step 2: After user join' as step,
    COUNT(DISTINCT ua.id) as count
FROM users u
LEFT JOIN user_assignments ua ON ua.user_id = u.id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
    AND ua.build_id = 4
WHERE u.id = 111
AND u.active = 1

UNION ALL

SELECT 
    'Step 3: After testplan_tcversions join' as step,
    COUNT(DISTINCT ua.id) as count
FROM users u
LEFT JOIN user_assignments ua ON ua.user_id = u.id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
    AND ua.build_id = 4
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
WHERE u.id = 111
AND u.active = 1

UNION ALL

SELECT 
    'Step 4: After testplans join' as step,
    COUNT(DISTINCT ua.id) as count
FROM users u
LEFT JOIN user_assignments ua ON ua.user_id = u.id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
    AND ua.build_id = 4
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE u.id = 111
AND u.active = 1

UNION ALL

SELECT 
    'Step 5: After WHERE clause filter' as step,
    COUNT(DISTINCT ua.id) as count
FROM users u
LEFT JOIN user_assignments ua ON ua.user_id = u.id 
    AND ua.type IN (1, 2) 
    AND ua.status = 1
    AND ua.build_id = 4
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE u.id = 111
AND u.active = 1
AND (u.id = 111 OR ua.id IS NULL OR tp.testproject_id = 242099);
