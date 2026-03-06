-- =====================================================================================
-- DIAGNOSTIC: Find Test Plans with Execution Data
-- =====================================================================================
-- Use this to find the correct test plan ID that has actual execution data
-- =====================================================================================

-- STEP 1: Find all test plans and their basic info
SELECT 
    TP.id as testplan_id,
    TP.name as testplan_name,
    TP.active,
    TP.is_open,
    TPR.name as project_name,
    TPR.prefix as project_prefix
FROM testplans TP
INNER JOIN testprojects TPR ON TPR.id = TP.testproject_id
ORDER BY TP.id DESC;

-- STEP 2: Find test plans that actually have executions
SELECT 
    E.testplan_id,
    TP.name as testplan_name,
    COUNT(E.id) as total_executions,
    COUNT(DISTINCT E.build_id) as builds_with_executions,
    COUNT(DISTINCT E.tcversion_id) as testcases_executed,
    MIN(E.execution_ts) as earliest_execution,
    MAX(E.execution_ts) as latest_execution
FROM executions E
INNER JOIN testplans TP ON TP.id = E.testplan_id
GROUP BY E.testplan_id, TP.name
ORDER BY total_executions DESC;

-- STEP 3: Find test plans with active builds that have executions
SELECT 
    E.testplan_id,
    TP.name as testplan_name,
    B.id as build_id,
    B.name as build_name,
    B.active as build_active,
    COUNT(E.id) as executions_in_build
FROM executions E
INNER JOIN testplans TP ON TP.id = E.testplan_id
INNER JOIN builds B ON B.id = E.build_id
WHERE B.active = 1
GROUP BY E.testplan_id, TP.name, B.id, B.name, B.active
ORDER BY E.testplan_id, executions_in_build DESC;

-- STEP 4: Test the LEBBP logic with different test plan IDs
-- Replace the test plan IDs below with actual IDs from STEP 2
SELECT 
    'Test Plan 2' as test_plan,
    COUNT(*) as lebbp_results
FROM (
    SELECT 
        E.tcversion_id,
        E.testplan_id,
        E.platform_id,
        E.build_id,
        MAX(E.id) AS id
    FROM executions E
    INNER JOIN builds B ON B.id = E.build_id AND B.active = 1
    WHERE E.testplan_id = 2  -- Try different IDs here
    GROUP BY E.tcversion_id, E.testplan_id, E.platform_id, E.build_id
) AS lebbp_test

UNION ALL

SELECT 
    'Test Plan 3' as test_plan,
    COUNT(*) as lebbp_results
FROM (
    SELECT 
        E.tcversion_id,
        E.testplan_id,
        E.platform_id,
        E.build_id,
        MAX(E.id) AS id
    FROM executions E
    INNER JOIN builds B ON B.id = E.build_id AND B.active = 1
    WHERE E.testplan_id = 3  -- Try different IDs here
    GROUP BY E.tcversion_id, E.testplan_id, E.platform_id, E.build_id
) AS lebbp_test

UNION ALL

SELECT 
    'Test Plan 4' as test_plan,
    COUNT(*) as lebbp_results
FROM (
    SELECT 
        E.tcversion_id,
        E.testplan_id,
        E.platform_id,
        E.build_id,
        MAX(E.id) AS id
    FROM executions E
    INNER JOIN builds B ON B.id = E.build_id AND B.active = 1
    WHERE E.testplan_id = 4  -- Try different IDs here
    GROUP BY E.tcversion_id, E.testplan_id, E.platform_id, E.build_id
) AS lebbp_test;

-- =====================================================================================
-- INSTRUCTIONS:
-- 1. Run STEP 1 to see all available test plans
-- 2. Run STEP 2 to see which test plans have execution data
-- 3. Run STEP 3 to see which have active builds with executions
-- 4. Pick a test plan ID from STEP 2/3 and update the main query
-- 5. Run STEP 4 to verify LEBBP works with the correct test plan ID
-- =====================================================================================
