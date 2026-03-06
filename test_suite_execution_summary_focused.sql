-- =====================================================
-- Test Suite Execution Summary - Focused Query
-- =====================================================
-- This SQL file generates the exact data shown in the "Test Suite Execution Summary" section
-- of the TestLink execution summary page. It groups execution data by hierarchical test paths
-- and calculates counts and rates for each path exactly as displayed in the UI.
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
-- TEST SUITE EXECUTION SUMMARY QUERY
-- =====================================================
-- This query produces the exact output shown in the "Test Suite Execution Summary" section
-- with proper hierarchy grouping and accurate rate calculations

SELECT 
    -- Test Path column (hierarchical suite path)
    nhp.full_path AS test_path,
    
    -- Total test cases in this path (unfiltered count - always shows full count regardless of filters)
    (
        SELECT COUNT(DISTINCT tcv_total.id)
        FROM testplan_tcversions tptcv_total
        JOIN tcversions tcv_total ON tptcv_total.tcversion_id = tcv_total.id
        JOIN nodes_hierarchy nh_tcv_total ON tcv_total.id = nh_tcv_total.id
        JOIN nodes_hierarchy nh_tc_total ON nh_tcv_total.parent_id = nh_tc_total.id
        JOIN nodes_hierarchy parent_nh_total ON nh_tc_total.parent_id = parent_nh_total.id
        LEFT JOIN node_hierarchy_paths_v2 nhp_total ON parent_nh_total.id = nhp_total.node_id
        JOIN testplans tp_total ON tptcv_total.testplan_id = tp_total.id
        JOIN testprojects tproj_total ON tp_total.testproject_id = tproj_total.id
        WHERE nhp_total.full_path = nhp.full_path
        AND (@selected_project = 0 OR tproj_total.id = @selected_project)
        AND (@selected_plan = 0 OR tp_total.id = @selected_plan)
    ) AS total_testcases,
    
    -- Test Case Count column
    COUNT(*) AS testcase_count,
    
    -- Status count columns
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS not_run_count,
    
    -- Pass Rate column (passed / executed tests) * 100
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0.00
    END AS pass_rate,
    
    -- Fail Rate column (failed / executed tests) * 100
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0.00
    END AS fail_rate,
    
    -- Block Rate column (blocked / total tests) * 100
    CASE 
        WHEN COUNT(*) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2)
        ELSE 0.00
    END AS block_rate,
    
    -- Pending Rate column (not run / non-blocked tests) * 100
    CASE 
        WHEN (COUNT(*) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) / 
                   (COUNT(*) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END))) * 100, 2)
        ELSE 0.00
    END AS pending_rate

FROM 
    executions e
    -- Get only the latest execution for each test case version per build/testplan combination
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
    -- Join with parent suite to get the hierarchical path
    JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    -- Join with test plan and project information for filtering
    JOIN testplans tp ON e.testplan_id = tp.id
    JOIN builds b ON e.build_id = b.id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    -- Join with execution path information using parent suite node (key fix)
    INNER JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id
WHERE 1=1
    -- Apply all the same filters as the main application
    AND (@selected_project = 0 OR tp.testproject_id = @selected_project)
    AND (@selected_plan = 0 OR e.testplan_id = @selected_plan)
    AND (@selected_build = 0 OR e.build_id = @selected_build)
    AND (@selected_status = '' OR e.status = @selected_status)
    AND (@execution_path = '' OR nhp.full_path LIKE CONCAT('%', @execution_path, '%'))
    AND (@start_date = '' OR e.execution_ts >= CONCAT(@start_date, ' 00:00:00'))
    AND (@end_date = '' OR e.execution_ts <= CONCAT(@end_date, ' 23:59:59'))
    -- Only include paths that have execution data
    AND nhp.full_path IS NOT NULL
    AND nhp.full_path != ''
GROUP BY 
    nhp.full_path
ORDER BY 
    nhp.full_path
;

-- =====================================================
-- CALCULATION VERIFICATION NOTES
-- =====================================================
-- This query matches the exact calculations used in the PHP backend:
--
-- 1. Pass Rate = (Passed Tests / Executed Tests) * 100
--    where Executed Tests = Passed + Failed (excludes blocked and not run)
--
-- 2. Fail Rate = (Failed Tests / Executed Tests) * 100
--    where Executed Tests = Passed + Failed (excludes blocked and not run)
--
-- 3. Block Rate = (Blocked Tests / Total Tests) * 100
--    where Total Tests = All test cases in the path
--
-- 4. Pending Rate = (Not Run Tests / Non-Blocked Tests) * 100
--    where Non-Blocked Tests = Total Tests - Blocked Tests
--
-- 5. Test cases inherit the execution path from their parent test suite
--    Multiple test cases in the same suite share the same execution path
--
-- 6. Make sure to run "CALL refresh_node_hierarchy_paths_v2();" before using
--    this query to ensure current hierarchy data
