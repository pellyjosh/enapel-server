# Windows Builds

This repo now includes Windows packaging paths for both `enapel-terminal` and `enapel-server`.

## What matters on macOS

You cannot produce the Windows desktop build for the Flutter app from macOS directly. The practical options are:

1. Use a Windows machine or VM.
2. Use GitHub Actions with a Windows runner.

The repo is set up for option 2 through this workflow:

- `.github/workflows/build-windows.yml`

## enapel-terminal

The terminal is a Flutter desktop app. The Windows installer flow is:

1. Build the release app with `flutter build windows --release`.
2. Compile the installer from `enapel-terminal/build/windows/installer.iss`.
3. Ship the generated `EnapelTerminalSetup.exe`.

The installer includes the Microsoft Visual C++ runtime installer because clean Windows machines may need it.

## enapel-server

The server is packaged as a NativePHP (Electron) desktop app. The Windows build now targets a single portable `.exe`
via `electron-builder` and bundles the Laravel app plus a portable PHP runtime.

Runtime model:

- SQLite database stored under the app's user data folder.
- Embedded PHP server.
- Desktop UI connects via localhost.
- LAN access enabled in production builds; the server binds to `0.0.0.0:8000` by default.
- Windows will prompt to allow firewall access on first run.

If you need a more production-grade Windows host later, replace the built-in PHP server with a proper Windows service and web server stack.

## Build from GitHub Actions

The Windows workflow runs on push to `main`/`master` and can be triggered manually.
It prepares a clean build `.env`, generates an application key, applies the NativePHP vendor patch, and then builds the portable executable.

1. Push your latest code to GitHub.
2. Open the `Actions` tab.
3. Run `Build Enapel Server Windows App` or wait for the push build.
4. Download the artifact `enapel-server-windows`.

## Local Windows build commands

If you use a Windows machine manually:

### Terminal

```powershell
cd enapel-terminal
flutter config --enable-windows-desktop
flutter pub get
flutter build windows --release
```

Then compile `enapel-terminal/build/windows/installer.iss` with Inno Setup.

### Server

```powershell
cd enapel-server
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan native:build win
```

## Important notes

- If you want true zero-config install for end users, keep the packaged server on SQLite. Bundling MySQL is a separate installer problem.
- The terminal connects to the server by IP, so the server machine must stay on the same LAN and keep port `8000` open.
- Other devices on the same LAN can open `http://<server-lan-ip>:8000` and use API endpoints from that host.
- The packaged app keeps its writable runtime `.env`, SQLite database, cache, and logs under `%LOCALAPPDATA%\\Enapel Server`.
- If startup fails, check `%LOCALAPPDATA%\\Enapel Server\\storage\\logs\\laravel.log` on the Windows host.
