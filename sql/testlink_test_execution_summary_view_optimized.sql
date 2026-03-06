-- TestLink Test Execution Summary View (Optimized Version)
-- Created: 2025-07-03
-- Purpose: Creates an optimized view that provides hierarchical test execution reporting
--          with significantly improved performance over previous versions

-- First, create a materialized hierarchy path table that pre-computes all paths
-- This avoids expensive recursive CTEs at query time

DROP TABLE IF EXISTS node_hierarchy_paths;
CREATE TABLE node_hierarchy_paths (
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
INSERT INTO node_hierarchy_paths (node_id, level1_id, level1_name, level2_id, level2_name, 
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
        CAST(id AS CHAR(50)) AS id_path
    FROM nodes_hierarchy 
    WHERE parent_id IS NULL OR parent_id = 0
    
    UNION ALL
    
    -- Recursive member: nodes with parents
    SELECT 
        nh.id, 
        nh.name, 
        nh.parent_id,
        CONCAT(nhp.path, ' > ', nh.name) AS path,
        nhp.level + 1 AS level,
        CONCAT(nhp.id_path, ',', nh.id) AS id_path
    FROM 
        nodes_hierarchy nh
        JOIN node_hierarchy_path nhp ON nh.parent_id = nhp.id
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
FROM node_hierarchy_path n;

-- Create additional indexes to speed up common filter patterns
CREATE INDEX idx_hierarchy_path_fullpath ON node_hierarchy_paths (full_path(255));

-- Drop the view if it exists already
DROP VIEW IF EXISTS test_execution_hierarchy_summary_optimized;

-- Create the optimized execution summary view
-- This view avoids most of the expensive operations by using the precomputed hierarchy paths
CREATE OR REPLACE VIEW test_execution_hierarchy_summary_optimized AS
SELECT 
    -- Execution Details
    e.id AS execution_id,
    e.status AS execution_status,
    
    -- Add human-readable status name
    CASE e.status
        WHEN 'p' THEN 'passed'
        WHEN 'f' THEN 'failed'
        WHEN 'b' THEN 'blocked'
        WHEN 'n' THEN 'not run'
        WHEN 'i' THEN 'incomplete'
        ELSE e.status
    END AS execution_status_name,
    
    e.notes AS execution_notes,
    e.execution_ts AS execution_date,
    
    -- Test Case Details
    nh_tc.id AS tc_id,
    nh_tc.name AS tc_name,
    tcv.version AS tc_version,
    REPLACE(REPLACE(tcv.summary, '<p>', ''), '</p>', '') AS tc_summary,
    
    -- Test Suite Hierarchy
    suite.id AS suite_id,
    suite.name AS suite_name,
    
    -- Use the pre-calculated paths from our materialized table
    -- This avoids expensive path calculations during query execution
    nhp.full_path AS suite_path,
    
    -- Hierarchy levels - directly from materialized table
    nhp.level1_id,
    nhp.level1_name,
    nhp.level2_id,
    nhp.level2_name,
    nhp.level3_id,
    nhp.level3_name,
    nhp.level4_id,
    nhp.level4_name,
    nhp.level5_id,
    nhp.level5_name,
    
    -- Test Plan Information
    e.testplan_id,
    REPLACE(REPLACE(tp.notes, '<p>', ''), '</p>', '') AS testplan_name,
    
    -- Project Information
    tp.testproject_id AS project_id,
    REPLACE(REPLACE(tproj.notes, '<p>', ''), '</p>', '') AS project_name,
    
    -- Build Information
    e.build_id,
    b.name AS build_name,
    
    -- Platform Information (if used)
    e.platform_id,
    p.name AS platform_name,
    
    -- Tester Information
    e.tester_id,
    u.login AS tester_login,
    CONCAT(u.first, ' ', u.last) AS tester_name,
    
    -- Additional useful metrics
    TIMESTAMPDIFF(SECOND, tcv.creation_ts, e.execution_ts) AS time_to_execution
FROM 
    executions e
    JOIN tcversions tcv ON e.tcversion_id = tcv.id
    JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
    JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
    JOIN testplans tp ON e.testplan_id = tp.id
    JOIN builds b ON e.build_id = b.id
    LEFT JOIN platforms p ON e.platform_id = p.id
    LEFT JOIN users u ON e.tester_id = u.id
    LEFT JOIN nodes_hierarchy suite ON nh_tc.parent_id = suite.id
    LEFT JOIN node_hierarchy_paths nhp ON suite.id = nhp.node_id
    JOIN testprojects tproj ON tp.testproject_id = tproj.id;

-- Add additional indexes to the underlying tables to optimize common queries
CREATE INDEX IF NOT EXISTS idx_executions_status_tp ON executions(status, testplan_id);
CREATE INDEX IF NOT EXISTS idx_executions_date ON executions(execution_ts);
CREATE INDEX IF NOT EXISTS idx_executions_tester ON executions(tester_id);
CREATE INDEX IF NOT EXISTS idx_executions_build ON executions(build_id);

-- Grant select permissions on the view
-- GRANT SELECT ON test_execution_hierarchy_summary_optimized TO 'testlink_user';

-- Sample queries
/*
-- Get all test executions for a specific project with hierarchy
SELECT * FROM test_execution_hierarchy_summary_optimized 
WHERE project_id = 123
ORDER BY execution_date DESC;

-- Count executions by status for each test suite
SELECT suite_name, execution_status, COUNT(*) as count
FROM test_execution_hierarchy_summary_optimized
GROUP BY suite_id, execution_status
ORDER BY suite_name, execution_status;

-- Find test suites with failing tests
SELECT DISTINCT suite_path, project_name, testplan_name
FROM test_execution_hierarchy_summary_optimized
WHERE execution_status = 'failed'
ORDER BY suite_path;

-- Get execution metrics by tester
SELECT 
    tester_name, 
    COUNT(*) as total_executions,
    SUM(CASE WHEN execution_status = 'passed' THEN 1 ELSE 0 END) as passed,
    SUM(CASE WHEN execution_status = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN execution_status = 'blocked' THEN 1 ELSE 0 END) as blocked
FROM 
    test_execution_hierarchy_summary_optimized
GROUP BY 
    tester_id, tester_name
ORDER BY 
    total_executions DESC;
*/
