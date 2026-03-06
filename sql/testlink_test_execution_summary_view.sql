-- TestLink Test Execution Summary View
-- Created: 2025-07-02
-- Purpose: Creates a view that provides hierarchical test execution reporting

-- Drop the view if it exists already
DROP VIEW IF EXISTS test_execution_hierarchy_summary;

-- Create the test execution summary view
CREATE OR REPLACE VIEW test_execution_hierarchy_summary AS
WITH RECURSIVE node_hierarchy_path(id, name, parent_id, path, level) AS (
    -- Anchor member: starting points (nodes without parent or with parent_id=0)
    SELECT 
        id, 
        name, 
        parent_id,
        name AS path,
        1 AS level
    FROM nodes_hierarchy 
    WHERE parent_id IS NULL OR parent_id = 0
    
    UNION ALL
    
    -- Recursive member: nodes with parents
    SELECT 
        nh.id, 
        nh.name, 
        nh.parent_id,
        CONCAT(nhp.path, ' > ', nh.name) AS path,
        nhp.level + 1 AS level
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
    tcv.summary AS tc_summary,
    
    -- Test Suite Hierarchy
    suite.id AS suite_id,
    suite.name AS suite_name,
    
    -- Use the hierarchy path from our CTE
    CASE 
        WHEN suite.id IS NOT NULL THEN 
            (SELECT path FROM node_hierarchy_path WHERE id = suite.id)
        ELSE 'No Suite'
    END AS suite_path,
    
    -- Level hierarchy - attempt to get up to 5 levels of parent hierarchy
    -- The approach is slightly different as we're using ancestors in CTE
    -- Level 1 would be the top-most parent
    (SELECT id FROM node_hierarchy_path 
     WHERE parent_id IS NULL OR parent_id = 0
     LIMIT 1) AS level1_id,
    (SELECT name FROM node_hierarchy_path 
     WHERE parent_id IS NULL OR parent_id = 0
     LIMIT 1) AS level1_name,
     
    -- Simplified approach for hierarchy levels (for MariaDB compatibility)
    NULL AS level2_id,
    NULL AS level2_name,
    NULL AS level3_id,
    NULL AS level3_name,
    NULL AS level4_id,
    NULL AS level4_name,
    suite.id AS level5_id,
    suite.name AS level5_name,
    
    -- Test Plan Information
    e.testplan_id,
    tp.notes AS testplan_name,
    
    -- Project Information
    tp.testproject_id AS project_id,
    tproj.notes AS project_name,
    
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
