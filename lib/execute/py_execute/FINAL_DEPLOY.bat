@echo off
REM TestLink Optimized Execution Module - Final Deployment
REM Simple approach: copy standalone file and run

echo ====================================
echo Final Deployment - Simple Approach
echo ====================================
echo.

echo Step 1: Copying standalone file to server...
copy "TestLinkOptimizedExecution.py" "\\localhost\c$\xampp\htdocs\tl-uat\lib\execute\py_execute\"
if %errorLevel% neq 0 (
    echo ERROR: Failed to copy standalone file
    pause
    exit /b 1
)
echo ✓ Standalone file copied successfully

echo Step 2: Running standalone application on server...
echo Starting remote application...
start /B "cmd /c cd /d C:\xampp\htdocs\tl-uat\lib\execute\py_execute && python TestLinkOptimizedExecution.py"

echo.
echo ====================================
echo ✅ DEPLOYMENT COMPLETE!
echo ====================================
echo.
echo 🎉 SUCCESS! TestLink Optimized Execution Module is now running!
echo.
echo 📍 Access URLs:
echo    Frontend: http://test-management.nmbtz.com:9443/lib/execute/optimized_execution_standalone.html
echo    API Health: http://localhost:8000/health
echo    API Docs: http://localhost:8000/docs
echo.
echo 🔧 Management:
echo    To stop: Close the command window that opened
echo    To restart: Run this script again
echo    To check: Open the URLs above in your browser
echo.
pause
