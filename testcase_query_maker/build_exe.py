#!/usr/bin/env python3
"""
Build Script for TestLink Excel Import Tool

This script creates a standalone executable file (.exe) for the TestLink Excel Import Tool
using PyInstaller. It bundles all necessary dependencies into a single file.
"""

import os
import sys
import subprocess
import shutil

def ensure_pyinstaller_installed():
    """Check if PyInstaller is installed, and install it if not."""
    try:
        import PyInstaller
        print("PyInstaller is already installed.")
        return True
    except ImportError:
        print("PyInstaller is not installed. Installing now...")
        try:
            subprocess.check_call([sys.executable, "-m", "pip", "install", "pyinstaller"])
            print("PyInstaller installed successfully.")
            return True
        except Exception as e:
            print(f"Error installing PyInstaller: {e}")
            return False

def build_exe():
    """Build the executable file using PyInstaller."""
    # Configuration
    main_script = "interactive-import-script.py"
    output_name = "TestLink_Excel_Import_Tool_V3"
    icon_file = None  # You could add an icon file here if you have one
    
    # Check if the main script exists
    if not os.path.isfile(main_script):
        print(f"Error: Main script '{main_script}' not found.")
        return False
    
    # Ensure dependencies exist
    required_files = ["inspect_excel.py", "excel_cleanup.py"]
    for file in required_files:
        if not os.path.isfile(file):
            print(f"Error: Required file '{file}' not found.")
            return False
    
    # Build the command
    command = [
        sys.executable, "-m", "PyInstaller",
        "--onefile",  # Create a single file
        "--clean",  # Clean PyInstaller cache and remove temporary files
        "--name", output_name,  # Name of the output executable
        "--console",  # Console application (not a windowed one)
    ]
    
    # Add icon if specified
    if icon_file and os.path.isfile(icon_file):
        command.extend(["--icon", icon_file])
    
    # Add the main script
    command.append(main_script)
    
    # Execute PyInstaller
    print(f"Building executable for {main_script}...")
    print(f"Command: {' '.join(command)}")
    
    try:
        subprocess.check_call(command)
        exe_path = os.path.join("dist", f"{output_name}.exe")
        
        if os.path.isfile(exe_path):
            print(f"\nBuild successful! Executable created at: {exe_path}")
            return True
        else:
            print(f"Error: Executable file not found at expected path: {exe_path}")
            return False
    except Exception as e:
        print(f"Error building executable: {e}")
        return False

def cleanup():
    """Clean up build files and directories."""
    dirs_to_remove = ["build", "__pycache__"]
    files_to_remove = [f"{output_name}.spec" for output_name in ["TestLink_Excel_Import_Tool"]]
    
    for dir_name in dirs_to_remove:
        if os.path.isdir(dir_name):
            try:
                shutil.rmtree(dir_name)
                print(f"Removed directory: {dir_name}")
            except Exception as e:
                print(f"Error removing directory {dir_name}: {e}")
    
    for file_name in files_to_remove:
        if os.path.isfile(file_name):
            try:
                os.remove(file_name)
                print(f"Removed file: {file_name}")
            except Exception as e:
                print(f"Error removing file {file_name}: {e}")

if __name__ == "__main__":
    print("===== Building TestLink Excel Import Tool Executable =====")
    
    if ensure_pyinstaller_installed():
        if build_exe():
            # Ask if user wants to clean up build files
            cleanup_choice = input("\nClean up build files? (y/n, default: y): ").strip().lower() or 'y'
            if cleanup_choice == 'y':
                cleanup()
                
            print("\nBuild process completed!")
        else:
            print("\nBuild process failed.")
    else:
        print("\nUnable to install PyInstaller. Build process aborted.")
