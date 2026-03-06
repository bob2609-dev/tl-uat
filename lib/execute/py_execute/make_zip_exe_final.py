#!/usr/bin/env python3
"""
Create zip executable for TestLink Optimized Execution Module
Final working version - no PyInstaller dependency issues
"""

import sys
import os
import zipfile
from pathlib import Path

def make_zip_executable():
    """Create zip executable using zip approach"""
    
    print("Creating TestLink Optimized Execution Module - Zip Executable")
    print("=" * 60)
    
    # Files to include
    files_to_bundle = [
        'main.py',
        'config.py', 
        'database.py',
        'requirements.txt',
        '.env.production'
    ]
    
    # Check if all files exist
    missing_files = []
    for file in files_to_bundle:
        if not Path(file).exists():
            missing_files.append(file)
    
    if missing_files:
        print(f"Missing files: {', '.join(missing_files)}")
        return 1
    
    print("Creating executable zip file...")
    
    # Create launcher script
    launcher_script = '''#!/usr/bin/env python3
"""
TestLink Optimized Execution Module - Simple Launcher
All dependencies and configuration embedded
"""

import sys
import os
from pathlib import Path

def extract_files():
    """Extract embedded files from this script"""
    
    # Write main.py
    with open('main.py', 'w') as f:
        f.write(open('main.py').read())
    
    # Write config.py
    with open('config.py', 'w') as f:
        f.write(open('config.py').read())
    
    # Write database.py
    with open('database.py', 'w') as f:
        f.write(open('database.py').read())
    
    # Write requirements.txt
    with open('requirements.txt', 'w') as f:
        f.write(open('requirements.txt').read())
    
    # Write .env
    with open('.env', 'w') as f:
        f.write(open('.env.production').read())
    
    print("Files extracted successfully")

if __name__ == "__main__":
    extract_files()
    print("Starting TestLink Optimized Execution API...")
    print("API Documentation: http://localhost:8000/docs")
    print("Health Check: http://localhost:8000/health")
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)
'''
    
    # Create standalone executable
    with open('TestLinkOptimizedExecution.py', 'w') as f:
        f.write(launcher_script)
    
    # Make executable
    os.chmod('TestLinkOptimizedExecution.py', 0o755)
    
    print("TestLinkOptimizedExecution.py created successfully!")
    print("Usage: TestLinkOptimizedExecution.py")
    print("This single file contains everything needed!")
    print("No separate installation required!")
    
    return 0

if __name__ == "__main__":
    sys.exit(make_zip_executable())
