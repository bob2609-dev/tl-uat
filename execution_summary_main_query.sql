-- =====================================================
-- Test Execution Summary - Main Query
-- =====================================================
-- This SQL file replicates the main query from lib/execute/test_execution_summary.php
-- Use this to test and verify the execution summary data directly in your database IDE
-- 
-- Instructions:
-- 1. Update the parameter values below as needed
-- 2. Run the query to see the same data that appears in the PHP application
-- 3. Use this for troubleshooting and data verification
-- =====================================================

-- PARAMETERS - Update these values as needed for testing
SET @selected_project = 0;      -- 0 for all projects, or specific project ID
SET @selected_plan = 0;         -- 0 for all test plans, or specific test plan ID  
SET @selected_build = 0;        -- 0 for all builds, or specific build ID
SET @selected_status = '';      -- '' for all statuses, or 'p'/'f'/'b'/'n' for specific status
SET @execution_path = '';       -- '' for all paths, or partial path text for filtering
SET @start_date = '';           -- '' for no start date, or 'YYYY-MM-DD' format
SET @end_date = '';             -- '' for no end date, or 'YYYY-MM-DD' format

-- =====================================================
-- MAIN EXECUTION SUMMARY QUERY
-- =====================================================
-- This query gets the latest execution for each test case version per build/testplan combination
-- and includes all the hierarchical and metadata information needed for the summary display

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
    -- Get only the latest execution for each test case version per build/testplan
    JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
          FROM executions
          GROUP BY tcversion_id, build_id, testplan_id) latest_e 
        ON e.tcversion_id = latest_e.tcversion_id 
        AND e.build_id = latest_e.build_id 
        AND e.testplan_id = latest_e.testplan_id 
        AND e.execution_ts = latest_e.latest_exec_ts
    -- Join with test case version and hierarchy information
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    -- Join with test plan, build, and project information
    JOIN testplans tp ON e.testplan_id = tp.id
    JOIN builds b ON e.build_id = b.id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    -- Optional joins for additional metadata
    LEFT JOIN platforms p ON e.platform_id = p.id
    LEFT JOIN users u ON e.tester_id = u.id
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    -- Join with execution path information
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 1=1
    -- Apply project filter
    AND (@selected_project = 0 OR tp.testproject_id = @selected_project)
    -- Apply test plan filter
    AND (@selected_plan = 0 OR e.testplan_id = @selected_plan)
    -- Apply build filter
    AND (@selected_build = 0 OR e.build_id = @selected_build)
    -- Apply status filter
    AND (@selected_status = '' OR e.status = @selected_status)
    -- Apply execution path filter (partial match)
    AND (@execution_path = '' OR nhp.full_path LIKE CONCAT('%', @execution_path, '%'))
    -- Apply date range filters
    AND (@start_date = '' OR e.execution_ts >= CONCAT(@start_date, ' 00:00:00'))
    AND (@end_date = '' OR e.execution_ts <= CONCAT(@end_date, ' 23:59:59'))
ORDER BY 
    tproj.notes,        -- Project name
    tp.notes,           -- Test plan name  
    parent_nh.name,     -- Suite name
    nh_tc.name,         -- Test case name
    e.execution_ts DESC -- Latest executions first
;

-- =====================================================
-- SUMMARY STATISTICS QUERY
-- =====================================================
-- This query provides overall statistics similar to what's shown in the dashboard

SELECT 
    'EXECUTION SUMMARY STATISTICS' AS section,
    COUNT(*) AS total_executions,
    COUNT(DISTINCT e.tcversion_id) AS unique_test_cases,
    COUNT(DISTINCT e.testplan_id) AS test_plans_involved,
    COUNT(DISTINCT e.build_id) AS builds_involved,
    COUNT(DISTINCT e.tester_id) AS testers_involved,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' THEN 1 ELSE 0 END) AS not_run_count,
    ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / 
           NULLIF(SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END), 0)) * 100, 2) AS pass_rate_percent
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
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 1=1
    AND (@selected_project = 0 OR tp.testproject_id = @selected_project)
    AND (@selected_plan = 0 OR e.testplan_id = @selected_plan)
    AND (@selected_build = 0 OR e.build_id = @selected_build)
    AND (@selected_status = '' OR e.status = @selected_status)
    AND (@execution_path = '' OR nhp.full_path LIKE CONCAT('%', @execution_path, '%'))
    AND (@start_date = '' OR e.execution_ts >= CONCAT(@start_date, ' 00:00:00'))
    AND (@end_date = '' OR e.execution_ts <= CONCAT(@end_date, ' 23:59:59'))
;

-- =====================================================
-- TESTER SUMMARY QUERY  
-- =====================================================
-- This query shows execution counts by tester (similar to the "Top Testers" section)

SELECT 
    'TESTER SUMMARY' AS section,
    e.tester_id,
    COALESCE(CONCAT(u.first, ' ', u.last), u.login, CONCAT('User ID: ', e.tester_id)) AS tester_name,
    COUNT(*) AS execution_count,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' THEN 1 ELSE 0 END) AS not_run_count
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
    LEFT JOIN users u ON e.tester_id = u.id
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
WHERE 1=1
    AND (@selected_project = 0 OR tp.testproject_id = @selected_project)
    AND (@selected_plan = 0 OR e.testplan_id = @selected_plan)
    AND (@selected_build = 0 OR e.build_id = @selected_build)
    AND (@selected_status = '' OR e.status = @selected_status)
    AND (@execution_path = '' OR nhp.full_path LIKE CONCAT('%', @execution_path, '%'))
    AND (@start_date = '' OR e.execution_ts >= CONCAT(@start_date, ' 00:00:00'))
    AND (@end_date = '' OR e.execution_ts <= CONCAT(@end_date, ' 23:59:59'))
GROUP BY 
    e.tester_id, tester_name
ORDER BY 
    execution_count DESC
;

-- =====================================================
-- SUITE SUMMARY QUERY
-- =====================================================
-- This query shows execution counts by test suite (similar to the "Test Suite Progress" section)

SELECT 
    'SUITE SUMMARY' AS section,
    parent_nh.id AS suite_id,
    parent_nh.name AS suite_name,
    nhp_suite.full_path AS suite_path,
    COUNT(*) AS execution_count,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' THEN 1 ELSE 0 END) AS not_run_count,
    ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / 
           NULLIF(COUNT(*), 0)) * 100, 2) AS pass_rate_percent
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
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    LEFT JOIN node_hierarchy_paths nhp ON nh_tc.id = nhp.node_id
    LEFT JOIN node_hierarchy_paths nhp_suite ON parent_nh.id = nhp_suite.node_id
WHERE 1=1
    AND (@selected_project = 0 OR tp.testproject_id = @selected_project)
    AND (@selected_plan = 0 OR e.testplan_id = @selected_plan)
    AND (@selected_build = 0 OR e.build_id = @selected_build)
    AND (@selected_status = '' OR e.status = @selected_status)
    AND (@execution_path = '' OR nhp.full_path LIKE CONCAT('%', @execution_path, '%'))
    AND (@start_date = '' OR e.execution_ts >= CONCAT(@start_date, ' 00:00:00'))
    AND (@end_date = '' OR e.execution_ts <= CONCAT(@end_date, ' 23:59:59'))
GROUP BY 
    parent_nh.id, parent_nh.name, suite_path
ORDER BY 
    suite_name
;

-- =====================================================
-- NOTES
-- =====================================================
-- 1. Make sure to run "CALL refresh_node_hierarchy_paths();" before using these queries
--    to ensure the node_hierarchy_paths table has current data
--
-- 2. The main query uses the same logic as the PHP application:
--    - Gets latest execution per test case version/build/testplan combination
--    - Includes all necessary joins for hierarchical and metadata information
--    - Applies the same filtering logic as the web interface
--
-- 3. Update the @parameter variables at the top to match your testing needs
--
-- 4. These queries can be used for:
--    - Verifying data accuracy against the web interface
--    - Troubleshooting execution path filtering issues
--    - Performance testing and optimization
--    - Creating custom reports and analysis
