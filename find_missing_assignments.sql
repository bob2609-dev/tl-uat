-- Find the 37 missing assignments
SELECT 
    ua.id as assignment_id,
    ua.feature_id,
    ua.type,
    ua.build_id,
    ua.status,
    tptc.id as tptc_exists,
    tp.id as testplan_exists,
    tp.testproject_id
FROM 
    user_assignments ua
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE 
    ua.user_id = 111 
    AND ua.type = 1 
    AND ua.build_id = 4
    AND ua.status = 1
    AND (tptc.id IS NULL OR tp.testproject_id != 242099 OR tp.testproject_id IS NULL)
ORDER BY ua.id
LIMIT 50;
