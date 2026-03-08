$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$buildRoot = Join-Path $projectRoot 'build'
$packageRoot = Join-Path $buildRoot 'package'
$appRoot = Join-Path $packageRoot 'app'
$phpRoot = Join-Path $packageRoot 'php'
$scriptsRoot = Join-Path $packageRoot 'scripts'
$supportRoot = Join-Path $packageRoot 'support'

if (Test-Path $packageRoot) {
    Remove-Item $packageRoot -Recurse -Force
}

New-Item -ItemType Directory -Force -Path $appRoot, $phpRoot, $scriptsRoot, $supportRoot | Out-Null

$directoriesToCopy = @(
    'app',
    'bootstrap',
    'config',
    'database',
    'public',
    'routes',
    'storage',
    'vendor'
)

foreach ($directory in $directoriesToCopy) {
    $source = Join-Path $projectRoot $directory
    $destination = Join-Path $appRoot $directory
    Copy-Item -Path $source -Destination $destination -Recurse -Force
}

$filesToCopy = @(
    'artisan',
    'composer.json',
    'composer.lock'
)

foreach ($file in $filesToCopy) {
    $source = Join-Path $projectRoot $file
    if (Test-Path $source) {
        Copy-Item -Path $source -Destination (Join-Path $appRoot $file) -Force
    }
}

Copy-Item `
    -Path (Join-Path $projectRoot 'build\launch.vbs') `
    -Destination (Join-Path $packageRoot 'launch.vbs') `
    -Force

Copy-Item `
    -Path (Join-Path $projectRoot 'build\launch-background.vbs') `
    -Destination (Join-Path $packageRoot 'launch-background.vbs') `
    -Force

Copy-Item `
    -Path (Join-Path $projectRoot 'build\scripts\*') `
    -Destination $scriptsRoot `
    -Recurse `
    -Force

Copy-Item `
    -Path (Join-Path $projectRoot 'build\templates\.env.windows') `
    -Destination (Join-Path $appRoot '.env.windows') `
    -Force

$requiredStorageDirs = @(
    (Join-Path $appRoot 'storage\app\public'),
    (Join-Path $appRoot 'storage\framework\cache\data'),
    (Join-Path $appRoot 'storage\framework\sessions'),
    (Join-Path $appRoot 'storage\framework\views'),
    (Join-Path $appRoot 'storage\logs')
)

foreach ($directory in $requiredStorageDirs) {
    New-Item -ItemType Directory -Force -Path $directory | Out-Null
}

$databaseFile = Join-Path $appRoot 'database\database.sqlite'
if (-not (Test-Path $databaseFile)) {
    New-Item -ItemType File -Path $databaseFile | Out-Null
}

$phpUrl = if ($env:ENAPEL_WINDOWS_PHP_URL) {
    $env:ENAPEL_WINDOWS_PHP_URL
} else {
    'https://windows.php.net/downloads/releases/latest/php-8.2-nts-Win32-vs16-x64-latest.zip'
}

$phpZip = Join-Path $buildRoot 'php.zip'
Invoke-WebRequest -Uri $phpUrl -OutFile $phpZip
Expand-Archive -Path $phpZip -DestinationPath $phpRoot -Force
Remove-Item $phpZip -Force

Copy-Item `
    -Path (Join-Path $projectRoot 'build\templates\php.ini') `
    -Destination (Join-Path $phpRoot 'php.ini') `
    -Force

$redistUrl = if ($env:ENAPEL_VC_REDIST_URL) {
    $env:ENAPEL_VC_REDIST_URL
} else {
    'https://aka.ms/vc14/vc_redist.x64.exe'
}

Invoke-WebRequest `
    -Uri $redistUrl `
    -OutFile (Join-Path $supportRoot 'vc_redist.x64.exe')
