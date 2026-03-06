-- Check executions around 2026-01-28 to verify the date filtering
SELECT 
    u.first,
    u.last,
    e.execution_ts,
    e.status,
    COUNT(*) as execution_count
FROM executions e
JOIN users u ON e.tester_id = u.id
WHERE DATE(e.execution_ts) >= '2026-01-27'
  AND DATE(e.execution_ts) <= '2026-01-29'
GROUP BY u.id, u.first, u.last, DATE(e.execution_ts), e.status
ORDER BY e.execution_ts DESC
LIMIT 20;

-- Also check total executions in January 2026
SELECT 
    COUNT(*) as total_executions_jan_2026,
    MIN(DATE(execution_ts)) as earliest_jan,
    MAX(DATE(execution_ts)) as latest_jan
FROM executions 
WHERE DATE(execution_ts) >= '2026-01-01' 
  AND DATE(execution_ts) <= '2026-01-31';
