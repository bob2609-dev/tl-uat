-- Deployment script for Historical Tester Execution Report
-- This script creates the stored procedures and sets up the database

-- Step 1: Drop existing stored procedures if they exist
DROP PROCEDURE IF EXISTS sp_tester_execution_report_historical;
DROP PROCEDURE IF EXISTS sp_tester_execution_report_historical_summary;

-- Step 2: Create the main stored procedure
DELIMITER $$

CREATE PROCEDURE sp_tester_execution_report_historical(
    IN p_testproject_id INT,
    IN p_testplan_id INT, 
    IN p_build_id INT,
    IN p_tester_id INT,
    IN p_report_type VARCHAR(20),  -- 'all' or 'assigned'
    IN p_start_date DATE,          -- Historical date filter
    IN p_end_date DATE            -- End date for range filtering
)
BEGIN
    DECLARE v_target_date DATE;
    
    -- Convert empty strings to NULL for proper date handling
    IF p_start_date = '' THEN
        SET p_start_date = NULL;
    END IF;
    
    IF p_end_date = '' THEN
        SET p_end_date = NULL;
    END IF;
    
    -- Set target date for historical filtering
    IF p_end_date IS NOT NULL THEN
        SET v_target_date = p_end_date;
    ELSEIF p_start_date IS NOT NULL THEN
        SET v_target_date = p_start_date;
    ELSE
        SET v_target_date = CURDATE();
    END IF;

    -- Main query with historical logic
    WITH 
    -- Get all unique test case assignments per user as of target date
    user_assignments_historical AS (
        SELECT 
            ua.user_id,
            ua.feature_id AS testplan_tcversion_id,
            ua.build_id,
            tptc.tcversion_id,
            tptc.testplan_id,
            tptc.platform_id,
            tp.testproject_id
        FROM user_assignments ua
        JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
        JOIN testplans tp ON tptc.testplan_id = tp.id
        WHERE ua.type IN (1, 2) 
            AND ua.status = 1
            AND tp.testproject_id IS NOT NULL
            -- Only include assignments that existed as of target date
            AND (ua.creation_date IS NULL OR ua.creation_date <= v_target_date)
    ),

    -- Get latest execution for each test case as of target date
    latest_executions_historical AS (
        SELECT 
            e.tcversion_id,
            e.testplan_id,
            e.platform_id,
            e.build_id,
            e.id AS execution_id,
            e.status AS execution_status,
            e.execution_ts,
            e.tester_id AS executed_by,
            ROW_NUMBER() OVER (
                PARTITION BY e.tcversion_id, e.testplan_id, e.platform_id 
                ORDER BY e.execution_ts DESC, e.id DESC
            ) AS rn
        FROM executions e
        WHERE e.execution_ts <= CONCAT(v_target_date, ' 23:59:59')  -- Only executions up to target date
    ),

    -- Get unique test cases with their historical execution status
    test_cases_historical_status AS (
        SELECT 
            uah.user_id,
            uah.build_id,
            uah.testproject_id,
            uah.testplan_id,
            uah.tcversion_id,
            uah.platform_id,
            COALESCE(leh.execution_status, 'n') AS execution_status,  -- Default to 'n' (not run) if no execution
            leh.execution_ts,
            leh.executed_by,
            CASE 
                WHEN leh.execution_id IS NOT NULL THEN 1 
                ELSE 1  -- Count all assignments, executed or not
            END AS has_execution,
            -- Check if assignment existed as of target date
            CASE 
                WHEN uah.creation_date IS NULL OR uah.creation_date <= v_target_date THEN 1 
                ELSE 0 
            END AS assignment_existed
        FROM user_assignments_historical uah
        LEFT JOIN latest_executions_historical leh ON 
            leh.tcversion_id = uah.tcversion_id 
            AND leh.testplan_id = uah.testplan_id 
            AND leh.platform_id = uah.platform_id
            AND leh.rn = 1  -- Only get the latest execution as of target date
            AND (uah.build_id = leh.build_id OR uah.build_id = 0 OR uah.build_id IS NULL)
        WHERE assignment_existed = 1  -- Only include assignments that existed as of target date
    )

    -- Final aggregation with historical data
    SELECT 
        u.id AS tester_id,
        CONCAT(u.first, ' ', u.last) AS tester_name,
        u.login,
        COUNT(*) AS total_assigned,  -- Count each assignment only once as of target date
        SUM(has_execution) AS total_executions,
        SUM(CASE WHEN execution_status = 'p' THEN 1 ELSE 0 END) AS passed,
        SUM(CASE WHEN execution_status = 'f' THEN 1 ELSE 0 END) AS failed,
        SUM(CASE WHEN execution_status = 'b' THEN 1 ELSE 0 END) AS blocked,
        SUM(CASE WHEN execution_status = 'n' THEN 1 ELSE 0 END) AS not_run,
        SUM(CASE WHEN execution_status = 's' THEN 1 ELSE 0 END) AS skipped,
        SUM(CASE WHEN execution_status = 'w' THEN 1 ELSE 0 END) AS warning,
        MAX(execution_ts) AS last_execution,  -- Latest execution as of target date
        -- Add filtering columns
        MIN(tchs.testproject_id) AS testproject_id,
        MIN(tchs.testplan_id) AS testplan_id,
        MIN(tchs.build_id) AS build_id
    FROM users u
    JOIN test_cases_historical_status tchs ON u.id = tchs.user_id
    WHERE u.active = 1
        AND (p_testproject_id IS NULL OR p_testproject_id = 0 OR tchs.testproject_id = p_testproject_id)
        AND (p_testplan_id IS NULL OR p_testplan_id = 0 OR tchs.testplan_id = p_testplan_id)
        AND (p_build_id IS NULL OR p_build_id = 0 OR tchs.build_id = p_build_id)
        AND (p_tester_id IS NULL OR p_tester_id = 0 OR u.id = p_tester_id)
        -- Apply report type filter
        AND (
            p_report_type = 'all' 
            OR (p_report_type = 'assigned' AND COUNT(*) > 0)
        )
        -- Apply date range filtering if start date is specified
        AND (
            p_start_date IS NULL OR p_start_date = '' 
            OR (
                (p_start_date IS NOT NULL AND p_start_date != '') 
                AND tchs.testproject_id IS NOT NULL
            )
        )
    GROUP BY u.id, u.login, u.first, u.last, tchs.testproject_id
    HAVING 
        -- For 'assigned' report type, only show testers with assignments
        (p_report_type = 'all' OR (p_report_type = 'assigned' AND COUNT(*) > 0))
        -- Ensure we have data for the selected project
        AND (p_testproject_id IS NULL OR p_testproject_id = 0 OR MIN(tchs.testproject_id) = p_testproject_id)
    ORDER BY tester_name;

END$$

DELIMITER ;

-- Step 3: Verify stored procedure creation
SELECT 'Stored procedure sp_tester_execution_report_historical created successfully' AS status;

-- Step 4: Test the stored procedure with basic parameters
-- Uncomment the following lines to test:
-- CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'assigned', NULL, NULL);

-- Step 5: Grant execute permissions (uncomment and adjust as needed)
-- GRANT EXECUTE ON PROCEDURE sp_tester_execution_report_historical TO 'testlink_user'@'%';

SELECT 'Deployment completed successfully' AS deployment_status;
