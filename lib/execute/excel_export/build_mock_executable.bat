@echo off
REM Build script for TestLink Excel Export Mock executable
REM This will create a standalone executable that uses mock data instead of a real database connection

echo Building TestLink Mock Excel Export executable...

REM Install PyInstaller if not already installed
pip install pyinstaller

REM Generate the executable with all dependencies
pyinstaller --noconfirm ^
    --onefile ^
    --console ^
    --name "testlink_export_mock" ^
    --add-data ".\*.py;." ^
    --hidden-import openpyxl ^
    --hidden-import pymysql ^
    --hidden-import argparse ^
    --hidden-import datetime ^
    --hidden-import json ^
    --hidden-import os ^
    --hidden-import sys ^
    --hidden-import re ^
    test_execution_export_mock.py

echo.
echo Build complete! Executable can be found in the "dist" folder.
echo.
echo Usage: testlink_export_mock [options]
echo.
