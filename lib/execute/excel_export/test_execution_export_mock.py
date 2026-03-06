#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
TestLink Test Execution Summary Export Tool (Mock Version)

This script exports mock test execution data to Excel format for testing purposes.
It uses mock data instead of connecting to a real database.
"""

import os
import sys
import argparse
import datetime
from mock_db_connector import MockDatabaseConnector
from fixed_data_processor import TestExecutionDataProcessor
from excel_exporter import TestExecutionExcelExporter


def parse_arguments():
    """Parse command line arguments."""
    parser = argparse.ArgumentParser(description='Export TestLink test execution data to Excel (Mock version).')
    
    parser.add_argument('-p', '--project', type=int, default=0,
                        help='Filter by project ID')
    parser.add_argument('-t', '--testplan', type=int, default=0,
                        help='Filter by test plan ID')
    parser.add_argument('-b', '--build', type=int, default=0,
                        help='Filter by build ID')
    parser.add_argument('-s', '--status', choices=['p', 'f', 'b', 'n', ''],
                        default='', help='Filter by status (p=passed, f=failed, b=blocked, n=not run)')
    parser.add_argument('--start-date', default='',
                        help='Filter by start date (YYYY-MM-DD)')
    parser.add_argument('--end-date', default='',
                        help='Filter by end date (YYYY-MM-DD)')
    parser.add_argument('-o', '--output', default='',
                        help='Output file path')
    parser.add_argument('-d', '--output-dir', default='',
                        help='Output directory')
    parser.add_argument('-l', '--list', action='store_true',
                        help='List available projects, test plans, and builds')
    
    return parser.parse_args()


def list_available_options(db_connector):
    """List available projects, test plans, and builds."""
    print("\n=== Available Projects (Mock Data) ===")
    projects = db_connector.get_projects()
    if not projects:
        print("No projects found.")
    else:
        for project in projects:
            print(f"ID: {project['id']} | Name: {project['name']}")
    
    print("\n=== Available Test Plans (Mock Data) ===")
    testplans = db_connector.get_test_plans()
    if not testplans:
        print("No test plans found.")
    else:
        for testplan in testplans:
            print(f"ID: {testplan['id']} | Name: {testplan['name']} | Project ID: {testplan['testproject_id']}")
    
    print("\n=== Available Builds (Mock Data) ===")
    builds = db_connector.get_builds()
    if not builds:
        print("No builds found.")
    else:
        for build in builds:
            print(f"ID: {build['id']} | Name: {build['name']} | Test Plan ID: {build['testplan_id']}")


def transform_data_for_exporter(raw_data, metrics):
    """Transform raw data into format expected by Excel exporter."""
    # Initialize the data structure expected by the exporter
    transformed_data = {
        'hierarchical_data': {},  # Dictionary with project_id as keys
        'status_counts': {},
        'tester_counts': {},
        'suite_counts': {},
        'metrics_by_suite': {},  # This is what the exporter expects
        'project_name': 'FCUBS V14.7 Upgrade Functional Testing',  # Add project name
        'testplan_name': 'spsUAT 1'  # Add test plan name
    }
    
    # Process projects
    for project_id, project in raw_data['projects'].items():
        project_data = {
            'id': project_id,
            'name': project['name'],
            'notes': project['notes'],
            'testplans': {}
        }
        
        # Process testplans for this project
        for testplan_id in project['testplans']:
            if testplan_id in raw_data['testplans']:
                testplan = raw_data['testplans'][testplan_id]
                testplan_data = {
                    'id': testplan_id,
                    'name': testplan['name'],
                    'notes': testplan['notes'],
                    'suites': {}
                }
                
                # Process testsuites for this testplan
                # Check if testsuites is a list or dict and handle accordingly
                if isinstance(testplan['testsuites'], list):
                    testsuite_items = [(ts_id, raw_data['testsuites'][ts_id]) for ts_id in testplan['testsuites'] if ts_id in raw_data['testsuites']]
                else:
                    testsuite_items = testplan['testsuites'].items()
                
                for testsuite_id, testsuite in testsuite_items:
                    testsuite_data = {
                        'id': testsuite_id,
                        'name': testsuite['name'],
                        'testcases': [],
                        'executions': []  # Add executions list that the Excel exporter expects
                    }
                    
                    # Collect status counts for this suite
                    suite_status_counts = {'p': 0, 'f': 0, 'b': 0, 'n': 0}
                    
                    # Process testcases for this testsuite
                    for testcase_id in testsuite['testcases']:
                        if testcase_id in raw_data['testcases']:
                            testcase = raw_data['testcases'][testcase_id]
                            
                            # Get the latest execution for this testcase
                            latest_exec = None
                            if testcase['executions']:
                                exec_id = testcase['executions'][-1]
                                if exec_id in raw_data['executions']:
                                    latest_exec = raw_data['executions'][exec_id]
                            
                            # Extract status and other details
                            status = latest_exec['status'] if latest_exec else 'n'
                            tester_name = latest_exec['tester_name'] if latest_exec else 'Unknown'
                            build_name = latest_exec['build_name'] if latest_exec else 'Unknown Build'
                            
                            # Update status counts
                            suite_status_counts[status] += 1
                            
                            # Update tester counts
                            if tester_name not in transformed_data['tester_counts']:
                                transformed_data['tester_counts'][tester_name] = {
                                    'count': 0,
                                    'statuses': {'p': 0, 'f': 0, 'b': 0, 'n': 0}
                                }
                            transformed_data['tester_counts'][tester_name]['count'] += 1
                            transformed_data['tester_counts'][tester_name]['statuses'][status] += 1
                            
                            # Create execution record that Excel exporter expects
                            execution_record = {
                                'tc_name': testcase['name'],
                                'tc_version': testcase['version'],
                                'build_name': latest_exec.get('build_name', 'Unknown Build'),
                                'execution_status': latest_exec.get('status', 'n'),
                                'execution_notes': latest_exec.get('notes', ''),
                                'timestamp': latest_exec.get('timestamp', ''),
                                # Add tester information expected by Excel exporter
                                'tester_firstname': latest_exec.get('tester_name', '').split(' ')[0] if latest_exec.get('tester_name') else '',
                                'tester_lastname': ' '.join(latest_exec.get('tester_name', '').split(' ')[1:]) if latest_exec.get('tester_name') and len(latest_exec.get('tester_name', '').split(' ')) > 1 else '',
                                'tester_name': latest_exec.get('tester_name', 'Unknown')
                            }
                            testsuite_data['testcases'].append(execution_record)
                        
                        # Add suite status counts to global counts - use testsuite_id as key to ensure uniqueness
                        transformed_data['suite_counts'][testsuite_id] = {
                            'name': testsuite['name'],  # Include name in the value object as expected by Excel exporter
                            'path': testsuite.get('path', ''),  # Optional path info
                            'count': len(testsuite_data['testcases']),
                            'statuses': suite_status_counts
                        }
                        
                        # Add testsuite to testplan dictionary with testsuite_id as key
                        testplan_data['suites'][testsuite_id] = testsuite_data
                
                # Add testplan to project dictionary with testplan_id as key
                project_data['testplans'][testplan_id] = testplan_data
        
        # Add project to hierarchical data dictionary with project_id as key
        transformed_data['hierarchical_data'][project_id] = project_data
    
    # Generate metrics_by_suite using mock data
    # Get all suites from raw_data
    suite_ids = set()
    for project_id, project in raw_data['projects'].items():
        for testplan_id in project['testplans']:
            if testplan_id in raw_data['testplans']:
                testplan = raw_data['testplans'][testplan_id]
                for testsuite_id in testplan.get('testsuites', {}):
                    suite_ids.add(testsuite_id)
    
    # Populate metrics for each suite
    for suite_id in suite_ids:
        if suite_id in raw_data.get('testsuites', {}):
            suite = raw_data['testsuites'][suite_id]
            
            # Get test cases for this suite
            suite_testcases = []
            for tc_id in suite.get('testcases', []):
                if tc_id in raw_data.get('testcases', {}):
                    suite_testcases.append(raw_data['testcases'][tc_id])
            
            # Count by status
            passed_count = sum(1 for tc in suite_testcases if tc.get('last_execution_status') == 'p')
            failed_count = sum(1 for tc in suite_testcases if tc.get('last_execution_status') == 'f')
            blocked_count = sum(1 for tc in suite_testcases if tc.get('last_execution_status') == 'b')
            not_run_count = sum(1 for tc in suite_testcases if tc.get('last_execution_status') == 'n')
            total = len(suite_testcases)
            
            # Use the suite name from the testsuites dictionary
            suite_name = suite.get('name', 'Unknown Suite')
            
            # Add to metrics_by_suite
            transformed_data['metrics_by_suite'][suite_id] = {
                'name': suite_name,
                'full_path': suite.get('path', ''),  # Add full_path from the testsuite structure
                'testcase_count': total,
                'passed_count': passed_count,
                'failed_count': failed_count, 
                'blocked_count': blocked_count,
                'not_run_count': not_run_count
            }
    
    # If metrics_by_suite is empty, add some sample data
    if not transformed_data['metrics_by_suite']:
        # Sample suite metrics matching the UI screenshot
        suites = [
            {'id': 1, 'name': 'ATM', 'total': 373, 'passed': 117, 'failed': 13, 'blocked': 243, 'not_run': 0},
            {'id': 2, 'name': 'BASE_OTHERS', 'total': 48, 'passed': 48, 'failed': 0, 'blocked': 0, 'not_run': 0},
            {'id': 3, 'name': 'BASE OTHERS', 'total': 45, 'passed': 2, 'failed': 4, 'blocked': 39, 'not_run': 0},
            {'id': 4, 'name': 'BOOK TRANSFER', 'total': 313, 'passed': 83, 'failed': 181, 'blocked': 49, 'not_run': 0},
            {'id': 5, 'name': 'BRANCH & COPS MGT', 'total': 280, 'passed': 272, 'failed': 8, 'blocked': 0, 'not_run': 0},
        ]
        
        for suite in suites:
            transformed_data['metrics_by_suite'][suite['id']] = {
                'name': suite['name'],
                'full_path': f"FCUBS V14.7 Upgrade Functional Testing > spsUAT 1 > {suite['name']}",  # Add full path from project/testplan/suite
                'testcase_count': suite['total'],
                'passed_count': suite['passed'],
                'failed_count': suite['failed'],
                'blocked_count': suite['blocked'],
                'not_run_count': suite['not_run']
            }
    
    # Process global status counts
    status_mapping = {
        'p': 'passed_count',
        'f': 'failed_count',
        'b': 'blocked_count',
        'n': 'not_run_count'
    }
    for status in ['p', 'f', 'b', 'n']:
        metric_key = status_mapping[status]
        transformed_data['status_counts'][status] = metrics.get(metric_key, 0)
    
    print(f"\nTransformed data metrics_by_suite has {len(transformed_data['metrics_by_suite'])} items")
    
    # Second pass: Debug and validate full_path values in metrics_by_suite
    for suite_id, suite_metrics in transformed_data['metrics_by_suite'].items():
        if 'full_path' in suite_metrics and suite_metrics['full_path']:
            print(f"Suite {suite_id} has path: {suite_metrics['full_path']}")
        else:
            print(f"Suite {suite_id} is missing full_path!")
            
        # Ensure path is not empty
        if 'full_path' not in suite_metrics or not suite_metrics['full_path']:
            # For mock data, set a default path that includes the suite name
            suite_metrics['full_path'] = f"FCUBS V14.7 Upgrade Functional Testing > spsUAT 1 > {suite_metrics['name']}"
            print(f"Set default path for suite {suite_id}: {suite_metrics['full_path']}")

    
    return transformed_data


def main():
    """Main function."""
    args = parse_arguments()
    
    print("MOCK MODE: Using mock data instead of real database connection")
    
    # Create output directory if it doesn't exist
    output_dir = args.output_dir or os.getcwd()
    if not os.path.exists(output_dir):
        os.makedirs(output_dir)
    
    # Connect to the mock database
    db_connector = MockDatabaseConnector()
    if not db_connector.connect():
        sys.exit(1)
    
    # List available options if requested
    if args.list:
        list_available_options(db_connector)
        db_connector.disconnect()
        return
    
    # Process filters
    filters = {
        'project_id': args.project,
        'testplan_id': args.testplan,
        'build_id': args.build,
        'status': args.status,
        'start_date': args.start_date,
        'end_date': args.end_date
    }
    
    # Print current filter settings
    print("\n=== Current Filter Settings ===")
    print(f"Project ID: {filters['project_id'] or 'All'}")
    print(f"Test Plan ID: {filters['testplan_id'] or 'All'}")
    print(f"Build ID: {filters['build_id'] or 'All'}")
    print(f"Status: {filters['status'] or 'All'}")
    print(f"Start Date: {filters['start_date'] or 'All'}")
    print(f"End Date: {filters['end_date'] or 'All'}")
    
    # Get the data
    print("\nRetrieving mock test execution data...")
    data_processor = TestExecutionDataProcessor(db_connector)
    
    # Get raw execution data
    raw_data = data_processor.get_execution_summary(filters)
    metrics = data_processor.get_overall_metrics(filters)
    
    # Get suite progress data for second sheet
    suite_progress = data_processor.get_test_suite_progress(filters)
    print(f"Retrieved test suite progress data for {len(suite_progress)} suites")
    
    # Transform data for Excel exporter
    execution_data = transform_data_for_exporter(raw_data, metrics)
    
    # Add suite progress to execution data
    execution_data['suite_progress'] = suite_progress
    
    # Export to Excel
    print("Exporting data to Excel...")
    excel_exporter = TestExecutionExcelExporter(output_dir)
    
    # Generate filename if not provided
    output_file = args.output
    if not output_file:
        timestamp = datetime.datetime.now().strftime('%Y%m%d_%H%M%S')
        test_plan_info = f"_plan{filters['testplan_id']}" if filters['testplan_id'] else ""
        build_info = f"_build{filters['build_id']}" if filters['build_id'] else ""
        status_info = f"_{filters['status']}" if filters['status'] else ""
        output_file = f"mock_test_execution_summary{test_plan_info}{build_info}{status_info}_{timestamp}.xlsx"
    
    # Export the data
    excel_path = excel_exporter.export_data(execution_data, output_file)
    
    print(f"\nExcel file created successfully at: {excel_path}")
    
    # Disconnect from the database
    db_connector.disconnect()


if __name__ == "__main__":
    main()
