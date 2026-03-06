-- =====================================================
-- DEBUG EMPTY RESULTS - STEP BY STEP DIAGNOSIS
-- =====================================================
-- These queries help identify why the execution path queries return empty results
-- =====================================================

USE tl_uat;

-- =====================================================
-- STEP 1: CHECK IF BASIC TABLES HAVE DATA
-- =====================================================

-- Check executions table
SELECT 'executions' as table_name, COUNT(*) as record_count FROM executions;

-- Check node_hierarchy_paths table
SELECT 'node_hierarchy_paths' as table_name, COUNT(*) as record_count FROM node_hierarchy_paths;

-- Check tcversions table
SELECT 'tcversions' as table_name, COUNT(*) as record_count FROM tcversions;

-- Check nodes_hierarchy table
SELECT 'nodes_hierarchy' as table_name, COUNT(*) as record_count FROM nodes_hierarchy;

-- =====================================================
-- STEP 2: CHECK THE JOIN CHAIN STEP BY STEP
-- =====================================================

-- Step 2a: Basic executions data
SELECT 
    COUNT(*) as total_executions,
    COUNT(DISTINCT tcversion_id) as unique_tcversions,
    COUNT(DISTINCT testplan_id) as unique_testplans,
    COUNT(DISTINCT build_id) as unique_builds
FROM executions;

-- Step 2b: Executions with latest execution logic
SELECT COUNT(*) as executions_with_latest
FROM executions e
JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
      FROM executions
      GROUP BY tcversion_id, build_id, testplan_id) latest_e 
    ON e.tcversion_id = latest_e.tcversion_id 
    AND e.build_id = latest_e.build_id 
    AND e.testplan_id = latest_e.testplan_id 
    AND e.execution_ts = latest_e.latest_exec_ts;

-- Step 2c: Add tcversions join
SELECT COUNT(*) as executions_with_tcversions
FROM executions e
JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
      FROM executions
      GROUP BY tcversion_id, build_id, testplan_id) latest_e 
    ON e.tcversion_id = latest_e.tcversion_id 
    AND e.build_id = latest_e.build_id 
    AND e.testplan_id = latest_e.testplan_id 
    AND e.execution_ts = latest_e.latest_exec_ts
JOIN tcversions tcv ON e.tcversion_id = tcv.id;

-- Step 2d: Add nodes_hierarchy joins
SELECT COUNT(*) as executions_with_nodes_hierarchy
FROM executions e
JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
      FROM executions
      GROUP BY tcversion_id, build_id, testplan_id) latest_e 
    ON e.tcversion_id = latest_e.tcversion_id 
    AND e.build_id = latest_e.build_id 
    AND e.testplan_id = latest_e.testplan_id 
    AND e.execution_ts = latest_e.latest_exec_ts
JOIN tcversions tcv ON e.tcversion_id = tcv.id
JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id;

-- Step 2e: Add node_hierarchy_paths join (THIS IS WHERE IT MIGHT FAIL)
SELECT COUNT(*) as executions_with_hierarchy_paths
FROM executions e
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
LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id;

-- =====================================================
-- STEP 3: ANALYZE THE JOIN MISMATCH
-- =====================================================

-- Check what node types are in node_hierarchy_paths
SELECT 
    nh.node_type_id,
    CASE 
        WHEN nh.node_type_id = 1 THEN 'Test Project'
        WHEN nh.node_type_id = 2 THEN 'Test Suite'
        WHEN nh.node_type_id = 3 THEN 'Test Case'
        WHEN nh.node_type_id = 4 THEN 'Test Case Version'
        ELSE 'Unknown'
    END as node_type_name,
    COUNT(*) as count_in_hierarchy_paths
FROM node_hierarchy_paths nhp
JOIN nodes_hierarchy nh ON nhp.node_id = nh.id
GROUP BY nh.node_type_id
ORDER BY nh.node_type_id;

-- Check what node types our test cases (nh_tc) are
SELECT 
    nh_tc.node_type_id,
    CASE 
        WHEN nh_tc.node_type_id = 1 THEN 'Test Project'
        WHEN nh_tc.node_type_id = 2 THEN 'Test Suite'
        WHEN nh_tc.node_type_id = 3 THEN 'Test Case'
        WHEN nh_tc.node_type_id = 4 THEN 'Test Case Version'
        ELSE 'Unknown'
    END as node_type_name,
    COUNT(*) as count_in_executions
FROM executions e
JOIN tcversions tcv ON e.tcversion_id = tcv.id
JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
GROUP BY nh_tc.node_type_id
ORDER BY nh_tc.node_type_id;

-- =====================================================
-- STEP 4: FIND THE CORRECT JOIN
-- =====================================================

-- Option 1: Try joining on the test case version node (nh_tcv) instead of test case (nh_tc)
SELECT COUNT(*) as executions_with_tcv_hierarchy_paths
FROM executions e
JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
      FROM executions
      GROUP BY tcversion_id, build_id, testplan_id) latest_e 
    ON e.tcversion_id = latest_e.tcversion_id 
    AND e.build_id = latest_e.build_id 
    AND e.testplan_id = latest_e.testplan_id 
    AND e.execution_ts = latest_e.latest_exec_ts
JOIN tcversions tcv ON e.tcversion_id = tcv.id
JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
LEFT JOIN node_hierarchy_paths nhp ON nh_tcv.id = nhp.node_id;

-- Option 2: Try joining on the parent suite node
SELECT COUNT(*) as executions_with_parent_hierarchy_paths
FROM executions e
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
LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
LEFT JOIN node_hierarchy_paths nhp ON parent_nh.id = nhp.node_id;

-- =====================================================
-- STEP 5: SAMPLE DATA TO UNDERSTAND THE STRUCTURE
-- =====================================================

-- Show sample execution data with node hierarchy
SELECT 
    e.id as execution_id,
    e.tcversion_id,
    nh_tcv.id as tcv_node_id,
    nh_tcv.name as tcv_name,
    nh_tcv.node_type_id as tcv_node_type,
    nh_tc.id as tc_node_id,
    nh_tc.name as tc_name,
    nh_tc.node_type_id as tc_node_type,
    parent_nh.id as parent_node_id,
    parent_nh.name as parent_name,
    parent_nh.node_type_id as parent_node_type
FROM executions e
JOIN tcversions tcv ON e.tcversion_id = tcv.id
JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
LIMIT 5;

-- Show sample node_hierarchy_paths data
SELECT 
    node_id,
    full_path,
    level1_name,
    level2_name,
    level3_name,
    level4_name
FROM node_hierarchy_paths
WHERE full_path IS NOT NULL
LIMIT 5;

-- =====================================================
-- INSTRUCTIONS:
-- =====================================================
-- 1. Run these queries in order to identify where the join fails
-- 2. Step 2 queries will show you where the record count drops to 0
-- 3. Step 3 will show you what node types are involved
-- 4. Step 4 will test alternative join approaches
-- 5. Step 5 will show sample data to understand the structure
-- =====================================================
