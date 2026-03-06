-- =====================================================================================
-- TestLink Results TC Flat - UI Display Version (LIMITED TO 100 RECORDS)
-- =====================================================================================
-- This query is optimized for UI display with a limit of 100 records
-- For full export, use TRACEABILITY_DUMP_SCRIPT_UPDATED.sql
-- =====================================================================================

-- Set GROUP_CONCAT limit for MySQL Enterprise compatibility
SET SESSION group_concat_max_len = 1000000;

-- =====================================================================================
-- MAIN QUERY - Matches the exact PHP UNION structure (LIMITED)
-- =====================================================================================

-- Get the complete path for each test suite (longest path = most complete)
WITH TestSuitePaths AS (
    SELECT 
        node_id as id,
        full_path,
        ROW_NUMBER() OVER (PARTITION BY node_id ORDER BY LENGTH(full_path) DESC) as rn
    FROM node_hierarchy_paths_v4
),

-- Latest Executions By Build and Platform (LEBBP) - matches PHP subquery
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

-- UNION of executed and not-run test cases (matches PHP logic exactly)
SELECT 
    -- Test Suite and Test Case Information
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
    
    -- Execution Information
    COALESCE(ExecutedCases.build_name, 'Not Executed') AS 'Build',
    COALESCE(CONCAT(U1.first, ' ', U1.last), '') AS 'Assigned To',
    
    -- Status mapping
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
    
    -- Execution Type mapping
    CASE COALESCE(ExecutedCases.exec_type, TCV.execution_type)
        WHEN 1 THEN 'Manual'
        WHEN 2 THEN 'Automated'
        ELSE 'Not Configured'
    END AS 'Execution Type',
    
    -- Additional columns for traceability
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

FROM testplan_tcversions TPTCV

-- Get test case hierarchy (always needed)
INNER JOIN nodes_hierarchy NHTCV ON NHTCV.id = TPTCV.tcversion_id
INNER JOIN nodes_hierarchy NHTC ON NHTC.id = NHTCV.parent_id
INNER JOIN nodes_hierarchy NHTC_PARENT ON NHTC_PARENT.id = NHTC.parent_id
INNER JOIN TestSuitePaths tsp ON tsp.id = NHTC_PARENT.id AND tsp.rn = 1

-- Get test case version details
INNER JOIN tcversions TCV ON TCV.id = TPTCV.tcversion_id

-- Get test project info for prefix
INNER JOIN testplans TPL ON TPL.id = TPTCV.testplan_id
INNER JOIN testprojects TP ON TP.id = TPL.testproject_id

-- Get platform information (optional)
LEFT JOIN platforms P ON P.id = TPTCV.platform_id

-- Get Test Case Type from custom field
LEFT JOIN cfield_design_values cfdv ON cfdv.node_id = NHTCV.id 
    AND cfdv.field_id = 5  -- ID for Test Case Type custom field

-- LEFT JOIN to get latest execution data per test case
LEFT JOIN (
    -- This subquery gets the latest execution per test case
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
    
    -- Join with latest executions (matches PHP LEBBP join)
    INNER JOIN LEBBP ON 
        LEBBP.testplan_id = TPTCV_EXEC.testplan_id 
        AND LEBBP.platform_id = TPTCV_EXEC.platform_id 
        AND LEBBP.tcversion_id = TPTCV_EXEC.tcversion_id
        AND LEBBP.testplan_id = @testplan_id
    
    -- Get execution details (matches PHP execution join)
    INNER JOIN executions E ON 
        E.id = LEBBP.id 
        AND E.build_id = LEBBP.build_id
    
    -- Get build info for the execution
    INNER JOIN builds B ON B.id = E.build_id
    
    -- Get user assignment (optional)
    LEFT JOIN user_assignments UA ON 
        UA.feature_id = TPTCV_EXEC.id 
        AND UA.build_id = E.build_id
        AND UA.type = 1 AND UA.status = 1
    
    WHERE TPTCV_EXEC.testplan_id = @testplan_id
) AS ExecutedCases ON 
    ExecutedCases.testplan_id = TPTCV.testplan_id
    AND ExecutedCases.tcversion_id = TPTCV.tcversion_id
    AND ExecutedCases.platform_id = TPTCV.platform_id
    AND ExecutedCases.rn = 1  -- Only get the latest execution

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
    COALESCE(ExecutedCases.build_name, 'Not Executed')

LIMIT 100;
