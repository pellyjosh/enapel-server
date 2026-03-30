$ErrorActionPreference = 'Stop'

param(
    [Parameter(Mandatory = $true)]
    [string]$EnvPath
)

if (-not (Test-Path -LiteralPath $EnvPath)) {
    Write-Host "[repair-env] .env file not found: $EnvPath"
    exit 1
}

$lines = Get-Content -LiteralPath $EnvPath
$updated = New-Object System.Collections.Generic.List[string]
$changed = $false

foreach ($line in $lines) {
    $current = $line

    if ($current -match '^\s*#' -or $current -notmatch '=') {
        $updated.Add($current)
        continue
    }

    $parts = $current -split '=', 2
    $key = $parts[0].Trim()
    $value = $parts[1].Trim()

    if ([string]::IsNullOrWhiteSpace($key)) {
        $updated.Add($line)
        continue
    }

    if ($value -match '\s' -and $value -notmatch '^\s*".*"\s*$' -and $value -notmatch "^\s*'.*'\s*$") {
        $escaped = $value.Replace('"', '\"')
        $current = $key + '="' + $escaped + '"'
        $changed = $true
    }

    $updated.Add($current)
}

if ($changed) {
    $utf8NoBom = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllLines($EnvPath, $updated, $utf8NoBom)
    Write-Host "[repair-env] Repaired .env values that contained unquoted whitespace."
} else {
    Write-Host "[repair-env] .env format check passed."
}

exit 0
