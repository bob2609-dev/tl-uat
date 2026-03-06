@echo off
REM TestLink Optimized Execution Module - Quick Deploy
REM Your Python: C:\Python\Python314
REM Target Server: localhost

echo ====================================
echo Quick Deploy to Production Server
echo ====================================
echo.

echo Target Server: localhost
echo Python Path: C:\Python\Python314\python.exe
echo.

echo Step 1: Copying Python backend files...
xcopy ".\*" "\\localhost\c$\xampp\htdocs\tl-uat\lib\execute\py_execute\" /E /Y /I /EXCLUDE:.env.example,QUICK_DEPLOY.bat,DEPLOYMENT_GUIDE.md,README.md,requirements.txt,start.py"
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy Python backend files
    pause
    exit /b 1
)
echo ✓ Python backend files copied

echo Step 2: Copying frontend files...
xcopy "..\optimized_execution_standalone.html" "\\localhost\c$\xampp\htdocs\tl-uat\lib\execute\" /Y"
xcopy "..\optimized_execution_module.php" "\\localhost\c$\xampp\htdocs\tl-uat\lib\execute\" /Y"
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy frontend files
    pause
    exit /b 1
)
echo ✓ Frontend files copied

echo Step 3: Copying production config...
copy ".env.production" "\\localhost\c$\xampp\htdocs\tl-uat\lib\execute\py_execute\.env"
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy production config
    pause
    exit /b 1
)
echo ✓ Production config copied

echo Step 4: Installing Python dependencies...
cd /d "\\localhost\c$\xampp\htdocs\tl-uat\lib\execute\py_execute\"
"C:\Python\Python314\python.exe" -m pip install -r requirements.txt
if %errorLevel% neq 0 (
    echo ERROR: Failed to install dependencies
    pause
    exit /b 1
)
echo ✓ Python dependencies installed

echo Step 5: Installing Windows Service...
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

"C:\nssm\nssm.exe" install TestLinkPythonAPI "C:\Python\Python314\python.exe" "main.py" "\\localhost\c$\xampp\htdocs\tl-uat\lib\execute\py_execute"
if %errorLevel% neq 0 (
    echo ERROR: Failed to install service
    pause
    exit /b 1
)

echo Step 6: Starting service...
"C:\nssm\nssm.exe" start TestLinkPythonAPI
if %errorLevel% neq 0 (
    echo ERROR: Failed to start service
    pause
    exit /b 1
)

echo.
echo ====================================
echo ✅ DEPLOYMENT COMPLETE!
echo ====================================
echo.
echo 🎉 SUCCESS! TestLink Optimized Execution Module is now running!
echo.
echo 📍 Access URLs:
echo    Frontend: http://test-management.nmbtz.com:9443/lib/execute/optimized_execution_standalone.html
echo    API Docs:  http://localhost:8000/docs
echo    API Health: http://localhost:8000/health
echo.
echo 🔧 Service Management:
echo    Start: nssm start TestLinkPythonAPI
echo    Stop: nssm stop TestLinkPythonAPI
echo    Restart: nssm restart TestLinkPythonAPI
echo    Status: nssm status TestLinkPythonAPI
echo.
pause
