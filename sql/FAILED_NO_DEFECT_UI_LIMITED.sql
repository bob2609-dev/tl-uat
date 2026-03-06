-- TestLink Failed Test Cases with No Defect - UI Display Version (LIMITED TO 100 RECORDS)
-- This query extracts test cases that have FAILED latest execution with NO defect IDs
-- For full export, use testcase_Extraction_Query_failed_no_defect.sql

-- Set GROUP_CONCAT limit for MySQL Enterprise compatibility
SET SESSION group_concat_max_len = 1000000;

SELECT 
    -- Column order matches export format
    
    -- 1. Scenario/ System Functionality Primary Module/ Function Name
    COALESCE(cf1.value, '') AS 'Module/Function Name',
    
    -- 2. Scenario ID / Function ScreenID
    COALESCE(cf2.value, '') AS 'Scenario ID/Function ID',
    
    -- 3. Sub-Scenario/Action
    COALESCE(cf3.value, '') AS 'Sub-Scenario/Action',
    
    -- 4. Test Case S. No.
    nh_tc.name AS 'Test Case S. No.',
    
    -- 5. Test Case Description
    COALESCE(cf6.value, tcv.summary) AS 'Test Case Description',
    
    -- 6. Test Type (-ve/+ve)
    COALESCE(cf5.value, '') AS 'Test Type',
    
    -- 7. Test Execution path
    COALESCE(cf7.value, '') AS 'Test Execution Path',
    
    -- 8. Expected Results (Functional)
    COALESCE(cf8.value, '') AS 'Expected Results (Functional)',
    
    -- 9. Expected Results (Process & Business Rules)
    COALESCE(cf9.value, '') AS 'Expected Results (Business)',
    
    -- 10. Execution Status
    CASE exec.status
        WHEN 'p' THEN 'Passed'
        WHEN 'f' THEN 'Failed'
        WHEN 'b' THEN 'Blocked'
        ELSE 'Unknown'
    END AS 'Execution Status',
    
    -- 11. First Execution Date
    first_exec.first_execution_ts AS 'First Execution Date',
    
    -- 12. Last Execution Date
    exec.execution_ts AS 'Last Execution Date',
    
    -- 13. Executed By (User who ran the test)
    COALESCE(CONCAT(u.first, ' ', u.last), u.login, '') AS 'Executed By',
    
    -- 14. Test Plan (HTML tags stripped)
    REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(tpl.notes, ''), '<p>', ''), '</p>', ''), '<br>', ''), '<br/>', '') AS 'Test Plan',
    
    -- 15. CEV Value
    COALESCE(cev.value, '') AS 'CEV Value'

FROM 
    nodes_hierarchy nh_tc
    
    -- Join with test case version nodes
    INNER JOIN nodes_hierarchy nh_tcv ON nh_tcv.parent_id = nh_tc.id 
        AND nh_tcv.node_type_id = 4  -- test case version
    
    -- Join with tcversions table
    INNER JOIN tcversions tcv ON tcv.id = nh_tcv.id
    
    -- Left join with executions table to get the most recent execution
    LEFT JOIN (
        SELECT 
            tcversion_id,
            execution_ts,
            status,
            testplan_id,
            tester_id,
            execution_duration,
            notes,
            id as execution_id,
            ROW_NUMBER() OVER (PARTITION BY tcversion_id ORDER BY execution_ts DESC) as rn
        FROM executions
        WHERE execution_ts IS NOT NULL
    ) exec ON exec.tcversion_id = nh_tcv.id AND exec.rn = 1
    INNER JOIN testplans tpl ON tpl.id = exec.testplan_id
    INNER JOIN testprojects tp ON tp.id = tpl.testproject_id
    
    -- Join with users table to get executor name
    LEFT JOIN users u ON u.id = exec.tester_id
    
    -- Left join to get the first execution date
    LEFT JOIN (
        SELECT 
            tcversion_id,
            MIN(execution_ts) as first_execution_ts
        FROM executions
        GROUP BY tcversion_id
    ) first_exec ON first_exec.tcversion_id = nh_tcv.id
    
    -- Left join with bug IDs aggregated
    LEFT JOIN (
        SELECT 
            execution_id,
            GROUP_CONCAT(bug_id ORDER BY bug_id SEPARATOR ', ') as bug_ids
        FROM vw_execution_bugs
        GROUP BY execution_id
    ) bugs ON bugs.execution_id = exec.execution_id
    
    -- Left join with custom fields
    LEFT JOIN cfield_design_values cf1 ON cf1.node_id = nh_tcv.id AND cf1.field_id = 1
    LEFT JOIN cfield_design_values cf2 ON cf2.node_id = nh_tcv.id AND cf2.field_id = 2
    LEFT JOIN cfield_design_values cf3 ON cf3.node_id = nh_tcv.id AND cf3.field_id = 3
    LEFT JOIN cfield_design_values cf5 ON cf5.node_id = nh_tcv.id AND cf5.field_id = 5
    LEFT JOIN cfield_design_values cf6 ON cf6.node_id = nh_tcv.id AND cf6.field_id = 6
    LEFT JOIN cfield_design_values cf7 ON cf7.node_id = nh_tcv.id AND cf7.field_id = 7
    LEFT JOIN cfield_design_values cf8 ON cf8.node_id = nh_tcv.id AND cf8.field_id = 8
    LEFT JOIN cfield_design_values cf9 ON cf9.node_id = nh_tcv.id AND cf9.field_id = 9
    LEFT JOIN cfield_execution_values cev ON cev.execution_id = exec.execution_id AND cev.field_id = 13

WHERE 
    nh_tc.node_type_id = 3  -- test case nodes only
    AND tcv.active = 1      -- only active test cases
    AND tcv.is_open = 1     -- only open test cases
    AND exec.status = 'f'   -- Only FAILED latest executions
    AND bugs.execution_id IS NULL  -- No defect IDs

ORDER BY 
    first_exec.first_execution_ts,
    exec.execution_ts DESC,
    nh_tc.node_order,
    tcv.tc_external_id

LIMIT 100;
