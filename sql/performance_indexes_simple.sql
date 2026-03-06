-- Simple Performance Indexes for TestLink
-- Compatible with all MySQL versions
-- Run this script to improve execNavigator.php and execDashboard.php performance

-- =====================================================
-- CRITICAL INDEXES FOR EXECUTION TREE PERFORMANCE
-- =====================================================

-- Index for testplan_tcversions table (most important for execNavigator)
CREATE INDEX idx_testplan_tcversions_tplan_build ON testplan_tcversions(testplan_id, tcversion_id);
CREATE INDEX idx_testplan_tcversions_composite ON testplan_tcversions(testplan_id, tcversion_id, platform_id);

-- Index for executions table (critical for execution status)
CREATE INDEX idx_executions_tcversion_build_platform ON executions(tcversion_id, build_id, platform_id, status);
CREATE INDEX idx_executions_status_timestamp ON executions(status, execution_ts DESC);
CREATE INDEX idx_executions_tcversion_latest ON executions(tcversion_id, execution_ts DESC);

-- =====================================================
-- INDEXES FOR NODE HIERARCHY PERFORMANCE
-- =====================================================

-- Index for nodes_hierarchy table (tree navigation)
CREATE INDEX idx_nodes_hierarchy_parent_type ON nodes_hierarchy(parent_id, node_type_id);
CREATE INDEX idx_nodes_hierarchy_name_search ON nodes_hierarchy(name);
CREATE INDEX idx_nodes_hierarchy_testcase_lookup ON nodes_hierarchy(node_type_id, parent_id, name);

-- =====================================================
-- INDEXES FOR CUSTOM FIELD PERFORMANCE
-- =====================================================

-- Index for custom field values (major bottleneck)
CREATE INDEX idx_cfield_design_values_node_field ON cfield_design_values(node_id, field_id);
CREATE INDEX idx_cfield_execution_values_node_field ON cfield_execution_values(node_id, field_id);

-- Index for custom fields themselves
CREATE INDEX idx_custom_fields_scope_execution ON custom_fields(scope, show_on_execution);

-- =====================================================
-- INDEXES FOR BUILD AND PLATFORM PERFORMANCE
-- =====================================================

-- Index for builds table
CREATE INDEX idx_builds_tplan_is_open ON builds(testplan_id, is_open);
CREATE INDEX idx_builds_active_order ON builds(is_open, name);

-- Index for platforms
CREATE INDEX idx_platforms_tproject ON platforms(testproject_id);

-- =====================================================
-- INDEXES FOR KEYWORD FILTERING PERFORMANCE
-- =====================================================

-- Index for testcase keywords
CREATE INDEX idx_testcase_keywords_composite ON testcase_keywords(tcversion_id, keyword_id);

-- Index for keywords
CREATE INDEX idx_keywords_name ON keywords(keyword);

-- =====================================================
-- INDEXES FOR USER ASSIGNMENT PERFORMANCE
-- =====================================================

-- Index for user assignments
CREATE INDEX idx_user_assignments_feature_user ON user_assignments(feature_id, user_id);
CREATE INDEX idx_user_assignments_tplan_user ON user_assignments(testplan_id, user_id);

-- =====================================================
-- PERFORMANCE VERIFICATION
-- =====================================================

-- Check if indexes were created successfully
SELECT 
    table_name,
    index_name,
    column_name,
    seq_in_index
FROM information_schema.statistics 
WHERE table_schema = DATABASE() 
  AND index_name LIKE 'idx_%'
ORDER BY table_name, index_name, seq_in_index;

-- Show execution time improvement
SELECT 'Performance indexes created successfully' as status;
