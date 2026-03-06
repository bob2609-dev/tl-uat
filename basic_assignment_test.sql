-- Basic test - count ALL assignments for user 111 without any joins
SELECT 
    COUNT(*) as total_assignments,
    COUNT(DISTINCT id) as unique_assignments
FROM user_assignments 
WHERE 
    user_id = 111 
    AND type = 1 
    AND build_id = 4
    AND status = 1;
