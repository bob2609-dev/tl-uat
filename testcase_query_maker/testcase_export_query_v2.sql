-- TestLink Test Case Export Query
-- This query extracts test case data in a format similar to C2B1_CCL_CLEARING.xlsx
-- Generated based on CHEQUE CASA_20250724222505.sql structure and import logic
-- Column order matches C2B1_CCL_CLEARING.xlsx exactly

SELECT 
    -- Column order matches C2B1_CCL_CLEARING.xlsx exactly
    
    -- 1. Scenario/ System Functionality Primary Module/ Function Name (from custom field 1)
    COALESCE(cf1.value, '') AS ' Scenario/ System Functionality\nPrimary Module/ Function Name',
    
    -- 2. Scenario ID / Function ScreenID Primary Module/ Function ID (from custom field 2)
    COALESCE(cf2.value, '') AS 'Scenario ID /  Function ScreenID\nPrimary Module/ Function ID',
    
    -- 3. Sub-Scenario/Action (from custom field 3)
    COALESCE(cf3.value, '') AS ' Sub-Scenarion/Action\ne.g. New, Modify, Close',
    
    -- 4. Test Case S. No. (from nodes_hierarchy name)
    nh_tc.name AS 'Test Case S. No.',
    
    -- 5. Test Case Description (from custom field 6 or test script)
    COALESCE(cf6.value, tcv.summary) AS 'Test Case Description',
    
    -- 6. Test Type (-ve/+ve) (from custom field 5)
    COALESCE(cf5.value, '') AS 'Test Type\n(-ve/+ve)',
    
    -- 7. Test script (from custom field 6 or summary)
    COALESCE(cf6.value, REPLACE(REPLACE(tcv.summary, '<p>', ''), '</p>', '')) AS 'Test script',
    
    -- 8. Test Execution path (from custom field 7)
    COALESCE(cf7.value, '') AS 'Test Execution path',
    
    -- 9. Expected Results (Functional) (from custom field 8)
    COALESCE(cf8.value, '') AS 'Expected Results\n(Functional)',
    
    -- 10. Expected Results (Process & Business Rules) (from custom field 9 or additional field)
    COALESCE(cf9.value, '') AS 'Expected Results\n(Process & Business Rules )',
    
    -- 11. BUG_IDS (comma-separated from vw_execution_bugs)
    COALESCE(bugs.bug_ids, '') AS 'BUG_IDS',
    
    -- Additional useful fields for reference
    nh_tc.name AS 'Test Case Name',
    tcv.importance AS 'Importance Level',
    CASE tcv.execution_type 
        WHEN 1 THEN 'Manual'
        WHEN 2 THEN 'Automated'
        ELSE 'Unknown'
    END AS 'Execution Type',
    CASE tcv.status
        WHEN 1 THEN 'Draft'
        WHEN 2 THEN 'Ready for review'
        WHEN 3 THEN 'Review in progress'
        WHEN 4 THEN 'Rework'
        WHEN 5 THEN 'Obsolete'
        WHEN 6 THEN 'Future'
        WHEN 7 THEN 'Final'
        ELSE 'Unknown'
    END AS 'Status',
    tcv.creation_ts AS 'Created Date',
    tcv.modification_ts AS 'Modified Date'

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
            execution_duration,
            notes,
            id as execution_id,
            ROW_NUMBER() OVER (PARTITION BY tcversion_id ORDER BY execution_ts DESC) as rn
        FROM executions
    ) exec ON exec.tcversion_id = nh_tcv.id AND exec.rn = 1
    
    -- Left join with bug IDs aggregated from vw_execution_bugs
    LEFT JOIN (
        SELECT 
            execution_id,
            GROUP_CONCAT(bug_id ORDER BY bug_id SEPARATOR ', ') as bug_ids
        FROM vw_execution_bugs
        GROUP BY execution_id
    ) bugs ON bugs.execution_id = exec.execution_id
    
    -- Left join with custom fields
    LEFT JOIN cfield_design_values cf1 ON cf1.node_id = nh_tcv.id AND cf1.field_id = 1  -- Module Name/Test Type
    LEFT JOIN cfield_design_values cf2 ON cf2.node_id = nh_tcv.id AND cf2.field_id = 2  -- Scenario ID/Function
    LEFT JOIN cfield_design_values cf3 ON cf3.node_id = nh_tcv.id AND cf3.field_id = 3  -- Sub-Scenario/Action
    LEFT JOIN cfield_design_values cf4 ON cf4.node_id = nh_tcv.id AND cf4.field_id = 4  -- Account Type/Summary
    LEFT JOIN cfield_design_values cf5 ON cf5.node_id = nh_tcv.id AND cf5.field_id = 5  -- Test Type (+ve/-ve)
    LEFT JOIN cfield_design_values cf6 ON cf6.node_id = nh_tcv.id AND cf6.field_id = 6  -- Test Case Description/Test script
    LEFT JOIN cfield_design_values cf7 ON cf7.node_id = nh_tcv.id AND cf7.field_id = 7  -- Expected Results
    LEFT JOIN cfield_design_values cf8 ON cf8.node_id = nh_tcv.id AND cf8.field_id = 8  -- Test Execution Path
    LEFT JOIN cfield_design_values cf9 ON cf9.node_id = nh_tcv.id AND cf9.field_id = 9  -- Bug IDs

WHERE 
    nh_tc.node_type_id = 3  -- test case nodes only
    AND tcv.active = 1      -- only active test cases
    AND tcv.is_open = 1     -- only open test cases
    
    -- Optional: Filter by specific test suite (uncomment and modify as needed)
    -- AND nh_tc.parent_id = 190447  -- Replace with your test suite ID
    
    -- Optional: Filter by test case status (uncomment and modify as needed)
    -- AND tcv.status = 7  -- Final status only
    
    -- Optional: Filter by specific module (uncomment and modify as needed)
    -- AND cf1.value LIKE '%CHEQUE%'  -- Filter by module name
    
    -- Optional: Filter by execution date (uncomment and modify as needed)
    -- AND exec.execution_ts >= '2025-01-01'  -- Test cases executed on or after specific date
    -- AND exec.execution_ts <= '2025-12-31'  -- Test cases executed on or before specific date
    -- AND exec.execution_ts > '2025-01-01 00:00:00'  -- Test cases executed after specific datetime
    -- AND exec.execution_ts < '2025-12-31 23:59:59'  -- Test cases executed before specific datetime
    -- AND exec.execution_ts BETWEEN '2025-01-01' AND '2025-12-31'  -- Test cases executed in date range
    -- AND DATE(exec.execution_ts) = '2025-01-01'  -- Test cases executed on specific date
    -- AND exec.execution_ts >= DATE_SUB(NOW(), INTERVAL 30 DAY)  -- Test cases executed in last 30 days
    -- AND exec.execution_ts >= DATE_SUB(NOW(), INTERVAL 7 DAY)   -- Test cases executed in last 7 days
    -- AND exec.execution_ts >= DATE_SUB(NOW(), INTERVAL 1 YEAR)  -- Test cases executed in last year
    
    -- Optional: Filter by execution status (uncomment and modify as needed)
    -- AND exec.status = 'p'  -- Only passed test cases (p=Passed, f=Failed, b=Blocked, n=Not Run, s=Skipped, w=Wrong)
    
    -- Optional: Filter to show only executed test cases (uncomment if needed)
    -- AND exec.execution_ts IS NOT NULL  -- Only test cases that have been executed

ORDER BY 
    exec.execution_ts DESC,  -- Order by most recent execution first
    nh_tc.node_order,        -- Then by test case order in hierarchy
    tcv.tc_external_id       -- Then by external ID

-- Optional: Limit results for testing (uncomment if needed)
-- LIMIT 100;
