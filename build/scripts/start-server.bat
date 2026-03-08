@echo off
setlocal enabledelayedexpansion

set "ROOT=%~dp0"
set "APP_ROOT=%ROOT%..\app"
set "PHP=%ROOT%..\php\php.exe"
set "PORT=8000"
set "OPEN_BROWSER=1"
set "SERVER_RUNNING="

if /I "%~1"=="--no-browser" set "OPEN_BROWSER=0"

call "%ROOT%init-server.bat"
if errorlevel 1 exit /b 1

cd /d "%APP_ROOT%"

for /f %%I in ('netstat -ano ^| findstr /R /C:":%PORT% .*LISTENING"') do set "SERVER_RUNNING=1"

if not defined SERVER_RUNNING (
  start /b "" "%PHP%" artisan serve --host=0.0.0.0 --port=%PORT%
  timeout /t 2 /nobreak >nul
)

if "%OPEN_BROWSER%"=="1" start "" "http://127.0.0.1:%PORT%"
