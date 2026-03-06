-- =====================================================
-- EXECUTION PATH FILTERING DIAGNOSTIC QUERIES
-- =====================================================
-- These queries help diagnose why execution path filtering
-- may not be working as expected in the TestLink UI
-- =====================================================

USE tl_uat;

-- =====================================================
-- 1. CHECK WHAT'S ACTUALLY IN vw_testcase_summary
-- =====================================================

-- Query 1: See all unique execution paths in vw_testcase_summary
SELECT DISTINCT 
    Test_Execution_Path,
    LENGTH(Test_Execution_Path) as path_length,
    CASE 
        WHEN Test_Execution_Path LIKE '%mgt%' THEN 'Contains mgt'
        WHEN Test_Execution_Path LIKE '%Mgt%' THEN 'Contains Mgt'
        WHEN Test_Execution_Path LIKE '%MGT%' THEN 'Contains MGT'
        ELSE 'No mgt found'
    END as mgt_check
FROM vw_testcase_summary 
WHERE Test_Execution_Path IS NOT NULL 
    AND Test_Execution_Path != ''
ORDER BY Test_Execution_Path
LIMIT 20;

-- Query 2: Check if there are any paths containing 'mgt' (case insensitive)
SELECT 
    Test_Execution_Path,
    tcversion_id
FROM vw_testcase_summary 
WHERE Test_Execution_Path LIKE '%mgt%' 
   OR Test_Execution_Path LIKE '%Mgt%' 
   OR Test_Execution_Path LIKE '%MGT%'
LIMIT 10;

-- =====================================================
-- 2. CHECK WHAT THE UI QUERY IS ACTUALLY RETURNING
-- =====================================================

-- Query 3: Check what tcversion_ids have executions
SELECT DISTINCT 
    e.tcversion_id,
    COUNT(*) as execution_count
FROM executions e
GROUP BY e.tcversion_id
ORDER BY execution_count DESC
LIMIT 10;

-- Query 4: Check if executions have corresponding vw_testcase_summary records
SELECT 
    COUNT(DISTINCT e.tcversion_id) as total_execution_tcversions,
    COUNT(DISTINCT vts.tcversion_id) as tcversions_with_path_data,
    COUNT(DISTINCT e.tcversion_id) - COUNT(DISTINCT vts.tcversion_id) as missing_path_data
FROM executions e
LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id;

-- =====================================================
-- 3. ALTERNATIVE PATH SOURCES
-- =====================================================

-- Query 5: Check if execution paths are built from nodes_hierarchy instead
SELECT 
    e.id AS execution_id,
    e.tcversion_id,
    nh_tc.name AS tc_name,
    parent_nh.name AS parent_suite_name,
    CONCAT(tproj.notes, ' > ', tp.notes, ' > ', parent_nh.name) as constructed_path,
    vts.Test_Execution_Path as vts_path
FROM 
    executions e
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    JOIN testplans tp ON e.testplan_id = tp.id
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE parent_nh.name LIKE '%mgt%' 
   OR parent_nh.name LIKE '%Mgt%' 
   OR parent_nh.name LIKE '%MGT%'
LIMIT 10;

-- =====================================================
-- 4. CHECK THE ACTUAL VIEW DEFINITION
-- =====================================================

-- Query 6: Check if vw_testcase_summary view exists and its structure
SHOW CREATE VIEW vw_testcase_summary;

-- Query 7: Alternative - Check information_schema for view definition
SELECT 
    TABLE_NAME,
    VIEW_DEFINITION
FROM information_schema.VIEWS 
WHERE TABLE_SCHEMA = 'tl_uat' 
    AND TABLE_NAME = 'vw_testcase_summary';

-- =====================================================
-- 5. HIERARCHICAL PATH CONSTRUCTION
-- =====================================================

-- Query 8: Try to construct paths using node_hierarchy_paths_v2 (if it exists)
SELECT 
    e.id AS execution_id,
    e.tcversion_id,
    nhp.path as hierarchy_path,
    vts.Test_Execution_Path as vts_path
FROM executions e
LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
LEFT JOIN node_hierarchy_paths_v2 nhp ON e.tcversion_id = nhp.node_id
WHERE nhp.path LIKE '%mgt%' 
   OR nhp.path LIKE '%Mgt%' 
   OR nhp.path LIKE '%MGT%'
LIMIT 10;

-- =====================================================
-- 6. CASE SENSITIVITY TEST
-- =====================================================

-- Query 9: Test different case variations for 'Branch & COPs Mgt'
SELECT 
    Test_Execution_Path,
    tcversion_id,
    CASE 
        WHEN Test_Execution_Path LIKE '%Branch & COPs Mgt%' THEN 'Exact match'
        WHEN Test_Execution_Path LIKE '%branch & cops mgt%' THEN 'Lowercase match'
        WHEN Test_Execution_Path LIKE '%BRANCH & COPS MGT%' THEN 'Uppercase match'
        WHEN Test_Execution_Path LIKE '%Branch%COPs%Mgt%' THEN 'Partial match'
        WHEN Test_Execution_Path LIKE '%COPs%' THEN 'COPs found'
        WHEN Test_Execution_Path LIKE '%Mgt%' THEN 'Mgt found'
        ELSE 'No match'
    END as match_type
FROM vw_testcase_summary 
WHERE Test_Execution_Path IS NOT NULL 
    AND Test_Execution_Path != ''
    AND (Test_Execution_Path LIKE '%Branch%' 
         OR Test_Execution_Path LIKE '%COPs%' 
         OR Test_Execution_Path LIKE '%Mgt%')
LIMIT 20;

-- =====================================================
-- 7. FINAL WORKING QUERY TEST
-- =====================================================

-- Query 10: Try the exact query from your PHP but with different search terms
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    vts.Test_Execution_Path AS execution_path,
    nh_tc.name AS tc_name,
    parent_nh.name AS parent_suite_name,
    tp.notes AS testplan_notes,
    b.name AS build_name,
    e.execution_ts AS execution_timestamp
FROM 
    executions e
    JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
          FROM executions
          GROUP BY tcversion_id, build_id, testplan_id) latest_e 
        ON e.tcversion_id = latest_e.tcversion_id 
        AND e.build_id = latest_e.build_id 
        AND e.testplan_id = latest_e.testplan_id 
        AND e.execution_ts = latest_e.latest_exec_ts
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    JOIN testplans tp ON e.testplan_id = tp.id
    JOIN builds b ON e.build_id = b.id
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE 1=1
    AND (vts.Test_Execution_Path LIKE '%COPs%' 
         OR parent_nh.name LIKE '%COPs%'
         OR parent_nh.name LIKE '%Mgt%')
ORDER BY vts.Test_Execution_Path, e.execution_ts DESC
LIMIT 10;

-- =====================================================
-- INSTRUCTIONS:
-- =====================================================
-- Run these queries in order to diagnose the issue:
-- 1. Start with Query 1 to see what paths actually exist
-- 2. Run Query 2 to check for 'mgt' variations
-- 3. Run Query 5 to see if paths are constructed differently
-- 4. Run Query 9 to test case sensitivity
-- 5. Run Query 10 to test alternative filtering approaches
-- =====================================================
