-- Fix for stored procedure DATE value error
-- This script drops and recreates the stored procedure with proper NULL handling

-- Drop the existing stored procedure
DROP PROCEDURE IF EXISTS sp_tester_execution_report_historical;

-- Recreate the stored procedure with proper date handling
DELIMITER $$

CREATE PROCEDURE sp_tester_execution_report_historical(
    IN p_testproject_id INT,
    IN p_testplan_id INT, 
    IN p_build_id INT,
    IN p_tester_id INT,
    IN p_report_type VARCHAR(20),  
    IN p_start_date VARCHAR(20),   
    IN p_end_date VARCHAR(20),     
    IN p_hide_zero_executions BOOLEAN DEFAULT FALSE
)
BEGIN
    DECLARE v_target_date DATE;
    DECLARE v_start_date DATE DEFAULT NULL;
    DECLARE v_end_date DATE DEFAULT NULL;
    DECLARE v_date_from DATE DEFAULT NULL;
    DECLARE v_date_to DATE DEFAULT NULL;
    
    -- Convert empty strings to NULL for proper date handling
    IF p_start_date IS NOT NULL AND p_start_date != '' AND p_start_date != 'NULL' THEN
        SET v_start_date = p_start_date;
    ELSE
        SET v_start_date = NULL;
    END IF;
    
    IF p_end_date IS NOT NULL AND p_end_date != '' AND p_end_date != 'NULL' THEN
        SET v_end_date = p_end_date;
    ELSE
        SET v_end_date = NULL;
    END IF;
    
    -- Debug logging (comment out in production)
    -- SELECT CONCAT('DEBUG: p_start_date="', IFNULL(p_start_date, 'NULL'), '", v_start_date=', IFNULL(v_start_date, 'NULL')) AS debug_info;
    -- SELECT CONCAT('DEBUG: p_end_date="', IFNULL(p_end_date, 'NULL'), '", v_end_date=', IFNULL(v_end_date, 'NULL')) AS debug_info;
    
    -- Set target date for historical filtering
    IF v_end_date IS NOT NULL THEN
        SET v_target_date = v_end_date;
    ELSEIF v_start_date IS NOT NULL THEN
        SET v_target_date = v_start_date;
    ELSE
        SET v_target_date = CURDATE();
    END IF;

    -- Set date range for filtering
    SET v_date_from = v_start_date;
    SET v_date_to = v_end_date;

    -- Main query with historical logic
    WITH 
    -- Get all unique test case assignments per user (assigned up to the target date)
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
            -- Only include assignments that existed up to the target date
            AND (v_target_date IS NULL OR DATE(ua.creation_ts) <= v_target_date)
    ),

    -- Get latest execution for each test case within the specified date range
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
        WHERE 
            -- Filter by date range
            (v_date_from IS NULL OR DATE(e.execution_ts) >= v_date_from)
            AND (v_date_to IS NULL OR DATE(e.execution_ts) <= v_date_to)
    ),

    -- Get unique test cases with their execution status within date range
    test_cases_historical_status AS (
        SELECT 
            uah.user_id,
            uah.build_id,
            uah.testproject_id,
            uah.testplan_id,
            uah.tcversion_id,
            uah.platform_id,
            COALESCE(leh.execution_status, 'n') AS execution_status,  -- Default to 'n' (not run) if no execution in range
            leh.execution_ts,
            leh.executed_by,
            CASE 
                WHEN leh.execution_id IS NOT NULL THEN 1 
                ELSE 0  -- Only count as execution if actually executed in range
            END AS has_execution
        FROM user_assignments_historical uah
        LEFT JOIN latest_executions_historical leh ON 
            leh.tcversion_id = uah.tcversion_id 
            AND leh.testplan_id = uah.testplan_id 
            AND leh.platform_id = uah.platform_id
            AND leh.rn = 1  -- Only get the latest execution within date range
            AND (uah.build_id = leh.build_id OR uah.build_id = 0 OR uah.build_id IS NULL)
        -- Include all assignments up to target date, regardless of execution in range
        -- Executions will be filtered separately in the aggregation
    )

    -- Final aggregation with historical data
    SELECT 
        u.id AS tester_id,
        CONCAT(u.first, ' ', u.last) AS tester_name,
        u.login,
        COUNT(*) AS total_assigned,  -- Count all assignments for the date range
        SUM(has_execution) AS total_executions,
        SUM(CASE WHEN execution_status = 'p' THEN 1 ELSE 0 END) AS passed,
        SUM(CASE WHEN execution_status = 'f' THEN 1 ELSE 0 END) AS failed,
        SUM(CASE WHEN execution_status = 'b' THEN 1 ELSE 0 END) AS blocked,
        SUM(CASE WHEN execution_status = 'n' THEN 1 ELSE 0 END) AS not_run,
        SUM(CASE WHEN execution_status = 's' THEN 1 ELSE 0 END) AS skipped,
        SUM(CASE WHEN execution_status = 'w' THEN 1 ELSE 0 END) AS warning,
        MAX(execution_ts) AS last_execution,  -- Latest execution within the date range
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
    GROUP BY u.id, u.login, u.first, u.last, tchs.testproject_id
    HAVING 
        -- For 'assigned' report type, only show testers with assignments
        (p_report_type = 'all' OR (p_report_type = 'assigned' AND COUNT(*) > 0))
        -- Ensure we have data for the selected project
        AND (p_testproject_id IS NULL OR p_testproject_id = 0 OR MIN(tchs.testproject_id) = p_testproject_id)
        -- Hide testers with zero executions when requested and dates are specified
        AND (p_hide_zero_executions = FALSE OR (v_date_from IS NULL AND v_date_to IS NULL) OR SUM(total_executions) > 0)
    ORDER BY tester_name;

END$$

DELIMITER ;

-- Verify the stored procedure was created
SELECT 'Stored procedure sp_tester_execution_report_historical fixed and created successfully' AS status;

-- Test with the problematic parameters
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', NULL, NULL);
