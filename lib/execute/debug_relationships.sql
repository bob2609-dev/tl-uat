-- Understand the actual data relationships

-- Check tcversions table structure
SELECT 
    'tcversions structure' as info,
    COUNT(*) as total_records,
    MIN(id) as min_id,
    MAX(id) as max_id,
    COUNT(DISTINCT tc_id) as distinct_tc_ids
FROM tcversions;

-- Check testplan_tcversions structure  
SELECT 
    'testplan_tcversions structure' as info,
    COUNT(*) as total_records,
    MIN(tcversion_id) as min_tcversion_id,
    MAX(tcversion_id) as max_tcversion_id,
    COUNT(DISTINCT tcversion_id) as distinct_tcversion_ids
FROM testplan_tcversions;

-- Check if tcversions.id matches testplan_tcversions.tcversion_id
SELECT 
    'ID overlap check' as info,
    COUNT(*) as matching_ids
FROM tcversions tv
WHERE tv.id IN (SELECT tcversion_id FROM testplan_tcversions);

-- Find the correct relationship - check if tcversions.tc_id matches nodes_hierarchy.id
SELECT 
    'tcversions.tc_id to nodes_hierarchy.id' as info,
    COUNT(*) as matching_records
FROM tcversions tv
WHERE tv.tc_id IN (SELECT id FROM nodes_hierarchy WHERE node_type_id = 3);

-- Get sample data to understand the relationships
SELECT 
    'Sample relationships' as info,
    nh.id as testcase_node_id,
    nh.name as testcase_name,
    tv.id as tcversion_id,
    tv.tc_id as tcversion_tc_id,
    tptc.tcversion_id as tptc_tcversion_id
FROM nodes_hierarchy nh
LEFT JOIN tcversions tv ON nh.id = tv.tc_id
LEFT JOIN testplan_tcversions tptc ON tv.id = tptc.tcversion_id
WHERE nh.node_type_id = 3
    AND nh.parent_id IN (SELECT id FROM nodes_hierarchy WHERE name LIKE '%PAYMENT%' AND node_type_id = 2)
LIMIT 10;
