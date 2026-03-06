-- =====================================================
-- TEST SUITE EXECUTION SUMMARY QUERY
-- =====================================================
-- This query produces output similar to the "Test Suite Execution Summary" 
-- section in your TestLink UI, showing execution paths with statistics
-- =====================================================

USE tl_uat;

-- First, ensure the node_hierarchy_paths table is up to date
-- UNCOMMENT AND RUN THIS LINE FIRST:
-- CALL refresh_node_hierarchy_paths();

-- =====================================================
-- MAIN QUERY: TEST SUITE EXECUTION SUMMARY
-- =====================================================
-- This matches the format from your UI screenshot:
-- Test Path | Test Case Count | Passed | Failed | Blocked | Not Run | Pass Rate | Fail Rate | Block Rate | Pending Rate

SELECT 
    nhp.full_path AS 'Test Path',
    COUNT(DISTINCT e.tcversion_id) AS 'Test Case Count',
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS 'Passed',
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS 'Failed',
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS 'Blocked',
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS 'Not Run',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Pass Rate',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Fail Rate',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Block Rate',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Pending Rate'
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
WHERE 
    nhp.full_path IS NOT NULL
    AND nhp.full_path != ''
GROUP BY 
    nhp.full_path
ORDER BY 
    nhp.full_path;

-- =====================================================
-- FILTERED VERSION: TEST EXECUTION PATH FILTERING
-- =====================================================
-- Same query but with execution path filtering (like your UI filter)

-- Example 1: Filter by "Mgt" (matches "Branch & COPs Mgt")
SELECT 
    nhp.full_path AS 'Test Path',
    COUNT(DISTINCT e.tcversion_id) AS 'Test Case Count',
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS 'Passed',
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS 'Failed',
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS 'Blocked',
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS 'Not Run',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Pass Rate',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Fail Rate',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Block Rate',
    CONCAT(
        ROUND(
            (SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) * 100.0 / 
             NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2
        ), '%'
    ) AS 'Pending Rate'
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
WHERE 
    nhp.full_path IS NOT NULL
    AND nhp.full_path != ''
    AND nhp.full_path LIKE '%Mgt%'  -- EXECUTION PATH FILTER
GROUP BY 
    nhp.full_path
ORDER BY 
    nhp.full_path;

-- Example 2: Filter by "COPs"
SELECT 
    nhp.full_path AS 'Test Path',
    COUNT(DISTINCT e.tcversion_id) AS 'Test Case Count',
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS 'Passed',
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS 'Failed',
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS 'Blocked',
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS 'Not Run',
    CONCAT(ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2), '%') AS 'Pass Rate',
    CONCAT(ROUND((SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2), '%') AS 'Fail Rate',
    CONCAT(ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2), '%') AS 'Block Rate',
    CONCAT(ROUND((SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 2), '%') AS 'Pending Rate'
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
WHERE 
    nhp.full_path IS NOT NULL
    AND nhp.full_path != ''
    AND nhp.full_path LIKE '%COPs%'  -- EXECUTION PATH FILTER
GROUP BY 
    nhp.full_path
ORDER BY 
    nhp.full_path;

-- =====================================================
-- SIMPLIFIED VERSION FOR QUICK TESTING
-- =====================================================
-- Shorter query to quickly see if the filtering is working

SELECT 
    nhp.full_path AS 'Test Path',
    COUNT(DISTINCT e.tcversion_id) AS 'Test Cases',
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS 'Passed',
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS 'Failed',
    ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT e.tcversion_id), 0)), 1) AS 'Pass %'
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
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 
    nhp.full_path IS NOT NULL
    -- CHANGE THIS LINE TO TEST DIFFERENT FILTERS:
    AND nhp.full_path LIKE '%Mgt%'  -- Try: '%COPs%', '%Branch%', '%Financial%', etc.
GROUP BY 
    nhp.full_path
ORDER BY 
    nhp.full_path;

-- =====================================================
-- INSTRUCTIONS:
-- =====================================================
-- 1. First run: CALL refresh_node_hierarchy_paths();
-- 2. Run the main query to see all test suite execution summary data
-- 3. Run the filtered versions to test execution path filtering
-- 4. Use the simplified version for quick testing with different search terms
-- 5. The output should match the format of your "Test Suite Execution Summary" UI section
-- =====================================================
