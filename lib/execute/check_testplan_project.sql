-- Check which test project testplan_id = 242100 belongs to
SELECT 
    tp.id as testplan_id,
    tp.notes as testplan_name,
    tp.testproject_id,
    tproj.notes as testproject_name
FROM testplans tp
JOIN testprojects tproj ON tp.testproject_id = tproj.id
WHERE tp.id = 242100;

-- Also check which test projects are available
SELECT 
    id,
    notes as name,
    notes
FROM testprojects 
ORDER BY id;
