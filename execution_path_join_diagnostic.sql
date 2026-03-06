-- =====================================================
-- Execution Path Join Diagnostic
-- =====================================================
-- This diagnostic helps identify why the execution summary queries are empty
-- and determines the correct join logic between executions and hierarchy paths
-- =====================================================

-- Check 1: Sample execution data with hierarchy info
SELECT query
    'EXECUTION DATA SAMPLE' AS section,
    e.id AS execution_id,
    e.tcversion_id,
    nh_tc.id AS testcase_node_id,
    nh_tc.name AS testcase_name,
    nh_tc.parent_id AS testcase_parent_id,
    parent_nh.name AS parent_suite_name,
    parent_nh.id AS parent_suite_id
FROM executions e
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
LIMIT 10;

-- Check 2: Test if test case nodes have paths in node_hierarchy_paths_v2
SELECT 
    'TESTCASE NODE PATHS CHECK' AS section,
    COUNT(*) AS testcases_with_paths
FROM executions e
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    INNER JOIN node_hierarchy_paths_v2 nhp ON nh_tc.id = nhp.node_id;

-- Check 3: Test if parent suite nodes have paths in node_hierarchy_paths_v2
SELECT 
    'PARENT SUITE PATHS CHECK' AS section,
    COUNT(*) AS suites_with_paths
FROM executions e
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    INNER JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id;

-- Check 4: Show node types in hierarchy paths
SELECT 
    'NODE TYPES IN PATHS' AS section,
    nh.node_type_id,
    CASE nh.node_type_id
        WHEN 1 THEN 'Project'
        WHEN 2 THEN 'Test Suite'
        WHEN 3 THEN 'Test Case'
        WHEN 4 THEN 'Test Case Version'
        ELSE 'Unknown'
    END AS node_type_name,
    COUNT(*) AS count_in_paths
FROM node_hierarchy_paths_v2 nhp
    JOIN nodes_hierarchy nh ON nhp.node_id = nh.id
GROUP BY nh.node_type_id
ORDER BY nh.node_type_id;

-- Check 5: Sample execution data with CORRECT join (using parent suite)
SELECT 
    'CORRECTED EXECUTION SAMPLE' AS section,
    e.id AS execution_id,
    e.status,
    nh_tc.name AS testcase_name,
    parent_nh.name AS parent_suite_name,
    nhp.full_path AS execution_path
FROM executions e
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    INNER JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id
LIMIT 10;

-- =====================================================
-- CORRECTED TEST SUITE EXECUTION SUMMARY QUERY
-- =====================================================
-- This version joins on the parent suite node instead of the test case node

SELECT 
    nhp.full_path AS test_path,
    COUNT(*) AS testcase_count,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked_count,
    SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) AS not_run_count,
    
    -- Calculate pass rate (passed / executed tests)
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0
    END AS pass_rate,
    
    -- Calculate fail rate (failed / executed tests)
    CASE 
        WHEN (SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) / 
                   SUM(CASE WHEN e.status IN ('p','f') THEN 1 ELSE 0 END)) * 100, 2)
        ELSE 0
    END AS fail_rate,
    
    -- Calculate block rate (blocked / total tests)
    CASE 
        WHEN COUNT(*) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2)
        ELSE 0
    END AS block_rate,
    
    -- Calculate pending rate (not run / non-blocked tests)
    CASE 
        WHEN (COUNT(*) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END)) > 0 THEN
            ROUND((SUM(CASE WHEN e.status = 'n' OR e.status IS NULL THEN 1 ELSE 0 END) / 
                   (COUNT(*) - SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END))) * 100, 2)
        ELSE 0
    END AS pending_rate

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
    -- Join with parent suite (this is the key change)
    JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    -- Join with test plan and project information for filtering
    JOIN testplans tp ON e.testplan_id = tp.id
    JOIN builds b ON e.build_id = b.id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id
    -- Join with execution path information using PARENT SUITE node
    INNER JOIN node_hierarchy_paths_v2 nhp ON parent_nh.id = nhp.node_id
WHERE 1=1
    -- Apply filters (using parameters from original query)
    -- AND (@selected_project = 0 OR tp.testproject_id = @selected_project)
    -- AND (@selected_plan = 0 OR e.testplan_id = @selected_plan)
    -- AND (@selected_build = 0 OR e.build_id = @selected_build)
    -- AND (@selected_status = '' OR e.status = @selected_status)
    -- AND (@execution_path = '' OR nhp.full_path LIKE CONCAT('%', @execution_path, '%'))
    -- AND (@start_date = '' OR e.execution_ts >= CONCAT(@start_date, ' 00:00:00'))
    -- AND (@end_date = '' OR e.execution_ts <= CONCAT(@end_date, ' 23:59:59'))
    AND nhp.full_path IS NOT NULL
    AND nhp.full_path != ''
GROUP BY 
    nhp.full_path
ORDER BY 
    nhp.full_path
;

-- =====================================================
-- NOTES
-- =====================================================
-- The key insight is that node_hierarchy_paths_v2 contains paths for test suites/folders,
-- not individual test cases. Therefore, we need to join on the parent suite node
-- (parent_nh.id) rather than the test case node (nh_tc.id).
--
-- This matches the pattern seen in your sample data where paths like:
-- "FUNCTIONAL TESTING > ARCHIVED > Lending > EC" represent the suite hierarchy,
-- and individual test cases within those suites inherit the suite's path.
