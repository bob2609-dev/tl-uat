-- Check actual structure of tcversions table
DESCRIBE tcversions;

-- Check what columns actually exist in tcversions
SELECT 
    'tcversions columns' as info,
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'tl_uat' 
    AND TABLE_NAME = 'tcversions'
ORDER BY ORDINAL_POSITION;

-- Check sample data from tcversions
SELECT 
    'tcversions sample data' as info,
    *
FROM tcversions
LIMIT 5;

-- Check sample data from testplan_tcversions
SELECT 
    'testplan_tcversions sample data' as info,
    *
FROM testplan_tcversions
LIMIT 5;

-- Try to find the relationship by looking at overlapping IDs
SELECT 
    'ID overlap analysis' as info,
    COUNT(*) as tcversion_count,
    MIN(id) as min_id,
    MAX(id) as max_id
FROM tcversions;

SELECT 
    'testplan_tcversions ID analysis' as info,
    COUNT(*) as tptc_count,
    MIN(tcversion_id) as min_tcversion_id,
    MAX(tcversion_id) as max_tcversion_id
FROM testplan_tcversions;

-- Check if tcversions.id matches testplan_tcversions.tcversion_id
SELECT 
    'Direct ID match' as info,
    COUNT(*) as matching_count
FROM tcversions tv
WHERE tv.id IN (SELECT tcversion_id FROM testplan_tcversions);
