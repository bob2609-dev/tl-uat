-- Stored procedure to refresh the node_hierarchy_paths_v2 table
-- This procedure will drop and recreate the table, then populate it with the current hierarchy data
USE testlink_db;
DELIMITER //

DROP PROCEDURE IF EXISTS refresh_node_hierarchy_paths_v2//

CREATE PROCEDURE refresh_node_hierarchy_paths_v2()
BEGIN
    -- Drop the existing table if it exists
    DROP TABLE IF EXISTS node_hierarchy_paths_v2;
    
    -- Create the table structure
    CREATE TABLE node_hierarchy_paths_v2 (
        node_id INT NOT NULL,
        level1_id INT NULL,
        level1_name VARCHAR(100) NULL,
        level2_id INT NULL,
        level2_name VARCHAR(100) NULL,
        level3_id INT NULL,
        level3_name VARCHAR(100) NULL,
        level4_id INT NULL,
        level4_name VARCHAR(100) NULL,
        level5_id INT NULL,
        level5_name VARCHAR(100) NULL,
        full_path VARCHAR(500) NULL,
        PRIMARY KEY (node_id),
        INDEX idx_l1 (level1_id),
        INDEX idx_l2 (level2_id),
        INDEX idx_l3 (level3_id),
        INDEX idx_l4 (level4_id),
        INDEX idx_l5 (level5_id)
    );
    
    -- Populate the hierarchy path table with a single execution of the recursive CTE
    -- This avoids recalculating the paths on every query
    INSERT INTO node_hierarchy_paths_v2 (node_id, level1_id, level1_name, level2_id, level2_name, 
                                      level3_id, level3_name, level4_id, level4_name, 
                                      level5_id, level5_name, full_path)
    WITH RECURSIVE node_hierarchy_path AS (
        -- Anchor member: starting points (nodes without parent or with parent_id=0)
        SELECT 
            id, 
            name, 
            parent_id,
            name AS path,
            1 AS level,
            CAST(id AS CHAR(50)) AS id_path,
            node_type_id
        FROM nodes_hierarchy 
        WHERE (parent_id IS NULL OR parent_id = 0)
        -- Only include folder nodes (node_type_id 1 and 2) and exclude test cases (node_type_id 3 and 4)
        AND node_type_id IN (1, 2)
        
        UNION ALL
        
        -- Recursive member: nodes with parents
        SELECT 
            nh.id, 
            nh.name, 
            nh.parent_id,
            CONCAT(nhp.path, ' > ', nh.name) AS path,
            nhp.level + 1 AS level,
            CONCAT(nhp.id_path, ',', nh.id) AS id_path,
            nh.node_type_id
        FROM 
            nodes_hierarchy nh
            JOIN node_hierarchy_path nhp ON nh.parent_id = nhp.id
        -- Only include folder nodes (node_type_id 1 and 2) and exclude test cases (node_type_id 3 and 4)
        WHERE nh.node_type_id IN (1, 2)
    )
    SELECT 
        n.id AS node_id,
        -- Extract each level based on position in the path
        -- Store both IDs and names to avoid joins later
        SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 1), ',', -1) AS level1_id,
        (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 1), ',', -1)) AS level1_name,
        
        -- Level 2 (only if we have enough levels)
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 1
             THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 2), ',', -1) ELSE NULL END AS level2_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 1
             THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 2), ',', -1)) ELSE NULL END AS level2_name,
        
        -- Level 3 (only if we have enough levels)
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 2
             THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 3), ',', -1) ELSE NULL END AS level3_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 2
             THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 3), ',', -1)) ELSE NULL END AS level3_name,
        
        -- Level 4 (only if we have enough levels)
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 3
             THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 4), ',', -1) ELSE NULL END AS level4_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 3
             THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 4), ',', -1)) ELSE NULL END AS level4_name,
        
        -- Level 5 (only if we have enough levels)
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 4
             THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 5), ',', -1) ELSE NULL END AS level5_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 4
             THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 5), ',', -1)) ELSE NULL END AS level5_name,
        
        -- Store the full path as well
        path AS full_path
    FROM node_hierarchy_path n
    -- Make sure we include all folder nodes, even if they're at the leaf level
    WHERE n.node_type_id IN (1, 2);
    
    -- Create additional indexes to speed up common filter patterns
    CREATE INDEX idx_hierarchy_path_fullpath ON node_hierarchy_paths_v2 (full_path(255));
    
    -- Return the count of records inserted
    SELECT COUNT(*) AS records_inserted FROM node_hierarchy_paths_v2;
END//

DELIMITER ;

-- Example of how to call the stored procedure:
-- CALL refresh_node_hierarchy_paths_v2();

-- Example of how to call the stored procedure:
CALL refresh_node_hierarchy_paths_v2();
