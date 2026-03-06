-- Suite Execution Summary - Higher Level View (Corrected based on working logic)
-- This view counts test cases at the suite level, grouping suites with the same name across different test projects
-- Based on the working logic from test_execution_summary.php

WITH all_testcase_versions AS (
    -- Get all test case versions from the nodes_hierarchy and tcversions tables
    SELECT 
        tcv.id AS tcversion_id,
        tc.id AS tc_id, 
        tc.parent_id AS suite_id,
        tc.name AS testcase_name,
        parent_tc.name AS suite_name,
        parent_tc.parent_id AS parent_suite_id,
        tcversion.version,
        tcversion.tc_external_id
    FROM 
        nodes_hierarchy tcv
        JOIN nodes_hierarchy tc ON tc.id = tcv.parent_id
        JOIN nodes_hierarchy parent_tc ON tc.parent_id = parent_tc.id
        JOIN tcversions tcversion ON tcversion.id = tcv.id
    WHERE 
        tcv.node_type_id = 4  -- Test case versions
        AND tc.node_type_id = 3  -- Test cases
        AND parent_tc.node_type_id = 2  -- Test suites
        AND tcversion.active = 1
),

-- Get test case versions assigned to test plans
testcase_assignments AS (
    SELECT 
        atcv.tcversion_id,
        atcv.tc_id,
        atcv.suite_id,
        atcv.suite_name,
        atcv.testcase_name,
        tptc.testplan_id,
        tp.testproject_id,
        proj.notes AS testproject_name
    FROM 
        all_testcase_versions atcv
        JOIN testplan_tcversions tptc ON atcv.tcversion_id = tptc.tcversion_id
        JOIN testplans tp ON tptc.testplan_id = tp.id
        JOIN testprojects proj ON tp.testproject_id = proj.id
),

-- Get the latest execution for each test case version if it exists
latest_executions AS (
    SELECT 
        ta.tcversion_id,
        ta.tc_id,
        ta.suite_id,
        ta.suite_name,
        ta.testcase_name,
        ta.testplan_id,
        ta.testproject_id,
        ta.testproject_name,
        e.status,
        e.execution_ts,
        e.tester_id,
        e.build_id
    FROM 
        testcase_assignments ta
    LEFT JOIN (
        -- Get the latest execution for each test case version
        SELECT 
            e.tcversion_id, 
            e.id,
            e.status,
            e.testplan_id,
            e.build_id,
            e.execution_ts,
            e.tester_id
        FROM 
            executions e
        JOIN (
            SELECT 
                tcversion_id, 
                build_id, 
                testplan_id, 
                MAX(execution_ts) AS latest_exec_ts
            FROM 
                executions
            GROUP BY 
                tcversion_id, build_id, testplan_id
        ) latest ON e.tcversion_id = latest.tcversion_id 
          AND e.build_id = latest.build_id 
          AND e.testplan_id = latest.testplan_id 
          AND e.execution_ts = latest.latest_exec_ts
    ) e ON ta.tcversion_id = e.tcversion_id 
        AND ta.testplan_id = e.testplan_id
),

-- Aggregate by suite name (higher level grouping)
suite_summary AS (
    SELECT 
        suite_name,
        COUNT(*) AS total_test_cases,
        SUM(CASE WHEN status = 'p' THEN 1 ELSE 0 END) AS passed,
        SUM(CASE WHEN status = 'f' THEN 1 ELSE 0 END) AS failed,
        SUM(CASE WHEN status = 'b' THEN 1 ELSE 0 END) AS blocked,
        SUM(CASE WHEN status = 's' THEN 1 ELSE 0 END) AS skipped,
        SUM(CASE WHEN status = 'w' THEN 1 ELSE 0 END) AS warning,
        SUM(CASE WHEN status IS NULL OR status = 'n' THEN 1 ELSE 0 END) AS not_run,
        SUM(CASE WHEN status IS NOT NULL THEN 1 ELSE 0 END) AS total_executed,
        MAX(execution_ts) AS last_execution,
        -- Count how many different test projects contain this suite name
        COUNT(DISTINCT testproject_id) AS project_count,
        -- List of test projects containing this suite
        GROUP_CONCAT(DISTINCT testproject_name ORDER BY testproject_name SEPARATOR ', ') AS projects
    FROM latest_executions
    GROUP BY suite_name
)

-- Final view definition
SELECT 
    suite_name,
    total_test_cases,
    total_executed,
    passed,
    failed,
    blocked,
    not_run,
    skipped,
    warning,
    -- Calculate pass rate (only on executed tests)
    CASE 
        WHEN (passed + failed) > 0 
        THEN ROUND((passed / (passed + failed)) * 100, 1) 
        ELSE 0 
    END AS pass_rate,
    -- Calculate execution rate
    CASE 
        WHEN total_test_cases > 0 
        THEN ROUND((total_executed / total_test_cases) * 100, 1) 
        ELSE 0 
    END AS execution_rate,
    last_execution,
    project_count,
    projects
FROM suite_summary
ORDER BY suite_name;
