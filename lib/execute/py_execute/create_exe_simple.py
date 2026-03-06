#!/usr/bin/env python3
"""
Create true executable for TestLink Optimized Execution Module
Simple version without Unicode issues
"""

import sys
import os
import subprocess
from pathlib import Path

def create_executable():
    """Create standalone executable using PyInstaller"""
    
    print("Creating TestLink Optimized Execution Module - Executable")
    print("=" * 60)
    
    # Check if PyInstaller is available
    try:
        subprocess.run(['pyinstaller', '--version'], capture_output=True, check=True)
        print("PyInstaller found")
    except (subprocess.CalledProcessError, FileNotFoundError):
        print("PyInstaller not found!")
        print("Installing PyInstaller...")
        try:
            subprocess.run([sys.executable, '-m', 'pip', 'install', 'pyinstaller'], check=True)
            print("PyInstaller installed successfully")
        except Exception as e:
            print(f"Failed to install PyInstaller: {e}")
            return 1
    
    # Files to include in executable
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
    
    print("Creating executable with PyInstaller...")
    
    # Create PyInstaller spec file
    spec_content = '''# -*- mode: python ; coding: utf-8 -*-

block_cipher_used = None

a = Analysis(
    ['main.py'],
    pathex=[],
    binaries=[],
    datas=[
        ('config.py', '.'),
        ('database.py', '.'),
        ('requirements.txt', '.'),
        ('.env.production', '.'),
    ],
    hiddenimports=[],
    hookspath=[],
    hooksconfig={},
    runtime_hooks=[],
    excludes=[],
    win_no_prefer_redirects=False,
    win_private_assemblies=False,
    cipher=block_cipher_used,
    noarchive=False,
    strip=False,
    upx_dir=None,
    upx_exclude=[],
    runtime_tmpdir=None,
    console=True,
    disable_windowed_traceback=False,
    argv_emulation=False,
    target_arch=None,
    codesign_identity=None,
    entitlements_file=None,
    name='TestLinkOptimizedExecution',
    debug=False,
    bootloader_ignore_signals=False,
    strip=False,
    upx=True,
    upx_exclude=[],
    runtime_tmpdir=None,
    console=True,
    icon=None,
    version=None,
    uac_admin=False,
    uac_uiaccess=False,
    onefile=False,
    onefile=None,
    distpath='dist',
    workpath='build',
    specpath='.',
)

pyz = PYZ(a.pure, a.zipped_data, cipher=block_cipher_used)
exe = EXE(pyz, a.name, a.binaries, a.zipfiles, a.datas, upx_dir=UPX)
coll = COLLECT(exe, a.binaries)
'''
    
    with open('TestLinkOptimizedExecution.spec', 'w') as f:
        f.write(spec_content)
    
    print("Spec file created")
    
    # Run PyInstaller
    try:
        result = subprocess.run([
            'pyinstaller', 
            '--clean',
            '--onefile',
            '--windowed',
            '--name=TestLinkOptimizedExecution',
            'TestLinkOptimizedExecution.spec'
        ], capture_output=True, text=True)
        
        if result.returncode == 0:
            print("Executable created successfully!")
            print("Location: dist/TestLinkOptimizedExecution.exe")
            print("Usage: TestLinkOptimizedExecution.exe")
            print("This single .exe contains everything needed!")
            print("No separate installation required!")
            
            return 0
        else:
            print("PyInstaller failed")
            return 1
            
    except Exception as e:
        print(f"Error creating executable: {e}")
        return 1

if __name__ == "__main__":
    sys.exit(create_executable())
