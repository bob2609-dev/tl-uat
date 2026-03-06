-- =====================================================
-- MANUAL EXECUTION PATH FILTERING TEST QUERY
-- =====================================================
-- This query matches exactly what the PHP code is doing
-- Run this directly in your MySQL IDE to test execution path filtering
-- =====================================================

USE tl_uat;

-- First, ensure the node_hierarchy_paths table is up to date
-- UNCOMMENT AND RUN THIS LINE FIRST:
-- CALL refresh_node_hierarchy_paths();

-- =====================================================
-- 1. CHECK IF NODE_HIERARCHY_PATHS TABLE HAS DATA
-- =====================================================
SELECT 
    COUNT(*) as total_paths,
    COUNT(CASE WHEN full_path LIKE '%Mgt%' THEN 1 END) as paths_with_mgt,
    COUNT(CASE WHEN full_path LIKE '%COPs%' THEN 1 END) as paths_with_cops
FROM node_hierarchy_paths;

-- =====================================================
-- 2. SAMPLE PATHS FROM NODE_HIERARCHY_PATHS TABLE
-- =====================================================
SELECT 
    node_id,
    full_path,
    level1_name,
    level2_name,
    level3_name,
    level4_name
FROM node_hierarchy_paths 
WHERE full_path IS NOT NULL
ORDER BY full_path
LIMIT 10;

-- =====================================================
-- 3. EXACT QUERY FROM YOUR PHP CODE (NO FILTERS)
-- =====================================================
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    e.testplan_id,
    tp.notes AS testplan_notes,
    e.build_id,
    b.name AS build_name,
    b.notes AS build_notes,
    e.platform_id,
    p.name AS platform_name,
    p.notes AS platform_notes,
    e.tcversion_id,
    tcv.version AS tc_version,
    tcv.summary AS tc_summary,
    nh_tc.id AS tc_id,
    nh_tc.name AS tc_name,
    parent_nh.id AS parent_suite_id,
    parent_nh.name AS parent_suite_name,
    e.execution_ts AS execution_timestamp,
    e.tester_id,
    u.login AS tester_login,
    u.first AS tester_firstname,
    u.last AS tester_lastname,
    tp.testproject_id AS project_id,
    tproj.notes AS project_notes,
    nhp.full_path AS execution_path
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
    LEFT JOIN platforms p ON e.platform_id = p.id
    LEFT JOIN users u ON e.tester_id = u.id
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 1=1
ORDER BY tproj.notes, tp.notes, parent_nh.name, nh_tc.name, e.execution_ts DESC
LIMIT 20;

-- =====================================================
-- 4. TEST EXECUTION PATH FILTERING - CASE VARIATIONS
-- =====================================================

-- Test 1: Search for "Mgt" (exact case from your UI)
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    nhp.full_path AS execution_path,
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
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 1=1
    AND nhp.full_path LIKE '%Mgt%'
ORDER BY nhp.full_path, e.execution_ts DESC;

-- Test 2: Search for "COPs" (from your UI screenshot)
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    nhp.full_path AS execution_path,
    nh_tc.name AS tc_name,
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
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 1=1
    AND nhp.full_path LIKE '%COPs%'
ORDER BY nhp.full_path, e.execution_ts DESC;

-- Test 3: Search for "Branch" (part of "Branch & COPs Mgt")
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    nhp.full_path AS execution_path,
    nh_tc.name AS tc_name,
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
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 1=1
    AND nhp.full_path LIKE '%Branch%'
ORDER BY nhp.full_path, e.execution_ts DESC;

-- =====================================================
-- 5. DIAGNOSTIC QUERIES
-- =====================================================

-- Check if executions are properly joined with node_hierarchy_paths
SELECT 
    COUNT(e.id) as total_executions,
    COUNT(nhp.node_id) as executions_with_path_data,
    COUNT(e.id) - COUNT(nhp.node_id) as executions_missing_path_data
FROM 
    executions e
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id;

-- Check specific test case nodes and their paths
SELECT 
    nh_tc.id as tc_node_id,
    nh_tc.name as tc_name,
    nhp.node_id as path_node_id,
    nhp.full_path,
    COUNT(e.id) as execution_count
FROM 
    nodes_hierarchy nh_tc
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
    LEFT JOIN tcversions tcv ON nh_tc.id = tcv.id
    LEFT JOIN executions e ON tcv.id = e.tcversion_id
WHERE nh_tc.node_type_id = 3  -- Test cases
GROUP BY nh_tc.id, nh_tc.name, nhp.node_id, nhp.full_path
HAVING execution_count > 0
ORDER BY execution_count DESC
LIMIT 10;

-- =====================================================
-- INSTRUCTIONS:
-- =====================================================
-- 1. First run the refresh_node_hierarchy_paths() procedure
-- 2. Run query 1 to check if the table has data
-- 3. Run query 2 to see sample paths
-- 4. Run query 3 to see the base query results
-- 5. Run queries 4.1, 4.2, 4.3 to test different search terms
-- 6. Run query 5 if you need to diagnose join issues
-- =====================================================
