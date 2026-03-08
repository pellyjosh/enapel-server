@echo off
setlocal enabledelayedexpansion

set "ROOT=%~dp0"
set "APP_ROOT=%ROOT%..\app"
set "PHP=%ROOT%..\php\php.exe"

if not exist "%PHP%" (
  echo Portable PHP runtime was not found.
  exit /b 1
)

cd /d "%APP_ROOT%"

if not exist ".env" (
  copy /Y ".env.windows" ".env" >nul
  "%PHP%" artisan key:generate --force --ansi
)

if not exist "database\database.sqlite" (
  type nul > "database\database.sqlite"
)

if not exist "public\storage" (
  "%PHP%" artisan storage:link >nul 2>&1
)

"%PHP%" artisan migrate --force --ansi
