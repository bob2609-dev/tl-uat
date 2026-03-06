#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Mock database connector for testing the TestLink test execution export script.
This module mimics the behavior of db_connector.py but uses mock data instead.
"""

from mock_data import MockDataProvider


class MockDatabaseConnector:
    """
    Mock class for simulating database connections and queries.
    """
    def __init__(self):
        """Initialize the mock database connector with mock data."""
        self.mock_data = MockDataProvider()
        self.connection = True  # Simulate connected state
        self.cursor = True      # Simulate cursor
    
    def connect(self):
        """Simulate establishing a connection to the database."""
        self.connection = True
        print("Connected to mock database successfully.")
        return True
    
    def disconnect(self):
        """Simulate closing the database connection."""
        self.connection = False
        self.cursor = None
        print("Disconnected from mock database.")
    
    def is_connected(self):
        """Check if the connection is established."""
        return self.connection is not None and self.connection is not False
    
    def execute_query(self, query, params=None):
        """
        Simulate executing a SQL query with optional parameters.
        Returns mock data based on the query content.
        
        Args:
            query (str): SQL query to simulate
            params (tuple, optional): Parameters for the query
            
        Returns:
            list: Mock results as a list of dictionaries
        """
        # We don't actually parse the SQL, just look for keywords to determine what to return
        query_lower = query.lower()
        
        # For filtering
        filters = {}
        if params:
            # Extract project_id
            if "testproject_id = %s" in query_lower and len(params) > 0:
                filters['project_id'] = params[0]
            
            # Extract testplan_id
            if "testplan_id = %s" in query_lower and len(params) > 0:
                idx = 0
                if "testproject_id = %s" in query_lower:
                    idx = 1
                if len(params) > idx:
                    filters['testplan_id'] = params[idx]
            
            # Extract build_id
            if "build_id = %s" in query_lower:
                idx = 0
                if "testproject_id = %s" in query_lower:
                    idx += 1
                if "testplan_id = %s" in query_lower:
                    idx += 1
                if len(params) > idx:
                    filters['build_id'] = params[idx]
            
            # Extract status
            if "status = '" in query_lower or "e.status = %s" in query_lower:
                idx = 0
                if "testproject_id = %s" in query_lower:
                    idx += 1
                if "testplan_id = %s" in query_lower:
                    idx += 1
                if "build_id = %s" in query_lower:
                    idx += 1
                if len(params) > idx:
                    filters['status'] = params[idx]
            
            # Extract date range
            if "execution_ts >=" in query_lower:
                idx = 0
                if "testproject_id = %s" in query_lower:
                    idx += 1
                if "testplan_id = %s" in query_lower:
                    idx += 1
                if "build_id = %s" in query_lower:
                    idx += 1
                if "status = '" in query_lower or "e.status = %s" in query_lower:
                    idx += 1
                if len(params) > idx:
                    filters['start_date'] = params[idx].split()[0]  # Extract date part
            
            if "execution_ts <=" in query_lower:
                idx = 0
                if "testproject_id = %s" in query_lower:
                    idx += 1
                if "testplan_id = %s" in query_lower:
                    idx += 1
                if "build_id = %s" in query_lower:
                    idx += 1
                if "status = '" in query_lower or "e.status = %s" in query_lower:
                    idx += 1
                if "execution_ts >=" in query_lower:
                    idx += 1
                if len(params) > idx:
                    filters['end_date'] = params[idx].split()[0]  # Extract date part
        
        # Determine what to return based on query content
        if "from testprojects" in query_lower:
            return self.mock_data.get_projects()
        elif "from testplans" in query_lower:
            project_id = filters.get('project_id')
            return self.mock_data.get_test_plans(project_id)
        elif "from builds" in query_lower:
            testplan_id = filters.get('testplan_id')
            return self.mock_data.get_builds(testplan_id)
        elif "execution" in query_lower or "executions e" in query_lower:
            # For metrics query (can identify by COUNT or SUM in the query)
            if "count(" in query_lower or "sum(" in query_lower or ("select" in query_lower and "group by" not in query_lower) or "with status_counts as" in query_lower:
                # Get mock test suites
                test_suites = self.mock_data.get_test_suites()
                metrics_by_suite = {}
                project_name = "FCUBS V14.7 Upgrade Functional Testing"
                testplan_name = "spsUAT 1"
                
                # Create metrics for each test suite
                for suite in test_suites:
                    suite_id = suite.get('id', 0)
                    suite_name = suite.get('name', 'Unknown Suite')
                    
                    # Simulate metrics for this suite
                    suite_executions = [e for e in self.mock_data.get_executions(filters) 
                                       if e.get('testsuite_id') == suite_id]
                    
                    if not suite_executions:
                        continue
                        
                    passed = sum(1 for e in suite_executions if e.get('execution_status') == 'p')
                    failed = sum(1 for e in suite_executions if e.get('execution_status') == 'f')
                    blocked = sum(1 for e in suite_executions if e.get('execution_status') == 'b')
                    not_run = sum(1 for e in suite_executions if e.get('execution_status') == 'n')
                    total = len(suite_executions)
                    
                    # Create metrics entry
                    metrics_by_suite[suite_id] = {
                        'name': suite_name,
                        'testcase_count': total,
                        'passed_count': passed,
                        'failed_count': failed,
                        'blocked_count': blocked,
                        'not_run_count': not_run,
                        'project_notes': project_name,
                        'testplan_notes': testplan_name,
                        'testplan_id': 1,
                        'project_id': 1
                    }
                
                # If it's the query for the main metrics, return the suite metrics
                if "with status_counts as" in query_lower or "testsuite_id" in query_lower:
                    results = []
                    for suite_id, metrics in metrics_by_suite.items():
                        results.append({
                            'suite_id': suite_id,
                            'suite_name': metrics['name'],
                            'testcase_count': metrics['testcase_count'],
                            'passed_count': metrics['passed_count'],
                            'failed_count': metrics['failed_count'], 
                            'blocked_count': metrics['blocked_count'],
                            'not_run_count': metrics['not_run_count'],
                            'project_notes': project_name,
                            'testplan_notes': testplan_name,
                            'testplan_id': 1,
                            'testproject_id': 1
                        })
                    return results
                                
                # Return formatted metrics for overall counts
                executions = self.mock_data.get_executions(filters)
                passed = sum(1 for e in executions if e['execution_status'] == 'p')
                failed = sum(1 for e in executions if e['execution_status'] == 'f')
                blocked = sum(1 for e in executions if e['execution_status'] == 'b')
                not_run = sum(1 for e in executions if e['execution_status'] == 'n')
                total = len(executions)
                
                return [{
                    'passed_count': passed,
                    'failed_count': failed,
                    'blocked_count': blocked,
                    'not_run_count': not_run,
                    'testcase_count': total
                }]
            else:
                # Regular execution data
                return self.mock_data.get_executions(filters)
        
        # Default empty result
        return []
    
    def get_projects(self):
        """Get a list of all test projects."""
        return self.mock_data.get_projects()
    
    def get_test_plans(self, project_id=None):
        """Get test plans, optionally filtered by project."""
        return self.mock_data.get_test_plans(project_id)
    
    def get_builds(self, testplan_id=None):
        """Get builds, optionally filtered by test plan."""
        return self.mock_data.get_builds(testplan_id)
