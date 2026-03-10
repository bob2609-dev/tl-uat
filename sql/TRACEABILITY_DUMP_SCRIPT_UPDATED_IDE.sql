-- =====================================================================================
-- TestLink Results TC Flat - Database Query Equivalent (CORRECTED)
-- =====================================================================================
-- This query produces the same output as lib/results/resultsTCFlat.php
-- It generates a flat spreadsheet format of test case execution results
-- =====================================================================================
use testlink_db;



 call refresh_node_hierarchy_paths_v4();


-- Set your parameters here:
SET @testplan_id = 242100;  -- Replace with your test plan ID (CORRECTED: was 1, now 2 based on diagnostic)

-- =====================================================================================
-- MAIN QUERY - Matches the exact PHP UNION structure
-- =====================================================================================

-- Get the complete path for each test suite (longest path = most complete)
WITH TestSuitePaths AS (
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

-- UNION of executed and not-run test cases (matches PHP logic exactly)
SELECT DISTINCT 
    -- Test Suite and Test Case Information
    COALESCE(tsp.full_path, NHTC_PARENT.name, 'Unknown Suite') AS "Test Suite",
    CONCAT(
        COALESCE(TP.prefix, 'TC-'),
        TCV.tc_external_id, 
        ':', 
        NHTC.name
    ) AS 'Test Case',
    
    -- Test Case S. No. (just the test case name)
    NHTC.name AS 'Test Case S. No.',
    
    -- Custom Fields for test case design
    COALESCE(cf1.value, '') AS 'Scenario/ System Functionality Primary Module/ Function Name',
    COALESCE(cf2.value, '') AS 'Scenario ID / Function ScreenID Primary Module/ Function ID',
    COALESCE(cf3.value, '') AS 'Sub-Scenarion/Action e.g. New, Modify, Close',
    COALESCE(cf6.value, TCV.summary) AS 'Test Case Description',
    COALESCE(cf5.value, '') AS 'Test Type (-ve/+ve)',
    COALESCE(cf6.value, TCV.summary) AS 'Test script',
    COALESCE(cf7.value, '') AS 'Test Execution path',
    COALESCE(cf8.value, '') AS 'Expected Results (Functional)',
    COALESCE(cf9.value, '') AS 'Expected Results (Process & Business Rules)',
    COALESCE(cf4.value, '') AS 'Case_Priority',
    
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
        ELSE 'Not Run'
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
    COALESCE(bug_list.bug_ids, '') AS 'Bug ID',
    COALESCE(bug_list.bug_ids, '') AS 'BUG_IDS',
    CASE 
        WHEN ExecutedCases.executions_id IS NULL THEN 'N/A'
        WHEN EXISTS (
            SELECT 1
            FROM attachments A
            WHERE A.fk_table = 'executions'
              AND A.fk_id = ExecutedCases.executions_id
            LIMIT 1
        ) THEN 'TRUE'
        ELSE 'FALSE'
    END AS 'HAS_ATTACHMENT',
    COALESCE(cev.value, '') AS 'CEV Value',
    COALESCE(cfdv.value, 'Not Specified') AS 'Test Case Type',
    
    -- Additional fields
    CASE 
        WHEN TCV.importance >= 3 THEN 'High'
        WHEN TCV.importance <= 1 THEN 'Low'
        ELSE 'Medium'
    END AS 'Importance Level',
    CASE 
        WHEN TCV.status = '1' THEN 'Draft'
        WHEN TCV.status = '2' THEN 'Ready for Review'
        WHEN TCV.status = '3' THEN 'Ready for Test Execution'
        WHEN TCV.status = '4' THEN 'Obsolete'
        ELSE 'Unknown'
    END AS 'Status',
    TCV.creation_ts AS 'Created Date',
    TCV.modification_ts AS 'Modified Date'

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

-- Get all custom fields for test case design
LEFT JOIN cfield_design_values cf1 ON cf1.node_id = NHTCV.id AND cf1.field_id = 1  -- Scenario/ System Functionality
LEFT JOIN cfield_design_values cf2 ON cf2.node_id = NHTCV.id AND cf2.field_id = 2  -- Scenario ID / Function ScreenID
LEFT JOIN cfield_design_values cf3 ON cf3.node_id = NHTCV.id AND cf3.field_id = 3  -- Sub-Scenario/Action
LEFT JOIN cfield_design_values cf4 ON cf4.node_id = NHTCV.id AND cf4.field_id = 4  -- Case_Priority
LEFT JOIN cfield_design_values cf5 ON cf5.node_id = NHTCV.id AND cf5.field_id = 5  -- Test Type (-ve/+ve)
LEFT JOIN cfield_design_values cf6 ON cf6.node_id = NHTCV.id AND cf6.field_id = 6  -- Test Case Description/Test script
LEFT JOIN cfield_design_values cf7 ON cf7.node_id = NHTCV.id AND cf7.field_id = 7  -- Test Execution path
LEFT JOIN cfield_design_values cf8 ON cf8.node_id = NHTCV.id AND cf8.field_id = 8  -- Expected Results (Functional)
LEFT JOIN cfield_design_values cf9 ON cf9.node_id = NHTCV.id AND cf9.field_id = 9  -- Expected Results (Process & Business Rules)

-- LEFT JOIN to get latest execution data per test case (using LEBBP like working query)
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

-- Get bug IDs associated with executions (optimized)
LEFT JOIN (
    SELECT 
        execution_id,
        GROUP_CONCAT(bug_id ORDER BY bug_id SEPARATOR ', ') AS bug_ids
    FROM execution_bugs
    GROUP BY execution_id
) bug_list ON bug_list.execution_id = ExecutedCases.executions_id

WHERE TPTCV.testplan_id = @testplan_id

ORDER BY 
    COALESCE(tsp.full_path, NHTC_PARENT.name, 'Unknown Suite'),
    NHTC.name,
    COALESCE(P.name, 'No Platform'),
    COALESCE(ExecutedCases.build_name, 'Not Executed')
LIMIT 50000;