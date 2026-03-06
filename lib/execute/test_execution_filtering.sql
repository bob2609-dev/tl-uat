-- Test execution date filtering
-- This should show different results for different date ranges

-- Test 1: No date filter (should show all current data)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', NULL, NULL);

-- Test 2: Only executions from 2026-01-28 onwards (should show much fewer executions)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-28', NULL);

-- Test 3: Only executions from 2025-12-01 onwards (should show some executions)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2025-12-01', NULL);

-- Test 4: Only executions from a single day 2025-09-09 (should show many executions from that day)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2025-09-09', '2025-09-09');
