#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$ROOT_DIR/.env"
ENV_EXAMPLE="$ROOT_DIR/.env.example"
DB_FILE="$ROOT_DIR/database/database.sqlite"
DIST_DIR="$ROOT_DIR/dist"

usage() {
  cat <<'USAGE'
Build a local macOS DMG for enapel-server.

Usage:
  bash scripts/build-macos-dmg.sh [arm64|x86|all]

Examples:
  bash scripts/build-macos-dmg.sh
  bash scripts/build-macos-dmg.sh arm64
  bash scripts/build-macos-dmg.sh x86
  bash scripts/build-macos-dmg.sh all
USAGE
}

detect_arch() {
  case "$(uname -m)" in
    arm64|aarch64)
      echo "arm64"
      ;;
    x86_64)
      echo "x86"
      ;;
    *)
      echo ""
      ;;
  esac
}

# Force universal (Intel + Apple Silicon) by default for wider compatibility
ARCH="${1:-all}"

# Targeted for macOS 10.15+ (as supported by Electron 32)
export MACOSX_DEPLOYMENT_TARGET="10.15"

for cmd in php composer npm; do
  if ! command -v "$cmd" >/dev/null 2>&1; then
    echo "Missing required command: $cmd" >&2
    exit 1
  fi
done

if [[ ! -f "$ENV_EXAMPLE" ]]; then
  echo "Missing $ENV_EXAMPLE" >&2
  exit 1
fi

ENV_BACKUP=""
if [[ -f "$ENV_FILE" ]]; then
  ENV_BACKUP="$(mktemp)"
  cp "$ENV_FILE" "$ENV_BACKUP"
fi

cleanup() {
  if [[ -n "$ENV_BACKUP" && -f "$ENV_BACKUP" ]]; then
    cp "$ENV_BACKUP" "$ENV_FILE"
    rm -f "$ENV_BACKUP"
  else
    rm -f "$ENV_FILE"
  fi
}

trap cleanup EXIT

cp "$ENV_EXAMPLE" "$ENV_FILE"

export ENAPEL_BUILD_ENV_FILE="$ENV_FILE"
php <<'PHP'
<?php
$envPath = getenv('ENAPEL_BUILD_ENV_FILE');

if (! is_string($envPath) || $envPath === '') {
    fwrite(STDERR, "ENAPEL_BUILD_ENV_FILE is not set.\n");
    exit(1);
}

$contents = file_get_contents($envPath);

if ($contents === false) {
    fwrite(STDERR, "Failed to read {$envPath}.\n");
    exit(1);
}

$updates = [
    'APP_NAME' => '"Enapel Server"',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_URL' => 'http://127.0.0.1:8000',
    'DB_CONNECTION' => 'sqlite',
    'SESSION_DRIVER' => 'file',
    'CACHE_STORE' => 'file',
    'QUEUE_CONNECTION' => 'sync',
    'NATIVEPHP_ALLOW_LAN' => 'true',
    'NATIVEPHP_SERVER_HOST' => '0.0.0.0',
    'NATIVEPHP_SERVER_PORT' => '8000',
];

foreach ($updates as $key => $value) {
    $pattern = '/^'.preg_quote($key, '/').'=.*$/m';
    $replacement = $key.'='.$value;

    if (preg_match($pattern, $contents) === 1) {
        $contents = preg_replace($pattern, $replacement, $contents, 1) ?? $contents;
        continue;
    }

    $contents .= (str_ends_with($contents, PHP_EOL) ? '' : PHP_EOL).$replacement.PHP_EOL;
}

if (file_put_contents($envPath, $contents) === false) {
    fwrite(STDERR, "Failed to write {$envPath}.\n");
    exit(1);
}
PHP

mkdir -p "$ROOT_DIR/database"
[[ -f "$DB_FILE" ]] || touch "$DB_FILE"

echo "==> Installing Composer dependencies"
composer install --working-dir="$ROOT_DIR" --no-interaction --prefer-dist --optimize-autoloader

echo "==> Installing NPM dependencies"
npm --prefix "$ROOT_DIR" ci

echo "==> Generating Laravel app key for build env"
php "$ROOT_DIR/artisan" key:generate --force --ansi

echo "==> Cleaning old build artifacts and caches"
rm -rf "$ROOT_DIR/public/build"
rm -f "$ROOT_DIR/public/hot"
php "$ROOT_DIR/artisan" view:clear --ansi
php "$ROOT_DIR/artisan" config:clear --ansi
php "$ROOT_DIR/artisan" cache:clear --ansi

echo "==> Building Vite assets"
npm --prefix "$ROOT_DIR" run build

echo "==> Reapplying NativePHP patches"
php "$ROOT_DIR/scripts/patch-nativephp.php"

echo "==> Building macOS DMG (${ARCH})"
php "$ROOT_DIR/artisan" native:build mac "$ARCH" --no-interaction

echo "==> Finalizing: Keeping only the standalone DMG"
if [[ -d "$DIST_DIR" ]]; then
  # Find all files that are NOT the final .dmg and remove them
  # We use the arch-specific DMG name produced by electron-builder
  find "$DIST_DIR" -maxdepth 1 -type f ! -name "*.dmg" -delete
  find "$DIST_DIR" -maxdepth 1 -type d -name "*.app" -exec rm -rf {} +
fi

echo
echo "DMG build complete. Standalone DMG is available in:"
echo "  $DIST_DIR"

if [[ -d "$DIST_DIR" ]]; then
  find "$DIST_DIR" -maxdepth 1 \( -name '*.dmg' -o -name '*.app' -o -name '*.zip' \) -print | sed 's/^/  - /'
fi