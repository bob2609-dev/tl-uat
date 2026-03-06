-- Create a stored procedure to query the test path metrics with filters
-- Parameters allow filtering by date range and tester

DROP PROCEDURE IF EXISTS query_test_path_metrics;

DELIMITER //

CREATE PROCEDURE query_test_path_metrics(
    IN p_start_date DATETIME,         -- Start date for filter (NULL for no lower bound)
    IN p_end_date DATETIME,           -- End date for filter (NULL for no upper bound)
    IN p_tester_id INT,               -- Tester ID (NULL for all testers)
    IN p_project_id INT,              -- Project ID (NULL for all projects)
    IN p_test_plan_id INT,            -- Test Plan ID (NULL for all test plans)
    IN p_build_id INT,                -- Build ID (NULL for all builds)
    IN p_path_filter VARCHAR(255)     -- Text to filter paths (NULL or empty for all paths)
)
BEGIN
    -- First, get all test cases with their latest versions
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_testcases AS
    SELECT 
        nh_tc.id AS tc_id,
        nh_tc.parent_id AS suite_id, 
        nh_tcv.id AS tcversion_id,
        tcv.tc_external_id,
        tcv.version
    FROM 
        nodes_hierarchy nh_tc
    JOIN 
        nodes_hierarchy nh_tcv ON nh_tcv.parent_id = nh_tc.id
    JOIN 
        tcversions tcv ON tcv.id = nh_tcv.id
    WHERE 
        nh_tc.node_type_id = 3 -- assuming 3 is the testcase type ID
    AND tcv.id = (
        SELECT MAX(id) FROM tcversions WHERE tcversions.tc_external_id = tcv.tc_external_id
    );
    
    -- Next, get the latest executions for each test case version
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_latest_executions AS
    SELECT 
        tc.tc_id,
        tc.suite_id,
        tc.tcversion_id,
        tc.tc_external_id,
        e.id AS execution_id,
        e.build_id,
        e.testplan_id,
        e.tester_id,
        e.status,
        e.execution_ts
    FROM 
        temp_testcases tc
    LEFT JOIN (
        SELECT 
            e.tcversion_id, 
            e.id, 
            e.status,
            e.testplan_id,
            e.build_id,
            e.tester_id,
            e.execution_ts
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
            WHERE 
                (p_start_date IS NULL OR execution_ts >= p_start_date)
                AND (p_end_date IS NULL OR execution_ts <= p_end_date)
                AND (p_tester_id IS NULL OR tester_id = p_tester_id)
                AND (p_test_plan_id IS NULL OR testplan_id = p_test_plan_id)
                AND (p_build_id IS NULL OR build_id = p_build_id)
            GROUP BY 
                tcversion_id, build_id, testplan_id
        ) latest ON e.tcversion_id = latest.tcversion_id 
          AND e.build_id = latest.build_id 
          AND e.testplan_id = latest.testplan_id 
          AND e.execution_ts = latest.latest_exec_ts
        WHERE 
            (p_start_date IS NULL OR e.execution_ts >= p_start_date)
            AND (p_end_date IS NULL OR e.execution_ts <= p_end_date)
            AND (p_tester_id IS NULL OR e.tester_id = p_tester_id)
            AND (p_test_plan_id IS NULL OR e.testplan_id = p_test_plan_id)
            AND (p_build_id IS NULL OR e.build_id = p_build_id)
    ) e ON tc.tcversion_id = e.tcversion_id
    WHERE
        (p_project_id IS NULL OR EXISTS (
            SELECT 1 FROM testplans tp 
            WHERE tp.id = e.testplan_id AND tp.testproject_id = p_project_id
        ));
    
    -- Create the path metrics by aggregating the executions by test path
    CREATE TEMPORARY TABLE IF NOT EXISTS temp_path_metrics AS
    SELECT 
        COALESCE(nhp.full_path, parent_nhp.full_path) AS test_path,
        COUNT(DISTINCT le.tc_id) as testcase_count,
        SUM(CASE WHEN le.status = 'p' THEN 1 ELSE 0 END) as passed_count,
        SUM(CASE WHEN le.status = 'f' THEN 1 ELSE 0 END) as failed_count,
        SUM(CASE WHEN le.status = 'b' THEN 1 ELSE 0 END) as blocked_count,
        SUM(CASE WHEN le.status IS NULL OR le.status NOT IN ('p', 'f', 'b') THEN 1 ELSE 0 END) as not_run_count
    FROM 
        temp_latest_executions le
    JOIN 
        nodes_hierarchy suite ON suite.id = le.suite_id
    -- Find path for each test suite directly using suite ID rather than parent
    LEFT JOIN 
        node_hierarchy_paths_v2 nhp ON nhp.node_id = suite.id
    -- Fall back to parent path if suite itself doesn't have a path entry
    LEFT JOIN 
        node_hierarchy_paths_v2 parent_nhp ON parent_nhp.node_id = suite.parent_id AND nhp.node_id IS NULL
    WHERE
        (p_path_filter IS NULL OR p_path_filter = '' 
         OR COALESCE(nhp.full_path, parent_nhp.full_path) LIKE CONCAT('%', p_path_filter, '%'))
    GROUP BY 
        COALESCE(nhp.full_path, parent_nhp.full_path);

    -- Calculate final metrics including rates
    SELECT 
        test_path,
        testcase_count,
        passed_count,
        failed_count,
        blocked_count,
        not_run_count,
        -- Handle division by zero cases
        CASE 
            WHEN testcase_count = 0 THEN 0 
            ELSE ROUND((passed_count / testcase_count) * 100, 2) 
        END AS pass_rate,
        CASE 
            WHEN testcase_count = 0 THEN 0 
            ELSE ROUND((failed_count / testcase_count) * 100, 2) 
        END AS fail_rate,
        CASE 
            WHEN testcase_count = 0 THEN 0 
            ELSE ROUND((blocked_count / testcase_count) * 100, 2) 
        END AS block_rate,
        -- Ensure the pending rate makes all rates sum to 100%
        CASE 
            WHEN testcase_count = 0 THEN 0 
            ELSE 
                GREATEST(0, -- Ensure non-negative
                    ROUND(100 - 
                        ROUND((passed_count / testcase_count) * 100, 2) - 
                        ROUND((failed_count / testcase_count) * 100, 2) - 
                        ROUND((blocked_count / testcase_count) * 100, 2), 2)
                )
        END AS pending_rate
    FROM 
        temp_path_metrics
    WHERE 
        testcase_count > 0
    ORDER BY 
        test_path;
    
    -- Clean up temporary tables
    DROP TEMPORARY TABLE IF EXISTS temp_testcases;
    DROP TEMPORARY TABLE IF EXISTS temp_latest_executions;
    DROP TEMPORARY TABLE IF EXISTS temp_path_metrics;
END //

DELIMITER ;

-- Example usage:
-- CALL query_test_path_metrics('2025-01-01 00:00:00', '2025-07-09 23:59:59', NULL, 1, NULL, NULL, NULL);
-- Parameters: start_date, end_date, tester_id, project_id, test_plan_id, build_id, path_filter
