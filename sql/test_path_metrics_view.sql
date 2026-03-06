-- Create a view that provides test path metrics similar to the summary display
-- This view combines test suites, path hierarchy, and execution results

-- NOTE: Consider adding these indexes for better performance:
-- CREATE INDEX idx_tcversions_tc_external_id ON tcversions(tc_external_id);
-- CREATE INDEX idx_executions_tcversion_id ON executions(tcversion_id);
-- CREATE INDEX idx_executions_build_testplan ON executions(build_id, testplan_id);
-- CREATE INDEX idx_node_hierarchy_paths_v2_node_id ON node_hierarchy_paths_v2(node_id);

DROP VIEW IF EXISTS test_path_metrics_view;

-- ALTERNATIVE: Instead of a view, consider creating and periodically refreshing a table:
-- CREATE TABLE test_path_metrics_table AS
-- SELECT * FROM (
--    ... query below ...
-- ) AS derived;
-- This would be much faster to query from

CREATE VIEW test_path_metrics_view AS
-- More efficient version with fewer nested queries
SELECT 
    COALESCE(nhp.full_path, parent_nhp.full_path) AS test_path,
    COUNT(DISTINCT tc.id) as testcase_count,
    SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) as passed_count,
    SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) as failed_count,
    SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) as blocked_count,
    SUM(CASE WHEN e.status IS NULL OR e.status NOT IN ('p', 'f', 'b') THEN 1 ELSE 0 END) as not_run_count,
    
    -- Calculate rates directly inline
    CASE 
        WHEN COUNT(DISTINCT tc.id) = 0 THEN 0
        ELSE ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / COUNT(DISTINCT tc.id)) * 100, 2)
    END AS pass_rate,
    
    CASE 
        WHEN COUNT(DISTINCT tc.id) = 0 THEN 0
        ELSE ROUND((SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) / COUNT(DISTINCT tc.id)) * 100, 2)
    END AS fail_rate,
    
    CASE 
        WHEN COUNT(DISTINCT tc.id) = 0 THEN 0
        ELSE ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(DISTINCT tc.id)) * 100, 2)
    END AS block_rate,
    
    -- Ensure pending rate is calculated to make total = 100%
    CASE 
        WHEN COUNT(DISTINCT tc.id) = 0 THEN 0
        ELSE 
            GREATEST(0,
                ROUND(100 - 
                    ROUND((SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) / COUNT(DISTINCT tc.id)) * 100, 2) - 
                    ROUND((SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) / COUNT(DISTINCT tc.id)) * 100, 2) - 
                    ROUND((SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) / COUNT(DISTINCT tc.id)) * 100, 2), 2)
            )
    END AS pending_rate
FROM 
    -- Start with the test case hierarchy to ensure all test cases are included
    nodes_hierarchy tc
    JOIN nodes_hierarchy tc_version_node ON tc_version_node.parent_id = tc.id
    JOIN tcversions tcv ON tcv.id = tc_version_node.id AND tcv.active = 1
    
    -- Join to the test suite (parent)
    JOIN nodes_hierarchy suite ON suite.id = tc.parent_id
    
    -- Get the paths
    LEFT JOIN node_hierarchy_paths_v2 nhp ON nhp.node_id = suite.id
    LEFT JOIN node_hierarchy_paths_v2 parent_nhp ON parent_nhp.node_id = suite.parent_id AND nhp.node_id IS NULL
    
    -- Left join to executions to include test cases without executions
    LEFT JOIN (
        -- Simple subquery to get only the latest execution
        SELECT e1.tcversion_id, e1.id, e1.status, e1.execution_ts
        FROM executions e1
        JOIN (
            SELECT tcversion_id, MAX(execution_ts) AS latest_ts
            FROM executions
            GROUP BY tcversion_id
        ) e2 ON e1.tcversion_id = e2.tcversion_id AND e1.execution_ts = e2.latest_ts
    ) e ON tcv.id = e.tcversion_id
WHERE
    tc.node_type_id = 3 -- Ensure we're only getting test cases
    AND tcv.id = (SELECT MAX(id) FROM tcversions WHERE tcversions.tc_external_id = tcv.tc_external_id)
GROUP BY 
    COALESCE(nhp.full_path, parent_nhp.full_path)
HAVING
    COUNT(DISTINCT tc.id) > 0
ORDER BY
    test_path;
