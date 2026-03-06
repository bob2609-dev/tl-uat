#!/usr/bin/env python3
"""
Excel Column Updater Script

This script reads the specified Excel file and updates column names to match
the expected column structure in the interactive-import-script.py.
It creates a new Excel file with the updated column names.
It also removes excess empty rows (5+ consecutive empty rows after the last row with data).
"""

import pandas as pd
import os
import sys
import numpy as np
import argparse

def clean_empty_rows(df, verbose=True):
    """Remove excess empty rows (5+ consecutive empty rows after last data row)."""
    # Create a mask to identify empty rows (all values are NaN or empty strings)
    is_row_empty = df.apply(lambda row: all(pd.isna(val) or 
                                           (isinstance(val, str) and not str(val).strip()) 
                                           for val in row), axis=1)
    
    if verbose:
        print(f"\nTotal rows: {len(df)}")
        print(f"Empty rows detected: {is_row_empty.sum()}")
    
    # Find the last row that contains data
    if not is_row_empty.all():
        # Find the last non-empty row
        non_empty_indices = np.where(~is_row_empty)[0]
        if len(non_empty_indices) > 0:
            last_data_row = non_empty_indices[-1]
            
            if verbose:
                print(f"Last data row index: {last_data_row}")
            
            # Count consecutive empty rows after the last data row
            empty_row_count = 0
            cut_off_row = len(df)
            
            for i in range(last_data_row + 1, len(df)):
                if is_row_empty[i]:
                    empty_row_count += 1
                    if empty_row_count >= 5:  # If we find 5+ consecutive empty rows
                        cut_off_row = i - 4  # Keep at most 4 empty rows after the last data
                        break
                else:
                    empty_row_count = 0
            
            # Cut off the dataframe at the determined row
            if cut_off_row < len(df):
                removed_count = len(df) - cut_off_row
                if verbose:
                    print(f"\nRemoving {removed_count} excess empty rows after index {cut_off_row}")
                return df.iloc[:cut_off_row].copy(), removed_count
    
    return df, 0

def update_excel_columns(input_excel, output_excel=None, clean_only=False, verbose=True):
    # Check if input file exists
    if not os.path.exists(input_excel):
        print(f"Error: File '{input_excel}' not found.")
        return False
    
    # If output file not specified, create one with "_updated" suffix
    if output_excel is None:
        file_name, file_ext = os.path.splitext(input_excel)
        output_excel = f"{file_name}_updated{file_ext}"
    
    # Get available sheet names
    try:
        xls = pd.ExcelFile(input_excel)
        sheet_names = xls.sheet_names
        
        print(f"\nAvailable sheets in the Excel file: {', '.join(sheet_names)}")
        
        # Process each sheet
        writer = pd.ExcelWriter(output_excel, engine='openpyxl')
        
        for sheet_name in sheet_names:
            print(f"\nProcessing sheet: {sheet_name}")
            
            # Read the Excel sheet
            df = pd.read_excel(input_excel, sheet_name=sheet_name)
            
            # Display original column names
            print("\nOriginal columns:")
            for i, col in enumerate(df.columns):
                print(f"{i+1}. '{col}'")
            
            # Expected column structure based on interactive-import-script.py
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
            
            # Map existing columns to expected columns
            # This preserves data while updating column names
            column_mapping = {}
            
            # If we have enough columns, map them by position
            if len(df.columns) >= len(expected_columns):
                for i, expected_col in enumerate(expected_columns):
                    column_mapping[df.columns[i]] = expected_col
            else:
                # If not enough columns, map what we can and warn about the rest
                for i, col in enumerate(df.columns):
                    if i < len(expected_columns):
                        column_mapping[col] = expected_columns[i]
                print(f"Warning: The Excel file has fewer columns ({len(df.columns)}) than expected ({len(expected_columns)})")
            
            # Rename columns, unless clean_only is True
            if not clean_only:
                df = df.rename(columns=column_mapping)
                
                # Display the new column names
                print("\nUpdated columns:")
                for i, col in enumerate(df.columns):
                    print(f"{i+1}. '{col}'")
            else:
                print("\nSkipping column rename (clean-only mode)")
                
            # Clean up excess empty rows
            original_row_count = len(df)
            df, rows_removed = clean_empty_rows(df)
            if rows_removed > 0:
                print(f"Removed {rows_removed} empty rows. New row count: {len(df)}")
            
            # Write the updated dataframe to the output Excel file
            df.to_excel(writer, sheet_name=sheet_name, index=False)
        
        # Save the output Excel file
        writer.close()
        
        print(f"\nUpdated Excel file saved as: {output_excel}")
        
    except Exception as e:
        print(f"Error processing Excel file: {str(e)}")
        return False
        
    return True

def parse_arguments():
    """Parse command line arguments."""
    parser = argparse.ArgumentParser(description="Excel Column Updater and Empty Row Cleanup Tool")
    
    parser.add_argument('input_excel', nargs='?', default=None,
                        help='Path to the input Excel file')
    
    parser.add_argument('output_excel', nargs='?', default=None,
                        help='Path to save the output Excel file (optional, will auto-generate if not provided)')
    
    parser.add_argument('-v', '--verbose', action='store_true',
                        help='Enable verbose output')
                        
    parser.add_argument('-c', '--clean-only', action='store_true',
                        help='Only clean empty rows, do not rename columns')
    
    return parser.parse_args()

if __name__ == "__main__":
    # Parse command line arguments
    args = parse_arguments()
    
    # If input file not provided, prompt for it
    input_excel = args.input_excel
    if input_excel is None:
        input_excel = input("Enter the path to your Excel file: ").strip()
    
    # Validate input file
    if not os.path.exists(input_excel):
        print(f"Error: File '{input_excel}' not found.")
        sys.exit(1)
    
    # If output file not provided, create one with "_updated" suffix
    output_excel = args.output_excel
    if output_excel is None:
        file_name, file_ext = os.path.splitext(input_excel)
        output_suffix = "_cleaned" if args.clean_only else "_updated"
        output_excel = f"{file_name}{output_suffix}{file_ext}"
        
    # Perform the update/cleanup
    update_excel_columns(input_excel, output_excel, clean_only=args.clean_only, verbose=args.verbose)
