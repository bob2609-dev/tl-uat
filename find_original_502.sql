-- Try to reproduce your original 502 result
SELECT 
    'Original style: user_id=111, type=1, build_id=4' as query_type,
    COUNT(*) as count
FROM user_assignments u 
WHERE u.user_id = 111 AND u.type = 1 AND u.build_id = 4

UNION ALL

SELECT 
    'Without status filter: user_id=111, type=1, build_id=4' as query_type,
    COUNT(*) as count
FROM user_assignments u 
WHERE u.user_id = 111 AND u.type = 1 AND u.build_id = 4

UNION ALL

SELECT 
    'All types: user_id=111, build_id=4' as query_type,
    COUNT(*) as count
FROM user_assignments u 
WHERE u.user_id = 111 AND u.build_id = 4

UNION ALL

SELECT 
    'All builds: user_id=111, type=1' as query_type,
    COUNT(*) as count
FROM user_assignments u 
WHERE u.user_id = 111 AND u.type = 1

UNION ALL

SELECT 
    'Everything for user 111' as query_type,
    COUNT(*) as count
FROM user_assignments u 
WHERE u.user_id = 111;
