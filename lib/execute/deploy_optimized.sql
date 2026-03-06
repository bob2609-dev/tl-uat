-- Optimized version of the stored procedure for better performance

-- Drop the existing stored procedure
DROP PROCEDURE IF EXISTS sp_tester_execution_report_historical;

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

    -- Optimized query with simplified logic
    SELECT 
        u.id AS tester_id,
        CONCAT(u.first, ' ', u.last) AS tester_name,
        u.login,
        COUNT(DISTINCT ua.id) AS total_assigned,
        COUNT(DISTINCT e.id) AS total_executions,
        COUNT(DISTINCT CASE WHEN e.status = 'p' THEN e.id END) AS passed,
        COUNT(DISTINCT CASE WHEN e.status = 'f' THEN e.id END) AS failed,
        COUNT(DISTINCT CASE WHEN e.status = 'b' THEN e.id END) AS blocked,
        COUNT(DISTINCT CASE WHEN e.status = 'n' THEN e.id END) AS not_run,
        COUNT(DISTINCT CASE WHEN e.status = 's' THEN e.id END) AS skipped,
        COUNT(DISTINCT CASE WHEN e.status = 'w' THEN e.id END) AS warning,
        MAX(e.execution_ts) AS last_execution,
        MIN(tp.testproject_id) AS testproject_id,
        MIN(e.testplan_id) AS testplan_id,
        MIN(e.build_id) AS build_id
    FROM users u
    LEFT JOIN user_assignments ua ON 
        ua.user_id = u.id 
        AND ua.type IN (1, 2) 
        AND ua.status = 1
        AND (v_target_date IS NULL OR DATE(ua.creation_ts) <= v_target_date)
    LEFT JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
    LEFT JOIN testplans tp ON tptc.testplan_id = tp.id
    LEFT JOIN executions e ON 
        e.tester_id = u.id 
        AND (v_date_from IS NULL OR e.execution_ts >= v_date_from)
        AND (v_date_to IS NULL OR e.execution_ts < v_date_to)
        AND (p_testproject_id IS NULL OR tp.testproject_id = p_testproject_id)
        AND (p_testplan_id IS NULL OR e.testplan_id = p_testplan_id)
        AND (p_build_id IS NULL OR e.build_id = p_build_id)
    WHERE u.active = 1
        AND (p_tester_id IS NULL OR p_tester_id = 0 OR u.id = p_tester_id)
        -- Ensure we have data for the selected project (either from assignments or executions)
        AND (p_testproject_id IS NULL OR p_testproject_id = 0 OR tp.testproject_id = p_testproject_id)
    GROUP BY u.id, u.login, u.first, u.last
    HAVING 
        -- For 'assigned' report type, show testers with assignments OR executions
        (p_report_type = 'all' OR (p_report_type = 'assigned' AND (COUNT(DISTINCT ua.id) > 0 OR COUNT(DISTINCT e.id) > 0)))
        -- Ensure we have data for the selected project
        AND (p_testproject_id IS NULL OR p_testproject_id = 0 OR MIN(tp.testproject_id) = p_testproject_id)
        -- Hide testers with zero executions when requested and dates are specified
        AND (p_hide_zero_executions = FALSE 
             OR (v_date_from IS NULL AND v_date_to IS NULL) 
             OR COUNT(DISTINCT e.id) > 0)
    ORDER BY tester_name;

END$$

DELIMITER ;

-- Verify the stored procedure was created
SELECT 'Optimized stored procedure sp_tester_execution_report_historical deployed successfully' AS status;
