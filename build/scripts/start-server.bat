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
set "SERVER_RUNNING="
set "EXISTING_LOCAL_ADDR="
set "WAIT_COUNT=0"
set "WAIT_MAX=20"
set "LAN_IP="

:parse_args
if "%~1"=="" goto args_done
if /I "%~1"=="--no-browser" set "OPEN_BROWSER=0"
if /I "%~1"=="--network" set "NETWORK_MODE=1"
if /I "%~1"=="--local" set "NETWORK_MODE=0"
shift
goto parse_args

:args_done
if "%NETWORK_MODE%"=="1" set "BIND_HOST=0.0.0.0"

if not exist "%LOG_DIR%" mkdir "%LOG_DIR%" >nul 2>&1
>> "%LOG_FILE%" echo.
>> "%LOG_FILE%" echo ==================================================
>> "%LOG_FILE%" echo [%date% %time%] Launch request received.

if "%NETWORK_MODE%"=="1" (
  >> "%LOG_FILE%" echo [%date% %time%] Requested startup mode: NETWORK (LAN exposed).
) else (
  >> "%LOG_FILE%" echo [%date% %time%] Requested startup mode: LOCAL (this PC only).
)

if not exist "%APP_ROOT%" (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: App directory not found: "%APP_ROOT%"
  exit /b 1
)

if not exist "%PHP%" (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: PHP executable not found: "%PHP%"
  exit /b 1
)

call "%ROOT%init-server.bat" >> "%LOG_FILE%" 2>&1
set "INIT_EXIT=%ERRORLEVEL%"
if not "%INIT_EXIT%"=="0" (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: init-server failed with exit code %INIT_EXIT%.
  exit /b %INIT_EXIT%
)

cd /d "%APP_ROOT%"

if "%NETWORK_MODE%"=="1" (
  for /f "usebackq delims=" %%I in (`powershell -NoProfile -Command "$ip=(Get-NetIPAddress -AddressFamily IPv4 -ErrorAction SilentlyContinue ^| Where-Object { $_.IPAddress -notlike '127.*' -and $_.IPAddress -notlike '169.254*' -and $_.PrefixOrigin -ne 'WellKnown' } ^| Select-Object -First 1 -ExpandProperty IPAddress); if($ip){Write-Output $ip}"`) do (
    if not defined LAN_IP set "LAN_IP=%%I"
  )

  if defined LAN_IP (
    set "BROWSER_URL=http://!LAN_IP!:%PORT%"
    >> "%LOG_FILE%" echo [%date% %time%] LAN access URL: http://!LAN_IP!:%PORT%
  ) else (
    >> "%LOG_FILE%" echo [%date% %time%] WARNING: Could not detect LAN IP. Using localhost in browser.
  )
)

for /f "tokens=1,2,3,4,5" %%A in ('netstat -ano ^| findstr /R /C:"TCP .*:%PORT% .*LISTENING"') do (
  if not defined SERVER_RUNNING set "SERVER_RUNNING=1"
  if not defined EXISTING_LOCAL_ADDR set "EXISTING_LOCAL_ADDR=%%B"
)

if defined SERVER_RUNNING if "%NETWORK_MODE%"=="1" (
  echo !EXISTING_LOCAL_ADDR! | findstr /I /C:"0.0.0.0:%PORT%" /C:"[::]:%PORT%" >nul
  if errorlevel 1 (
    >> "%LOG_FILE%" echo [%date% %time%] ERROR: Port %PORT% is already listening on !EXISTING_LOCAL_ADDR!.
    >> "%LOG_FILE%" echo [%date% %time%] ERROR: That is not LAN-exposed mode. Stop the existing server and relaunch in Network Mode.
    exit /b 2
  )
)

if not defined SERVER_RUNNING (
  >> "%LOG_FILE%" echo [%date% %time%] Starting Laravel server on %BIND_HOST%:%PORT%...
  start /b "" "%PHP%" artisan serve --host=%BIND_HOST% --port=%PORT% >> "%LOG_FILE%" 2>&1
  :wait_for_server
  timeout /t 1 /nobreak >nul
  set /a WAIT_COUNT+=1
  set "SERVER_RUNNING="
  set "EXISTING_LOCAL_ADDR="
  for /f "tokens=1,2,3,4,5" %%A in ('netstat -ano ^| findstr /R /C:"TCP .*:%PORT% .*LISTENING"') do (
    if not defined SERVER_RUNNING set "SERVER_RUNNING=1"
    if not defined EXISTING_LOCAL_ADDR set "EXISTING_LOCAL_ADDR=%%B"
  )
  if not defined SERVER_RUNNING if !WAIT_COUNT! LSS !WAIT_MAX! goto wait_for_server

  if not defined SERVER_RUNNING (
    >> "%LOG_FILE%" echo [%date% %time%] ERROR: Server did not start listening on port %PORT% after %WAIT_MAX% seconds.
    exit /b 1
  )
)

if defined SERVER_RUNNING (
  if defined EXISTING_LOCAL_ADDR (
    >> "%LOG_FILE%" echo [%date% %time%] Server is listening at !EXISTING_LOCAL_ADDR!.
  ) else (
    >> "%LOG_FILE%" echo [%date% %time%] Server is listening on port %PORT%.
  )
)

if "%NETWORK_MODE%"=="1" if defined LAN_IP (
  >> "%LOG_FILE%" echo [%date% %time%] Remote devices on the same network can use: http://!LAN_IP!:%PORT%
)

if "%OPEN_BROWSER%"=="1" (
  >> "%LOG_FILE%" echo [%date% %time%] Opening browser at !BROWSER_URL! ...
  start "" "!BROWSER_URL!"
)

>> "%LOG_FILE%" echo [%date% %time%] Launch sequence completed.
exit /b 0
