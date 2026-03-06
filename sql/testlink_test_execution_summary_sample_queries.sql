-- TestLink Test Execution Summary Sample Queries
-- Created: 2025-07-02
-- Purpose: Sample queries to demonstrate using the test_execution_hierarchy_summary view

-- 1. Basic query: Get all test executions for a specific project with hierarchy
SELECT 
    execution_id, 
    tc_name, 
    execution_status, 
    execution_status_name,
    suite_path, 
    execution_date, 
    tester_name
FROM 
    test_execution_hierarchy_summary 
WHERE 
    project_id = 1  -- Replace with your project ID
ORDER BY 
    execution_date DESC
LIMIT 100;

-- 2. Count executions by status for each test suite
SELECT 
    suite_name, 
    execution_status_name, 
    COUNT(*) as count
FROM 
    test_execution_hierarchy_summary
GROUP BY 
    suite_id, suite_name, execution_status_name
ORDER BY 
    suite_name, execution_status_name;

-- 3. Find test suites with failing tests
SELECT DISTINCT 
    suite_path, 
    project_id, 
    testplan_name
FROM 
    test_execution_hierarchy_summary
WHERE 
    execution_status_name = 'failed'
ORDER BY 
    suite_path;

-- 4. Get execution metrics by tester
SELECT 
    tester_name, 
    COUNT(*) as total_executions,
    SUM(CASE WHEN execution_status_name = 'passed' THEN 1 ELSE 0 END) as passed,
    SUM(CASE WHEN execution_status_name = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN execution_status_name = 'blocked' THEN 1 ELSE 0 END) as blocked,
    ROUND((SUM(CASE WHEN execution_status_name = 'passed' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as pass_rate
FROM 
    test_execution_hierarchy_summary
GROUP BY 
    tester_id, tester_name
ORDER BY 
    total_executions DESC;

-- 5. Count test executions by top-level folder (hierarchy level 1)
SELECT 
    level1_name, 
    COUNT(*) as total,
    SUM(CASE WHEN execution_status_name = 'passed' THEN 1 ELSE 0 END) as passed,
    SUM(CASE WHEN execution_status_name = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN execution_status_name = 'blocked' THEN 1 ELSE 0 END) as blocked
FROM 
    test_execution_hierarchy_summary
GROUP BY 
    level1_id, level1_name;

-- 6. Drill down report for a specific top level folder
SELECT 
    level2_name, 
    COUNT(*) as total,
    SUM(CASE WHEN execution_status_name = 'passed' THEN 1 ELSE 0 END) as passed,
    SUM(CASE WHEN execution_status_name = 'failed' THEN 1 ELSE 0 END) as failed
FROM 
    test_execution_hierarchy_summary
WHERE 
    level1_name = 'Your Top Level Folder'  -- Replace with your top level folder name
GROUP BY 
    level2_id, level2_name;

-- 7. Test cases that have never been executed
SELECT 
    nh_tc.id AS tc_id,
    nh_tc.name AS tc_name,
    parent_nh.name AS suite_name
FROM 
    nodes_hierarchy nh_tc
    JOIN nodes_hierarchy nh_tcv ON nh_tcv.parent_id = nh_tc.id
    JOIN tcversions tcv ON tcv.id = nh_tcv.id
    LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
    LEFT JOIN executions e ON e.tcversion_id = tcv.id
WHERE 
    e.id IS NULL
ORDER BY 
    parent_nh.name, nh_tc.name;

-- 8. Execution trend by date (last 30 days)
SELECT 
    DATE(execution_date) as exec_date,
    COUNT(*) as total_executions,
    SUM(CASE WHEN execution_status_name = 'passed' THEN 1 ELSE 0 END) as passed,
    SUM(CASE WHEN execution_status_name = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN execution_status_name = 'blocked' THEN 1 ELSE 0 END) as blocked
FROM 
    test_execution_hierarchy_summary
WHERE 
    execution_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY 
    DATE(execution_date)
ORDER BY 
    exec_date;

-- 9. Find test cases with multiple executions and status changes
SELECT 
    tc_id,
    tc_name,
    COUNT(*) as execution_count,
    COUNT(DISTINCT execution_status) as status_count,
    MIN(execution_date) as first_execution,
    MAX(execution_date) as last_execution,
    CASE 
        WHEN COUNT(DISTINCT execution_status) > 1 THEN 'Unstable'
        ELSE 'Stable'
    END as stability
FROM 
    test_execution_hierarchy_summary
GROUP BY 
    tc_id, tc_name
HAVING 
    execution_count > 1
ORDER BY 
    execution_count DESC;

-- 10. Dashboard summary
SELECT
    'Overall Summary' as metric,
    COUNT(*) as total_executions,
    SUM(CASE WHEN execution_status_name = 'passed' THEN 1 ELSE 0 END) as passed,
    SUM(CASE WHEN execution_status_name = 'failed' THEN 1 ELSE 0 END) as failed,
    SUM(CASE WHEN execution_status_name = 'blocked' THEN 1 ELSE 0 END) as blocked,
    COUNT(DISTINCT tc_id) as unique_testcases,
    COUNT(DISTINCT tester_id) as unique_testers,
    ROUND((SUM(CASE WHEN execution_status_name = 'passed' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as pass_percentage
FROM
    test_execution_hierarchy_summary;
