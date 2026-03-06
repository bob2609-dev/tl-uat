-- Check if node_hierarchy_paths_v4 has data for your project
SELECT 
    COUNT(*) as total_paths,
    COUNT(DISTINCT node_id) as distinct_nodes,
    MIN(full_path) as sample_path
FROM node_hierarchy_paths_v4;

-- Check specific project's test suites
SELECT 
    nh.id,
    nh.name,
    nh.node_type_id,
    nhp.full_path
FROM nodes_hierarchy nh
LEFT JOIN node_hierarchy_paths_v4 nhp ON nh.id = nhp.node_id
WHERE nh.node_type_id = 2  -- Test suites
  AND nh.id IN (
    SELECT DISTINCT parent_id 
    FROM nodes_hierarchy 
    WHERE parent_id IN (
      SELECT id FROM nodes_hierarchy WHERE node_type_id = 1 AND id = 242099
    )
  )
LIMIT 20;
