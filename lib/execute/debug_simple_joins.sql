-- Simple Debug - Test each join step by step

-- Test 1: Just get suites and test cases (no other joins)
SELECT 
    'Test 1: Suites + Test Cases only' as test_step,
    COUNT(*) as total_records,
    COUNT(DISTINCT nh.name) as distinct_suites
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
WHERE nh.node_type_id = 2;

-- Test 2: Add testplan_tcversions join
SELECT 
    'Test 2: + testplan_tcversions' as test_step,
    COUNT(*) as total_records,
    COUNT(DISTINCT nh.name) as distinct_suites
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
JOIN testplan_tcversions tptc ON tc.id = tptc.tcversion_id
WHERE nh.node_type_id = 2;

-- Test 3: Add testplans join
SELECT 
    'Test 3: + testplans' as test_step,
    COUNT(*) as total_records,
    COUNT(DISTINCT nh.name) as distinct_suites
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
JOIN testplan_tcversions tptc ON tc.id = tptc.tcversion_id
JOIN testplans tp ON tptc.testplan_id = tp.id
WHERE nh.node_type_id = 2;

-- Test 4: Add testprojects join
SELECT 
    'Test 4: + testprojects' as test_step,
    COUNT(*) as total_records,
    COUNT(DISTINCT nh.name) as distinct_suites
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
JOIN testplan_tcversions tptc ON tc.id = tptc.tcversion_id
JOIN testplans tp ON tptc.testplan_id = tp.id
JOIN testprojects proj ON tp.testproject_id = proj.id
WHERE nh.node_type_id = 2;

-- Test 5: Check specific suite names at each step
SELECT 
    'Test 5: Specific suite names' as test_step,
    nh.name as suite_name,
    COUNT(*) as test_case_count
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
WHERE nh.node_type_id = 2
    AND (nh.name LIKE '%PAYMENT%' OR nh.name LIKE '%TRXN%')
GROUP BY nh.name
ORDER BY test_case_count DESC;

-- Test 6: Check if test case IDs match tcversion_ids
SELECT 
    'Test 6: ID match check' as test_step,
    COUNT(*) as total_testcases,
    COUNT(CASE WHEN tc.id IN (SELECT tcversion_id FROM testplan_tcversions) THEN 1 END) as matching_tcversions,
    COUNT(CASE WHEN tc.id NOT IN (SELECT tcversion_id FROM testplan_tcversions) THEN 1 END) as non_matching_tcversions
FROM nodes_hierarchy tc
WHERE tc.node_type_id = 3;

-- Test 7: Show sample data for the problematic join
SELECT 
    'Test 7: Sample data' as test_step,
    tc.id as testcase_id,
    tc.name as testcase_name,
    tptc.tcversion_id,
    tptc.testplan_id
FROM nodes_hierarchy tc
LEFT JOIN testplan_tcversions tptc ON tc.id = tptc.tcversion_id
WHERE tc.node_type_id = 3
    AND tc.parent_id IN (SELECT id FROM nodes_hierarchy WHERE name LIKE '%PAYMENT%' AND node_type_id = 2)
LIMIT 10;
