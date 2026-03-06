-- Batch Update Test Case Priority Scripts
-- This file contains two scripts for updating test case priorities from a source table
-- Source table columns: tc_id (node_id), tc_priority (value)
-- Target table: cfield_design_values (field_id=15 for priority)

-- ========================================
-- SCRIPT 1: INSERT ONLY (Skip existing records)
-- If node_id is not in cfield_design_values then insert, if exists then skip
-- ========================================

-- Insert only new records (skip existing ones)
INSERT INTO cfield_design_values (field_id, node_id, value)
SELECT 
    15 as field_id,  -- Priority field ID
    tc_id as node_id, 
    tc_priority as value
FROM source_table s
WHERE NOT EXISTS (
    SELECT 1 
    FROM cfield_design_values c 
    WHERE c.field_id = 15 
    AND c.node_id = s.tc_id
);

-- Display results
SELECT 
    'Script 1 - Insert Only Results' as operation,
    COUNT(*) as records_processed
FROM source_table s
WHERE NOT EXISTS (
    SELECT 1 
    FROM cfield_design_values c 
    WHERE c.field_id = 15 
    AND c.node_id = s.tc_id
);

-- ========================================
-- SCRIPT 2: INSERT OR UPDATE (UPSERT)
-- If node_id is not in cfield_design_values then insert, if exists then update
-- ========================================

-- Method 1: Using ON DUPLICATE KEY UPDATE (requires unique constraint)
-- Note: This requires a unique constraint on (field_id, node_id)
-- ALTER TABLE cfield_design_values ADD UNIQUE KEY unique_field_node (field_id, node_id);

-- Current version (will show deprecation warning):
INSERT INTO cfield_design_values (field_id, node_id, value)
SELECT 
    15 as field_id,  -- Priority field ID
    tc_id as node_id, 
    tc_priority as value
FROM source_table s
ON DUPLICATE KEY UPDATE 
    value = VALUES(value);

-- Alternative version (compatible with older MySQL versions):
INSERT INTO cfield_design_values (field_id, node_id, value)
SELECT 
    15 as field_id,  -- Priority field ID
    tc_id as node_id, 
    tc_priority as value
FROM source_table s
ON DUPLICATE KEY UPDATE 
    value = s.tc_priority;

-- Alternative Method 2: Using separate UPDATE and INSERT statements
-- (works without unique constraint)

-- First, update existing records
UPDATE cfield_design_values c
INNER JOIN source_table s ON c.node_id = s.tc_id
SET c.value = s.tc_priority
WHERE c.field_id = 15;

-- Then, insert new records
INSERT INTO cfield_design_values (field_id, node_id, value)
SELECT 
    15 as field_id,  -- Priority field ID
    tc_id as node_id, 
    tc_priority as value
FROM source_table s
WHERE NOT EXISTS (
    SELECT 1 
    FROM cfield_design_values c 
    WHERE c.field_id = 15 
    AND c.node_id = s.tc_id
);

-- Display results
SELECT 
    'Script 2 - Insert or Update Results' as operation,
    COUNT(*) as total_records_processed
FROM source_table;

-- ========================================
-- HELPER QUERIES (for verification)
-- ========================================

-- Check source table data
SELECT 
    'Source Table Preview' as info,
    tc_id,
    tc_priority
FROM source_table 
LIMIT 10;

-- Check target table before update
SELECT 
    'Target Table Before Update' as info,
    field_id,
    node_id,
    value
FROM cfield_design_values 
WHERE field_id = 15
ORDER BY node_id;

-- Check target table after update
SELECT 
    'Target Table After Update' as info,
    field_id,
    node_id,
    value
FROM cfield_design_values 
WHERE field_id = 15
ORDER BY node_id;

-- Count records in target table
SELECT 
    'Target Table Record Count' as info,
    COUNT(*) as total_priority_records
FROM cfield_design_values 
WHERE field_id = 15;

-- ========================================
-- USAGE INSTRUCTIONS
-- ========================================

/*
To use these scripts:

1. Replace 'source_table' with your actual source table name
2. For Script 1: Run only the INSERT statement to add new records only
3. For Script 2: 
   - Option A: Use ON DUPLICATE KEY UPDATE (requires unique constraint)
   - Option B: Use UPDATE + INSERT approach (works without constraints)
4. Run the helper queries to verify results

Example usage:
-- Replace source_table with your actual table name
-- e.g., FROM testcase_import_data WHERE tc_priority IS NOT NULL

Note: Make sure your source table has the columns:
- tc_id (maps to node_id)
- tc_priority (maps to value column)
*/
