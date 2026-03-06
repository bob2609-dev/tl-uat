-- Step-by-step approach to avoid the performance bottleneck

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
    DECLARE v_start_date DATE DEFAULT NULL;
    DECLARE v_end_date DATE DEFAULT NULL;
    
    -- Convert dates
    IF p_start_date IS NOT NULL AND p_start_date != '' THEN
        SET v_start_date = p_start_date;
    END IF;
    
    IF p_end_date IS NOT NULL AND p_end_date != '' THEN
        SET v_end_date = DATE_ADD(p_end_date, INTERVAL 1 DAY);
    END IF;
    
    -- Step 1: Get basic user info first (fast)
    SELECT 
        u.id AS tester_id,
        CONCAT(u.first, ' ', u.last) AS tester_name,
        u.login,
        0 AS total_assigned,
        0 AS total_executions,
        0 AS passed,
        0 AS failed,
        0 AS blocked,
        0 AS not_run,
        0 AS skipped,
        0 AS warning,
        NULL AS last_execution,
        NULL AS testproject_id,
        NULL AS testplan_id,
        NULL AS build_id
    FROM users u
    WHERE u.active = 1
        AND (p_tester_id IS NULL OR p_tester_id = 0 OR u.id = p_tester_id)
    ORDER BY u.first, u.last;
    
END$$

DELIMITER ;

SELECT 'Stepwise stored procedure deployed - basic user info only' AS status;
