@echo off
setlocal enabledelayedexpansion

set "ROOT=%~dp0"
set "APP_ROOT=%ROOT%..\app"
set "PHP=%ROOT%..\php\php.exe"
set "LOG_DIR=%ROOT%..\logs"
set "LOG_FILE=%LOG_DIR%\startup.log"
set "PORT=8000"
set "OPEN_BROWSER=1"
set "NETWORK_MODE=0"
set "BIND_HOST=127.0.0.1"
set "BROWSER_URL=http://127.0.0.1:%PORT%"
set "WAIT_COUNT=0"
set "WAIT_MAX=30"
set "SCRIPT_VERSION=2026-03-30.2"
set "LOCK_ROOT=%ROOT%..\run"
set "LOCK_DIR=%LOCK_ROOT%\start-server.lock"
set "SERVER_PID="
set "EXIT_CODE=0"

:parse_args
if "%~1"=="" goto args_done
if /I "%~1"=="--no-browser" set "OPEN_BROWSER=0"
if /I "%~1"=="--network" set "NETWORK_MODE=1"
if /I "%~1"=="--local" set "NETWORK_MODE=0"
shift
goto parse_args

:args_done
if "%NETWORK_MODE%"=="1" (
  set "BIND_HOST=0.0.0.0"
)

if not exist "%LOG_DIR%" mkdir "%LOG_DIR%" >nul 2>&1
call :log ""
call :log "=================================================="
call :log "Launch request received."
call :log "start-server.bat version: %SCRIPT_VERSION%"

if not exist "%LOCK_ROOT%" mkdir "%LOCK_ROOT%" >nul 2>&1
2>nul mkdir "%LOCK_DIR%"
if errorlevel 1 (
  call :log "Another startup sequence is already in progress. Exiting this request."
  exit /b 0
)

call :main
set "EXIT_CODE=%ERRORLEVEL%"

rmdir "%LOCK_DIR%" >nul 2>&1
exit /b %EXIT_CODE%

:main

if "%NETWORK_MODE%"=="1" (
  call :log "Requested startup mode: NETWORK (LAN exposed)."
) else (
  call :log "Requested startup mode: LOCAL (this PC only)."
)

if not exist "%APP_ROOT%" (
  call :log "ERROR: App directory not found: %APP_ROOT%"
  exit /b 1
)

if not exist "%PHP%" (
  call :log "ERROR: PHP executable not found: %PHP%"
  exit /b 1
)

if not exist "%APP_ROOT%\artisan" (
  call :log "ERROR: artisan entry point not found: %APP_ROOT%\artisan"
  exit /b 1
)

call "%ROOT%init-server.bat"
set "INIT_EXIT=%ERRORLEVEL%"
if not "%INIT_EXIT%"=="0" (
  call :log "ERROR: init-server failed with exit code %INIT_EXIT%."
  exit /b %INIT_EXIT%
)

cd /d "%APP_ROOT%"

call :detect_listener

if not defined SERVER_PID (
  call :start_server_background
) else (
  call :log "Detected existing listener on port %PORT% (PID %SERVER_PID%)."
  call :log "Reusing existing listener and skipping new server start."
)

:wait_for_server
call :detect_listener

if defined SERVER_PID goto server_ready

set /a WAIT_COUNT+=1
if !WAIT_COUNT! GEQ !WAIT_MAX! (
  call :log "ERROR: Server did not start listening on port %PORT% after %WAIT_MAX% seconds."
  call :log "Port diagnostics for %PORT%:"
  for /f "usebackq delims=" %%L in (`netstat -ano ^| findstr /R /C:":%PORT% "`) do (
    call :log "  %%L"
  )
  exit /b 1
)

timeout /t 1 /nobreak >nul
goto wait_for_server

:server_ready
call :log "Server is listening on port %PORT% (PID %SERVER_PID%)."

if "%NETWORK_MODE%"=="1" (
  call :log "Network mode enabled. Remote devices can use this machine IP on port %PORT%."
)

if "%OPEN_BROWSER%"=="1" (
  call :log "Opening browser at %BROWSER_URL% ..."
  start "" "%BROWSER_URL%"
)

call :log "Launch sequence completed."
exit /b 0

:detect_listener
set "SERVER_PID="

for /f "usebackq delims=" %%P in (`powershell -NoProfile -ExecutionPolicy Bypass -Command "$p=Get-NetTCPConnection -State Listen -LocalPort %PORT% -ErrorAction SilentlyContinue ^| Select-Object -First 1 -ExpandProperty OwningProcess; if($p){$p}"`) do (
  if not defined SERVER_PID set "SERVER_PID=%%P"
)

if not defined SERVER_PID (
  for /f "tokens=5" %%P in ('netstat -ano ^| findstr /R /C:":%PORT% .*LISTENING"') do (
    if not defined SERVER_PID set "SERVER_PID=%%P"
  )
)
exit /b 0

:start_server_background
call :log "Starting Laravel server on %BIND_HOST%:%PORT%..."
set "TMP_BAT=%TEMP%\enapel_serve_%RANDOM%.bat"
set "TMP_VBS=%TEMP%\enapel_serve_%RANDOM%.vbs"
echo @echo off > "%TMP_BAT%"
echo cd /d "%APP_ROOT%" >> "%TMP_BAT%"
echo "%PHP%" artisan serve --host=%BIND_HOST% --port=%PORT% --no-reload ^> "%LOG_DIR%\server.log" 2^>^&1 >> "%TMP_BAT%"
echo del "%%~f0" >> "%TMP_BAT%"
echo Set ws = CreateObject("WScript.Shell"^) > "%TMP_VBS%"
echo ws.Run """%TMP_BAT%""", 0, False >> "%TMP_VBS%"
cscript //nologo "%TMP_VBS%"
del "%TMP_VBS%"
exit /b 0

:log
>> "%LOG_FILE%" echo [%date% %time%] %~1
exit /b 0
