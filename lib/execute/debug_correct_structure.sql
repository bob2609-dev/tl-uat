-- Check sample data with proper syntax
SELECT 
    'tcversions sample data' as info,
    id,
    tc_external_id,
    version,
    status,
    summary
FROM tcversions
LIMIT 5;

-- Check sample data from testplan_tcversions
SELECT 
    'testplan_tcversions sample data' as info,
    *
FROM testplan_tcversions
LIMIT 5;

-- Try to find the relationship by looking at overlapping IDs
SELECT 
    'ID overlap analysis' as info,
    COUNT(*) as tcversion_count,
    MIN(id) as min_id,
    MAX(id) as max_id
FROM tcversions;

SELECT 
    'testplan_tcversions ID analysis' as info,
    COUNT(*) as tptc_count,
    MIN(tcversion_id) as min_tcversion_id,
    MAX(tcversion_id) as max_tcversion_id
FROM testplan_tcversions;

-- Check if tcversions.id matches testplan_tcversions.tcversion_id
SELECT 
    'Direct ID match' as info,
    COUNT(*) as matching_count
FROM tcversions tv
WHERE tv.id IN (SELECT tcversion_id FROM testplan_tcversions);

-- Check if tcversions.tc_external_id matches nodes_hierarchy.id
SELECT 
    'External ID match' as info,
    COUNT(*) as matching_count
FROM tcversions tv
WHERE tv.tc_external_id IN (SELECT id FROM nodes_hierarchy WHERE node_type_id = 3);

-- Get sample data to understand the correct relationship
SELECT 
    'Sample relationship data' as info,
    nh.id as testcase_node_id,
    nh.name as testcase_name,
    tv.id as tcversion_id,
    tv.tc_external_id,
    tptc.tcversion_id as tptc_tcversion_id
FROM nodes_hierarchy nh
LEFT JOIN tcversions tv ON nh.id = tv.tc_external_id
LEFT JOIN testplan_tcversions tptc ON tv.id = tptc.tcversion_id
WHERE nh.node_type_id = 3
    AND nh.parent_id IN (SELECT id FROM nodes_hierarchy WHERE name LIKE '%PAYMENT%' AND node_type_id = 2)
LIMIT 10;
