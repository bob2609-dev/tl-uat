#!/usr/bin/env python3
"""
Interactive TestLink Excel Import Script

This script reads test cases from an Excel file and
generates SQL statements to insert them into your TestLink database,
ensuring they appear correctly in the UI.

Requirements:
- Python 3.6+
- pandas and openpyxl libraries (install via pip)

Usage:
1. Install dependencies: pip install pandas openpyxl
2. Run: python interactive-import-script.py
3. For help: python interactive-import-script.py --help
"""

import pandas as pd
import datetime
import re
import os
import argparse

# Import the custom scripts
try:
    from inspect_excel import inspect_excel
    from excel_cleanup import update_excel_columns
    scripts_imported = True
except ImportError:
    scripts_imported = False
    print("Warning: Helper scripts not found. Will continue without preprocessing the Excel file.")

def get_test_case_status():
    """Get the test case status from user input."""
    status_map = {
        1: "Draft",
        2: "Ready for review",
        3: "Review in progress",
        4: "Rework",
        5: "Obsolete",
        6: "Future",
        7: "Final"
    }
    
    print("\nAvailable test case statuses:")
    for status_id, status_name in status_map.items():
        print(f"{status_id}. {status_name}")
    
    while True:
        try:
            status = int(input("\nEnter the test case status (1-7, default: 1): ").strip() or "1")
            if 1 <= status <= 7:
                return status
            print("Please enter a number between 1 and 7")
        except ValueError:
            print("Please enter a valid number")

def get_author_id():
    """Get the author ID from user input."""
    while True:
        try:
            author_id = int(input("Enter the author ID (default: 1): ").strip() or "1")
            if author_id > 0:
                return author_id
            print("Error: Author ID must be a positive number.")
        except ValueError:
            print("Error: Please enter a valid number.")

def get_importance():
    """Get the importance level from user input."""
    importance_map = {
        1: "Low",
        2: "Medium",
        3: "High"
    }
    
    print("\nAvailable importance levels:")
    for imp_id, imp_name in importance_map.items():
        print(f"{imp_id}. {imp_name}")
    
    while True:
        try:
            importance = int(input("\nEnter the importance level (1-3, default: 2): ").strip() or "2")
            if 1 <= importance <= 3:
                return importance
            print("Please enter a number between 1 and 3")
        except ValueError:
            print("Please enter a valid number")

def get_execution_type():
    """Get the execution type from user input."""
    execution_map = {
        1: "Manual",
        2: "Automated"
    }
    
    print("\nAvailable execution types:")
    for exec_id, exec_name in execution_map.items():
        print(f"{exec_id}. {exec_name}")
    
    while True:
        try:
            execution_type = int(input("\nEnter the execution type (1-2, default: 1): ").strip() or "1")
            if 1 <= execution_type <= 2:
                return execution_type
            print("Please enter a number between 1 and 2")
        except ValueError:
            print("Please enter a valid number")

def get_sheet_name(excel_file):
    """Get the Excel sheet name from user input."""
    try:
        # Get available sheet names
        xls = pd.ExcelFile(excel_file)
        sheet_names = xls.sheet_names
        
        print("\nAvailable sheets in the Excel file:")
        for i, name in enumerate(sheet_names, 1):
            print(f"{i}. {name}")
        
        if len(sheet_names) == 1:
            return sheet_names[0]
        
        while True:
            sheet_input = input(f"\nEnter the sheet name or number (1-{len(sheet_names)}, default: 1): ").strip() or "1"
            try:
                # If input is a number, use it as an index
                sheet_index = int(sheet_input) - 1
                if 0 <= sheet_index < len(sheet_names):
                    return sheet_names[sheet_index]
                print(f"Please enter a number between 1 and {len(sheet_names)}")
            except ValueError:
                # If input is not a number, use it as a sheet name
                if sheet_input in sheet_names:
                    return sheet_input
                print(f"Sheet '{sheet_input}' not found. Please enter a valid sheet name or number.")
    except Exception as e:
        print(f"Error reading Excel sheets: {e}")
        return "Sheet1"  # Default fallback

def get_node_order():
    """Get the starting node order from user input."""
    while True:
        try:
            node_order = int(input("Enter the starting node order (default: 1020): ").strip() or "1020")
            if node_order > 0:
                return node_order
            print("Error: Node order must be a positive number.")
        except ValueError:
            print("Error: Please enter a valid number.")

def main(args=None):
    # If command-line arguments were provided, use them
    # Otherwise, prompt the user for input interactively
    if args and args.excel_file and os.path.exists(args.excel_file):
        excel_file = args.excel_file
    else:
        # Get input file path from user
        while True:
            excel_file = input("Enter the path to your Excel file: ").strip()
            if os.path.exists(excel_file):
                break
            print(f"Error: File '{excel_file}' not found. Please try again.")
    
    # Preprocess the Excel file using the helper scripts if available
    preprocessed_file = excel_file
    if scripts_imported:
        print("\nInspecting the Excel file...")
        inspect_excel(excel_file)
        
        # Use command line arguments instead of prompting if available
        if args and hasattr(args, 'preprocess'):
            # Use the preprocess and clean_only flags from arguments
            if args.preprocess:
                # Use clean_only flag from arguments
                clean_only = args.clean_only
                print(f"\nAutomatically preprocessing file with clean_only={clean_only}")
                
                file_name, file_ext = os.path.splitext(excel_file)
                updated_file = f"{file_name}_processed{file_ext}"
                
                if update_excel_columns(excel_file, updated_file, clean_only=clean_only, verbose=True):
                    preprocessed_file = updated_file
                    print(f"\nUsing preprocessed file: {preprocessed_file}")
        else:
            # Use interactive prompts when no arguments provided
            update_choice = input("\nWould you like to preprocess the Excel file? (y/n, default: y): ").strip().lower() or 'y'
            if update_choice == 'y':
                # Ask if they want to just clean empty rows or also rename columns
                clean_only_choice = input("\nOnly clean empty rows? Column names will remain unchanged. (y/n, default: n): ").strip().lower() or 'n'
                clean_only = (clean_only_choice == 'y')
                
                file_name, file_ext = os.path.splitext(excel_file)
                updated_file = f"{file_name}_processed{file_ext}"
                
                if update_excel_columns(excel_file, updated_file, clean_only=clean_only, verbose=True):
                    preprocessed_file = updated_file
                    print(f"\nUsing preprocessed file: {preprocessed_file}")
    
    excel_file = preprocessed_file

    if args and args.output_sql:
        output_sql = args.output_sql
        if not output_sql.endswith('.sql'):
            output_sql += '.sql'
    else:
        output_sql = input("Enter the name for the output SQL file (e.g., output.sql): ").strip()
        if not output_sql.endswith('.sql'):
            output_sql += '.sql'

    # Get sheet name
    if args and args.sheet_name:
        sheet_name = args.sheet_name
    else:
        sheet_name = get_sheet_name(excel_file)

    # Get author ID
    if args and args.author_id:
        author_id = args.author_id
    else:
        author_id = get_author_id()

    # Get test suite ID
    if args and args.test_suite_id:
        test_suite_id = args.test_suite_id
    else:
        while True:
            try:
                test_suite_id = int(input("Enter the test suite ID (default: 2): ").strip() or "2")
                if test_suite_id > 0:
                    break
                print("Error: Test suite ID must be a positive number.")
            except ValueError:
                print("Error: Please enter a valid number.")

    # Get importance level
    if args and args.importance:
        importance = args.importance
    else:
        importance = get_importance()

    # Get execution type
    if args and args.execution_type:
        execution_type = args.execution_type
    else:
        execution_type = get_execution_type()

    # Get test case status from user
    if args and args.test_case_status:
        test_case_status = args.test_case_status
    else:
        test_case_status = get_test_case_status()
        
    # Get starting node order
    if args and args.node_order:
        start_node_order = args.node_order
    else:
        start_node_order = get_node_order()

    # Configuration
    config = {
        'excel_file': excel_file,
        'output_sql': output_sql,
        'author_id': author_id,
        'test_suite_id': test_suite_id,
        'importance': importance,
        'execution_type': execution_type,
        'status': 1,           # Valid (1=Valid)
        'active': 1,           # Active
        'is_open': 1,          # Open
        'test_case_status': test_case_status,
        'sheet_name': sheet_name,
        'start_node_order': start_node_order,
    }

    # Print configuration
    print("\nConfiguration:")
    print(f"- Excel file: {config['excel_file']}")
    print(f"- Output SQL: {config['output_sql']}")
    print(f"- Test Suite ID: {config['test_suite_id']}")
    print(f"- Author ID: {config['author_id']}")
    print(f"- Test Case Status: {config['test_case_status']}")

    # Map custom fields to their IDs in TestLink
    custom_field_map = {
        'scenario_id': 1,     # "Scenario ID"
        'primary_module': 2,  # "Primary Module"
        'sub_scenario': 3,    # "Sub-Scenario / Action"
        'test_case_description': 4, # "Test Case Description"
        'test_type': 5,       # "Test Type"
        'test_script': 6,     # "Test Script"
        'test_execution_path': 7, # "Test Execution Path"
        'expected_results': 8, # "Expected Results (Functional)"
        'er_process': 9,      # "Expected Results (Process & Business Rules)"
    }

    # Read the Excel file
    try:
        df = pd.read_excel(config['excel_file'], sheet_name=config['sheet_name'])
        print(f"Found {len(df)} rows in the Excel file.")
    except Exception as e:
        print(f"Error reading Excel file: {e}")
        return
    
    # Map the columns
    column_names = df.columns.tolist()
    print("\nFound columns:", ", ".join(column_names))
    print(f"Number of columns found: {len(column_names)}")
    
    # Expected column structure
    expected_columns = [
        'Scenario/ System Functionality Primary Module/ Function Name',  # primary_module
        'Scenario ID / Function ScreenID Primary Module/ Function ID',   # scenario_id
        'Sub-Scenarion/Action e.g. New, Modify, Close',                # sub_scenario
        'Test Case S. No.',                                            # test_case_number
        'Test Case Description',                                       # test_case_description
        'Test Type (-ve/+ve)',                                        # test_type
        'Test script',                                                # test_script
        'Test Execution path',                                        # test_execution_path
        'Expected Results (Functional)',                              # expected_results
        'Expected Results (Process & Business Rules )'                # er_process
    ]
    
    print("\nExpected column structure:")
    for i, col in enumerate(expected_columns, 1):
        print(f"{i}. {col}")
    
    # Clean column headers (strip whitespace and newlines)
    clean_columns = [re.sub(r'\s+', ' ', col).strip() for col in column_names]
    df.columns = clean_columns
    
    print("\nCleaned column headers:")
    for i, col in enumerate(clean_columns):
        print(f"{i+1}. '{col}'")
    
    # Create mapping of our expected fields to actual column names
    num_columns = len(df.columns)
    column_map = {
        'primary_module': df.columns[0] if num_columns > 0 else None,  # First column
        'scenario_id': df.columns[1] if num_columns > 1 else None,     # Second column 
        'sub_scenario': df.columns[2] if num_columns > 2 else None,    # Third column
        'test_case_number': df.columns[3] if num_columns > 3 else None, # Fourth column
        'test_case_description': df.columns[4] if num_columns > 4 else None, # Fifth column
        'test_type': df.columns[5] if num_columns > 5 else None,      # Sixth column
        'test_script': df.columns[6] if num_columns > 6 else None,    # Seventh column
        'test_execution_path': df.columns[7] if num_columns > 7 else None, # Eighth column
        'expected_results': df.columns[8] if num_columns > 8 else None, # Ninth column
        'er_process': df.columns[9] if num_columns > 9 else None,     # Tenth column
    }
    
    print("\nColumn mapping details:")
    for key, value in column_map.items():
        print(f"  {key} -> '{value}'")
        if key == 'test_case_number':
            print(f"  First few values in test_case_number column:")
            try:
                print(df[value].head())
            except:
                print("  Error accessing test_case_number column!")
    
    print("\nColumn mapping:")
    for key, value in column_map.items():
        if value is not None:
            print(f"  {key}: {value}")
        else:
            print(f"  {key}: Not found in Excel (will use TEMPLATE_EMPTY)")
            # Add the missing column to the dataframe with NaN values
            df[key] = pd.NA
            
    # Verify if we have the test_case_number column as it's required
    if column_map['test_case_number'] is None:
        print("\nError: The Excel file must have a 'Test Case S. No.' column (4th column)")
        return
    
    # Open SQL file for writing
    with open(config['output_sql'], 'w', encoding='utf-8') as sql_file:
        # Write SQL header
        sql_file.write(f"-- Enhanced TestLink Import SQL Script\n")
        sql_file.write(f"-- Generated on {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        sql_file.write(f"-- Test Suite ID: {config['test_suite_id']}\n\n")
        sql_file.write(f"START TRANSACTION;\n\n")
        
        # Process each row
        successful_rows = 0
        node_order = config['start_node_order']
        tc_counter = 0  # Counter for generating test case numbers
        
        print("\nChecking test case numbers in rows:")
        
        # First pass: generate test case numbers
        for index, row in df.iterrows():
            if pd.isna(row[column_map['test_case_number']]):
                new_tc = f"NO_TC_{tc_counter}"
                print(f"Row {index+1}: Generating test case number {new_tc}")
                df.at[index, column_map['test_case_number']] = new_tc
                tc_counter += 1
        
        for index, row in df.iterrows():
            # Get values from row, use "TEMPLATE_EMPTY" for any empty or missing values
            def get_column_value(column_key):
                if column_map[column_key] is None:
                    return 'TEMPLATE_EMPTY'
                try:
                    value = row[column_map[column_key]]
                    return escape_sql_string(value) if not pd.isna(value) else 'TEMPLATE_EMPTY'
                except (KeyError, TypeError):
                    return 'TEMPLATE_EMPTY'
            
            # Get values using the safe getter function
            primary_module = get_column_value('primary_module')
            scenario_id = get_column_value('scenario_id')
            sub_scenario = get_column_value('sub_scenario')
            test_case_number = row[column_map['test_case_number']]  # Use the actual test case number
            test_case_description = get_column_value('test_case_description')
            test_type = get_column_value('test_type')
            test_script = get_column_value('test_script')
            test_execution_path = get_column_value('test_execution_path')
            expected_results = get_column_value('expected_results')
            er_process = get_column_value('er_process')

            # Log empty fields that were replaced (only for the first few rows)
            if index < 5:  # Only show for first 5 rows to avoid too much output
                empty_fields = []
                for field, value in [
                    ('primary_module', primary_module),
                    ('scenario_id', scenario_id),
                    ('sub_scenario', sub_scenario),
                    ('test_case_number', test_case_number),
                    ('test_case_description', test_case_description),
                    ('test_type', test_type),
                    ('test_script', test_script),
                    ('test_execution_path', test_execution_path),
                    ('expected_results', expected_results),
                    ('er_process', er_process)
                ]:
                    if value == 'TEMPLATE_EMPTY':
                        empty_fields.append(field)
                if empty_fields:
                    print(f"Row {index+1}: Found empty fields in: {', '.join(empty_fields)}")
            
            # Skip test cases that are already imported
            if test_case_exists(sql_file, test_case_number):
                print(f"Skipping existing test case: {test_case_number}")
                continue
            
            # Format test case description as HTML
            summary_html = f"<p>{test_case_description}</p>"
            
            # Get normalized test type (remove " test" if present)
            test_type_normalized = test_type.replace(" test", "")
            
            # Write SQL for nodes_hierarchy with correct parent_id and node_order
            sql_file.write(f"""
-- Test Case {index+1}: {test_case_number}
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES ({config['test_suite_id']}, '{test_case_number}', 3, {node_order}); -- node_type_id 3 = test case
SET @tc_id = LAST_INSERT_ID();

-- Test Case Version node
INSERT INTO `nodes_hierarchy` (`parent_id`, `name`, `node_type_id`, `node_order`)
VALUES (@tc_id, '{test_case_number}', 4, 0); -- node_type_id 4 = test case version
SET @tcversion_id = LAST_INSERT_ID();

-- Insert into tcversions table
INSERT INTO `tcversions` 
(`id`, `tc_external_id`, `version`, `author_id`, `summary`, 
`importance`, `execution_type`, `creation_ts`, `modification_ts`, 
`status`, `active`, `is_open`)
VALUES 
(@tcversion_id, {index+1}, 1, {config['author_id']}, 
'{summary_html}', 
{config['importance']}, {config['execution_type']}, NOW(), NOW(), 
{config['test_case_status']}, {config['active']}, {config['is_open']});

-- Insert custom field values
""")
            
            # Insert custom field values
            if scenario_id:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['scenario_id']}, @tcversion_id, '{scenario_id}');\n")
                
            if primary_module:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['primary_module']}, @tcversion_id, '{primary_module}');\n")
                
            if sub_scenario:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['sub_scenario']}, @tcversion_id, '{sub_scenario}');\n")
                
            if test_case_description:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['test_case_description']}, @tcversion_id, '{test_case_description}');\n")
                
            if test_type:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['test_type']}, @tcversion_id, '{test_type_normalized}');\n")
                
            if test_script:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['test_script']}, @tcversion_id, '{test_script}');\n")
                
            if test_execution_path:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['test_execution_path']}, @tcversion_id, '{test_execution_path}');\n")
                
            if expected_results:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['expected_results']}, @tcversion_id, '{expected_results}');\n")
                
            if er_process:
                sql_file.write(f"INSERT INTO `cfield_design_values` (`field_id`, `node_id`, `value`) "
                               f"VALUES ({custom_field_map['er_process']}, @tcversion_id, '{er_process}');\n")
            
            sql_file.write("\n")  # Add newline between test cases
            successful_rows += 1
            node_order += 1
        
        # Write SQL footer
        sql_file.write("COMMIT;\n")
        
        print(f"\nProcessed {successful_rows} test cases successfully.")
        print(f"SQL file has been written to: {config['output_sql']}")

def escape_sql_string(text):
    """Escape a string for use in SQL."""
    if pd.isna(text):
        return 'TEMPLATE_EMPTY'
    return str(text).replace("'", "''")

def test_case_exists(sql_file, test_case_number):
    """Check if test case already exists (placeholder for actual check)."""
    # In a real implementation, this would query the database
    # For now, we'll just return False to allow all imports
    return False

def parse_arguments():
    """Parse command line arguments."""
    parser = argparse.ArgumentParser(description="Interactive TestLink Excel Import Script")
    
    parser.add_argument('-f', '--excel-file', 
                        help='Path to the Excel file containing test cases')
    
    parser.add_argument('-o', '--output-sql', 
                        help='Name for the output SQL file (e.g., output.sql)')
    
    parser.add_argument('-s', '--test-suite-id', type=int,
                        help='Test suite ID (default: 2)')
    
    parser.add_argument('-t', '--test-case-status', type=int, choices=range(1, 8),
                        help='Test case status (1=Draft, 2=Ready for review, 3=Review in progress, '
                             '4=Rework, 5=Obsolete, 6=Future, 7=Final)')
    
    parser.add_argument('-a', '--author-id', type=int,
                        help='Author ID (default: 1)')
    
    parser.add_argument('-i', '--importance', type=int, choices=range(1, 4),
                        help='Importance level (1=Low, 2=Medium, 3=High)')
    
    parser.add_argument('-e', '--execution-type', type=int, choices=[1, 2],
                        help='Execution type (1=Manual, 2=Automated)')
    
    parser.add_argument('--sheet-name',
                        help='Excel sheet name (default: first sheet)')
    
    parser.add_argument('-n', '--node-order', type=int,
                        help='Starting node order (default: 1020)')
                        
    # Add non-interactive mode arguments
    parser.add_argument('--preprocess', action='store_true',
                       help='Automatically preprocess the Excel file')
                       
    parser.add_argument('--no-preprocess', dest='preprocess', action='store_false',
                       help='Do not preprocess the Excel file')
                       
    parser.add_argument('--clean-only', action='store_true',
                       help='Only clean empty rows, do not modify column names')
                       
    parser.set_defaults(preprocess=True, clean_only=False)
    
    return parser.parse_args()

if __name__ == "__main__":
    args = parse_arguments()
    main(args)
