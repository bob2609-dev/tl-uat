-- Debug Suite Execution Summary - Step by Step
-- This will help us identify where the query is failing

-- Step 1: Check if we have test suites at all
SELECT 
    'Step 1: All nodes_hierarchy' as debug_step,
    COUNT(*) as total_nodes,
    COUNT(CASE WHEN node_type_id = 2 THEN 1 END) as test_suites,
    COUNT(CASE WHEN node_type_id = 3 THEN 1 END) as test_cases
FROM nodes_hierarchy;

-- Step 2: Check test suites specifically
SELECT 
    'Step 2: Test suites' as debug_step,
    nh.id,
    nh.name,
    nh.node_type_id,
    COUNT(tc.id) as child_test_cases
FROM nodes_hierarchy nh
LEFT JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
WHERE nh.node_type_id = 2
GROUP BY nh.id, nh.name, nh.node_type_id
HAVING COUNT(tc.id) > 0
LIMIT 10;

-- Step 3: Check tcversions table
SELECT 
    'Step 3: tcversions check' as debug_step,
    COUNT(*) as total_tcversions,
    COUNT(CASE WHEN id IN (SELECT id FROM nodes_hierarchy WHERE node_type_id = 3) THEN 1 END) as matching_testcases
FROM tcversions;

-- Step 4: Check testplan_tcversions join
SELECT 
    'Step 4: testplan_tcversions' as debug_step,
    COUNT(*) as total_tptc,
    COUNT(DISTINCT tcversion_id) as distinct_tcversions,
    COUNT(DISTINCT testplan_id) as distinct_testplans
FROM testplan_tcversions;

-- Step 5: Check the full join step by step
SELECT 
    'Step 5: Full join check' as debug_step,
    COUNT(*) as total_records
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
JOIN tcversions tcv ON tc.id = tcv.id
JOIN testplan_tcversions tptc ON tcv.id = tptc.tcversion_id
JOIN testplans tp ON tptc.testplan_id = tp.id
JOIN testprojects proj ON tp.testproject_id = proj.id
WHERE nh.node_type_id = 2;

-- Step 6: Check specific suite names
SELECT 
    'Step 6: Specific suites' as debug_step,
    nh.name as suite_name,
    COUNT(*) as test_case_count
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
WHERE nh.node_type_id = 2
    AND nh.name LIKE '%PAYMENT%' OR nh.name LIKE '%TRXN%'
GROUP BY nh.name
ORDER BY test_case_count DESC;

-- Step 7: Alternative approach - check all suite names
SELECT 
    'Step 7: All suite names' as debug_step,
    nh.name as suite_name,
    COUNT(*) as test_case_count
FROM nodes_hierarchy nh
JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
WHERE nh.node_type_id = 2
GROUP BY nh.name
ORDER BY test_case_count DESC
LIMIT 20;
