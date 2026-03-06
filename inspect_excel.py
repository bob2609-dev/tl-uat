#!/usr/bin/env python3
"""
Excel Inspector Script

This script reads the specified Excel file and shows the contents of the "Test Case S. No." column
or equivalent column (based on position) to help diagnose data issues.
"""

import pandas as pd
import os
import sys

def inspect_excel(excel_file):
    # Check if file exists
    if not os.path.exists(excel_file):
        print(f"Error: File '{excel_file}' not found.")
        return False
    
    # Get available sheet names
    try:
        xls = pd.ExcelFile(excel_file)
        sheet_names = xls.sheet_names
        
        print(f"\nAvailable sheets in the Excel file: {', '.join(sheet_names)}")
        
        if len(sheet_names) > 1:
            sheet_name = input(f"Enter the sheet name to inspect (default: {sheet_names[0]}): ").strip() or sheet_names[0]
            if sheet_name not in sheet_names:
                sheet_name = sheet_names[0]
                print(f"Sheet not found. Using '{sheet_name}' instead.")
        else:
            sheet_name = sheet_names[0]
            print(f"Using sheet: {sheet_name}")
            
        # Read the Excel file
        df = pd.read_excel(excel_file, sheet_name=sheet_name)
        
        # Display column names
        print("\nFound columns:")
        for i, col in enumerate(df.columns):
            print(f"{i+1}. '{col}'")
        
        # Try to identify the Test Case S. No. column
        test_case_col = None
        
        # First try by name
        potential_names = ["Test Case S. No.", "Test Case S.No.", "TestCaseNo", "Test Case Number"]
        for name in potential_names:
            if name in df.columns:
                test_case_col = name
                break
        
        # If not found by name, use the 4th column (index 3)
        if test_case_col is None and len(df.columns) > 3:
            test_case_col = df.columns[3]
            
        if test_case_col:
            print(f"\nInspecting column: '{test_case_col}'")
            
            # Display all values in the identified column
            print("\nValues in the Test Case S. No. column:")
            for idx, value in enumerate(df[test_case_col]):
                if pd.isna(value) or value == "" or str(value).strip() == "":
                    status = "EMPTY"
                else:
                    status = "OK"
                print(f"Row {idx+1}: {value} - [{status}]")
                
            # Count and display empty cells
            empty_rows = df[df[test_case_col].isna() | (df[test_case_col].astype(str).str.strip() == "")].index.to_list()
            if empty_rows:
                print(f"\nFound {len(empty_rows)} empty rows at positions: {[i+1 for i in empty_rows]}")
            else:
                print("\nNo empty values found in the Test Case S. No. column.")
        else:
            print("\nCould not identify the Test Case S. No. column.")
            
    except Exception as e:
        print(f"Error reading Excel file: {str(e)}")
        return False
        
    return True

if __name__ == "__main__":
    if len(sys.argv) > 1:
        excel_file = sys.argv[1]
    else:
        excel_file = input("Enter the path to your Excel file: ").strip()
        
    inspect_excel(excel_file)
