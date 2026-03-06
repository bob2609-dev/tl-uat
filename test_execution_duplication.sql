-- Test query to understand execution duplication issue
-- Let's see how many executions exist for user 111's assigned test cases

SELECT 
    COUNT(*) as total_executions,
    COUNT(DISTINCT e.id) as unique_executions,
    COUNT(DISTINCT tptc.id) as unique_test_cases,
    COUNT(DISTINCT CONCAT(tptc.tcversion_id, '-', tptc.testplan_id, '-', tptc.platform_id)) as unique_test_combinations
FROM 
    user_assignments ua
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN executions e ON e.tcversion_id = tptc.tcversion_id 
        AND e.testplan_id = tptc.testplan_id 
        AND e.platform_id = tptc.platform_id
WHERE 
    ua.user_id = 111 
    AND ua.type = 1 
    AND ua.build_id = 4
    AND ua.status = 1
    AND tptc.id IS NOT NULL;
