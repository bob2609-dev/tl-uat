-- Detailed breakdown of problematic assignments (the 25 missing assignments)
SELECT 
    ua.id as assignment_id,
    ua.feature_id,
    CASE 
        WHEN tptc.id IS NULL THEN 'ORPHANED - No testplan_tcversions record'
        WHEN tp.id IS NULL THEN 'NO TESTPLAN - testplan_tcversions exists but no testplan'
        WHEN tp.testproject_id != 242099 THEN 'WRONG PROJECT - Project ID: ' + COALESCE(CAST(tp.testproject_id AS CHAR), 'NULL')
        ELSE 'VALID - Should be counted'
    END as status,
    tptc.testplan_id,
    COALESCE(tp.testproject_id, -1) as actual_project_id
FROM user_assignments ua
LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE ua.user_id = 111 
AND ua.type = 1 
AND ua.build_id = 4
AND ua.status = 1
AND (tptc.id IS NULL OR tp.testproject_id != 242099 OR tp.testproject_id IS NULL)
ORDER BY ua.id
LIMIT 50;
