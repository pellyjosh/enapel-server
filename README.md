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
