-- Compare different query criteria
SELECT 
    'Query A: user_id=111, type=1, build_id=4, status=1' as query_type,
    COUNT(*) as count
FROM user_assignments 
WHERE user_id = 111 AND type = 1 AND build_id = 4 AND status = 1

UNION ALL

SELECT 
    'Query B: user_id=111, type=1, build_id=4 (no status filter)' as query_type,
    COUNT(*) as count
FROM user_assignments 
WHERE user_id = 111 AND type = 1 AND build_id = 4

UNION ALL

SELECT 
    'Query C: user_id=111, type IN (1,2), build_id=4, status=1' as query_type,
    COUNT(*) as count
FROM user_assignments 
WHERE user_id = 111 AND type IN (1,2) AND build_id = 4 AND status = 1

UNION ALL

SELECT 
    'Query D: user_id=111, type=1, build_id IN (0,4), status=1' as query_type,
    COUNT(*) as count
FROM user_assignments 
WHERE user_id = 111 AND type = 1 AND build_id IN (0,4) AND status = 1;
