-- Test queries to compare with UI results
-- Run these in your IDE and compare with the UI output

-- Test 1: No date filtering (should show all current data)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', NULL, NULL, FALSE);

-- Test 2: Point-in-time as of 2026-01-29 (should show Muumini with 4 executions)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', NULL, '2026-01-29', FALSE);

-- Test 3: Date range 2026-01-29 to 2026-01-29 (should show Muumini with 4 executions)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-29', '2026-01-29', FALSE);

-- Test 4: Date range 2025-09-09 to 2025-09-09 (should show many executions)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2025-09-09', '2025-09-09', FALSE);

-- Test 5: Date range 2026-01-28 to 2026-01-28 (should show Pauline with 1 execution)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-28', '2026-01-28', FALSE);

-- Test 6: Point-in-time as of 2025-12-01 (should show executions up to Dec 1, 2025)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', NULL, '2025-12-01', FALSE);

-- Test 7: Hide zero executions - Date range 2026-01-29 (should show only Muumini)
CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-29', '2026-01-29', TRUE);
