-- Original backup of stored procedure BEFORE hide_zero_executions parameter was added
-- This is the version that was working for date filtering

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
    IN p_end_date VARCHAR(20)
)
BEGIN
    DECLARE v_target_date DATE;
    DECLARE v_start_date DATE DEFAULT NULL;
    DECLARE v_end_date DATE DEFAULT NULL;
    DECLARE v_date_from DATE DEFAULT NULL;
    DECLARE v_date_to DATE DEFAULT NULL;
    
    -- Convert empty strings to NULL for proper date handling
    IF p_start_date IS NOT NULL AND p_start_date != '' AND p_start_date != 'NULL' THEN
        SET v_start_date = STR_TO_DATE(p_start_date, '%Y-%m-%d');
    END IF;
    
    IF p_end_date IS NOT NULL AND p_end_date != '' AND p_end_date != 'NULL' THEN
        SET v_end_date = STR_TO_DATE(p_end_date, '%Y-%m-%d');
    END IF;
    
    -- Set date filtering variables based on parameters
    IF v_start_date IS NOT NULL AND v_end_date IS NOT NULL THEN
        -- Date range mode: filter executions between start and end dates
        SET v_date_from = v_start_date;
        SET v_date_to = v_end_date;
        SET v_target_date = v_end_date;  -- For assignment filtering
    ELSEIF v_end_date IS NOT NULL THEN
        -- Point-in-time mode: filter assignments up to target date, executions up to target date
        SET v_target_date = v_end_date;
        SET v_date_from = NULL;
        SET v_date_to = v_end_date;
    ELSE
        -- Current mode: no date filtering
        SET v_target_date = NULL;
        SET v_date_from = NULL;
        SET v_date_to = NULL;
    END IF;
    
    -- CTE for user assignments filtered by creation date up to target date
    WITH user_assignments_historical AS (
        SELECT 
            ua.id,
            ua.user_id,
            ua.feature_id,
            ua.type,
            ua.status,
            ua.creation_ts,
            tptc.testplan_id,
            tp.testproject_id
        FROM user_assignments ua
        JOIN testplan_tcversions tptc ON ua.feature_id = tptc.id
        JOIN testplans tp ON tptc.testplan_id = tp.id
        WHERE ua.type IN (1, 2)  -- Test case assignments
        AND (v_target_date IS NULL OR ua.creation_ts <= v_target_date)
    ),
    
    -- CTE for latest executions filtered by execution date range
    latest_executions_historical AS (
        SELECT 
            e.id,
            e.testplan_id,
            e.tcversion_id,
            e.platform_id,
            e.build_id,
            e.tester_id,
            e.execution_ts,
            e.status,
            e.execution_type,
            e.notes,
            e.duration,
            -- Use ROW_NUMBER() to get the latest execution per test case/platform/build combination
            ROW_NUMBER() OVER (
                PARTITION BY e.tcversion_id, e.platform_id, e.build_id 
                ORDER BY e.execution_ts DESC
            ) AS rn
        FROM executions e
        WHERE (v_date_from IS NULL OR v_date_to IS NULL OR (e.execution_ts >= v_date_from AND e.execution_ts <= v_date_to))
    ),
    
    -- CTE for test cases with their historical status
    test_cases_historical_status AS (
        SELECT 
            tptc.id as assignment_id,
            tptc.testplan_id,
            tptc.tcversion_id,
            tptc.platform_id,
            tptc.build_id,
            tptc.node_order,
            tc.version,
            tc.author_id,
            tc.creation_ts as tc_creation_ts,
            tc.updater_id,
            tc.modification_ts as tc_modification_ts,
            tc.summary,
            tc.preconditions,
            tc重要性,
            tc.urgency,
            ua.user_id as assigned_user_id,
            ua.creation_ts as assignment_creation_ts,
            le.id as execution_id,
            le.tester_id as execution_user_id,
            le.execution_ts,
            le.status as execution_status,
            le.execution_type,
            le.notes as execution_notes,
            le.duration as execution_duration,
            COALESCE(le.status, 'n') as final_status,
            COALESCE(le.execution_ts, ua.creation_ts) as last_execution
        FROM testplan_tcversions tptc
        JOIN tcversions tc ON tptc.tcversion_id = tc.id
        LEFT JOIN user_assignments_historical ua ON tptc.id = ua.feature_id
        LEFT JOIN latest_executions_historical le ON (
            tptc.tcversion_id = le.tcversion_id 
            AND tptc.platform_id = le.platform_id
            AND tptc.build_id = le.build_id
            AND le.rn = 1  -- Only get the latest execution
        )
    )
    
    -- Final SELECT with aggregation and filtering
    SELECT 
        u.id AS user_id,
        u.login AS user_login,
        CONCAT(u.first, ' ', u.last) AS tester_name,
        COUNT(*) AS total_assigned,
        SUM(CASE WHEN final_status IN ('p', 'f', 'b', 's', 'w') THEN 1 ELSE 0 END) AS total_executions,
        SUM(CASE WHEN final_status = 'p' THEN 1 ELSE 0 END) AS passed,
        SUM(CASE WHEN final_status = 'f' THEN 1 ELSE 0 END) AS failed,
        SUM(CASE WHEN final_status = 'b' THEN 1 ELSE 0 END) AS blocked,
        SUM(CASE WHEN final_status = 'n' THEN 1 ELSE 0 END) AS not_run,
        SUM(CASE WHEN final_status = 's' THEN 1 ELSE 0 END) AS skipped,
        SUM(CASE WHEN final_status = 'w' THEN 1 ELSE 0 END) AS warning,
        MAX(last_execution) AS last_execution,  -- Latest execution within the date range
        -- Add filtering columns
        MIN(tchs.testproject_id) AS testproject_id,
        MIN(tchs.testplan_id) AS testplan_id,
        MIN(tchs.build_id) AS build_id
    FROM users u
    JOIN test_cases_historical_status tchs ON u.id = tchs.assigned_user_id
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
    ORDER BY tester_name;

END$$

DELIMITER ;

-- Verify the stored procedure was created
SELECT 'Original stored procedure sp_tester_execution_report_historical (without hide_zero_executions) backed up successfully' AS status;
