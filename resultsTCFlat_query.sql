-- =====================================================================================
-- TestLink Results TC Flat - Database Query Equivalent (CORRECTED)
-- =====================================================================================
-- This query produces the same output as lib/results/resultsTCFlat.php
-- It generates a flat spreadsheet format of test case execution results
--
-- IMPORTANT: This query now matches the exact PHP logic structure
-- =====================================================================================

-- Set your parameters here:
SET @testplan_id = 2;  -- Replace with your test plan ID (CORRECTED: was 1, now 2 based on diagnostic)

-- =====================================================================================
-- MAIN QUERY - Matches the exact PHP UNION structure
-- =====================================================================================

-- Get the complete path for each test suite (longest path = most complete)
WITH TestSuitePaths AS (
    SELECT 
        node_id as id,
        full_path,
        ROW_NUMBER() OVER (PARTITION BY node_id ORDER BY CHAR_LENGTH(full_path) DESC) as rn
    FROM node_hierarchy_paths_v2
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
    GROUP_CONCAT(EB.bug_id ORDER BY EB.bug_id SEPARATOR ', ') AS 'Bug ID'

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

-- =====================================================================================
-- ALTERNATIVE: Simplified Query (if the above is still complex)
-- =====================================================================================

/*
-- Simple version that just gets executed test cases
SELECT 
    NHTS.name AS 'Test Suite',
    CONCAT(TP.prefix, TCV.tc_external_id, ':', NHTC.name) AS 'Test Case',
    E.tcversion_number AS 'Version',
    COALESCE(P.name, 'No Platform') AS 'Platform',
    CASE 
        WHEN (TPTCV.urgency * TCV.importance) >= 9 THEN 'High'
        WHEN (TPTCV.urgency * TCV.importance) <= 4 THEN 'Low'
        ELSE 'Medium'
    END AS 'Priority',
    B.name AS 'Build',
    COALESCE(CONCAT(U1.first, ' ', U1.last), '') AS 'Assigned To',
    CASE E.status
        WHEN 'p' THEN 'Passed'
        WHEN 'f' THEN 'Failed'
        WHEN 'b' THEN 'Blocked'
        WHEN 'n' THEN 'Not Run'
        WHEN 's' THEN 'Skipped'
        WHEN 'w' THEN 'Warning'
        ELSE 'Unknown'
    END AS 'Execution Result',
    E.execution_ts AS 'Execution Date',
    COALESCE(CONCAT(U2.first, ' ', U2.last), '') AS 'Tested By',
    E.notes AS 'Notes',
    E.execution_duration AS 'Duration',
    CASE E.execution_type
        WHEN 1 THEN 'Manual'
        WHEN 2 THEN 'Automated'
        ELSE 'Not Configured'
    END AS 'Execution Type'

FROM testplan_tcversions TPTCV

-- Latest executions subquery
INNER JOIN (
    SELECT 
        E.tcversion_id,
        E.testplan_id,
        E.platform_id,
        E.build_id,
        MAX(E.id) AS latest_id
    FROM executions E
    INNER JOIN builds B ON B.id = E.build_id AND B.active = 1
    WHERE E.testplan_id = @testplan_id
    GROUP BY E.tcversion_id, E.testplan_id, E.platform_id, E.build_id
) LEBBP ON 
    LEBBP.testplan_id = TPTCV.testplan_id 
    AND LEBBP.platform_id = TPTCV.platform_id 
    AND LEBBP.tcversion_id = TPTCV.tcversion_id

-- Get the actual execution record
INNER JOIN executions E ON 
    E.id = LEBBP.latest_id 
    AND E.build_id = LEBBP.build_id

-- Get build info
INNER JOIN builds B ON B.id = E.build_id

-- Get test case hierarchy
INNER JOIN nodes_hierarchy NHTCV ON NHTCV.id = TPTCV.tcversion_id
INNER JOIN nodes_hierarchy NHTC ON NHTC.id = NHTCV.parent_id
INNER JOIN nodes_hierarchy NHTS ON NHTS.id = NHTC.parent_id

-- Get test case version (use execution version, not plan version)
INNER JOIN tcversions TCV ON TCV.id = E.tcversion_id

-- Get test project info
INNER JOIN testplans TPL ON TPL.id = TPTCV.testplan_id
INNER JOIN testprojects TP ON TP.id = TPL.testproject_id

-- Optional joins
LEFT JOIN platforms P ON P.id = TPTCV.platform_id
LEFT JOIN user_assignments UA ON 
    UA.feature_id = TPTCV.id 
    AND UA.build_id = E.build_id
    AND UA.type = 1 AND UA.status = 1
LEFT JOIN users U1 ON U1.id = UA.user_id
LEFT JOIN users U2 ON U2.id = E.tester_id

WHERE TPTCV.testplan_id = @testplan_id

ORDER BY NHTS.name, NHTC.name, P.name, B.name;
*/
