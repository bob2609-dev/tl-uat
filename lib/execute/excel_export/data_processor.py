#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Data processor for TestLink test execution export script.
This module handles SQL queries and data processing for test execution data.
"""

from db_connector import DatabaseConnector
import re


class TestExecutionDataProcessor:
    """
    Class for processing test execution data from the database.
    """
    def __init__(self, db_connector=None):
        """Initialize the data processor with a database connector."""
        self.db = db_connector
        
        # Check if database connection is established
        # Handle both real and mock DB connectors
        if hasattr(self.db, 'is_connected'):
            # For direct method on connector (mock case)
            if not self.db.is_connected():
                print("Error: Database connection not established.")
                return
        elif hasattr(self.db, 'connection') and hasattr(self.db.connection, 'is_connected'):
            # For method on connection object (real case)
            if not self.db.connection or not self.db.connection.is_connected():
                print("Error: Database connection not established.")
                return
        else:
            # For simple boolean connection flag (alternative mock case)
            if hasattr(self.db, 'connection') and not self.db.connection:
                print("Error: Database connection not established.")
                return

    def strip_tags(self, html_text):
        """Strip HTML tags from text using regex and decode common HTML entities."""
        if not html_text:
            return ""
        
        try:
            # Convert to string if not already
            text = str(html_text)
            
            # Remove HTML tags
            text = re.sub(r'<[^>]*>', '', text)
            
            # Replace common HTML entities
            html_entities = {
                '&lt;': '<',
                '&gt;': '>',
                '&amp;': '&',
                '&quot;': '"',
                '&apos;': "'",
                '&nbsp;': ' ',
                '&#39;': "'"
            }
            
            for entity, replacement in html_entities.items():
                text = text.replace(entity, replacement)
            
            # Handle numeric entities (like &#123;)
            text = re.sub(r'&#(\d+);', lambda m: chr(int(m.group(1))), text)
            
            return text.strip()
        except Exception as e:
            print(f"Warning: Error stripping HTML tags: {e}")
            # Fallback to basic regex stripping
            return re.sub(r'<[^>]*>', '', str(html_text)).strip()

    def get_metrics_query(self, filters=None):
        """
        Build a CTE-based metrics query similar to the PHP implementation in test_execution_summary.php.
        This provides accurate test case counts and status aggregation.
        
        Args:
            filters (dict, optional): Query filters
            
        Returns:
            tuple: (query_string, params_list)
        """
        filters = filters or {}
        params = []
        
        # Build CTE-based query exactly like the PHP version
        query = """
        WITH all_testcase_versions AS (
            -- Get all test case versions from the nodes_hierarchy and tcversions tables
            SELECT 
                tcv.id AS tcversion_id,
                tc.id AS tc_id, 
                tc.parent_id AS suite_id,
                tcversion.version,
                tcversion.tc_external_id
            FROM 
                nodes_hierarchy tcv
                JOIN nodes_hierarchy tc ON tc.id = tcv.parent_id
                JOIN tcversions tcversion ON tcversion.id = tcv.id
            WHERE 
                tcv.node_type_id = 4  -- Test case versions
                AND tc.node_type_id = 3  -- Test cases
                AND tcversion.active = 1
        ),
        
        -- Get the latest execution for each test case version if it exists
        latest_executions AS (
            SELECT 
                atcv.tcversion_id,
                atcv.tc_id,
                atcv.suite_id,
                e.status,
                e.testplan_id,
                tp.testproject_id,
                tp.notes AS testplan_notes,
                tproj.notes AS project_notes,
                parent_nh.name AS suite_name
            FROM 
                all_testcase_versions atcv
            LEFT JOIN (
                -- Get the latest execution for each test case version
                SELECT 
                    e.tcversion_id, 
                    e.status,
                    e.testplan_id,
                    e.build_id
                FROM 
                    executions e
                JOIN (
                    SELECT 
                        tcversion_id, 
                        build_id, 
                        testplan_id, 
                        MAX(execution_ts) AS latest_exec_ts
                    FROM 
                        executions
                    GROUP BY 
                        tcversion_id, build_id, testplan_id
                ) latest ON e.tcversion_id = latest.tcversion_id 
                  AND e.build_id = latest.build_id 
                  AND e.testplan_id = latest.testplan_id 
                  AND e.execution_ts = latest.latest_exec_ts
                WHERE 1=1
        """
        
        # Apply filters to executions
        if 'project_id' in filters and filters['project_id'] > 0:
            query += " AND e.testplan_id IN (SELECT id FROM testplans WHERE testproject_id = %s)"
            params.append(filters['project_id'])
            
        if 'testplan_id' in filters and filters['testplan_id'] > 0:
            query += " AND e.testplan_id = %s"
            params.append(filters['testplan_id'])
            
        if 'build_id' in filters and filters['build_id'] > 0:
            query += " AND e.build_id = %s"
            params.append(filters['build_id'])
            
        if 'status' in filters and filters['status']:
            query += " AND e.status = %s"
            params.append(filters['status'])
        
        # Continue the query to join executions to test cases
        query += """
            ) e ON atcv.tcversion_id = e.tcversion_id
            LEFT JOIN testplans tp ON e.testplan_id = tp.id
            LEFT JOIN testprojects tproj ON tp.testproject_id = tproj.id
            LEFT JOIN nodes_hierarchy parent_nh ON atcv.suite_id = parent_nh.id
        )
        
        -- Main query to aggregate metrics by suite
        SELECT 
            suite_id,
            suite_name,
            testplan_id,
            testplan_notes,
            testproject_id,
            project_notes,
            COUNT(DISTINCT tc_id) as testcase_count,
            SUM(CASE WHEN status = 'p' THEN 1 ELSE 0 END) as passed_count,
            SUM(CASE WHEN status = 'f' THEN 1 ELSE 0 END) as failed_count,
            SUM(CASE WHEN status = 'b' THEN 1 ELSE 0 END) as blocked_count,
            SUM(CASE WHEN status IS NULL OR status = 'n' THEN 1 ELSE 0 END) as not_run_count
        FROM
            latest_executions
        GROUP BY
            suite_id, suite_name, testplan_id, testplan_notes, testproject_id, project_notes
        ORDER BY
            project_notes, testplan_notes, suite_name
        """
        
        return query, params
    
    def get_execution_summary(self, filters=None):
        """
        Get test execution summary data with optional filters.
        
        Args:
            filters (dict, optional): Filters for the query
                - project_id: Project ID
                - testplan_id: Test Plan ID
                - build_id: Build ID
                - status: Status ('p', 'f', 'b', 'n')
                - start_date: Start date (YYYY-MM-DD) - ignored as per request
                - end_date: End date (YYYY-MM-DD) - ignored as per request
                
        Returns:
            dict: Hierarchical execution data
        """
        # First, refresh the node hierarchy paths to ensure they're up-to-date
        try:
            refresh_sql = "CALL refresh_node_hierarchy_paths_v2()"
            self.db.execute_query(refresh_sql)
            print("Successfully refreshed node hierarchy paths")
        except Exception as e:
            print(f"Warning: Error refreshing node hierarchy paths: {e}")
            # Continue anyway as the paths may already be updated
        
        # Default filters
        filters = filters or {}
        
        # Build the base SQL query with precomputed path hierarchy from node_hierarchy_paths_v2 table
        query = """
        SELECT 
            e.id AS execution_id,
            e.status AS execution_status,
            e.testplan_id,
            tp.notes AS testplan_notes,
            e.build_id,
            b.name AS build_name,
            b.notes AS build_notes,
            e.platform_id,
            p.name AS platform_name,
            p.notes AS platform_notes,
            e.tcversion_id,
            tcv.version AS tc_version,
            tcv.summary AS tc_summary,
            nh_tc.id AS tc_id,
            nh_tc.name AS tc_name,
            parent_nh.id AS parent_suite_id,
            parent_nh.name AS parent_suite_name,
            -- Use the full_path column directly from node_hierarchy_paths_v2 table
            -- Do NOT include the test case name in the path - only include suite hierarchy
            (SELECT full_path FROM node_hierarchy_paths_v2 WHERE node_id = nh_tc.parent_id) AS full_path,
            e.execution_ts AS execution_timestamp,
            e.tester_id,
            u.login AS tester_login,
            u.first AS tester_firstname,
            u.last AS tester_lastname,
            tp.testproject_id AS project_id,
            tproj.notes AS project_notes
        FROM 
            executions e
            JOIN (SELECT tcversion_id, build_id, testplan_id, MAX(execution_ts) AS latest_exec_ts
                  FROM executions
                  GROUP BY tcversion_id, build_id, testplan_id) latest_e 
                ON e.tcversion_id = latest_e.tcversion_id 
                AND e.build_id = latest_e.build_id 
                AND e.testplan_id = latest_e.testplan_id 
                AND e.execution_ts = latest_e.latest_exec_ts
            JOIN tcversions tcv ON e.tcversion_id = tcv.id
            JOIN nodes_hierarchy nh_tcv ON tcv.id = nh_tcv.id
            JOIN nodes_hierarchy nh_tc ON nh_tcv.parent_id = nh_tc.id
            JOIN testplans tp ON e.testplan_id = tp.id
            JOIN builds b ON e.build_id = b.id
            LEFT JOIN platforms p ON e.platform_id = p.id
            LEFT JOIN users u ON e.tester_id = u.id
            LEFT JOIN nodes_hierarchy parent_nh ON nh_tc.parent_id = parent_nh.id
            JOIN testprojects tproj ON tp.testproject_id = tproj.id
        WHERE 1=1
        """
        
        # Apply filters
        params = []
        if 'project_id' in filters and filters['project_id'] > 0:
            query += " AND tp.testproject_id = %s"
            params.append(filters['project_id'])
            
        if 'testplan_id' in filters and filters['testplan_id'] > 0:
            query += " AND e.testplan_id = %s"
            params.append(filters['testplan_id'])
            
        if 'build_id' in filters and filters['build_id'] > 0:
            query += " AND e.build_id = %s"
            params.append(filters['build_id'])
            
        if 'status' in filters and filters['status']:
            query += " AND e.status = %s"
            params.append(filters['status'])
            
        # Date filters are ignored as per user request
        # if 'start_date' in filters and filters['start_date']:
        #     query += " AND e.execution_ts >= %s"
        #     params.append(f"{filters['start_date']} 00:00:00")
        #     
        # if 'end_date' in filters and filters['end_date']:
        #     query += " AND e.execution_ts <= %s"
        #     params.append(f"{filters['end_date']} 23:59:59")
            
        # Add order by clause to match PHP implementation
        query += " ORDER BY tproj.notes, tp.notes, parent_nh.name, nh_tc.name, e.execution_ts DESC"
        
        # Log the complete SQL query with parameters
        print(f"\nExecuting main SQL query:\n{query}")
        if params:
            print(f"With parameters: {params}\n")
        
        # Execute the basic query to get detailed execution info
        results = self.db.execute_query(query, tuple(params) if params else None)
        
        # Also get aggregated metrics using CTE-based query for accurate counts
        metrics_query, metrics_params = self.get_metrics_query(filters)
        
        # Log the complete metrics SQL query with parameters
        print(f"\nExecuting metrics SQL query:\n{metrics_query}")
        if metrics_params:
            print(f"With parameters: {metrics_params}\n")
            
        metrics_results = self.db.execute_query(metrics_query, tuple(metrics_params) if metrics_params else None)
        
        if not results:
            return {
                'hierarchical_data': {},
                'status_counts': {
                    'p': 0, 'f': 0, 'b': 0, 'n': 0
                },
                'tester_counts': {},
                'suite_counts': {}
            }
        
        # Process results to build hierarchical data structure
        hierarchical_data = {}
        status_counts = {'p': 0, 'f': 0, 'b': 0, 'n': 0}
        tester_counts = {}
        suite_counts = {}
        
        # Process the metrics results to get accurate test case counts by suite
        metrics_by_suite = {}
        project_name = ''
        testplan_name = ''
        
        if metrics_results:
            for metric in metrics_results:
                suite_id = metric.get('suite_id', 0)
                # Store project and testplan names from the first result
                if not project_name:
                    project_name = metric.get('project_notes', 'Unknown Project')
                if not testplan_name:
                    testplan_name = metric.get('testplan_notes', 'Unknown Plan')
                    
                # First, get the parent_id of this suite to retrieve its path
                suite_path = ''
                if suite_id > 0:
                    # First try direct query to node_hierarchy_paths_v2 table
                    path_query = f"SELECT full_path FROM node_hierarchy_paths_v2 WHERE node_id = {suite_id}"
                    path_result = self.db.execute_query(path_query)
                    
                    if path_result and len(path_result) > 0 and path_result[0]['full_path']:
                        suite_path = path_result[0]['full_path']
                    else:
                        # Try fallback by getting parent first
                        parent_query = f"SELECT parent_id FROM nodes_hierarchy WHERE id = {suite_id}"
                        parent_result = self.db.execute_query(parent_query)
                        
                        if parent_result and len(parent_result) > 0:
                            parent_id = parent_result[0]['parent_id']
                            
                            # Then get the path using the parent_id
                            path_query = f"SELECT full_path FROM node_hierarchy_paths_v2 WHERE node_id = {parent_id}"
                            path_result = self.db.execute_query(path_query)
                            
                            if path_result and len(path_result) > 0 and path_result[0]['full_path']:
                                suite_path = path_result[0]['full_path']
                
                # If still empty, construct a fallback path using suite name
                if not suite_path:
                    project_name = self.strip_tags(metric.get('project_notes', 'Unknown Project'))
                    testplan_name = self.strip_tags(metric.get('testplan_notes', 'Unknown Plan'))
                    suite_name = self.strip_tags(metric.get('suite_name', 'Unknown Suite'))
                    suite_path = f"{project_name} > {testplan_name} > {suite_name}"
                    print(f"Generated fallback path for suite {suite_id}: {suite_path}")
                    
                # Add to suite_counts
                suite_counts[suite_id] = {
                    'id': suite_id,
                    'name': metric.get('suite_name', 'Unknown Suite'),
                    'path': suite_path,  
                    'testcase_count': metric.get('testcase_count', 0),
                    'passed_count': metric.get('passed_count', 0),
                    'failed_count': metric.get('failed_count', 0),
                    'blocked_count': metric.get('blocked_count', 0),
                    'not_run_count': metric.get('not_run_count', 0),
                }
                
                metrics_by_suite[suite_id] = {
                    'name': metric.get('suite_name', 'Unknown Suite'),
                    'full_path': suite_path,  
                    'testcase_count': metric.get('testcase_count', 0),
                    'passed_count': metric.get('passed_count', 0),
                    'failed_count': metric.get('failed_count', 0),
                    'blocked_count': metric.get('blocked_count', 0),
                    'not_run_count': metric.get('not_run_count', 0),
                    'project_notes': metric.get('project_notes', ''),
                    'testplan_notes': metric.get('testplan_notes', ''),
                    'testplan_id': metric.get('testplan_id'),
                    'project_id': metric.get('testproject_id')
                }
        
        for row in results:
            # Count by status
            status = row['execution_status']
            if status in status_counts:
                status_counts[status] += 1
                
            # Count by tester
            tester_id = row['tester_id']
            tester_name = f"{row['tester_firstname']} {row['tester_lastname']}"
            if tester_id not in tester_counts:
                tester_counts[tester_id] = {
                    'name': tester_name,
                    'count': 0,
                    'statuses': {'p': 0, 'f': 0, 'b': 0, 'n': 0}
                }
            tester_counts[tester_id]['count'] += 1
            if status in tester_counts[tester_id]['statuses']:
                tester_counts[tester_id]['statuses'][status] += 1
                
            # Get details for building the hierarchy
            project_id = row['project_id']
            testplan_id = row['testplan_id']
            suite_id = row['parent_suite_id'] or 0
            suite_name = row['parent_suite_name'] or 'No Suite'
            
            # Get project name from notes
            project_name = row['project_notes'] or 'Unknown Project'
            if isinstance(project_name, str) and '<' in project_name:
                # Strip HTML tags from project name
                project_name = self.strip_tags(project_name)
            
            # Initialize project in hierarchical data if not exists
            if project_id not in hierarchical_data:
                hierarchical_data[project_id] = {
                    'name': project_name,
                    'testplans': {}
                }
                
            # Get testplan name from notes
            testplan_name = row['testplan_notes'] or 'Unknown Test Plan'
            
            # Initialize testplan in project if not exists
            if testplan_id not in hierarchical_data[project_id]['testplans']:
                hierarchical_data[project_id]['testplans'][testplan_id] = {
                    'name': testplan_name,
                    'suites': {}
                }
                
            # Initialize suite in testplan if not exists
            if suite_id not in hierarchical_data[project_id]['testplans'][testplan_id]['suites']:
                # Get the full path hierarchy from the recursive query
                suite_path = row.get('full_path', '') if row.get('full_path') else ''
                # Strip any HTML tags from the path
                suite_path = self.strip_tags(suite_path)
                
                # Count for this suite
                if suite_id not in suite_counts:
                    suite_counts[suite_id] = {
                        'name': suite_name,
                        'path': suite_path,
                        'count': 0,
                        'statuses': {'p': 0, 'f': 0, 'b': 0, 'n': 0}
                    }
                
                # Initialize suite in hierarchical data
                hierarchical_data[project_id]['testplans'][testplan_id]['suites'][suite_id] = {
                    'name': suite_name,
                    'path': suite_path,
                    'executions': []
                }
                
            # Increment suite count - with safety checks
            if suite_id not in suite_counts:
                # Initialize if somehow missed earlier
                suite_counts[suite_id] = {
                    'name': suite_name,
                    'path': suite_path,
                    'count': 0,
                    'statuses': {'p': 0, 'f': 0, 'b': 0, 'n': 0}
                }
                
            # Ensure count exists
            if 'count' not in suite_counts[suite_id]:
                suite_counts[suite_id]['count'] = 0
                
            suite_counts[suite_id]['count'] += 1
            
            # Ensure statuses exists
            if 'statuses' not in suite_counts[suite_id]:
                suite_counts[suite_id]['statuses'] = {'p': 0, 'f': 0, 'b': 0, 'n': 0}
                
            if status in suite_counts[suite_id]['statuses']:
                suite_counts[suite_id]['statuses'][status] += 1
                
            # Add execution to suite
            hierarchical_data[project_id]['testplans'][testplan_id]['suites'][suite_id]['executions'].append(row)
        # Second pass: Now that suite_counts is populated, ensure metrics_by_suite has full path info
        # This addresses the timing issue where suite_counts wasn't populated when metrics_by_suite was first created
        for suite_id, metrics in metrics_by_suite.items():
            if suite_id in suite_counts and 'path' in suite_counts[suite_id] and suite_counts[suite_id]['path']:
                metrics_by_suite[suite_id]['full_path'] = suite_counts[suite_id]['path']
                print(f"Updated metrics_by_suite[{suite_id}] with path: {suite_counts[suite_id]['path']}")
            else:
                print(f"No path found for suite_id {suite_id} in suite_counts")
                
        # Return the hierarchical data structure and counts
        return {
            'hierarchical_data': hierarchical_data,
            'status_counts': status_counts,
            'tester_counts': tester_counts,
            'suite_counts': suite_counts,
            'metrics_by_suite': metrics_by_suite,
            'project_name': project_name,
            'testplan_name': testplan_name
        }
        
    def get_overall_metrics(self, filters=None):
        """
        Get overall metrics including untested test cases.
        
        Args:
            filters (dict, optional): Same filters as get_execution_summary
                
        Returns:
            dict: Overall metrics
        """
        # Default filters
        filters = filters or {}
        
        # Check if we're using mock data
        if hasattr(self.db, 'mock_data'):
            return self._get_mock_metrics(filters)
        
        # Build the SQL query for overall metrics
        params = []
        conditions = []
        
        # Apply filters
        if filters.get('project_id', 0) > 0:
            conditions.append("tp.testproject_id = %s")
            params.append(filters['project_id'])
        
        if filters.get('testplan_id', 0) > 0:
            conditions.append("e.testplan_id = %s")
            params.append(filters['testplan_id'])
            
        if filters.get('build_id', 0) > 0:
            conditions.append("e.build_id = %s")
            params.append(filters['build_id'])
            
        if filters.get('status', ''):
            conditions.append("e.status = %s")
            params.append(filters['status'])
            
        if filters.get('start_date', ''):
            conditions.append("e.execution_ts >= %s")
            params.append(f"{filters['start_date']} 00:00:00")
            
        if filters.get('end_date', ''):
            conditions.append("e.execution_ts <= %s")
            params.append(f"{filters['end_date']} 23:59:59")
        
        # Create the WHERE clause
        where_clause = 'WHERE ' + ' AND '.join(conditions) if conditions else ''
        
        # Format the SQL query with proper indentation
        query = f"""
        WITH latest_executions AS (
            SELECT
                tc.id as tc_id,
                MAX(e.execution_ts) as latest_ts,
                tcv.id as tcversion_id,
                COALESCE(e.status, 'n') as status
            FROM
                nodes_hierarchy tc
                JOIN nodes_hierarchy tcv ON tcv.parent_id = tc.id
                JOIN executions e ON e.tcversion_id = tcv.id
                JOIN testplans tp ON tp.id = e.testplan_id
            {where_clause}
            GROUP BY
                tc.id, tcv.id
        )
        SELECT
            COUNT(*) as testcase_count,
            SUM(CASE WHEN le.status = 'p' THEN 1 ELSE 0 END) as passed_count,
            SUM(CASE WHEN le.status = 'f' THEN 1 ELSE 0 END) as failed_count,
            SUM(CASE WHEN le.status = 'b' THEN 1 ELSE 0 END) as blocked_count,
            SUM(CASE WHEN le.status IS NULL OR le.status = 'n' THEN 1 ELSE 0 END) as not_run_count
        FROM 
            latest_executions le
        """
        
        # Execute the query
        results = self.db.execute_query(query, tuple(params) if params else None)
        
        if not results or not results[0]:
            return {
                'testcase_count': 0,
                'passed_count': 0,
                'failed_count': 0,
                'blocked_count': 0,
                'not_run_count': 0,
                'pass_rate': 0.0
            }
        
        metrics = results[0]
        
        # Calculate pass rate
        total_executed = metrics['passed_count'] + metrics['failed_count'] + metrics['blocked_count']
        metrics['pass_rate'] = (metrics['passed_count'] / total_executed * 100) if total_executed > 0 else 0.0
        
        return metrics
    
    def _get_mock_metrics(self, filters):
        """Get metrics for mock data with filters."""
        # Direct access to mock data for more reliable filtering
        if hasattr(self.db, 'mock_data'):
            executions = self.db.mock_data.get_executions(filters)
            
            # Calculate metrics manually
            passed = sum(1 for e in executions if e['execution_status'] == 'p')
            failed = sum(1 for e in executions if e['execution_status'] == 'f')
            blocked = sum(1 for e in executions if e['execution_status'] == 'b')
            not_run = sum(1 for e in executions if e['execution_status'] == 'n')
            total = len(executions)
            
            # Calculate pass rate
            total_executed = passed + failed + blocked
            pass_rate = (passed / total_executed * 100) if total_executed > 0 else 0.0
            
            # Return formatted metrics
            return {
                'passed_count': passed,
                'failed_count': failed,
                'blocked_count': blocked,
                'not_run_count': not_run,
                'testcase_count': total,
                'pass_rate': pass_rate
            }
        
        # Fallback to empty metrics
        return {
            'testcase_count': 0,
            'passed_count': 0,
            'failed_count': 0,
            'blocked_count': 0,
            'not_run_count': 0,
            'pass_rate': 0.0
        }
        
    def get_test_suite_progress(self, filters=None):
        """
        Get test suite progress data with optional filters.
        
        Args:
            filters (dict, optional): Same filters as get_execution_summary
                
        Returns:
            dict: Test suite progress metrics indexed by suite_id
        """
        # Check if we're using mock data
        if hasattr(self.db, 'mock_data'):
            return self._get_mock_suite_progress(filters)
            
        # Default filters
        filters = filters or {}
        
        # Build the base SQL query to get test suite progress metrics
        query = """
        SELECT 
            parent_nh.id AS suite_id,
            parent_nh.name AS suite_name,
            nhpv2.full_path AS suite_path,
            COUNT(tc.id) AS total,
            SUM(CASE WHEN e.status = 'p' THEN 1 ELSE 0 END) AS passed,
            SUM(CASE WHEN e.status = 'f' THEN 1 ELSE 0 END) AS failed,
            SUM(CASE WHEN e.status = 'b' THEN 1 ELSE 0 END) AS blocked,
            SUM(CASE WHEN e.status IS NULL OR e.status = 'n' THEN 1 ELSE 0 END) AS not_run
        FROM 
            nodes_hierarchy AS tc
            JOIN nodes_hierarchy AS tcv ON tcv.parent_id = tc.id
            JOIN nodes_hierarchy AS parent_nh ON parent_nh.id = tc.parent_id
            JOIN node_hierarchy_paths_v2 AS nhpv2 ON nhpv2.node_id = parent_nh.id
            LEFT JOIN executions AS e ON e.tcversion_id = tcv.id
            LEFT JOIN testplans AS tp ON tp.id = e.testplan_id
            LEFT JOIN builds AS b ON b.id = e.build_id
            LEFT JOIN platforms AS p ON p.id = e.platform_id
        """
        
        # Add filter conditions
        conditions = ["tc.node_type_id = 3"]  # Test case node type
        params = []
        
        if filters.get('project_id', 0) > 0:
            conditions.append("tp.testproject_id = %s")
            params.append(filters['project_id'])
            
        if filters.get('testplan_id', 0) > 0:
            conditions.append("e.testplan_id = %s")
            params.append(filters['testplan_id'])
            
        if filters.get('build_id', 0) > 0:
            conditions.append("e.build_id = %s")
            params.append(filters['build_id'])
            
        if filters.get('status', ''):
            conditions.append("e.status = %s")
            params.append(filters['status'])
        
        # Create the WHERE clause
        where_clause = 'WHERE ' + ' AND '.join(conditions) if conditions else ''
        
        # Complete the query with grouping
        query = f"""
        {query}
        {where_clause}
        GROUP BY 
            parent_nh.id, 
            parent_nh.name,
            nhpv2.full_path
        ORDER BY 
            nhpv2.full_path
        """
        
        # Execute the query
        results = self.db.execute_query(query, tuple(params) if params else None)
        
        if not results:
            return {}
        
        # Process the results into a suite progress dictionary
        suite_progress = {}
        for row in results:
            suite_id = row['suite_id']
            suite_progress[suite_id] = {
                'name': row['suite_name'],
                'path': self.strip_tags(row['suite_path']),
                'total': row['total'],
                'statuses': {
                    'p': row['passed'],
                    'f': row['failed'],
                    'b': row['blocked'],
                    'n': row['not_run']
                },
                'pass_rate': self._calculate_pass_rate(row['passed'], row['passed'] + row['failed'] + row['blocked'])
            }
        
        return suite_progress
    
    def _calculate_pass_rate(self, passed, total):
        """Calculate pass rate percentage."""
        return (passed / total * 100) if total > 0 else 0.0
        
    def _get_mock_suite_progress(self, filters):
        """Get mock test suite progress data with filters."""
        # Create mock suite progress data based on the image the user provided
        mock_suites = {
            1: {
                'name': 'Additional_ATM',
                'path': 'FCUBS UPGRADE PROJECT > CYCLE 1 > BATCH 1 > Payment & Transactions',
                'total': 37,
                'statuses': {'p': 0, 'f': 0, 'b': 0, 'n': 37},
                'pass_rate': 0.0
            },
            2: {
                'name': 'Additional_CE',
                'path': 'FCUBS UPGRADE PROJECT > CYCLE 1 > BATCH 1 > Payment & Transactions',
                'total': 22,
                'statuses': {'p': 0, 'f': 0, 'b': 0, 'n': 22},
                'pass_rate': 0.0
            },
            3: {
                'name': 'Additional_Dev',
                'path': 'FCUBS',
                'total': 9,
                'statuses': {'p': 0, 'f': 0, 'b': 0, 'n': 9},
                'pass_rate': 0.0
            }
        }
        return mock_suites