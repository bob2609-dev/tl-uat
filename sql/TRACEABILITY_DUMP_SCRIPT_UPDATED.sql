-- =====================================================================================
-- TestLink Results TC Flat - Database Query (Updated to handle test suites)
-- =====================================================================================
use testlink_db;

call refresh_node_hierarchy_paths_v4();

-- Set your parameters here:
SET @testplan_id = 242100;  -- Replace with your test plan ID

-- =====================================================================================
-- MAIN QUERY - Updated to handle test cases in test suites
-- =====================================================================================

-- First, get all test case versions in the test plan (directly or via test suites)
WITH TestCasesInPlan AS (
    -- Direct test case versions in the test plan
    SELECT DISTINCT 
        ptcv.tcversion_id,
        ptcv.testplan_id,
        ptcv.platform_id
    FROM testplan_tcversions ptcv
    WHERE ptcv.testplan_id = @testplan_id
    
    UNION
    
    -- Test case versions that are in test suites which are in the test plan
    SELECT DISTINCT
        tc.id as tcversion_id,
        ptcv.testplan_id,
        ptcv.platform_id
    FROM nodes_hierarchy tc
    INNER JOIN nodes_hierarchy ts ON ts.id = tc.parent_id
    INNER JOIN testplan_tcversions ptcv ON ptcv.tcversion_id = ts.id
    WHERE ptcv.testplan_id = @testplan_id
      AND tc.node_type_id = 3  -- Test case version
),

-- Get the complete path for each test suite (longest path = most complete)
TestSuitePaths AS (
    SELECT 
        node_id as id,
        full_path,
        ROW_NUMBER() OVER (PARTITION BY node_id ORDER BY CHAR_LENGTH(full_path) DESC) as rn
    FROM node_hierarchy_paths_v4
),

-- Latest Executions By Build and Platform (LEBBP)
LEBBP AS (
    SELECT 
        E.tcversion_id,
        E.testplan_id,
        E.platform_id,
        E.build_id,
        MAX(E.id) AS id
    FROM executions E
    INNER JOIN builds B ON B.id = E.build_id AND B.active = 1
    WHERE E.testplan_id = @testplan_id
    GROUP BY E.tcversion_id, E.testplan_id, E.platform_id, E.build_id
)

-- Main query
SELECT 
    COALESCE(tsp.full_path, nhtc_parent.name) AS "Test Suite",
    CONCAT(
        COALESCE(TP.prefix, 'TC-'),
        TCV.tc_external_id, 
        ':', 
        NHTC.name
    ) AS 'Test Case',
    CASE 
        WHEN ExecutedCases.version IS NOT NULL THEN ExecutedCases.version
        ELSE TCV.version
    END AS 'Version',
    COALESCE(P.name, 'No Platform') AS 'Platform',
    CASE 
        WHEN (TPTCV.urgency * TCV.importance) >= 9 THEN 'High'
        WHEN (TPTCV.urgency * TCV.importance) <= 4 THEN 'Low'
        ELSE 'Medium'
    END AS 'Priority',
    COALESCE(ExecutedCases.build_name, 'Not Executed') AS 'Build',
    COALESCE(CONCAT(U1.first, ' ', U1.last), '') AS 'Assigned To',
    CASE COALESCE(ExecutedCases.status, 'n')
        WHEN 'p' THEN 'Passed'
        WHEN 'f' THEN 'Failed'
        WHEN 'b' THEN 'Blocked'
        WHEN 'n' THEN 'Not Run'
        WHEN 's' THEN 'Skipped'
        WHEN 'w' THEN 'Warning'
        ELSE 'Unknown'
    END AS 'Execution Result',
    ExecutedCases.execution_ts AS 'Execution Date',
    COALESCE(CONCAT(U2.first, ' ', U2.last), '') AS 'Tested By',
    COALESCE(ExecutedCases.execution_notes, '') AS 'Notes',
    ExecutedCases.execution_duration AS 'Duration',
    CASE COALESCE(ExecutedCases.exec_type, TCV.execution_type)
        WHEN 1 THEN 'Manual'
        WHEN 2 THEN 'Automated'
        ELSE 'Not Configured'
    END AS 'Execution Type',
    ExecutedCases.executions_id AS 'Execution ID',
    GROUP_CONCAT(EB.bug_id ORDER BY EB.bug_id SEPARATOR ', ') AS 'Bug ID',
    CASE 
        WHEN ExecutedCases.executions_id IS NULL THEN 'N/A'
        WHEN EXISTS (
            SELECT 1
            FROM attachments A
            WHERE A.fk_table = 'executions'
              AND A.fk_id = ExecutedCases.executions_id
        ) THEN 'TRUE'
        ELSE 'FALSE'
    END AS 'HAS_ATTACHMENT',
    COALESCE(cev.value, '') AS 'CEV Value',
    COALESCE(cfdv.value, 'Not Specified') AS 'Test Case Type'

-- Start with test cases in the plan (direct or via test suites)
FROM TestCasesInPlan tcp

-- Get test case version details
INNER JOIN tcversions TCV ON TCV.id = tcp.tcversion_id

-- Get test case hierarchy
INNER JOIN nodes_hierarchy NHTCV ON NHTCV.id = tcp.tcversion_id
INNER JOIN nodes_hierarchy NHTC ON NHTC.id = NHTCV.parent_id
INNER JOIN nodes_hierarchy NHTC_PARENT ON NHTC_PARENT.id = NHTC.parent_id
INNER JOIN TestSuitePaths tsp ON tsp.id = NHTC_PARENT.id AND tsp.rn = 1

-- Get test plan and project info
INNER JOIN testplan_tcversions TPTCV ON TPTCV.tcversion_id = tcp.tcversion_id 
    AND TPTCV.testplan_id = tcp.testplan_id
    AND (TPTCV.platform_id = tcp.platform_id OR (TPTCV.platform_id IS NULL AND tcp.platform_id IS NULL))
INNER JOIN testplans TPL ON TPL.id = TPTCV.testplan_id
INNER JOIN testprojects TP ON TP.id = TPL.testproject_id

-- Get platform information (optional)
LEFT JOIN platforms P ON P.id = TPTCV.platform_id

-- Get Test Case Type from custom field
LEFT JOIN cfield_design_values cfdv ON cfdv.node_id = NHTCV.id 
    AND cfdv.field_id = 5  -- ID for Test Case Type custom field

-- Get latest execution data per test case
LEFT JOIN (
    SELECT 
        TPTCV_EXEC.testplan_id,
        TPTCV_EXEC.tcversion_id,
        TPTCV_EXEC.platform_id,
        E.build_id,
        B.name AS build_name,
        E.tcversion_number AS version,
        E.id AS executions_id,
        E.status,
        E.notes AS execution_notes,
        E.tester_id,
        E.execution_ts,
        E.execution_duration,
        E.execution_type AS exec_type,
        UA.user_id,
        ROW_NUMBER() OVER (
            PARTITION BY TPTCV_EXEC.tcversion_id, TPTCV_EXEC.platform_id 
            ORDER BY E.execution_ts DESC, E.id DESC
        ) as rn
    FROM testplan_tcversions TPTCV_EXEC
    INNER JOIN LEBBP ON 
        LEBBP.testplan_id = TPTCV_EXEC.testplan_id 
        AND LEBBP.platform_id = TPTCV_EXEC.platform_id 
        AND LEBBP.tcversion_id = TPTCV_EXEC.tcversion_id
    INNER JOIN executions E ON 
        E.id = LEBBP.id 
        AND E.build_id = LEBBP.build_id
    INNER JOIN builds B ON B.id = E.build_id
    LEFT JOIN user_assignments UA ON 
        UA.feature_id = TPTCV_EXEC.id 
        AND UA.build_id = E.build_id
        AND UA.type = 1 
        AND UA.status = 1
    WHERE TPTCV_EXEC.testplan_id = @testplan_id
) AS ExecutedCases ON 
    ExecutedCases.testplan_id = TPTCV.testplan_id
    AND ExecutedCases.tcversion_id = TPTCV.tcversion_id
    AND (ExecutedCases.platform_id = TPTCV.platform_id OR (ExecutedCases.platform_id IS NULL AND TPTCV.platform_id IS NULL))
    AND ExecutedCases.rn = 1

-- Get CEV custom field value from execution
LEFT JOIN cfield_execution_values cev ON cev.execution_id = ExecutedCases.executions_id 
    AND cev.field_id = 13  -- ID for CEV custom field (execution value)

-- Get user assignment for not-run cases (use latest build for assignment lookup)
LEFT JOIN (
    SELECT 
        UA.feature_id,
        UA.user_id,
        B.name as build_name,
        ROW_NUMBER() OVER (PARTITION BY UA.feature_id ORDER BY B.id DESC) as rn
    FROM user_assignments UA
    INNER JOIN builds B ON B.id = UA.build_id AND B.active = 1 AND B.testplan_id = @testplan_id
    WHERE UA.type = 1 AND UA.status = 1
) AS UA_NOT_RUN ON 
    UA_NOT_RUN.feature_id = TPTCV.id 
    AND UA_NOT_RUN.rn = 1

-- Get user details for assignments
LEFT JOIN users U1 ON U1.id = COALESCE(ExecutedCases.user_id, UA_NOT_RUN.user_id)
LEFT JOIN users U2 ON U2.id = ExecutedCases.tester_id

-- Get bug IDs associated with executions
LEFT JOIN execution_bugs EB ON EB.execution_id = ExecutedCases.executions_id

WHERE TPTCV.testplan_id = @testplan_id

GROUP BY 
    TPTCV.id,
    TPTCV.tcversion_id,
    TPTCV.platform_id,
    ExecutedCases.executions_id,
    ExecutedCases.build_name,
    TSP.full_path,
    NHTC_PARENT.name,
    NHTC.name,
    TCV.tc_external_id,
    TCV.version,
    TCV.importance,
    TCV.execution_type,
    TPTCV.urgency,
    P.name,
    TP.prefix,
    ExecutedCases.version,
    ExecutedCases.status,
    ExecutedCases.execution_ts,
    ExecutedCases.execution_notes,
    ExecutedCases.execution_duration,
    ExecutedCases.exec_type,
    U1.first,
    U1.last,
    U2.first,
    U2.last

ORDER BY 
    COALESCE(tsp.full_path, nhtc_parent.name),
    NHTC.name,
    COALESCE(P.name, 'No Platform'),
    COALESCE(ExecutedCases.build_name, 'Not Executed');