-- Test historical date filtering
-- Test 1: Show only executions from 2026-01-28 onwards
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-28', NULL);

-- Test 2: Show executions from January 2026 only  
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-01', '2026-01-31');

-- Test 3: Show executions from 2025-09-09 only (should show many results)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2025-09-09', '2025-09-09');
