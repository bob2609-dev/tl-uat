-- =====================================================================================
-- TestLink Results TC Flat - FULL EXPORT VERSION (UNLIMITED RECORDS)
-- MySQL 8 Enterprise Compatible Version
-- =====================================================================================
-- This query is optimized for MySQL 8 Enterprise with simpler joins
-- Compatible with MySQL 8 Enterprise strict mode
-- =====================================================================================

-- Set GROUP_CONCAT limit for MySQL Enterprise compatibility
SET SESSION group_concat_max_len = 1000000;

-- =====================================================================================
-- MAIN QUERY - Full Export for MySQL 8 Enterprise
-- =====================================================================================

-- Simplified query without complex CTEs for better MySQL 8 performance
SELECT 
    -- Test Suite and Test Case Information
    COALESCE(nhp.full_path, NHTC_PARENT.name) AS "Test Suite",
    CONCAT(
        COALESCE(TP.prefix, 'TC-'),
        TCV.tc_external_id, 
        ':', 
        NHTC.name
    ) AS 'Test Case',
    TCV.version AS 'Version',
    COALESCE(P.name, 'No Platform') AS 'Platform',
    CASE 
        WHEN (TPTCV.urgency * TCV.importance) >= 9 THEN 'High'
        WHEN (TPTCV.urgency * TCV.importance) <= 4 THEN 'Low'
        ELSE 'Medium'
    END AS 'Priority',
    
    -- Execution Information (simplified)
    COALESCE(B.name, 'Not Executed') AS 'Build',
    '' AS 'Assigned To',
    
    -- Status mapping
    CASE COALESCE(E.status, 'n')
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
    COALESCE(E.notes, '') AS 'Notes',
    E.execution_duration AS 'Duration',
    
    -- Execution Type mapping
    CASE COALESCE(E.execution_type, TCV.execution_type)
        WHEN 1 THEN 'Manual'
        WHEN 2 THEN 'Automated'
        ELSE 'Not Configured'
    END AS 'Execution Type',
    
    -- Test Case S. No.
    NHTC.name AS 'Test Case S. No.',
    
    -- Custom Fields for test case design
    COALESCE(cf_module.value, '') AS 'Primary Module / Function Name',
    COALESCE(cf_scenario.value, '') AS 'Scenario ID',
    COALESCE(cf_action.value, '') AS 'Sub-Scenario / Action',
    COALESCE(TCV.summary, '') AS 'Test Case Description',
    
    -- Additional columns for traceability
    E.id AS 'Execution ID',
    COALESCE(bug_list.bug_ids, '') AS 'Bug ID',
    CASE 
        WHEN E.id IS NULL THEN 'N/A'
        WHEN EXISTS (
            SELECT 1
            FROM attachments A
            WHERE A.fk_table = 'executions'
              AND A.fk_id = E.id
        ) THEN 'TRUE'
        ELSE 'FALSE'
    END AS 'HAS_ATTACHMENT',
    COALESCE(cev.value, '') AS 'CEV Value',
    COALESCE(cfdv.value, 'Not Specified') AS 'Test Case Type'

FROM testplan_tcversions TPTCV

-- Get test case hierarchy
INNER JOIN nodes_hierarchy NHTCV ON NHTCV.id = TPTCV.tcversion_id
INNER JOIN nodes_hierarchy NHTC ON NHTC.id = NHTCV.parent_id
INNER JOIN nodes_hierarchy NHTC_PARENT ON NHTC_PARENT.id = NHTC.parent_id

-- Get test case version details
INNER JOIN tcversions TCV ON TCV.id = TPTCV.tcversion_id

-- Get test project info for prefix
INNER JOIN testplans TPL ON TPL.id = TPTCV.testplan_id
INNER JOIN testprojects TP ON TP.id = TPL.testproject_id

-- Get platform information
LEFT JOIN platforms P ON P.id = TPTCV.platform_id

-- Get test suite path (simplified - using subquery instead of CTE)
LEFT JOIN (
    SELECT node_id, full_path 
    FROM node_hierarchy_paths_v4 
    WHERE full_path IS NOT NULL
) nhp ON nhp.node_id = NHTC_PARENT.id

-- Get latest execution (simplified approach)
LEFT JOIN executions E ON E.tcversion_id = TPTCV.tcversion_id 
    AND E.testplan_id = TPTCV.testplan_id 
    AND E.platform_id = TPTCV.platform_id

-- Get build name
LEFT JOIN builds B ON B.id = E.build_id

-- Get tester info
LEFT JOIN users U2 ON U2.id = E.tester_id

-- Get custom field values
LEFT JOIN cfield_design_values cev ON cev.node_id = NHTCV.id 
    AND cev.field_id = 3
LEFT JOIN cfield_design_values cfdv ON cfdv.node_id = NHTCV.id 
    AND cfdv.field_id = 5

-- Get custom fields for Scenario ID, Module/Function Name, and Sub-Scenario/Action
LEFT JOIN cfield_design_values cf_module ON cf_module.node_id = NHTCV.id AND cf_module.field_id = 1  -- Primary Module/Function Name
LEFT JOIN cfield_design_values cf_scenario ON cf_scenario.node_id = NHTCV.id AND cf_scenario.field_id = 2  -- Scenario ID
LEFT JOIN cfield_design_values cf_action ON cf_action.node_id = NHTCV.id AND cf_action.field_id = 3  -- Sub-Scenario/Action

-- Get bug IDs associated with executions
LEFT JOIN (
    SELECT 
        execution_id,
        GROUP_CONCAT(bug_id ORDER BY bug_id SEPARATOR ', ') AS bug_ids
    FROM execution_bugs
    GROUP BY execution_id
) bug_list ON bug_list.execution_id = E.id

-- Filter by test plan (NO LIMIT for full export)
WHERE TPTCV.testplan_id = @testplan_id
ORDER BY TCV.tc_external_id, P.name, B.name;
