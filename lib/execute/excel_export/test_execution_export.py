#!/usr/bin/env python
# -*- coding: utf-8 -*-

"""
TestLink Test Execution Export Script

This script exports test execution data from TestLink to Excel.
It uses the TestLink database directly rather than the API.
"""

import os
import sys
import json
import logging
import argparse
import datetime
import traceback
from db_connector import DatabaseConnector
from data_processor import TestExecutionDataProcessor
from excel_exporter import TestExecutionExcelExporter


def load_config(config_file):
    """
    Load database configuration from JSON file.

    Args:
        config_file (str): Path to JSON configuration file.

    Returns:
        dict: Database configuration.

    Raises:
        FileNotFoundError: If configuration file not found.
        json.JSONDecodeError: If configuration file is not valid JSON.
    """
    try:
        with open(config_file, 'r') as f:
            config = json.load(f)
            return config
    except FileNotFoundError:
        logging.critical(f"Configuration file not found: {config_file}")
        raise
    except json.JSONDecodeError as e:
        logging.critical(f"Invalid JSON in configuration file: {config_file} - {str(e)}")
        raise


def setup_logging(log_level=logging.INFO, log_file=None):
    """
    Setup logging configuration.

    Args:
        log_level: Logging level (default: INFO)
        log_file: Path to log file. If None, logs to console only.
    """
    # Create logs directory if it doesn't exist
    if log_file:
        log_dir = os.path.dirname(log_file)
        if log_dir and not os.path.exists(log_dir):
            os.makedirs(log_dir)
    
    # Configure logging
    log_format = '%(asctime)s - %(levelname)s - %(message)s'
    date_format = '%Y-%m-%d %H:%M:%S'
    
    handlers = []
    # Add console handler
    console_handler = logging.StreamHandler()
    console_handler.setFormatter(logging.Formatter(log_format, datefmt=date_format))
    handlers.append(console_handler)
    
    # Add file handler if log_file provided
    if log_file:
        file_handler = logging.FileHandler(log_file, 'a')
        file_handler.setFormatter(logging.Formatter(log_format, datefmt=date_format))
        handlers.append(file_handler)
    
    # Configure root logger
    logging.basicConfig(
        level=log_level,
        format=log_format,
        datefmt=date_format,
        handlers=handlers
    )


def parse_arguments():
    """Parse command line arguments."""
    parser = argparse.ArgumentParser(description='Export TestLink test execution data to Excel.')
    
    # Required arguments
    parser.add_argument('-c', '--config', required=True,
                        help='Path to database configuration JSON file')
    
    # Filter arguments
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
    
    # Output options
    parser.add_argument('-o', '--output', default='',
                        help='Output file name')
    parser.add_argument('-d', '--output-dir', default='',
                        help='Output directory')
    
    # Debug options
    parser.add_argument('--debug', action='store_true',
                        help='Enable debug logging')
    parser.add_argument('--log-level', choices=['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'],
                        default='INFO', help='Set log level')
    
    # List available options
    parser.add_argument('-l', '--list', action='store_true',
                        help='List available projects, test plans, and builds')
    
    return parser.parse_args()


def list_available_options(db_connector):
    """List available projects, test plans, and builds."""
    try:
        print("\nAvailable Projects:")
        projects = db_connector.query("SELECT id, name FROM nodes_hierarchy WHERE node_type_id = 1")
        for project in projects:
            print(f"  - {project['id']}: {project['name']}")
        
        print("\nAvailable Test Plans:")
        test_plans = db_connector.query("""
            SELECT tp.id, tp.name, nh.parent_id as project_id, p.name as project_name
            FROM testplans tp
            JOIN nodes_hierarchy nh ON tp.id = nh.id
            JOIN nodes_hierarchy p_nh ON nh.parent_id = p_nh.id
            JOIN projects p ON p_nh.id = p.id
            ORDER BY p.name, tp.name
        """)
        for test_plan in test_plans:
            print(f"  - {test_plan['id']}: {test_plan['name']} (Project: {test_plan['project_name']})")
        
        print("\nAvailable Builds:")
        builds = db_connector.query("""
            SELECT b.id, b.name, tp.id as testplan_id, tp.name as testplan_name
            FROM builds b
            JOIN testplans tp ON b.testplan_id = tp.id
            ORDER BY tp.name, b.name
        """)
        for build in builds:
            print(f"  - {build['id']}: {build['name']} (Test Plan: {build['testplan_name']})")
        
        return True
    except Exception as e:
        logging.error(f"Error listing options: {str(e)}")
        return False


def main():
    """Main function for TestLink test execution export"""
    try:
        # Parse arguments
        args = parse_arguments()
        
        # Setup logging
        log_level = logging.DEBUG if args.debug else getattr(logging, args.log_level)
        
        # Create timestamp for log file
        timestamp = datetime.datetime.now().strftime('%Y%m%d_%H%M%S')
        log_file = f"testlink_export_{timestamp}.log"
        log_dir = "logs"
        log_path = os.path.join(log_dir, log_file)
        
        setup_logging(log_level, log_path)
        
        logging.info("======= TestLink Test Execution Export ========")
        logging.info(f"Started at: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        logging.info(f"Arguments: {args}")
        
        # Output directory
        output_dir = args.output_dir if args.output_dir else "output"
        if not os.path.exists(output_dir):
            os.makedirs(output_dir)
            logging.info(f"Created output directory: {output_dir}")
        
        # List available options and exit if requested
        if args.list:
            try:
                # Load database configuration
                config = load_config(args.config)
                
                # Connect to the database
                db_connector = DatabaseConnector(config)
                db_connector.connect()
                
                # List options
                list_available_options(db_connector)
                
                # Disconnect from database
                db_connector.disconnect()
                
                return 0
            except Exception as e:
                logging.critical(f"Error listing options: {str(e)}")
                print(f"Error listing options: {str(e)}")
                traceback.print_exc()
                return 1
        
        # Load database configuration
        try:
            logging.info(f"Loading database configuration from: {args.config}")
            config = load_config(args.config)
            logging.info("Database configuration loaded successfully")
        except Exception as e:
            logging.critical(f"Error loading database configuration: {str(e)}")
            print(f"Error loading database configuration: {str(e)}")
            traceback.print_exc()
            return 1
        
        # Connect to database
        try:
            logging.info("Connecting to database...")
            db_connector = DatabaseConnector(config)
            db_connector.connect()
            logging.info("Successfully connected to database")
        except Exception as e:
            logging.critical(f"Error connecting to database: {str(e)}")
            print(f"Error connecting to database: {str(e)}")
            traceback.print_exc()
            return 1
        
        # Process filters
        filters = {
            'project_id': args.project,
            'testplan_id': args.testplan,
            'build_id': args.build,
            'status': args.status,
            'start_date': args.start_date,
            'end_date': args.end_date
        }
        logging.info(f"Using filters: {filters}")
        
        # Initialize data processor
        try:
            logging.info("Initializing data processor...")
            data_processor = TestExecutionDataProcessor(db_connector)
            logging.info("Data processor initialized successfully")
        except Exception as e:
            logging.critical(f"Error initializing data processor: {str(e)}")
            print(f"Error initializing data processor: {str(e)}")
            traceback.print_exc()
            return 1
        
        # Process test execution data
        try:
            logging.info("Processing test execution data...")
            # Get test execution summary for first sheet
            hierarchical_data = data_processor.get_execution_summary(filters)
            
            # Get test suite progress data for second sheet
            suite_progress = data_processor.get_test_suite_progress(filters)
            logging.info(f"Retrieved suite progress data for {len(suite_progress)} test suites")
            
            # Add suite progress to hierarchical data for the exporter
            hierarchical_data['suite_progress'] = suite_progress
            
            # Debug logging for key missing errors
            logging.debug("Inspecting hierarchical data structure")
            if hierarchical_data:
                logging.debug(f"Top-level keys: {list(hierarchical_data.keys()) if isinstance(hierarchical_data, dict) else 'Not a dict'}")
                
                if 'metrics_by_suite' in hierarchical_data:
                    logging.debug(f"metrics_by_suite has {len(hierarchical_data['metrics_by_suite'])} suites")
                    
                    # Check for count key in each suite's metrics
                    for suite_id, suite_metrics in hierarchical_data['metrics_by_suite'].items():
                        if not isinstance(suite_metrics, dict):
                            logging.warning(f"Suite {suite_id} metrics is not a dict: {type(suite_metrics)}")
                            continue
                            
                        logging.debug(f"Suite {suite_id} metrics keys: {list(suite_metrics.keys())}")
                        
            else:
                logging.warning("No data returned from data_processor")
                
            logging.info("Data processing completed successfully")
        except KeyError as ke:
            logging.critical(f"KeyError during data processing: {str(ke)}")
            print(f"Error: Missing key '{str(ke)}' in data structure")
            traceback.print_exc()
            return 1
        except Exception as e:
            logging.critical(f"Error processing data: {str(e)}")
            print(f"Error processing data: {str(e)}")
            traceback.print_exc()
            return 1
        
        # Generate Excel file
        try:
            # Generate filename with timestamp if not specified
            if not args.output:
                timestamp = datetime.datetime.now().strftime('%Y%m%d_%H%M%S')
                filename = f"testlink_export_{timestamp}.xlsx"
            else:
                filename = args.output
            
            # Ensure output directory exists
            if not os.path.exists(output_dir):
                os.makedirs(output_dir)
                logging.info(f"Created output directory: {output_dir}")
            
            # Construct full output path
            output_file = os.path.join(output_dir, filename)
            logging.info(f"Generating Excel file: {output_file}")
            
            # Create Excel exporter and set output directly
            excel_exporter = TestExecutionExcelExporter()
            
            # Export data with complete file path
            logging.info("Calling Excel exporter to generate file...")
            excel_exporter.export_data(hierarchical_data, output_file)
            
            logging.info(f"Excel file generated successfully: {output_file}")
            print(f"Excel file generated successfully: {output_file}")
            
            # Disconnect from database
            db_connector.disconnect()
            logging.info("Disconnected from database")
            
        except KeyError as ke:
            logging.critical(f"KeyError during Excel export: {str(ke)}")
            print(f"Error: Missing key '{str(ke)}' in data structure during Excel export")
            traceback.print_exc()
            return 1
        except Exception as e:
            logging.critical(f"Error generating Excel file: {str(e)}")
            print(f"Error generating Excel file: {str(e)}")
            traceback.print_exc()
            return 1
            
    except Exception as e:
        logging.critical(f"Unhandled exception: {str(e)}")
        print(f"Error: {str(e)}")
        traceback.print_exc()
        return 1
    
    # If we reach here, everything completed successfully
    logging.info("======= Export completed successfully ========")
    return 0


def entry_point():
    """Entry point for the script when executed directly"""
    try:
        exit_code = main()
        sys.exit(exit_code)
    except Exception as e:
        print(f"Critical error: {str(e)}")
        traceback.print_exc()
        sys.exit(1)


if __name__ == '__main__':
    entry_point()
