#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
Mock data provider for testing the TestLink test execution export script.
This module provides sample data that mimics database query results.
"""

import datetime
import random


class MockDataProvider:
    """
    Class for providing mock data for testing.
    """
    def __init__(self):
        """Initialize with mock data."""
        # Create some consistent IDs for reference
        self.project_ids = [1, 2]
        self.testplan_ids = [10, 11, 12, 13]
        self.build_ids = [100, 101, 102, 103, 104]
        self.suite_ids = [1000, 1001, 1002, 1003, 1004, 1005]
        self.tc_ids = list(range(2000, 2050))
        self.tcversion_ids = list(range(3000, 3050))
        self.execution_ids = list(range(4000, 4100))
        self.tester_ids = [1, 2, 3, 4, 5]
        
        # Status distribution for more realistic data
        self.status_weights = {'p': 0.65, 'f': 0.2, 'b': 0.1, 'n': 0.05}
        
        # Generate consistent mapping between entities
        self.project_testplans = {
            1: [10, 11, 12],  # Project 1 has test plans 10, 11, 12
            2: [13]           # Project 2 has test plan 13
        }
        
        self.testplan_builds = {
            10: [100, 101],    # Test plan 10 has builds 100, 101
            11: [102],         # Test plan 11 has build 102
            12: [103],         # Test plan 12 has build 103
            13: [104]          # Test plan 13 has build 104
        }
        
        # Generate mock data
        self._generate_mock_entities()
        
    def _generate_mock_entities(self):
        """Generate mock entities (projects, test plans, builds, etc.)."""
        # Generate projects
        self.projects = [
            {'id': 1, 'name': 'Banking System Migration', 'active': 1},
            {'id': 2, 'name': 'Customer Portal Redesign', 'active': 1}
        ]
        
        # Generate test plans
        self.testplans = [
            {'id': 10, 'testproject_id': 1, 'name': 'Core Banking Migration - Sprint 1', 'active': 1},
            {'id': 11, 'testproject_id': 1, 'name': 'Core Banking Migration - Sprint 2', 'active': 1},
            {'id': 12, 'testproject_id': 1, 'name': 'Core Banking Migration - Regression', 'active': 1},
            {'id': 13, 'testproject_id': 2, 'name': 'Portal Usability Testing', 'active': 1}
        ]
        
        # Generate builds
        self.builds = [
            {'id': 100, 'testplan_id': 10, 'name': 'Build 1.0.0', 'notes': 'Initial build for testing', 'active': 1},
            {'id': 101, 'testplan_id': 10, 'name': 'Build 1.0.1', 'notes': 'Fixes for auth issues', 'active': 1},
            {'id': 102, 'testplan_id': 11, 'name': 'Build 1.1.0', 'notes': 'New features added', 'active': 1},
            {'id': 103, 'testplan_id': 12, 'name': 'Build 1.2.0', 'notes': 'Regression build', 'active': 1},
            {'id': 104, 'testplan_id': 13, 'name': 'Portal v2.0', 'notes': 'New portal version', 'active': 1}
        ]
        
        # Generate test suites
        self.suites = [
            {'id': 1000, 'name': 'Authentication', 'parent_id': None, 'path': '/Authentication'},
            {'id': 1001, 'name': 'Transaction Processing', 'parent_id': None, 'path': '/Transaction Processing'},
            {'id': 1002, 'name': 'Reporting', 'parent_id': None, 'path': '/Reporting'},
            {'id': 1003, 'name': 'User Management', 'parent_id': 1000, 'path': '/Authentication/User Management'},
            {'id': 1004, 'name': 'Payment Processing', 'parent_id': 1001, 'path': '/Transaction Processing/Payment Processing'},
            {'id': 1005, 'name': 'Portal Features', 'parent_id': None, 'path': '/Portal Features'}
        ]
        
        # Generate testers
        self.testers = [
            {'id': 1, 'login': 'john.smith', 'first': 'John', 'last': 'Smith'},
            {'id': 2, 'login': 'mary.jones', 'first': 'Mary', 'last': 'Jones'},
            {'id': 3, 'login': 'bob.wilson', 'first': 'Bob', 'last': 'Wilson'},
            {'id': 4, 'login': 'alice.brown', 'first': 'Alice', 'last': 'Brown'},
            {'id': 5, 'login': 'samuel.jackson', 'first': 'Samuel', 'last': 'Jackson'}
        ]
        
        # Generate test cases with versions
        self.testcases = []
        self.tcversions = []
        
        tc_names = [
            'Verify user login with valid credentials',
            'Verify user login with invalid credentials',
            'Reset password functionality',
            'User registration process',
            'Verify session timeout',
            'Process standard payment',
            'Process international payment',
            'Handle payment failures',
            'Generate monthly statement',
            'Export report to PDF',
            'Export report to Excel',
            'User role assignment',
            'User account locking after failed attempts',
            'Two-factor authentication',
            'Dashboard view customization',
            'Profile update functionality',
            'Notification preferences',
            'Search functionality',
            'Transaction history filtering',
            'Document upload and verification'
        ]
        
        # Assign test cases to suites
        suite_tc_distribution = {
            1000: list(range(0, 5)),          # Authentication suite gets first 5 test cases
            1001: list(range(5, 9)),          # Transaction Processing gets next 4
            1002: list(range(9, 12)),         # Reporting gets next 3
            1003: list(range(12, 15)),        # User Management gets next 3
            1004: list(range(15, 18)),        # Payment Processing gets next 3
            1005: list(range(18, 20))         # Portal Features gets last 2
        }
        
        # Generate test cases and their versions
        for idx, tc_name in enumerate(tc_names):
            # Find which suite this test case belongs to
            suite_id = None
            for suite, tc_indices in suite_tc_distribution.items():
                if idx in tc_indices:
                    suite_id = suite
                    break
            
            tc_id = self.tc_ids[idx]
            tcversion_id = self.tcversion_ids[idx]
            
            # Create test case
            self.testcases.append({
                'id': tc_id,
                'name': tc_name,
                'parent_id': suite_id  # Link to suite
            })
            
            # Create test case version
            self.tcversions.append({
                'id': tcversion_id,
                'version': random.randint(1, 3),
                'summary': f'Summary for {tc_name}',
                'parent_id': tc_id  # Link to test case
            })
        
        # Generate executions
        self.executions = []
        
        # Dates for executions, ensuring they are within a reasonable range
        base_date = datetime.datetime(2025, 6, 1)
        date_range = (datetime.datetime.now() - base_date).days
        
        # Generate executions for each test case version across different builds/plans
        for tcversion_idx, tcversion in enumerate(self.tcversions):
            # Determine which project this test case belongs to based on the suite
            tc = self.testcases[tcversion_idx]
            suite_id = tc['parent_id']
            
            # Determine which project this suite belongs to (simplification)
            project_id = 1  # Default to first project
            if suite_id == 1005:  # Portal Features suite belongs to project 2
                project_id = 2
            
            # Get test plans for this project
            testplan_ids = self.project_testplans.get(project_id, [])
            
            # Create executions for each test plan and its builds
            for testplan_id in testplan_ids:
                build_ids = self.testplan_builds.get(testplan_id, [])
                
                for build_id in build_ids:
                    # Randomly decide if this test case was executed in this build
                    if random.random() < 0.8:  # 80% chance of execution
                        status = random.choices(
                            list(self.status_weights.keys()),
                            weights=list(self.status_weights.values()),
                            k=1
                        )[0]
                        
                        # Create execution
                        execution_id = self.execution_ids[len(self.executions)]
                        tester_id = random.choice(self.tester_ids)
                        tester = next(t for t in self.testers if t['id'] == tester_id)
                        
                        # Generate a random date within the range
                        days_ago = random.randint(0, date_range)
                        execution_date = base_date + datetime.timedelta(days=days_ago)
                        
                        # Format the date as string
                        execution_ts = execution_date.strftime('%Y-%m-%d %H:%M:%S')
                        
                        # Build name and notes
                        build = next(b for b in self.builds if b['id'] == build_id)
                        build_name = build['name']
                        build_notes = build['notes']
                        
                        # Test plan name
                        testplan = next(tp for tp in self.testplans if tp['id'] == testplan_id)
                        testplan_notes = testplan['name']
                        
                        # Suite name
                        suite = next(s for s in self.suites if s['id'] == suite_id)
                        suite_name = suite['name']
                        
                        # Create the execution record
                        self.executions.append({
                            'execution_id': execution_id,
                            'execution_status': status,
                            'testplan_id': testplan_id,
                            'testplan_notes': testplan_notes,
                            'build_id': build_id,
                            'build_name': build_name,
                            'build_notes': build_notes,
                            'platform_id': None,
                            'platform_name': None,
                            'platform_notes': None,
                            'tcversion_id': tcversion['id'],
                            'tc_version': tcversion['version'],
                            'tc_summary': tcversion['summary'],
                            'tc_id': tc['id'],
                            'tc_name': tc['name'],
                            'parent_suite_id': suite_id,
                            'parent_suite_name': suite_name,
                            'execution_timestamp': execution_ts,
                            'tester_id': tester_id,
                            'tester_login': tester['login'],
                            'tester_firstname': tester['first'],
                            'tester_lastname': tester['last'],
                            'project_id': project_id,
                            'project_notes': next(p['name'] for p in self.projects if p['id'] == project_id)
                        })
    
    def get_projects(self):
        """Get mock projects."""
        return self.projects
    
    def get_test_plans(self, project_id=None):
        """Get mock test plans, optionally filtered by project."""
        if project_id:
            return [tp for tp in self.testplans if tp['testproject_id'] == project_id]
        return self.testplans
    
    def get_builds(self, testplan_id=None):
        """Get mock builds, optionally filtered by test plan."""
        if testplan_id:
            return [b for b in self.builds if b['testplan_id'] == testplan_id]
        return self.builds
        
    def get_test_suites(self):
        """Get mock test suites."""
        return self.suites
    
    def get_executions(self, filters=None):
        """
        Get mock executions with optional filters.
        
        Args:
            filters (dict, optional): Filters for the query
        
        Returns:
            list: Filtered executions
        """
        filters = filters or {}
        result = self.executions
        
        # Apply filters
        if 'project_id' in filters and filters['project_id'] > 0:
            result = [e for e in result if e['project_id'] == filters['project_id']]
            
        if 'testplan_id' in filters and filters['testplan_id'] > 0:
            result = [e for e in result if e['testplan_id'] == filters['testplan_id']]
            
        if 'build_id' in filters and filters['build_id'] > 0:
            result = [e for e in result if e['build_id'] == filters['build_id']]
            
        if 'status' in filters and filters['status']:
            result = [e for e in result if e['execution_status'] == filters['status']]
            
        if 'start_date' in filters and filters['start_date']:
            start = datetime.datetime.strptime(filters['start_date'], '%Y-%m-%d')
            result = [e for e in result if datetime.datetime.strptime(e['execution_timestamp'], '%Y-%m-%d %H:%M:%S') >= start]
            
        if 'end_date' in filters and filters['end_date']:
            end = datetime.datetime.strptime(f"{filters['end_date']} 23:59:59", '%Y-%m-%d %H:%M:%S')
            result = [e for e in result if datetime.datetime.strptime(e['execution_timestamp'], '%Y-%m-%d %H:%M:%S') <= end]
        
        return result
