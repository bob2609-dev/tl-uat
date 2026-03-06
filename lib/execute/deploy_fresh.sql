-- Fresh deployment script to ensure the stored procedure is properly updated

-- Drop the existing stored procedure completely
DROP PROCEDURE IF EXISTS sp_tester_execution_report_historical;

-- Recreate the stored procedure with all fixes
DELIMITER $$

CREATE PROCEDURE sp_tester_execution_report_historical(
    IN p_testproject_id INT,
    IN p_testplan_id INT, 
    IN p_build_id INT,
    IN p_tester_id INT,
    IN p_report_type VARCHAR(20),  
    IN p_start_date VARCHAR(20),   
    IN p_end_date VARCHAR(20),     
    IN p_hide_zero_executions BOOLEAN
)
BEGIN
    DECLARE v_target_date DATE;
    DECLARE v_start_date DATE DEFAULT NULL;
    DECLARE v_end_date DATE DEFAULT NULL;
    DECLARE v_date_from DATE DEFAULT NULL;
    DECLARE v_date_to DATE DEFAULT NULL;
    
    -- Set default value for hide_zero_executions if NULL
    IF p_hide_zero_executions IS NULL THEN
        SET p_hide_zero_executions = FALSE;
    END IF;
    
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
    
    -- Set date filtering variables based on parameters
    IF v_start_date IS NOT NULL AND v_end_date IS NOT NULL THEN
        -- Date range mode: filter executions between start and end dates
        SET v_date_from = v_start_date;
        SET v_date_to = DATE_ADD(v_end_date, INTERVAL 1 DAY);  -- Include full end day (up to 23:59:59)
        SET v_target_date = v_end_date;  -- For assignment filtering
    ELSEIF v_end_date IS NOT NULL THEN
        -- Point-in-time mode: filter assignments up to target date, executions up to target date
        SET v_target_date = v_end_date;
    ELSEIF v_start_date IS NOT NULL THEN
        SET v_target_date = v_start_date;
    ELSE
        SET v_target_date = CURDATE();
    END IF;

    -- Set date range for filtering (only if not already set in the IF above)
    IF v_date_from IS NULL THEN
        SET v_date_from = v_start_date;
    END IF;
    
    IF v_date_to IS NULL THEN
        SET v_date_to = v_end_date;
    END IF;

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

    -- Get executions by users who were NOT assigned to the test case (users with special rights)
    executions_without_assignments AS (
        SELECT DISTINCT
            e.tester_id as user_id,
            e.tcversion_id,
            e.testplan_id,
            e.platform_id,
            e.build_id,
            tp.testproject_id
        FROM executions e
        JOIN testplans tp ON e.testplan_id = tp.id
        LEFT JOIN user_assignments ua ON 
            ua.user_id = e.tester_id 
            AND ua.feature_id IN (
                SELECT id FROM testplan_tcversions 
                WHERE tcversion_id = e.tcversion_id 
                AND testplan_id = e.testplan_id
            )
            AND ua.type IN (1, 2) 
            AND ua.status = 1
        WHERE 
            -- Filter by date range
            (v_date_from IS NULL OR e.execution_ts >= v_date_from)
            AND (v_date_to IS NULL OR e.execution_ts < v_date_to)
            -- Only include executions where user was NOT assigned
            AND ua.id IS NULL
            AND tp.testproject_id IS NOT NULL
    ),

    -- Get latest execution for each test case within the specified date range
    latest_executions_historical AS (
        SELECT 
            e.id,
            e.testplan_id,
            e.tcversion_id,
            e.platform_id,
            e.build_id,
            e.tester_id,
            e.execution_ts,
            e.status AS execution_status,  -- Rename to execution_status for consistency
            e.execution_type,
            e.notes,
            -- Use ROW_NUMBER() to get the latest execution per test case/platform/build combination
            ROW_NUMBER() OVER (
                PARTITION BY e.tcversion_id, e.platform_id, e.build_id 
                ORDER BY e.execution_ts DESC
            ) AS rn
        FROM executions e
        WHERE 
            -- Filter by date range
            (v_date_from IS NULL OR e.execution_ts >= v_date_from)
            AND (v_date_to IS NULL OR e.execution_ts < v_date_to)
    ),

    -- Get unique test cases with their execution status within date range
    test_cases_historical_status AS (
        SELECT 
            uah.user_id,
            uah.testplan_tcversion_id,
            uah.tcversion_id,
            uah.testplan_id,
            uah.platform_id,
            uah.build_id,
            uah.testproject_id,
            leh.execution_status,
            leh.execution_ts,
            leh.tester_id as executed_by,
            CASE 
                WHEN leh.id IS NOT NULL THEN 1 
                ELSE 0  -- Only count as execution if actually executed in range
            END AS has_execution
        FROM user_assignments_historical uah
        LEFT JOIN latest_executions_historical leh ON 
            leh.tcversion_id = uah.tcversion_id 
            AND leh.testplan_id = uah.testplan_id 
            AND leh.platform_id = uah.platform_id
            AND leh.rn = 1  -- Only get the latest execution within date range
            AND (uah.build_id = leh.build_id OR uah.build_id = 0 OR uah.build_id IS NULL)
        
        UNION ALL
        
        -- Include users who executed without assignments
        SELECT 
            ewa.user_id,
            NULL as testplan_tcversion_id,  -- No assignment record
            ewa.tcversion_id,
            ewa.testplan_id,
            ewa.platform_id,
            ewa.build_id,
            ewa.testproject_id,
            leh.execution_status,
            leh.execution_ts,
            leh.tester_id as executed_by,
            1 as has_execution  -- These are actual executions
        FROM executions_without_assignments ewa
        JOIN latest_executions_historical leh ON 
            leh.tcversion_id = ewa.tcversion_id 
            AND leh.testplan_id = ewa.testplan_id 
            AND leh.platform_id = ewa.platform_id
            AND leh.tester_id = ewa.user_id
            AND leh.rn = 1
    )

    -- Final aggregation with historical data
    SELECT 
        u.id AS tester_id,
        CONCAT(u.first, ' ', u.last) AS tester_name,
        u.login,
        COUNT(CASE WHEN tchs.testplan_tcversion_id IS NOT NULL THEN 1 ELSE NULL END) AS total_assigned,  -- Count only actual assignments
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
        -- For 'assigned' report type, show testers with assignments OR executions
        (p_report_type = 'all' OR (p_report_type = 'assigned' AND (COUNT(CASE WHEN tchs.testplan_tcversion_id IS NOT NULL THEN 1 ELSE NULL END) > 0 OR total_executions > 0)))
        -- Ensure we have data for the selected project
        AND (p_testproject_id IS NULL OR p_testproject_id = 0 OR MIN(tchs.testproject_id) = p_testproject_id)
        -- Hide testers with zero executions when requested and dates are specified
        AND (p_hide_zero_executions = FALSE 
             OR (v_date_from IS NULL AND v_date_to IS NULL) 
             OR total_executions > 0)
    ORDER BY tester_name;

END$$

DELIMITER ;

-- Verify the stored procedure was created
SELECT 'Stored procedure sp_tester_execution_report_historical deployed successfully' AS status;
