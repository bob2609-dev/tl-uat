@echo off
REM TestLink Optimized Execution Module - Windows Service Installation
REM Target Server: localhost

echo ====================================
echo Installing TestLink Optimized Execution Service
echo ====================================
echo.

REM Check if running as Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Please run this script as Administrator!
    pause
    exit /b 1
)

REM Set paths
set SERVICE_DIR=C:\TestLinkServices\py_execute
set SCRIPT_NAME=main.py

REM Set paths - allow environment override
set SERVICE_DIR=%TARGET_SERVICE_DIR%
set PYTHON_PATH=%TARGET_PYTHON_PATH%

REM If no environment variables set, try to find Python installation
if "%PYTHON_PATH%"=="" (
    if exist "C:\Python39\python.exe" set PYTHON_PATH=C:\Python39\python.exe
    if exist "C:\Python310\python.exe" set PYTHON_PATH=C:\Python310\python.exe
    if exist "C:\Python311\python.exe" set PYTHON_PATH=C:\Python311\python.exe
    if exist "C:\Python312\python.exe" set PYTHON_PATH=C:\Python312\python.exe
    if exist "C:\Program Files\Python39\python.exe" set PYTHON_PATH=C:\Program Files\Python39\python.exe
    if exist "C:\Program Files\Python310\python.exe" set PYTHON_PATH=C:\Program Files\Python310\python.exe
    if exist "C:\Program Files\Python311\python.exe" set PYTHON_PATH=C:\Program Files\Python311\python.exe
    if exist "C:\Program Files\Python312\python.exe" set PYTHON_PATH=C:\Program Files\Python312\python.exe
    
    REM If still not found, try to detect from PATH
    if "%PYTHON_PATH%"=="" (
        for /f "%%i" in ('python --version 2^>^&^& del /q') do (
            if exist "%%~dpI\python.exe" set PYTHON_PATH=%%~dpI\python.exe
        )
    )
)

echo Creating service directory...
if not exist "%SERVICE_DIR%" (
    mkdir "%SERVICE_DIR%"
    echo Created: %SERVICE_DIR%
)

echo Python installation found:
if "%PYTHON_PATH%"=="" (
    echo WARNING: Python not found in common locations
    echo Please install Python 3.9+ or update PYTHON_PATH manually
) else (
    echo Using Python: %PYTHON_PATH%
)

echo Copying files...
xcopy ".\*" "%SERVICE_DIR%\" /E /Y /I /EXCLUDE:.env.example
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy files
    pause
    exit /b 1
)

echo Installing Python dependencies...
cd /d "%SERVICE_DIR%"

REM Check if Python is in PATH
python --version >nul 2>&1
if %errorLevel% neq 0 (
    echo Python not found in PATH, trying direct path...
    if exist "%PYTHON_PATH%" (
        echo Using Python at: %PYTHON_PATH%
        "%PYTHON_PATH%" -m pip install -r requirements.txt
    ) else (
        echo ERROR: Python not found at %PYTHON_PATH%
        echo Please install Python 3.9+ and update PYTHON_PATH in this script
        pause
        exit /b 1
    )
) else (
    echo Python found in PATH, installing dependencies...
    python -m pip install -r requirements.txt
)

if %errorLevel% neq 0 (
    echo ERROR: Failed to install dependencies
    pause
    exit /b 1
)

echo Installing NSSM (Non-Sucking Service Manager)...
if not exist "C:\nssm\nssm.exe" (
    echo Downloading NSSM...
    powershell -Command "Invoke-WebRequest -Uri 'https://nssm.cc/release/nssm-2.24.zip' -OutFile 'nssm.zip'"
    if exist "nssm.zip" (
        powershell -Command "Expand-Archive 'nssm.zip' -DestinationPath 'C:\nssm'"
        del nssm.zip
    )
)

if not exist "C:\nssm\nssm.exe" (
    echo ERROR: Failed to install NSSM
    pause
    exit /b 1
)

echo Installing Windows Service...
"C:\nssm\nssm.exe" install TestLinkPythonAPI "%PYTHON_PATH%" "%SCRIPT_NAME%" "%SERVICE_DIR%"
if %errorLevel% neq 0 (
    echo ERROR: Failed to install service
    pause
    exit /b 1
)

echo Configuring service...
"C:\nssm\nssm.exe" set TestLinkPythonAPI Start SERVICE_AUTO_START
"C:\nssm\nssm.exe" set TestLinkPythonAPI Description "TestLink Optimized Execution API - High Performance Backend"
"C:\nssm\nssm.exe" set TestLinkPythonAPI AppDirectory "%SERVICE_DIR%"
"C:\nssm\nssm.exe" set TestLinkPythonAPI AppEnvironmentExtra "PYTHONPATH=%SERVICE_DIR%"

echo Starting service...
"C:\nssm\nssm.exe" start TestLinkPythonAPI

echo.
echo ====================================
echo Service Installation Complete!
echo ====================================
echo.
echo Service Name: TestLinkPythonAPI
echo Service URL: http://localhost:8000
echo API Docs: http://localhost:8000/docs
echo Log File: %SERVICE_DIR%\logs\py_execute_production.log
echo.
echo To manage service:
echo   Start: nssm start TestLinkPythonAPI
echo   Stop:  nssm stop TestLinkPythonAPI
echo   Restart: nssm restart TestLinkPythonAPI
echo   Status: nssm status TestLinkPythonAPI
echo   Remove: nssm remove TestLinkPythonAPI
echo.
pause
