-- Run this in your TestLink database:
DELIMITER $$
CREATE PROCEDURE IF NOT EXISTS sp_update_testcase_priority(
    IN p_field_id INT,
    IN p_node_id INT, 
    IN p_priority VARCHAR(50),
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    
    IF p_field_id IS NULL OR p_field_id <= 0 THEN
        SET p_success = FALSE;
        SET p_message = 'Invalid field ID';
    ELSEIF p_node_id IS NULL OR p_node_id <= 0 THEN
        SET p_success = FALSE;
        SET p_message = 'Invalid node ID';
    ELSEIF p_priority IS NULL OR p_priority = '' THEN
        SET p_success = FALSE;
        SET p_message = 'Priority value is required';
    ELSE
        SELECT COUNT(*) INTO v_count
        FROM cfield_design_values 
        WHERE field_id = p_field_id AND node_id = p_node_id;
        
        IF v_count > 0 THEN
            UPDATE cfield_design_values 
            SET value = p_priority
            WHERE field_id = p_field_id AND node_id = p_node_id;
            SET p_success = TRUE;
            SET p_message = 'Priority updated successfully';
        ELSE
            INSERT INTO cfield_design_values (field_id, node_id, value)
            VALUES (p_field_id, p_node_id, p_priority);
            SET p_success = TRUE;
            SET p_message = 'Priority inserted successfully';
        END IF;
    END IF;
END$$
DELIMITER ;