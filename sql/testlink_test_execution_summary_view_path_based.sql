-- TestLink Test Execution Summary View (Path-Based Hierarchy Extraction)
-- Created: 2025-07-02
-- Modified: 2025-07-02
-- Purpose: Creates a view that provides hierarchical test execution reporting
--          with hierarchy levels extracted from path strings

-- Drop the view if it exists already
DROP VIEW IF EXISTS test_execution_hierarchy_summary_path_based;

-- Create the test execution summary view with path-based hierarchy extraction
CREATE OR REPLACE VIEW test_execution_hierarchy_summary_path_based AS
-- First create a CTE to get the hierarchy paths
WITH RECURSIVE node_hierarchy_path AS (
    -- Anchor member: starting points (nodes without parent or with parent_id=0)
    SELECT 
        id, 
        name, 
        parent_id,
        name AS path
    FROM nodes_hierarchy 
    WHERE parent_id IS NULL OR parent_id = 0
    
    UNION ALL
    
    -- Recursive member: nodes with parents
    SELECT 
        nh.id, 
        nh.name, 
        nh.parent_id,
        CONCAT(nhp.path, ' > ', nh.name) AS path
    FROM 
        nodes_hierarchy nh
        JOIN node_hierarchy_path nhp ON nh.parent_id = nhp.id
)

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
    
    -- Get the full hierarchical path using the CTE
    CASE 
        WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE 'No Suite'
    END AS suite_path,
    
    -- Extract hierarchy levels from the suite_path using string manipulation
    -- We get the suite path first, then extract parts using SUBSTRING_INDEX
    -- This way we don't need to rely on custom functions
    
    -- Level 1 (topmost parent) - first part of the path before any '>' delimiter
    (SELECT id FROM nodes_hierarchy 
     WHERE name = SUBSTRING_INDEX(
         (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
         ' > ', 1) 
     LIMIT 1) AS level1_id,
    SUBSTRING_INDEX(
        CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE 'No Suite' END, 
        ' > ', 1) AS level1_name,
        
    -- Level 2 - second part of the hierarchy path
    (SELECT id FROM nodes_hierarchy 
     WHERE name = CASE 
        WHEN LOCATE(' > ', CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) > 0 
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 2),
            ' > ', -1)
        ELSE NULL
     END
     LIMIT 1) AS level2_id,
    CASE 
        WHEN LOCATE(' > ', CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) > 0 
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 2),
            ' > ', -1)
        ELSE NULL
    END AS level2_name,
    
    -- Level 3 - third part of the hierarchy path
    (SELECT id FROM nodes_hierarchy 
     WHERE name = CASE 
        WHEN LENGTH(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) - LENGTH(REPLACE(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END, ' > ', '')) > 4 -- at least 3 levels
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 3),
            ' > ', -1)
        ELSE NULL
     END
     LIMIT 1) AS level3_id,
    CASE 
        WHEN LENGTH(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) - LENGTH(REPLACE(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END, ' > ', '')) > 4 -- at least 3 levels
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 3),
            ' > ', -1)
        ELSE NULL
    END AS level3_name,
    
    -- Level 4 - fourth part of the hierarchy path
    (SELECT id FROM nodes_hierarchy 
     WHERE name = CASE 
        WHEN LENGTH(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) - LENGTH(REPLACE(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END, ' > ', '')) > 6 -- at least 4 levels
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 4),
            ' > ', -1)
        ELSE NULL
     END
     LIMIT 1) AS level4_id,
    CASE 
        WHEN LENGTH(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) - LENGTH(REPLACE(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END, ' > ', '')) > 6 -- at least 4 levels
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 4),
            ' > ', -1)
        ELSE NULL
    END AS level4_name,
    
    -- Level 5 - fifth part of the hierarchy path (most specific/lowest level)
    (SELECT id FROM nodes_hierarchy 
     WHERE name = CASE 
        WHEN LENGTH(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) - LENGTH(REPLACE(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END, ' > ', '')) > 8 -- at least 5 levels
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 5),
            ' > ', -1)
        ELSE suite.name -- if we don't have 5 levels, use current suite name
     END
     LIMIT 1) AS level5_id,
    CASE 
        WHEN LENGTH(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END) - LENGTH(REPLACE(CASE WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE '' END, ' > ', '')) > 8 -- at least 5 levels
        THEN SUBSTRING_INDEX(
            SUBSTRING_INDEX(
                (SELECT path FROM node_hierarchy_path WHERE id = suite.id), 
                ' > ', 5),
            ' > ', -1)
        ELSE suite.name -- if we don't have 5 levels, use current suite name
    END AS level5_name,
        
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
    JOIN testprojects tproj ON tp.testproject_id = tproj.id;

-- Grant select permissions on the view
-- GRANT SELECT ON test_execution_hierarchy_summary TO 'testlink_user';

-- Sample queries (comment these out before running in production)
/*
-- Get all test executions for a specific project with hierarchy
SELECT * FROM test_execution_hierarchy_summary 
WHERE project_id = 123
ORDER BY execution_date DESC;

-- Count executions by status for each test suite
SELECT suite_name, execution_status, COUNT(*) as count
FROM test_execution_hierarchy_summary
GROUP BY suite_id, execution_status
ORDER BY suite_name, execution_status;

-- Find test suites with failing tests
SELECT DISTINCT suite_path, project_name, testplan_name
FROM test_execution_hierarchy_summary
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
    test_execution_hierarchy_summary
GROUP BY 
    tester_id, tester_name
ORDER BY 
    total_executions DESC;
*/
