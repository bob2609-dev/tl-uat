-- Suite Execution Summary - Higher Level View
-- This view counts test cases at the suite level, grouping suites with the same name across different test projects
-- Example: All "PAYMENT & TRXN" suites will be aggregated together

-- Step 1: Create a CTE to find all test suites and their test cases
WITH suite_test_cases AS (
    SELECT 
        nh.id AS suite_id,
        nh.name AS suite_name,
        nh.parent_id AS suite_parent_id,
        tc.id AS testcase_id,
        tc.name AS testcase_name,
        tc.node_order,
        tcv.id AS tcversion_id,
        tp.id AS testplan_id,
        tp.testproject_id,
        proj.notes AS testproject_name
    FROM nodes_hierarchy nh
    -- Find test suites (nodes that have test case children)
    JOIN nodes_hierarchy tc ON tc.parent_id = nh.id
    -- Get test case version info (only cases that have versions)
    JOIN tcversions tcv ON tc.id = tcv.tc_external_id
    -- Join to test plans to get project context
    JOIN testplan_tcversions tptc ON tcv.id = tptc.tcversion_id
    JOIN testplans tp ON tptc.testplan_id = tp.id
    JOIN testprojects proj ON tp.testproject_id = proj.id
    WHERE nh.node_type_id = 2  -- Test suite node type
),

-- Step 2: Get execution information for each test case
test_case_executions AS (
    SELECT 
        stc.suite_id,
        stc.suite_name,
        stc.testcase_id,
        stc.testcase_name,
        stc.tcversion_id,
        stc.testplan_id,
        stc.testproject_id,
        stc.testproject_name,
        -- Get latest execution for each test case
        latest_e.id AS execution_id,
        latest_e.status AS execution_status,
        latest_e.execution_ts,
        latest_e.tester_id,
        latest_e.build_id,
        CASE 
            WHEN latest_e.id IS NOT NULL THEN 1 
            ELSE 0 
        END AS has_execution
    FROM suite_test_cases stc
    LEFT JOIN (
        SELECT 
            e1.tcversion_id,
            e1.testplan_id,
            e1.id,
            e1.status,
            e1.execution_ts,
            e1.tester_id,
            e1.build_id,
            ROW_NUMBER() OVER (
                PARTITION BY e1.tcversion_id, e1.testplan_id 
                ORDER BY e1.execution_ts DESC, e1.id DESC
            ) AS rn
        FROM executions e1
    ) latest_e ON latest_e.tcversion_id = stc.tcversion_id 
        AND latest_e.testplan_id = stc.testplan_id 
        AND latest_e.rn = 1  -- Only get latest execution
),

-- Step 3: Aggregate by suite name (higher level grouping)
suite_summary AS (
    SELECT 
        suite_name,
        COUNT(*) AS total_test_cases,
        SUM(has_execution) AS total_executed,
        SUM(CASE WHEN execution_status = 'p' THEN 1 ELSE 0 END) AS passed,
        SUM(CASE WHEN execution_status = 'f' THEN 1 ELSE 0 END) AS failed,
        SUM(CASE WHEN execution_status = 'b' THEN 1 ELSE 0 END) AS blocked,
        SUM(CASE WHEN execution_status = 'n' THEN 1 ELSE 0 END) AS not_run,
        SUM(CASE WHEN execution_status = 's' THEN 1 ELSE 0 END) AS skipped,
        SUM(CASE WHEN execution_status = 'w' THEN 1 ELSE 0 END) AS warning,
        MAX(execution_ts) AS last_execution,
        -- Count how many different test projects contain this suite name
        COUNT(DISTINCT testproject_id) AS project_count,
        -- List of test projects containing this suite
        GROUP_CONCAT(DISTINCT testproject_name ORDER BY testproject_name SEPARATOR ', ') AS projects
    FROM test_case_executions
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

-- Alternative: Create as a view
/*
CREATE OR REPLACE VIEW vw_suite_execution_summary_higher_level AS
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
    CASE 
        WHEN (passed + failed) > 0 
        THEN ROUND((passed / (passed + failed)) * 100, 1) 
        ELSE 0 
    END AS pass_rate,
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
*/
