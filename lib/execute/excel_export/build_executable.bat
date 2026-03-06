@echo off
REM Build script for TestLink Excel Export executable
REM This will create a standalone executable that includes all dependencies

echo Building TestLink Excel Export executable...

REM Install PyInstaller if not already installed
pip install pyinstaller

REM Generate the executable with all dependencies
pyinstaller --noconfirm ^
    --onefile ^
    --console ^
    --name "testlink_export" ^
    --add-data ".\*.py;." ^
    --hidden-import openpyxl ^
    --hidden-import pymysql ^
    --hidden-import argparse ^
    --hidden-import datetime ^
    --hidden-import json ^
    --hidden-import os ^
    --hidden-import sys ^
    --hidden-import re ^
    test_execution_export.py

echo.
echo Build complete! Executable can be found in the "dist" folder.
echo.
echo Usage: testlink_export --config path\to\db_config.json [other options]
echo.
