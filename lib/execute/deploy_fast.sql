-- Fast version to fix 45+ second execution time

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
    
    -- Simple, fast query
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
    LEFT JOIN user_assignments ua ON ua.user_id = u.id AND ua.type IN (1, 2) AND ua.status = 1
    LEFT JOIN executions e ON e.tester_id = u.id 
        AND (v_start_date IS NULL OR e.execution_ts >= v_start_date)
        AND (v_end_date IS NULL OR e.execution_ts < v_end_date)
    LEFT JOIN testplans tp ON tp.id = e.testplan_id
    WHERE u.active = 1
        AND (p_tester_id IS NULL OR p_tester_id = 0 OR u.id = p_tester_id)
        AND (p_testproject_id IS NULL OR p_testproject_id = 0 OR tp.testproject_id = p_testproject_id)
        AND (p_testplan_id IS NULL OR p_testplan_id = 0 OR e.testplan_id = p_testplan_id)
        AND (p_build_id IS NULL OR p_build_id = 0 OR e.build_id = p_build_id)
    GROUP BY u.id, u.login, u.first, u.last
    HAVING 
        (p_report_type = 'all' OR (p_report_type = 'assigned' AND (COUNT(DISTINCT ua.id) > 0 OR COUNT(DISTINCT e.id) > 0)))
        AND (p_hide_zero_executions = FALSE OR (v_start_date IS NULL AND v_end_date IS NULL) OR COUNT(DISTINCT e.id) > 0)
    ORDER BY tester_name;

END$$

DELIMITER ;

SELECT 'Fast stored procedure deployed' AS status;
