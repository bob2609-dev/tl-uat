delimiter $$
use testlink_db $$

drop procedure if exists refresh_node_hierarchy_paths_v4$$

CREATE  PROCEDURE if not exists `testlink_db`.`refresh_node_hierarchy_paths_v4`()
BEGIN
    
    DROP TABLE IF EXISTS node_hierarchy_paths_v4;
    
    CREATE TABLE node_hierarchy_paths_v4 (	
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
        level6_id INT NULL,
        level6_name VARCHAR(100) NULL,
        level7_id INT NULL,
        level7_name VARCHAR(100) NULL,
        level8_id INT NULL,
        level8_name VARCHAR(100) NULL,
        level9_id INT NULL,
        level9_name VARCHAR(100) NULL,
        level10_id INT NULL,
        level10_name VARCHAR(100) NULL,
        level11_id INT NULL,
        level11_name VARCHAR(100) NULL,
        level12_id INT NULL,
        level12_name VARCHAR(100) NULL,
        full_path TEXT NULL,
        PRIMARY KEY (node_id),
        INDEX idx_l1 (level1_id),
        INDEX idx_l2 (level2_id),
        INDEX idx_l3 (level3_id),
        INDEX idx_l4 (level4_id),
        INDEX idx_l5 (level5_id),
        INDEX idx_l6 (level6_id),
        INDEX idx_l7 (level7_id),
        INDEX idx_l8 (level8_id),
        INDEX idx_l9 (level9_id),
        INDEX idx_l10 (level10_id),
        INDEX idx_l11 (level11_id),
        INDEX idx_l12 (level12_id)
    );
    
    
    INSERT INTO node_hierarchy_paths_v4 (
        node_id, 
        level1_id, level1_name, 
        level2_id, level2_name, 
        level3_id, level3_name, 
        level4_id, level4_name, 
        level5_id, level5_name, 
        level6_id, level6_name, 
        level7_id, level7_name, 
        level8_id, level8_name, 
        level9_id, level9_name, 
        level10_id, level10_name, 
        level11_id, level11_name, 
        level12_id, level12_name, 
        full_path
    )
    WITH RECURSIVE node_hierarchy_path AS (
        SELECT 
            id, 
            name, 
            parent_id,
            CAST(name AS CHAR(1000)) AS path,
            1 AS level,
            CAST(id AS CHAR(1000)) AS id_path,
            node_type_id
        FROM nodes_hierarchy 
        WHERE (parent_id IS NULL OR parent_id = 0)
          AND node_type_id <= 2
        
        UNION ALL
        
        SELECT 
            nh.id, 
            nh.name, 
            nh.parent_id,
            CAST(CONCAT(nhp.path, ' > ', nh.name) AS CHAR(1000)) AS path,
            nhp.level + 1 AS level,
            CONCAT(nhp.id_path, ',', nh.id) AS id_path,
            nh.node_type_id
        FROM 
            nodes_hierarchy nh
            JOIN node_hierarchy_path nhp ON nh.parent_id = nhp.id
        WHERE nh.node_type_id <= 2
    )
    SELECT 
        n.id AS node_id,
        
        -- LEVEL 1
        SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 1), ',', -1) AS level1_id,
        (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 1), ',', -1)) AS level1_name,

        -- LEVEL 2
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 1
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 2), ',', -1) END AS level2_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 1
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 2), ',', -1)) END AS level2_name,

        -- LEVEL 3
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 2
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 3), ',', -1) END AS level3_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 2
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 3), ',', -1)) END AS level3_name,

        -- LEVEL 4
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 3
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 4), ',', -1) END AS level4_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 3
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 4), ',', -1)) END AS level4_name,

        -- LEVEL 5
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 4
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 5), ',', -1) END AS level5_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 4
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 5), ',', -1)) END AS level5_name,

        -- LEVEL 6
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 5
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 6), ',', -1) END AS level6_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 5
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 6), ',', -1)) END AS level6_name,

        -- LEVEL 7
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 6
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 7), ',', -1) END AS level7_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 6
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 7), ',', -1)) END AS level7_name,

        -- LEVEL 8
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 7
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 8), ',', -1) END AS level8_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 7
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 8), ',', -1)) END AS level8_name,

        -- LEVEL 9
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 8
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 9), ',', -1) END AS level9_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 8
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 9), ',', -1)) END AS level9_name,

        -- LEVEL 10
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 9
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 10), ',', -1) END AS level10_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 9
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 10), ',', -1)) END AS level10_name,

        -- LEVEL 11
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 10
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 11), ',', -1) END AS level11_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 10
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 11), ',', -1)) END AS level11_name,

        -- LEVEL 12
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 11
            THEN SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 12), ',', -1) END AS level12_id,
        CASE WHEN (LENGTH(id_path) - LENGTH(REPLACE(id_path, ',', ''))) >= 11
            THEN (SELECT name FROM nodes_hierarchy WHERE id = SUBSTRING_INDEX(SUBSTRING_INDEX(id_path, ',', 12), ',', -1)) END AS level12_name,
        
        path AS full_path
    FROM node_hierarchy_path n
    WHERE n.node_type_id <= 2;
    
    CREATE INDEX idx_hierarchy_path_fullpath ON node_hierarchy_paths_v4 (full_path(255));
    -- 
    -- SELECT COUNT(*) AS records_inserted FROM node_hierarchy_paths_v4;
END;
delimiter ;
call refresh_node_hierarchy_paths_v4();
