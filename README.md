# Enapel Server

The business engine of the Enapel ecosystem. This application handles the local operations for a specific business tenant.

## Purpose

Enapel Server is a localized business management application designed to handle day-to-day operations including inventory management, sales processing, financial tracking, and personnel management.

## Key Responsibilities

- **Business Operations**: Core logic for Sales (POS), Inventory tracking, Finance (Expenses), and Staff management.
- **License Client**: Communicates with `enapel-cloud` to validate the business installation and terminal capacity.
- **API Versioning**: Provides a robust versioned REST API (v1) to serve client applications like `enapel-terminal`.
- **Offline Resilience**: Implements a grace period mechanism allowing the server to function offline once validated.

## Tech Stack

- **Framework**: Laravel 11+
- **Frontend**: Blade Templates (for admin UI)
- **Database**: Localized MySQL (Tenant-specific)

## Integration

- **Outbound**: Calls `enapel-cloud` for license validation.
- **Inbound**: Hosts API endpoints for `enapel-terminal` (Flutter) mobile/tablet clients.

## Cloud URL Configuration

- **Server Setting**: The local server reads the hosted `enapel-cloud` base URL from `ENAPEL_CLOUD_URL` in its `.env`.
- **Example**: `ENAPEL_CLOUD_URL=https://your-cloud-domain.com`
- **Responsibility Split**: `enapel-server` uses `.env`, while `enapel-terminal` packages its cloud URL into the app build with `--dart-define=ENAPEL_CLOUD_URL=...`.

php artisan native:serve --no-interaction # boots the NativePHP/Electron app
npm run dev # starts Vite for hot-reloading
composer run native:dev

## Local macOS DMG build

If you want to package `enapel-server` into a macOS `.dmg` directly on this Mac, use:

```bash
bash scripts/build-macos-dmg.sh
```

You can also target a specific architecture:

```bash
bash scripts/build-macos-dmg.sh arm64
bash scripts/build-macos-dmg.sh x86
bash scripts/build-macos-dmg.sh all
```

What the script does:

- Backs up your current `.env` and restores it after the build.
- Creates a clean production build env based on `.env.example`.
- Forces the packaged app to use SQLite for a portable local build.
- Installs dependencies, builds Vite assets, reapplies the NativePHP patch, and runs `php artisan native:build mac ...`.

Build output is written to `dist/`.
