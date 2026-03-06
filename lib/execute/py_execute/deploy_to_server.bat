@echo off
REM TestLink Optimized Execution Module - Deployment to Target Server
REM Target: localhost

echo ====================================
echo Deploying to Target Server
echo ====================================
echo.

set TARGET_SERVER=localhost
set TARGET_PATH=\\%TARGET_SERVER%\c$\TestLink\lib\execute\py_execute
set TARGET_WEB=\\%TARGET_SERVER%\c$\TestLink\lib\execute

echo Checking connection to target server...
ping -n 1 %TARGET_SERVER% >nul
if %errorLevel% neq 0 (
    echo ERROR: Cannot connect to target server %TARGET_SERVER%
    pause
    exit /b 1
)

echo Creating target directories...
if not exist "%TARGET_PATH%" (
    mkdir "%TARGET_PATH%"
    echo Created: %TARGET_PATH%
)

if not exist "%TARGET_WEB%" (
    mkdir "%TARGET_WEB%"
    echo Created: %TARGET_WEB%
)

echo Copying Python backend files...
xcopy ".\*" "%TARGET_PATH%\" /E /Y /I /EXCLUDE:.env.example,install_service.bat,install_service_fixed.bat,deploy_to_server.bat
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy Python backend files
    pause
    exit /b 1
)

echo Copying production configuration...
copy ".env.production" "%TARGET_PATH%\.env"
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy production config
    pause
    exit /b 1
)

echo Copying HTML frontend files...
xcopy "..\optimized_execution_standalone.html" "%TARGET_WEB%\" /Y
xcopy "..\optimized_execution_module.php" "%TARGET_WEB%\" /Y
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy frontend files
    pause
    exit /b 1
)

echo Creating logs directory on target...
if not exist "%TARGET_PATH%\logs" (
    mkdir "%TARGET_PATH%\logs"
    echo Created: %TARGET_PATH%\logs
)

echo Creating updated install_service.bat with XAMPP paths...
(
echo @echo off
echo REM TestLink Optimized Execution Module - Windows Service Installation
echo REM Target Server: localhost
echo.
echo REM Set paths - use XAMPP paths
echo set SERVICE_DIR=C:\xampp\htdocs\tl-uat\lib\execute\py_execute
echo set PYTHON_PATH=C:\Python\Python314\python.exe
echo set SCRIPT_NAME=main.py
) > "%TARGET_PATH%\install_service.bat"
echo.

echo Copying production configuration...
copy ".env.production" "%TARGET_PATH%\.env"
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy production config
    pause
    exit /b 1
)

echo Copying HTML frontend files...
xcopy "..\optimized_execution_standalone.html" "%TARGET_WEB%\" /Y
xcopy "..\optimized_execution_module.php" "%TARGET_WEB%\" /Y
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy frontend files
    pause
    exit /b 1
)

echo Creating logs directory on target...
if not exist "%TARGET_PATH%\logs" (
    mkdir "%TARGET_PATH%\logs"
    echo Created: %TARGET_PATH%\logs
)

echo.
echo ====================================
echo Deployment Complete!
echo ====================================
echo.
echo Target Server: %TARGET_SERVER%
echo Database Server: %TARGET_SERVER% (MySQL)
echo Application Server: localhost (Python API)
echo Backend Path: %TARGET_PATH%
echo Frontend Path: %TARGET_WEB%
echo.
echo Next Steps:
echo 1. RDP to %TARGET_SERVER%
echo 2. Run: %TARGET_PATH%\install_service.bat
echo 3. Access: http://localhost:8000/docs
echo 4. Test: http://localhost:8000/health
echo.
pause
