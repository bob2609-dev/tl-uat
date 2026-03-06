# Test Execution Summary View Optimization Documentation

## Overview
This document describes the optimizations made to the test execution summary view in TestLink. The original implementation was experiencing performance issues when querying large test execution datasets due to complex recursive CTEs, excessive subqueries, and heavy string operations.

## Key Optimizations

### 1. Materialized Path Table
- Created a new `node_hierarchy_paths` table that precomputes and stores hierarchy paths
- Eliminates the need to recalculate paths on every query using recursive CTEs
- Includes hierarchy levels as individual columns (level1_id, level1_name, etc.)
- Has appropriate indexes for efficient lookups

### 2. Optimized SQL View
- Created a new SQL view `test_execution_hierarchy_summary_optimized` that joins with the materialized path table
- Removed all subqueries and string operations from the original views
- Efficiently retrieves hierarchy data without recursive CTE calculations
- Added strategic indexes on commonly filtered columns

### 3. PHP Code Optimizations
- Updated PHP code to use the optimized SQL view
- Added index hints to guide the query optimizer
- Implemented pagination to reduce memory usage and improve response times
- Enhanced logging for performance monitoring

### 4. UI Improvements
- Added pagination controls to the template
- Improved CSS styling for better user experience
- Added proper status styling and indicators

## Files Modified

1. **SQL Files**:
   - `testlink_test_execution_summary_view_optimized.sql`: New optimized SQL view and materialized path table

2. **PHP Files**:
   - `lib/execute/test_execution_summary_copy.php`: Updated to use optimized view, added pagination, index hints

3. **Template Files**:
   - `gui/templates/tl-classic/execute/test_execution_summary_copy.tpl`: Added pagination controls and styling

## Maintenance Recommendations

### 1. Materialized Path Table Refresh
The materialized path table (`node_hierarchy_paths`) needs to be kept in sync with changes to the test suite hierarchy. Consider implementing one of these approaches:

- **Scheduled Refresh**: Run the refresh SQL script on a regular schedule (daily/weekly)
- **Trigger-Based**: Add database triggers to update the materialized paths when hierarchy changes occur
- **Application-Triggered**: Update paths when the application modifies the hierarchy

Example refresh script:
```sql
TRUNCATE TABLE node_hierarchy_paths;
INSERT INTO node_hierarchy_paths
WITH RECURSIVE node_hierarchy_path(id, name, parent_id, path, level) AS (
    -- SQL from the materialized path table creation script
)
SELECT /* Columns from the insert script */;
```

### 2. Index Optimization
Regularly analyze index usage and performance:

```sql
ANALYZE TABLE node_hierarchy_paths;
ANALYZE TABLE executions;
```

Monitor index usage with:
```sql
SELECT index_name, table_name, stat_name, stat_value 
FROM mysql.innodb_index_stats 
WHERE table_name IN ('node_hierarchy_paths', 'executions')
AND stat_name = 'n_leaf_pages';
```

### 3. Performance Monitoring
- Review the logs at `lib/execute/test_execution_summary_query.log` to monitor query performance
- Consider adding `EXPLAIN` output to the logs for deeper insights
- Adjust pagination size based on server capacity and user requirements

### 4. Future Enhancements
- Consider implementing AJAX-based pagination for smoother UX
- Add ability to sort by different columns
- Implement search functionality to further filter results
- Consider adding more detailed execution statistics and visualizations

## Performance Impact
The optimized implementation should significantly improve query performance:

- Elimination of recursive CTEs reduces computational complexity
- Precomputed paths eliminate expensive string operations
- Strategic indexes speed up filtering operations
- Pagination reduces memory usage and response times

## Testing Notes
Before deploying to production, test thoroughly with large datasets and monitor:

1. Query execution time before and after optimization
2. Server memory usage during page loads
3. Database CPU usage during queries
4. Overall page load times
