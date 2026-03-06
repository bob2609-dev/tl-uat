-- =====================================================
-- TestLink Performance Test Queries
-- Generated from execNavigator.php and execDashboard.php
-- Run these queries in your IDE to monitor execution time
-- =====================================================

-- NOTE: Replace placeholder values with actual IDs from your system:
-- [TESTPLAN_ID] - Your test plan ID
-- [BUILD_ID] - Your build ID  
-- [PLATFORM_ID] - Your platform ID (if applicable)
-- [TESTPROJECT_ID] - Your test project ID

-- =====================================================
-- 1. EXECNAVIGATOR.PHP - Main Tree Building Queries
-- =====================================================

-- 1.1 Latest Execution per Test Case Version (Heavy Query)
-- This is the most complex query - finds the latest execution for each test case version
SELECT EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id, MAX(EE.id) AS id 
FROM testlink_executions EE 
WHERE EE.testplan_id = [TESTPLAN_ID]
AND EE.build_id = [BUILD_ID]
-- AND EE.platform_id = [PLATFORM_ID]  -- Uncomment if using platforms
GROUP BY EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id;

-- 1.2 Not Run Test Cases (Union Query Part 1)
-- Gets test cases that haven't been executed
SELECT NH_TCASE.id AS tcase_id, TPTCV.tcversion_id, TCV.version,
TCV.tc_external_id AS external_id, TPTCV.node_order AS exec_order,
COALESCE(E.status, 'n') AS exec_status
FROM testlink_testplan_tcversions TPTCV 
JOIN testlink_tcversions TCV ON TCV.id = TPTCV.tcversion_id 
JOIN testlink_nodes_hierarchy NH_TCV ON NH_TCV.id = TPTCV.tcversion_id 
JOIN testlink_nodes_hierarchy NH_TCASE ON NH_TCASE.id = NH_TCV.parent_id 
LEFT OUTER JOIN testlink_platforms PLAT ON PLAT.id = TPTCV.platform_id 
LEFT OUTER JOIN (SELECT EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id, MAX(EE.id) AS id 
                 FROM testlink_executions EE 
                 WHERE EE.testplan_id = [TESTPLAN_ID] AND EE.build_id = [BUILD_ID]
                 -- AND EE.platform_id = [PLATFORM_ID]  -- Uncomment if using platforms
                 GROUP BY EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id) AS LEBBP 
ON LEBBP.testplan_id = TPTCV.testplan_id 
AND LEBBP.tcversion_id = TPTCV.tcversion_id 
AND LEBBP.platform_id = TPTCV.platform_id 
AND LEBBP.testplan_id = [TESTPLAN_ID]
LEFT OUTER JOIN testlink_executions E 
ON E.tcversion_id = TPTCV.tcversion_id 
AND E.testplan_id = TPTCV.testplan_id 
AND E.platform_id = TPTCV.platform_id 
AND E.build_id = [BUILD_ID]
WHERE TPTCV.testplan_id = [TESTPLAN_ID]
AND E.id IS NULL AND LEBBP.id IS NULL;

-- 1.3 Executed Test Cases (Union Query Part 2)
-- Gets test cases that have been executed
SELECT NH_TCASE.id AS tcase_id, TPTCV.tcversion_id, TCV.version,
TCV.tc_external_id AS external_id, TPTCV.node_order AS exec_order,
COALESCE(E.status, 'n') AS exec_status
FROM testlink_testplan_tcversions TPTCV 
JOIN testlink_tcversions TCV ON TCV.id = TPTCV.tcversion_id 
JOIN testlink_nodes_hierarchy NH_TCV ON NH_TCV.id = TPTCV.tcversion_id 
JOIN testlink_nodes_hierarchy NH_TCASE ON NH_TCASE.id = NH_TCV.parent_id 
LEFT OUTER JOIN testlink_platforms PLAT ON PLAT.id = TPTCV.platform_id 
JOIN (SELECT EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id, MAX(EE.id) AS id 
      FROM testlink_executions EE 
      WHERE EE.testplan_id = [TESTPLAN_ID] AND EE.build_id = [BUILD_ID]
      -- AND EE.platform_id = [PLATFORM_ID]  -- Uncomment if using platforms
      GROUP BY EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id) AS LEBBP 
ON LEBBP.testplan_id = TPTCV.testplan_id 
AND LEBBP.tcversion_id = TPTCV.tcversion_id 
AND LEBBP.platform_id = TPTCV.platform_id 
AND LEBBP.testplan_id = [TESTPLAN_ID]
JOIN testlink_executions E 
ON E.id = LEBBP.id
AND E.tcversion_id = TPTCV.tcversion_id 
AND E.testplan_id = TPTCV.testplan_id 
AND E.platform_id = TPTCV.platform_id 
AND E.build_id = [BUILD_ID]
WHERE TPTCV.testplan_id = [TESTPLAN_ID];

-- 1.4 Tree Building Query (Get Test Suite Hierarchy)
-- Gets the test suite structure for the tree
SELECT NH.id, NH.name, NH.parent_id, NH.node_type_id, NH.node_order
FROM testlink_nodes_hierarchy NH
WHERE NH.node_type_id IN (1, 2) -- Test Project and Test Suite types
ORDER BY NH.parent_id, NH.node_order;

-- =====================================================
-- 2. EXECDASHBOARD.PHP - Dashboard Queries
-- =====================================================

-- 2.1 Get Test Plan Info
SELECT id, name, notes, is_active, active
FROM testlink_testplans
WHERE id = [TESTPLAN_ID];

-- 2.2 Get Build Info
SELECT id, name, notes, is_open, active, release_date
FROM testlink_builds
WHERE id = [BUILD_ID];

-- 2.3 Get Test Project Info
SELECT id, name, notes, is_active, active
FROM testlink_testprojects
WHERE id = [TESTPROJECT_ID];

-- 2.4 Get Platforms for Test Plan
SELECT PL.id, PL.name, TPL.testplan_id
FROM testlink_platforms PL
JOIN testlink_testplan_platforms TPL ON TPL.platform_id = PL.id
WHERE TPL.testplan_id = [TESTPLAN_ID];

-- 2.5 Get Builds for Test Plan
SELECT id, name, notes, is_open, active, release_date
FROM testlink_builds
WHERE testplan_id = [TESTPLAN_ID]
ORDER BY is_open DESC, release_date DESC;

-- 2.6 Get Test Case Prefix
SELECT tc_prefix
FROM testlink_testprojects
WHERE id = [TESTPROJECT_ID];

-- 2.7 Get Test Plan Custom Fields (Design Scope)
SELECT CF.name, CFV.value, CF.label
FROM testlink_custom_fields CF
JOIN testlink_cfield_design_values CFV ON CFV.field_id = CF.id
WHERE CFV.node_id = [TESTPLAN_ID]
AND CF.show_on_execution = 1
AND CF.scope = 'design';

-- 2.8 Get Build Custom Fields (Design Scope)
SELECT CF.name, CFV.value, CF.label
FROM testlink_custom_fields CF
JOIN testlink_cfield_design_values CFV ON CFV.field_id = CF.id
WHERE CFV.node_id = [BUILD_ID]
AND CF.show_on_execution = 1
AND CF.scope = 'design';

-- =====================================================
-- 3. ADDITIONAL PERFORMANCE CRITICAL QUERIES
-- =====================================================

-- 3.1 Count Test Cases in Test Plan (Used for tree display)
SELECT COUNT(DISTINCT TCV.id) as testcase_count
FROM testlink_testplan_tcversions TPTCV
JOIN testlink_tcversions TCV ON TCV.id = TPTCV.tcversion_id
WHERE TPTCV.testplan_id = [TESTPLAN_ID];

-- 3.2 Get Latest Execution Status Summary
SELECT E.status, COUNT(*) as count
FROM testlink_executions E
WHERE E.testplan_id = [TESTPLAN_ID]
AND E.build_id = [BUILD_ID]
-- AND E.platform_id = [PLATFORM_ID]  -- Uncomment if using platforms
GROUP BY E.status;

-- 3.3 Test Suite Execution Summary
SELECT TS.name, COUNT(DISTINCT TCASE.id) as total_testcases,
       SUM(CASE WHEN E.status = 'p' THEN 1 ELSE 0 END) as passed,
       SUM(CASE WHEN E.status = 'f' THEN 1 ELSE 0 END) as failed,
       SUM(CASE WHEN E.status = 'b' THEN 1 ELSE 0 END) as blocked,
       SUM(CASE WHEN E.status = 'n' THEN 1 ELSE 0 END) as not_run
FROM testlink_nodes_hierarchy TS
JOIN testlink_nodes_hierarchy TCASE ON TCASE.parent_id = TS.id
JOIN testlink_testplan_tcversions TPTCV ON TPTCV.tcversion_id IN (
    SELECT MAX(TCV2.id) 
    FROM testlink_tcversions TCV2 
    WHERE TCV2.tc_id = TCASE.id
)
LEFT OUTER JOIN testlink_executions E ON E.tcversion_id = TPTCV.tcversion_id
AND E.testplan_id = [TESTPLAN_ID]
AND E.build_id = [BUILD_ID]
-- AND E.platform_id = [PLATFORM_ID]  -- Uncomment if using platforms
WHERE TS.node_type_id = 2 -- Test Suite
GROUP BY TS.id, TS.name
ORDER BY TS.node_order;

-- =====================================================
-- 4. INDEX ANALYSIS QUERIES
-- =====================================================

-- 4.1 Check Index Usage on Executions Table
SHOW INDEX FROM testlink_executions;

-- 4.2 Check Index Usage on Test Plan TC Versions Table
SHOW INDEX FROM testlink_testplan_tcversions;

-- 4.3 Check Index Usage on Nodes Hierarchy Table
SHOW INDEX FROM testlink_nodes_hierarchy;

-- 4.4 Analyze Query Execution Plan
EXPLAIN SELECT EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id, MAX(EE.id) AS id 
FROM testlink_executions EE 
WHERE EE.testplan_id = [TESTPLAN_ID]
AND EE.build_id = [BUILD_ID]
GROUP BY EE.tcversion_id, EE.testplan_id, EE.platform_id, EE.build_id;

-- =====================================================
-- 5. PERFORMANCE MONITORING
-- =====================================================

-- 5.1 Enable Query Profiling (MySQL)
SET profiling = 1;

-- 5.2 Run your test queries here...

-- 5.3 Show Profile Results
SHOW PROFILE;
SHOW PROFILES;

-- 5.4 Check Slow Query Log Status
SHOW VARIABLES LIKE 'slow_query_log%';
SHOW VARIABLES LIKE 'long_query_time';

-- =====================================================
-- USAGE INSTRUCTIONS:
-- =====================================================
-- 1. Replace [TESTPLAN_ID], [BUILD_ID], [PLATFORM_ID], [TESTPROJECT_ID] with actual values
-- 2. Run queries individually in your IDE with execution time monitoring
-- 3. Focus on queries 1.1, 1.2, 1.3 as they are the most complex
-- 4. Use EXPLAIN before queries to analyze execution plans
-- 5. Monitor execution time - queries taking >1 second need optimization
-- 6. Check if recommended indexes exist on key columns
-- =====================================================
