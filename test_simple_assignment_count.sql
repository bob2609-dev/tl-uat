-- Simplified test - just count assignments for user 111 with minimal joins
SELECT 
    COUNT(DISTINCT ua.id) as total_assigned
FROM 
    users u
    LEFT JOIN user_assignments ua ON ua.user_id = u.id 
        AND ua.type IN (1, 2) 
        AND ua.status = 1
        AND ua.build_id = 4
WHERE 1=1 
    AND u.active = 1
    AND u.id = 111;
