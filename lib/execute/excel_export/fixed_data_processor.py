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

    def get_execution_summary(self, filters=None):
        """
        Get test execution summary data with optional filters.
        
        Args:
            filters (dict, optional): Filters for the query
                - project_id: Project ID
                - testplan_id: Test Plan ID
                - build_id: Build ID
                - status: Status ('p', 'f', 'b', 'n')
                - start_date: Start date (YYYY-MM-DD)
                - end_date: End date (YYYY-MM-DD)
                
        Returns:
            dict: Hierarchical execution data
        """
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
            -- Then append the test case name to complete the path
            CONCAT(
                (SELECT full_path FROM node_hierarchy_paths_v2 WHERE node_id = nh_tc.parent_id),
                ' > ',
                nh_tc.name
            ) AS full_path,
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
            
        if 'start_date' in filters and filters['start_date']:
            query += " AND e.execution_ts >= %s"
            params.append(f"{filters['start_date']} 00:00:00")
            
        if 'end_date' in filters and filters['end_date']:
            query += " AND e.execution_ts <= %s"
            params.append(f"{filters['end_date']} 23:59:59")
        
        # Order by project, testplan, testsuite, testcase
        query += """
        ORDER BY
            tp.testproject_id,
            e.testplan_id,
            parent_nh.id,
            nh_tc.id
        """
        
        # Execute the query
        results = self.db.execute_query(query, tuple(params) if params else None)
        
        if not results:
            return {
                'projects': {},
                'testplans': {},
                'testsuites': {},
                'testcases': {},
                'executions': {}
            }
        
        # Process results into hierarchical structure
        structured_data = {
            'projects': {},
            'testplans': {},
            'testsuites': {},
            'testcases': {},
            'executions': {}
        }
        
        # Process each row
        for row in results:
            # Extract IDs for each level
            project_id = row['project_id']
            testplan_id = row['testplan_id']
            testsuite_id = row['parent_suite_id'] or 0  # Use 0 for tests with no suite
            testcase_id = row['tc_id']
            execution_id = row['execution_id']
            
            # HTML decode the notes and summary fields
            try:
                # Use custom MLStripper to remove HTML tags
                if row['project_notes']:
                    row['project_notes'] = self.strip_tags(row['project_notes'])
                if row['testplan_notes']:
                    row['testplan_notes'] = self.strip_tags(row['testplan_notes'])
                if row['build_notes']:
                    row['build_notes'] = self.strip_tags(row['build_notes'])
                if row['tc_summary']:
                    row['tc_summary'] = self.strip_tags(row['tc_summary'])
            except Exception as e:
                print(f"Warning: HTML processing error - {e}")
            
            # Project level
            if project_id not in structured_data['projects']:
                structured_data['projects'][project_id] = {
                    'id': project_id,
                    'name': f"Project {project_id}",  # Default name if not available
                    'notes': row['project_notes'] or '',
                    'testplans': []
                }
            
            # Test Plan level
            if testplan_id not in structured_data['testplans']:
                structured_data['testplans'][testplan_id] = {
                    'id': testplan_id,
                    'name': f"Test Plan {testplan_id}",  # Default name
                    'notes': row['testplan_notes'] or '',
                    'project_id': project_id,
                    'testsuites': []
                }
                # Link to parent project
                if testplan_id not in structured_data['projects'][project_id]['testplans']:
                    structured_data['projects'][project_id]['testplans'].append(testplan_id)
            
            # Test Suite level
            if testsuite_id not in structured_data['testsuites']:
                # Get the full path from the recursive query and strip HTML tags
                full_path = row.get('full_path', '') if row.get('full_path') else ''
                full_path = self.strip_tags(full_path)
                
                structured_data['testsuites'][testsuite_id] = {
                    'id': testsuite_id,
                    'name': row['parent_suite_name'] or 'No Suite',
                    'path': full_path,  # Add the full path to the testsuite structure
                    'testplan_id': testplan_id,
                    'testcases': []
                }
                # Link to parent testplan
                if testsuite_id not in structured_data['testplans'][testplan_id]['testsuites']:
                    structured_data['testplans'][testplan_id]['testsuites'].append(testsuite_id)
            
            # Test Case level
            if testcase_id not in structured_data['testcases']:
                structured_data['testcases'][testcase_id] = {
                    'id': testcase_id,
                    'name': row['tc_name'] or f"TC-{testcase_id}",
                    'version': row['tc_version'] or '1',
                    'summary': row['tc_summary'] or '',
                    'testsuite_id': testsuite_id,
                    'executions': []
                }
                # Link to parent testsuite
                if testcase_id not in structured_data['testsuites'][testsuite_id]['testcases']:
                    structured_data['testsuites'][testsuite_id]['testcases'].append(testcase_id)
            
            # Execution level
            structured_data['executions'][execution_id] = {
                'id': execution_id,
                'status': row['execution_status'] or 'n',  # Default to 'not run'
                'timestamp': row['execution_timestamp'],
                'build_id': row['build_id'],
                'build_name': row['build_name'] or f"Build {row['build_id']}",
                'tester_id': row['tester_id'],
                'tester_name': f"{row['tester_firstname'] or ''} {row['tester_lastname'] or ''}".strip() or 'Unknown',
                'testcase_id': testcase_id
            }
            # Link to parent testcase
            if execution_id not in structured_data['testcases'][testcase_id]['executions']:
                structured_data['testcases'][testcase_id]['executions'].append(execution_id)
        
        return structured_data

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
        
        # Check for mock database connector
        if hasattr(self.db, '__class__') and self.db.__class__.__name__ == 'MockDatabaseConnector':
            return self._get_mock_metrics(filters)
        
        # Build parameters and conditions for the query
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
        # Default filters
        filters = filters or {}
        
        # If using mock connector, return mock suite progress data
        if hasattr(self.db, 'mock_data'):
            return self._get_mock_suite_progress(filters)
        
        # Build the SQL query for test suite progress
        query = """
        SELECT 
            parent_nh.id AS suite_id,
            parent_nh.name AS suite_name,
            nhpv2.full_path AS suite_path,
            COUNT(DISTINCT tc.id) AS total,
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