@echo off
setlocal enabledelayedexpansion

set "ROOT=%~dp0"
set "APP_ROOT=%ROOT%..\app"
set "PHP=%ROOT%..\php\php.exe"
set "LOG_DIR=%ROOT%..\logs"
set "LOG_FILE=%LOG_DIR%\startup.log"
set "ENV_REPAIR_SCRIPT=%ROOT%repair-env.ps1"

if not exist "%LOG_DIR%" mkdir "%LOG_DIR%" >nul 2>&1
>> "%LOG_FILE%" echo [%date% %time%] Running init-server.bat...

if not exist "%PHP%" (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: Portable PHP runtime was not found.
  echo Portable PHP runtime was not found.
  exit /b 1
)

if not exist "%APP_ROOT%" (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: Application root was not found: "%APP_ROOT%"
  exit /b 1
)

if not exist "%ENV_REPAIR_SCRIPT%" (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: Env repair script was not found: "%ENV_REPAIR_SCRIPT%"
  exit /b 1
)

cd /d "%APP_ROOT%"

if not exist ".env" (
  >> "%LOG_FILE%" echo [%date% %time%] .env missing. Creating from .env.windows...
  copy /Y ".env.windows" ".env" >nul

  >> "%LOG_FILE%" echo [%date% %time%] Validating .env format...
  powershell -NoProfile -ExecutionPolicy Bypass -File "%ENV_REPAIR_SCRIPT%" -EnvPath "%APP_ROOT%\.env" >> "%LOG_FILE%" 2>&1
  if errorlevel 1 (
    >> "%LOG_FILE%" echo [%date% %time%] ERROR: Failed to validate/repair .env format.
    exit /b 1
  )

  "%PHP%" artisan key:generate --force --ansi >> "%LOG_FILE%" 2>&1
  if errorlevel 1 (
    >> "%LOG_FILE%" echo [%date% %time%] ERROR: artisan key:generate failed.
    exit /b 1
  )
)

>> "%LOG_FILE%" echo [%date% %time%] Validating .env format...
powershell -NoProfile -ExecutionPolicy Bypass -File "%ENV_REPAIR_SCRIPT%" -EnvPath "%APP_ROOT%\.env" >> "%LOG_FILE%" 2>&1
if errorlevel 1 (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: Failed to validate/repair .env format.
  exit /b 1
)

if not exist "database\database.sqlite" (
  >> "%LOG_FILE%" echo [%date% %time%] database.sqlite missing. Creating...
  type nul > "database\database.sqlite"
)

if not exist "public\storage" (
  >> "%LOG_FILE%" echo [%date% %time%] Creating storage symlink...
  "%PHP%" artisan storage:link >> "%LOG_FILE%" 2>&1
)

>> "%LOG_FILE%" echo [%date% %time%] Running database migrations...
"%PHP%" artisan migrate --force --ansi >> "%LOG_FILE%" 2>&1
if errorlevel 1 (
  >> "%LOG_FILE%" echo [%date% %time%] ERROR: artisan migrate failed.
  exit /b 1
)

>> "%LOG_FILE%" echo [%date% %time%] init-server completed successfully.
exit /b 0
