-- Performance test script to compare execution times

-- Test 1: Simple query to check basic execution time
SELECT 'Starting performance test...' AS status;

-- Test 2: Test the optimized stored procedure with timing
SET @start_time = NOW(3);

CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-28', '2026-01-28', FALSE);

SET @end_time = NOW(3);
SET @execution_time = TIMESTAMPDIFF(MICROSECOND, @start_time, @end_time) / 1000;

SELECT CONCAT('Optimized procedure execution time: ', @execution_time, ' ms') AS performance_result;

-- Test 3: Test with hide zero executions
SET @start_time = NOW(3);

CALL sp_tester_execution_report_historical(1, NULL, NULL, NULL, 'all', '2026-01-28', '2026-01-28', TRUE);

SET @end_time = NOW(3);
SET @execution_time = TIMESTAMPDIFF(MICROSECOND, @start_time, @end_time) / 1000;

SELECT CONCAT('Optimized procedure with hide zero executions execution time: ', @execution_time, ' ms') AS performance_result;

-- Test 4: Test with specific tester (Berry)
SET @start_time = NOW(3);

CALL sp_tester_execution_report_historical(1, NULL, NULL, 15, 'all', '2026-01-28', '2026-01-28', FALSE);

SET @end_time = NOW(3);
SET @execution_time = TIMESTAMPDIFF(MICROSECOND, @start_time, @end_time) / 1000;

SELECT CONCAT('Optimized procedure for specific tester execution time: ', @execution_time, ' ms') AS performance_result;
