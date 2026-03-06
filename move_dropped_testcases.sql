-- Move test cases with 'DROPPED' status to new parent IDs
-- Based on parent_change.csv mapping and cfield_execution_values table
-- Field ID 13 represents the status field where 'DROPPED' values are stored

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
WHERE 
    -- Only update nodes with parent IDs that need to be changed
    nh.parent_id IN (
        170742, 170744, 190435, 190436, 147579, 148339, 170525, 
        151013, 151869, 151870, 156063, 168489, 168752, 168753, 
        168754, 168798, 168799, 168800, 190447
    )
    -- Only move test cases that have tcversion_id with 'DROPPED' status
    AND nh.id IN (
        SELECT DISTINCT tcv.parent_id
        FROM tcversions tcv
        WHERE tcv.id IN (
            SELECT cev.tcversion_id
            FROM cfield_execution_values cev
            WHERE cev.field_id = 13
            AND cev.value LIKE '%DROPPED%'
        )
    );

-- Optional: Show count of affected records before running the update
SELECT COUNT(*) as affected_records
FROM nodes_hierarchy nh
WHERE nh.parent_id IN (
    170742, 170744, 190435, 190436, 147579, 148339, 170525, 
    151013, 151869, 151870, 156063, 168489, 168752, 168753, 
    168754, 168798, 168799, 168800, 190447
)
AND nh.id IN (
    SELECT DISTINCT tcv.parent_id
    FROM tcversions tcv
    WHERE tcv.id IN (
        SELECT cev.tcversion_id
        FROM cfield_execution_values cev
        WHERE cev.field_id = 13
        AND cev.value LIKE '%DROPPED%'
    )
);
