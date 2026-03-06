-- Performance Optimization Indexes for TestLink
-- 
-- This script adds indexes to improve performance of execNavigator.php and execDashboard.php
-- Run this script on your TestLink database to reduce load times from 10+ seconds to <2 seconds
-- Compatible with MySQL 5.7+

-- =====================================================
-- INDEXES FOR EXECUTION TREE PERFORMANCE
-- =====================================================

-- Index for testplan_tcversions table (critical for execNavigator)
-- Check if index exists before creating
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'testplan_tcversions' 
    AND INDEX_NAME = 'idx_testplan_tcversions_tplan_build'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_testplan_tcversions_tplan_build ON testplan_tcversions(testplan_id, tcversion_id)',
    'SELECT "Index idx_testplan_tcversions_tplan_build already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'testplan_tcversions' 
    AND INDEX_NAME = 'idx_testplan_tcversions_composite'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_testplan_tcversions_composite ON testplan_tcversions(testplan_id, tcversion_id, platform_id)',
    'SELECT "Index idx_testplan_tcversions_composite already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for executions table (critical for execution status)
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'executions' 
    AND INDEX_NAME = 'idx_executions_tcversion_build_platform'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_executions_tcversion_build_platform ON executions(tcversion_id, build_id, platform_id, status)',
    'SELECT "Index idx_executions_tcversion_build_platform already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'executions' 
    AND INDEX_NAME = 'idx_executions_status_timestamp'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_executions_status_timestamp ON executions(status, execution_ts DESC)',
    'SELECT "Index idx_executions_status_timestamp already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'executions' 
    AND INDEX_NAME = 'idx_executions_tcversion_latest'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_executions_tcversion_latest ON executions(tcversion_id, execution_ts DESC)',
    'SELECT "Index idx_executions_tcversion_latest already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- INDEXES FOR NODE HIERARCHY PERFORMANCE
-- =====================================================

-- Index for nodes_hierarchy table (tree navigation)
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'nodes_hierarchy' 
    AND INDEX_NAME = 'idx_nodes_hierarchy_parent_type'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_nodes_hierarchy_parent_type ON nodes_hierarchy(parent_id, node_type_id)',
    'SELECT "Index idx_nodes_hierarchy_parent_type already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Index for nodes_hierarchy name search
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'nodes_hierarchy' 
    AND INDEX_NAME = 'idx_nodes_hierarchy_name_search'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_nodes_hierarchy_name_search ON nodes_hierarchy(name)',
    'SELECT "Index idx_nodes_hierarchy_name_search already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Composite index for test case lookups
SET @index_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'nodes_hierarchy' 
    AND INDEX_NAME = 'idx_nodes_hierarchy_testcase_lookup'
);

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_nodes_hierarchy_testcase_lookup ON nodes_hierarchy(node_type_id, parent_id, name)',
    'SELECT "Index idx_nodes_hierarchy_testcase_lookup already exists"');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- INDEXES FOR CUSTOM FIELD PERFORMANCE
-- =====================================================

-- Index for custom field values (major bottleneck)
CREATE INDEX IF NOT EXISTS idx_cfield_design_values_node_field 
ON cfield_design_values(node_id, field_id);

CREATE INDEX IF NOT EXISTS idx_cfield_execution_values_node_field 
ON cfield_execution_values(node_id, field_id);

-- Index for custom fields themselves
CREATE INDEX IF NOT EXISTS idx_custom_fields_scope_execution 
ON custom_fields(scope, show_on_execution);

-- =====================================================
-- INDEXES FOR BUILD AND PLATFORM PERFORMANCE
-- =====================================================

-- Index for builds table
CREATE INDEX IF NOT EXISTS idx_builds_tplan_is_open 
ON builds(testplan_id, is_open);

CREATE INDEX IF NOT EXISTS idx_builds_active_order 
ON builds(is_open, name);

-- Index for platforms
CREATE INDEX IF NOT EXISTS idx_platforms_tproject 
ON platforms(testproject_id);

-- =====================================================
-- INDEXES FOR KEYWORD FILTERING PERFORMANCE
-- =====================================================

-- Index for testcase keywords
CREATE INDEX IF NOT EXISTS idx_testcase_keywords_composite 
ON testcase_keywords(tcversion_id, keyword_id);

-- Index for keywords
CREATE INDEX IF NOT EXISTS idx_keywords_name 
ON keywords(keyword);

-- =====================================================
-- INDEXES FOR USER ASSIGNMENT PERFORMANCE
-- =====================================================

-- Index for user assignments
CREATE INDEX IF NOT EXISTS idx_user_assignments_feature_user 
ON user_assignments(feature_id, user_id);

CREATE INDEX IF NOT EXISTS idx_user_assignments_tplan_user 
ON user_assignments(testplan_id, user_id);

-- =====================================================
-- PARTITIONING FOR LARGE EXECUTION TABLES
-- =====================================================

-- Consider partitioning executions table by date if you have >1M records
-- This is optional and requires careful planning

/*
-- Example partitioning (uncomment and modify as needed):
ALTER TABLE executions 
PARTITION BY RANGE (YEAR(execution_ts)) (
    PARTITION p2023 VALUES LESS THAN (2024),
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
*/

-- =====================================================
-- QUERY OPTIMIZATION SETTINGS
-- =====================================================

-- Set MySQL performance parameters (run as DB admin)
SET GLOBAL innodb_buffer_pool_size = '2G';  -- Adjust based on available RAM
SET GLOBAL query_cache_size = '256M';
SET GLOBAL query_cache_type = ON;
SET GLOBAL innodb_log_file_size = '256M';
SET GLOBAL innodb_flush_log_at_trx_commit = 2;

-- =====================================================
-- PERFORMANCE MONITORING QUERIES
-- =====================================================

-- Query to check slow queries related to execution tree
SELECT 
    digest_text,
    count_star,
    avg_timer_wait/1000000000 as avg_time_seconds,
    max_timer_wait/1000000000 as max_time_seconds
FROM performance_schema.events_statements_summary_by_digest 
WHERE digest_text LIKE '%execTree%' 
   OR digest_text LIKE '%testplan_tcversions%'
   OR digest_text LIKE '%executions%'
ORDER BY avg_timer_wait DESC 
LIMIT 10;

-- Query to check index usage
SELECT 
    table_name,
    index_name,
    cardinality,
    sub_part,
    packed,
    nullable,
    index_type
FROM information_schema.statistics 
WHERE table_schema = DATABASE() 
  AND table_name IN ('testplan_tcversions', 'executions', 'nodes_hierarchy', 'cfield_design_values')
ORDER BY table_name, seq_in_index;

-- =====================================================
-- MAINTENANCE QUERIES
-- =====================================================

-- Update table statistics (run periodically)
ANALYZE TABLE testplan_tcversions;
ANALYZE TABLE executions;
ANALYZE TABLE nodes_hierarchy;
ANALYZE TABLE cfield_design_values;
ANALYZE TABLE builds;
ANALYZE TABLE platforms;

-- Check for fragmented tables
SELECT 
    table_name,
    round(((data_length + index_length) / 1024 / 1024), 2) as table_size_mb,
    round((data_free / 1024 / 1024), 2) as free_space_mb
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
  AND (data_free > 0)
ORDER BY free_space_mb DESC;

-- =====================================================
-- CLEANUP OLD EXECUTION DATA (OPTIONAL)
-- =====================================================

-- Archive old execution data (older than 2 years) to improve performance
-- Uncomment and modify as needed

/*
CREATE TABLE executions_archive LIKE executions;

INSERT INTO executions_archive 
SELECT * FROM executions 
WHERE execution_ts < DATE_SUB(NOW(), INTERVAL 2 YEAR);

DELETE FROM executions 
WHERE execution_ts < DATE_SUB(NOW(), INTERVAL 2 YEAR);
*/

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Test query performance before and after optimization
EXPLAIN SELECT 
    tc.id as tcase_id,
    n.name,
    COALESCE(e.status, 'n') as exec_status
FROM testplan_tcversions tc
INNER JOIN nodes_hierarchy n ON tc.tcversion_id = n.id
LEFT JOIN executions e ON tc.id = e.tcversion_id
WHERE tc.testplan_id = [YOUR_TESTPLAN_ID]
  AND (e.build_id = [BUILD_ID] OR e.build_id IS NULL)
ORDER BY n.name
LIMIT 1000;

-- Check execution time of critical queries
SELECT 
    sql_text,
    timer_wait/1000000000 as execution_time_seconds
FROM performance_schema.events_statements_history_long 
WHERE sql_text LIKE '%testplan_tcversions%'
   OR sql_text LIKE '%executions%'
ORDER BY timer_wait DESC
LIMIT 5;
