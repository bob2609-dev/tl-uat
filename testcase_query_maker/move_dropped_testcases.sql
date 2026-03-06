-- Move test cases with 'DROPPED' status to new parent IDs
-- Based on parent_change.csv mapping and cfield_execution_values table

UPDATE nodes_hierarchy nh
SET nh.parent_id = CASE nh.parent_id
    WHEN 170742 THEN 216428
    WHEN 170744 THEN 216429
    WHEN 190435 THEN 216430
    WHEN 190436 THEN 216431
    WHEN 147579 THEN 216432
    WHEN 148339 THEN 216433
    WHEN 170525 THEN 216434
    WHEN 151013 THEN 216435
    WHEN 151869 THEN 216436
    WHEN 151870 THEN 216437
    WHEN 156063 THEN 216438
    WHEN 168489 THEN 216439
    WHEN 168752 THEN 216440
    WHEN 168753 THEN 216441
    WHEN 168754 THEN 216442
    WHEN 168798 THEN 216443
    WHEN 168799 THEN 216444
    WHEN 168800 THEN 216445
    WHEN 190447 THEN 216446
END
WHERE nh.parent_id IN (
    170742, 170744, 190435, 190436, 147579, 148339, 170525, 151013, 
    151869, 151870, 156063, 168489, 168752, 168753, 168754, 168798, 
    168799, 168800, 190447
)
AND nh.id IN (
    -- Get test case IDs that have tcversion_id with 'DROPPED' status
    SELECT DISTINCT nh_tc.id
    FROM nodes_hierarchy nh_tc
    INNER JOIN nodes_hierarchy nh_tcv ON nh_tcv.parent_id = nh_tc.id 
        AND nh_tcv.node_type_id = 4  -- test case version
    INNER JOIN cfield_execution_values cev ON nh_tcv.id = cev.tcversion_id
    WHERE nh_tc.node_type_id = 3  -- test case nodes
    AND cev.field_id = 13
    AND cev.value LIKE '%DROPPED%'
);

-- Query to preview affected test cases before running the update
-- Uncomment the following query to see which test cases will be moved:


SELECT 
    nh.id as testcase_id,
    nh.name as testcase_name,
    nh.parent_id as current_parent_id,
    CASE nh.parent_id
        WHEN 170742 THEN 216428
        WHEN 170744 THEN 216429
        WHEN 190435 THEN 216430
        WHEN 190436 THEN 216431
        WHEN 147579 THEN 216432
        WHEN 148339 THEN 216433
        WHEN 170525 THEN 216434
        WHEN 151013 THEN 216435
        WHEN 151869 THEN 216436
        WHEN 151870 THEN 216437
        WHEN 156063 THEN 216438
        WHEN 168489 THEN 216439
        WHEN 168752 THEN 216440
        WHEN 168753 THEN 216441
        WHEN 168754 THEN 216442
        WHEN 168798 THEN 216443
        WHEN 168799 THEN 216444
        WHEN 168800 THEN 216445
        WHEN 190447 THEN 216446
    END as new_parent_id,
    cev.value as execution_status
FROM nodes_hierarchy nh
INNER JOIN nodes_hierarchy nh_tcv ON nh_tcv.parent_id = nh.id 
    AND nh_tcv.node_type_id = 4  -- test case version
INNER JOIN cfield_execution_values cev ON nh_tcv.id = cev.tcversion_id
WHERE nh.node_type_id = 3  -- test case nodes
AND nh.parent_id IN (
    170742, 170744, 190435, 190436, 147579, 148339, 170525, 151013, 
    151869, 151870, 156063, 168489, 168752, 168753, 168754, 168798, 
    168799, 168800, 190447
)
AND cev.field_id = 13
AND cev.value LIKE '%DROP%'
ORDER BY nh.parent_id, nh.name;
