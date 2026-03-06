-- =====================================================
-- TestLink Execution Path Filtering - SQL Examples
-- =====================================================
-- This file contains SQL queries that demonstrate the execution path filtering
-- functionality implemented in the Test Execution Summary feature.
--
-- Generated for: TestLink UAT Environment
-- Date: 2025-07-31
-- Purpose: Testing and demonstrating execution path filtering capabilities
-- =====================================================

-- =====================================================
-- 1. COMPLETE BASE QUERY (No Filters Applied)
-- =====================================================
-- This is the complete query structure used in the Test Execution Summary
-- with all joins and fields, but no filtering applied.

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
    vts.Test_Execution_Path AS execution_path
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
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE 1=1
ORDER BY tproj.notes, tp.notes, parent_nh.name, nh_tc.name, e.execution_ts DESC;

-- =====================================================
-- 2. EXECUTION PATH FILTERING EXAMPLES
-- =====================================================

-- Example 1: Filter by specific execution path (exact partial match)
-- Replace 'YourPathName' with an actual execution path from your data
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
    AND vts.Test_Execution_Path LIKE '%YourPathName%'
ORDER BY vts.Test_Execution_Path, e.execution_ts DESC;

-- Example 2: Filter by suite name in execution path
-- This will find all executions where the path contains 'Suite'
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    vts.Test_Execution_Path AS execution_path,
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
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE 1=1
    AND vts.Test_Execution_Path LIKE '%Suite%'
ORDER BY vts.Test_Execution_Path, e.execution_ts DESC;

-- =====================================================
-- 3. COMBINED FILTERING EXAMPLES
-- =====================================================

-- Example 3: Execution path + Status filtering
-- Filter by execution path containing 'API' and status 'p' (passed)
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    vts.Test_Execution_Path AS execution_path,
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
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE 1=1
    AND vts.Test_Execution_Path LIKE '%API%'
    AND e.status = 'p'
ORDER BY vts.Test_Execution_Path, e.execution_ts DESC;

-- Example 4: Execution path + Project + Date range filtering
-- Replace project_id, start_date, and end_date with actual values
SELECT 
    e.id AS execution_id,
    e.status AS execution_status,
    vts.Test_Execution_Path AS execution_path,
    nh_tc.name AS tc_name,
    tp.notes AS testplan_notes,
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
    AND vts.Test_Execution_Path LIKE '%YourPathName%'
    AND tp.testproject_id = 1  -- Replace with actual project ID
    AND e.execution_ts >= '2025-01-01 00:00:00'  -- Replace with actual start date
    AND e.execution_ts <= '2025-12-31 23:59:59'  -- Replace with actual end date
ORDER BY vts.Test_Execution_Path, e.execution_ts DESC;

-- =====================================================
-- 4. UTILITY QUERIES FOR TESTING
-- =====================================================

-- Query 1: Get all unique execution paths to see what's available
SELECT DISTINCT 
    vts.Test_Execution_Path AS execution_path,
    COUNT(*) AS execution_count
FROM 
    executions e
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE 
    vts.Test_Execution_Path IS NOT NULL
    AND vts.Test_Execution_Path != ''
GROUP BY vts.Test_Execution_Path
ORDER BY vts.Test_Execution_Path;

-- Query 2: Get execution path statistics
SELECT 
    vts.Test_Execution_Path AS execution_path,
    COUNT(*) AS total_executions,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS not_run_count,
    ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) * 100.0 / COUNT(*)), 2) AS pass_rate
FROM 
    executions e
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE 
    vts.Test_Execution_Path IS NOT NULL
    AND vts.Test_Execution_Path != ''
GROUP BY vts.Test_Execution_Path
ORDER BY vts.Test_Execution_Path;

-- Query 3: Test the vw_testcase_summary view structure
SELECT 
    tcversion_id,
    Test_Execution_Path,
    COUNT(*) as record_count
FROM vw_testcase_summary 
WHERE Test_Execution_Path IS NOT NULL
GROUP BY tcversion_id, Test_Execution_Path
LIMIT 10;

-- =====================================================
-- 5. TROUBLESHOOTING QUERIES
-- =====================================================

-- Query 1: Check if vw_testcase_summary view exists and has data
SELECT COUNT(*) as total_records 
FROM vw_testcase_summary 
WHERE Test_Execution_Path IS NOT NULL;

-- Query 2: Check executions without execution path data
SELECT 
    e.id AS execution_id,
    e.tcversion_id,
    vts.Test_Execution_Path
FROM 
    executions e
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id
WHERE 
    vts.Test_Execution_Path IS NULL
LIMIT 10;

-- Query 3: Verify the join between executions and vw_testcase_summary
SELECT 
    COUNT(e.id) as total_executions,
    COUNT(vts.tcversion_id) as executions_with_path_data,
    COUNT(e.id) - COUNT(vts.tcversion_id) as executions_missing_path_data
FROM 
    executions e
    LEFT JOIN vw_testcase_summary vts ON e.tcversion_id = vts.tcversion_id;

-- =====================================================
-- USAGE INSTRUCTIONS:
-- =====================================================
-- 1. Replace placeholder values (YourPathName, project_id, dates) with actual values from your TestLink database
-- 2. Run the utility queries first to understand your data structure
-- 3. Use the filtering examples to test different scenarios
-- 4. The troubleshooting queries help identify any data issues
-- 5. All queries use the same structure as the PHP implementation for consistency
-- =====================================================
