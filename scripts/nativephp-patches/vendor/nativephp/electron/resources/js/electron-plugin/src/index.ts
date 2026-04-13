import CrossProcessExports from "electron";
import { app, session, powerMonitor, BrowserWindow } from "electron";
import { ChildProcessWithoutNullStreams } from "child_process";
import { initialize } from "@electron/remote/main/index.js";
import state from "./server/state.js";
import { electronApp, optimizer } from "@electron-toolkit/utils";
import {
  retrieveNativePHPConfig,
  retrievePhpIniSettings,
  runScheduler,
  killScheduler,
  startAPI,
  startPhpApp,
} from "./server/index.js";
import { notifyLaravel } from "./server/utils.js";
import { resolve } from "path";
import { stopAllProcesses } from "./server/api/childProcess.js";
import ps from "ps-node";
import killSync from "kill-sync";

// Workaround for CommonJS module
import electronUpdater from 'electron-updater';
const { autoUpdater } = electronUpdater;

class NativePHP {
  processes: ChildProcessWithoutNullStreams[] = [];
  mainWindow: BrowserWindow | null = null;
  splashWindow: BrowserWindow | null = null;
  schedulerInterval: ReturnType<typeof setInterval> | null = null;

  public bootstrap(
    app: CrossProcessExports.App,
    icon: string,
    phpBinary: string,
    cert: string
  ) {

    initialize();

    if (!phpBinary || phpBinary === 'undefined') {
        throw new Error('PHP binary path could not be resolved. Please check your application paths.');
    }

    state.icon = icon;
    state.php = phpBinary;
    state.caCert = cert;

    this.bootstrapApp(app);
    this.addEventListeners(app);
  }

  private addEventListeners(app: Electron.CrossProcessExports.App) {
    app.on("open-url", (event, url) => {
      notifyLaravel("events", {
        event: "\\Native\\Laravel\\Events\\App\\OpenedFromURL",
        payload: [url],
      });
    });

    app.on("open-file", (event, path) => {
      notifyLaravel("events", {
        event: "\\Native\\Laravel\\Events\\App\\OpenFile",
        payload: [path],
      });
    });

    app.on("window-all-closed", () => {
      if (process.platform !== "darwin") {
        app.quit();
      }
    });

    app.on("before-quit", () => {
      if (this.schedulerInterval) {
          clearInterval(this.schedulerInterval);
      }

      // close all child processes from the app
      stopAllProcesses();

      this.killChildProcesses();
    });

    // Default open or close DevTools by F12 in development
    // and ignore CommandOrControl + R in production.
    // see https://github.com/alex8088/electron-toolkit/tree/master/packages/utils
    app.on("browser-window-created", (_, window) => {
      optimizer.watchWindowShortcuts(window);
    });

    app.on("activate", function (event, hasVisibleWindows) {
      // On macOS it's common to re-create a window in the app when the
      // dock icon is clicked and there are no other windows open.
      if (!hasVisibleWindows) {
        notifyLaravel("booted");
      }

      event.preventDefault();
    });
  }

  private async bootstrapApp(app: Electron.CrossProcessExports.App) {
    try {
      await app.whenReady();

      const config = await this.loadConfig();

      this.applyEmbeddedServerConfig(config);
      this.setDockIcon();
      this.setAppUserModelId(config);
      this.setDeepLinkHandler(config);
      this.startAutoUpdater(config);

      await this.startElectronApi();
      state.phpIni = await this.loadPhpIni();

      await this.startPhpApp();
      await this.waitForServerHttpReady();

      this.startScheduler();

      powerMonitor.on("suspend", () => {
        this.stopScheduler();
      });

      powerMonitor.on("resume", () => {
        this.stopScheduler();
        this.startScheduler();
      });

      const filter = {
          urls: [`http://127.0.0.1:${state.phpPort}/*`]
      };

      session.defaultSession.webRequest.onBeforeSendHeaders(filter, (details, callback) => {
          details.requestHeaders['X-NativePHP-Secret'] = state.randomSecret;

          callback({ requestHeaders: details.requestHeaders });
      });

      await notifyLaravel("booted");
    } catch (error) {
      console.error("Failed to bootstrap NativePHP app", error);
      this.showStartupError(error);
    }
  }

  private showSplashWindow(message: string, detail: string, isError = false) {
    if (process.env.NODE_ENV === "development") {
      return;
    }

    if (!this.splashWindow || this.splashWindow.isDestroyed()) {
      this.splashWindow = new BrowserWindow({
        width: 540,
        height: 340,
        show: false,
        center: true,
        frame: false,
        resizable: false,
        maximizable: false,
        minimizable: false,
        closable: isError,
        fullscreenable: false,
        alwaysOnTop: true,
        backgroundColor: '#020617',
        webPreferences: {
          sandbox: false,
          contextIsolation: true,
        },
      });

      this.splashWindow.once('ready-to-show', () => {
        this.splashWindow?.show();
      });

      this.splashWindow.on('closed', () => {
        this.splashWindow = null;
      });
    }

    this.splashWindow?.setClosable(isError);
    this.splashWindow?.loadURL(this.getSplashDataUrl(message, detail, isError));
  }

  private closeSplashWindow() {
    if (this.splashWindow && !this.splashWindow.isDestroyed()) {
      this.splashWindow.close();
    }

    this.splashWindow = null;
  }

  private getSplashDataUrl(message: string, detail: string, isError = false) {
    const safeMessage = this.escapeHtml(String(message ?? 'Starting Enapel Server'));
    const safeDetail = this.escapeHtml(String(detail ?? 'Please wait...'));
    const accent = isError ? '#ef4444' : '#2563eb';
    const pulse = isError
      ? '<div class="badge error">Startup issue detected</div>'
      : '<div class="badge">Starting local services</div><div class="spinner"></div>';

    const html = `<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Enapel Server</title>
    <style>
      :root { color-scheme: dark; }
      * { box-sizing: border-box; }
      body {
        margin: 0;
        min-height: 100vh;
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        background:
          radial-gradient(circle at top left, rgba(37,99,235,0.22), transparent 38%),
          radial-gradient(circle at bottom right, rgba(79,70,229,0.18), transparent 42%),
          #020617;
        color: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
      }
      .card {
        width: min(460px, calc(100vw - 32px));
        padding: 30px;
        border-radius: 24px;
        background: rgba(15, 23, 42, 0.88);
        border: 1px solid rgba(148, 163, 184, 0.12);
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
      }
      .brand {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 26px;
      }
      .logo {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: linear-gradient(135deg, ${accent}, #4f46e5);
        display: grid;
        place-items: center;
        color: white;
        font-size: 22px;
        font-weight: 900;
        letter-spacing: -0.02em;
      }
      .brand h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: -0.03em;
      }
      .brand p {
        margin: 4px 0 0;
        color: #94a3b8;
        font-size: 13px;
      }
      .badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.12);
        border: 1px solid rgba(37, 99, 235, 0.2);
        color: #93c5fd;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
      }
      .badge.error {
        background: rgba(239, 68, 68, 0.12);
        border-color: rgba(239, 68, 68, 0.24);
        color: #fca5a5;
      }
      .spinner {
        width: 42px;
        height: 42px;
        margin: 18px 0 18px;
        border-radius: 999px;
        border: 3px solid rgba(148, 163, 184, 0.18);
        border-top-color: ${accent};
        animation: spin 0.9s linear infinite;
      }
      h2 {
        margin: 18px 0 10px;
        font-size: 24px;
        line-height: 1.2;
        font-weight: 900;
        letter-spacing: -0.03em;
      }
      .detail {
        margin: 0;
        color: #94a3b8;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-wrap;
      }
      .hint {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid rgba(148, 163, 184, 0.12);
        color: #64748b;
        font-size: 12px;
      }
      @keyframes spin {
        to { transform: rotate(360deg); }
      }
    </style>
  </head>
  <body>
    <div class="card">
      <div class="brand">
        <div class="logo">E</div>
        <div>
          <h1>Enapel Server</h1>
          <p>Local business operations platform</p>
        </div>
      </div>
      ${pulse}
      <h2>${safeMessage}</h2>
      <p class="detail">${safeDetail}</p>
      <div class="hint">The window will switch automatically when the embedded server is ready.</div>
    </div>
  </body>
</html>`;

    return `data:text/html;charset=UTF-8,${encodeURIComponent(html)}`;
  }

  private escapeHtml(value: string) {
    return value
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  private async waitForServerHttpReady(timeoutMs = 30000) {
    const deadline = Date.now() + timeoutMs;
    const url = `http://127.0.0.1:${state.phpPort}/`;

    while (Date.now() < deadline) {
      try {
        const response = await fetch(url, {
          method: 'GET',
          redirect: 'manual',
          headers: {
            'X-NativePHP-Secret': state.randomSecret,
          },
        });

        if (response.status) {
          return;
        }
      } catch (error) {
        // keep polling until timeout
      }

      await new Promise((resolve) => setTimeout(resolve, 250));
    }

    throw new Error(`Timed out waiting for the embedded server on ${url}`);
  }

  private async waitForMainWindowLoaded(timeoutMs = 30000) {
    const deadline = Date.now() + timeoutMs;

    while (Date.now() < deadline) {
      const window = state.windows['main'];

      if (!window) {
        await new Promise((resolve) => setTimeout(resolve, 100));
        continue;
      }

      this.mainWindow = window;

      if (window.isVisible()) {
        return window;
      }

      await new Promise((resolve, reject) => {
        const remaining = Math.max(1, deadline - Date.now());

        const cleanup = () => {
          clearTimeout(timeout);
          window.webContents.off('did-finish-load', onFinish);
          window.webContents.off('did-fail-load', onFail);
        };

        const onFinish = () => {
          cleanup();
          resolve(window);
        };

        const onFail = (_event, _errorCode, errorDescription, validatedURL, isMainFrame) => {
          if (!isMainFrame) {
            return;
          }

          cleanup();
          reject(new Error(`Main window failed to load ${validatedURL}: ${errorDescription}`));
        };

        const timeout = setTimeout(() => {
          cleanup();
          reject(new Error('Timed out waiting for the main window to finish loading.'));
        }, remaining);

        window.webContents.once('did-finish-load', onFinish);
        window.webContents.on('did-fail-load', onFail);
      });

      return window;
    }

    throw new Error('Main application window was never created.');
  }

  private showStartupError(error: unknown) {
    const detail = error instanceof Error
      ? error.stack || error.message
      : String(error ?? 'Unknown startup error');

    require('fs').writeFileSync(require('path').join(require('os').homedir(), 'Desktop', 'enapel_error.txt'), detail, 'utf8');

    this.showSplashWindow(
      'Unable to start Enapel Server',
      `${error instanceof Error ? error.message : detail}\n\nCheck the application logs in:\n${resolve(app.getPath('userData'), 'storage', 'logs')}`,
      true,
    );
  }

  private async loadConfig() {
    let config = {};

    try {
      const result = await retrieveNativePHPConfig();

      config = JSON.parse(result.stdout);
    } catch (error) {
      console.error(error);
    }

    return config;
  }

  private applyEmbeddedServerConfig(config: any) {
    if (config?.server_host && !process.env.NATIVEPHP_SERVER_HOST) {
      process.env.NATIVEPHP_SERVER_HOST = String(config.server_host);
    }

    if (config?.server_port && !process.env.NATIVEPHP_SERVER_PORT) {
      process.env.NATIVEPHP_SERVER_PORT = String(config.server_port);
    }
  }

  private setDockIcon() {
    // Only run this on macOS
    if (
      process.platform === "darwin" &&
      process.env.NODE_ENV === "development"
    ) {
      app.dock.setIcon(state.icon);
    }
  }

  private setAppUserModelId(config: any) {
    if (config?.app_id && typeof config.app_id === 'string') {
        electronApp.setAppUserModelId(config.app_id);
    } else {
        console.warn('App ID is missing or invalid in config. Skipping setAppUserModelId.');
    }
  }

  private setDeepLinkHandler(config: any) {
    const deepLinkProtocol = config?.deeplink_scheme;

    if (deepLinkProtocol && typeof deepLinkProtocol === 'string') {
      if (process.defaultApp) {
        if (process.argv.length >= 2) {
          app.setAsDefaultProtocolClient(deepLinkProtocol, process.execPath, [
            resolve(process.argv[1]),
          ]);
        }
      } else {
        app.setAsDefaultProtocolClient(deepLinkProtocol);
      }
    } else {
      console.warn('Deep link protocol is missing or invalid in config. Skipping protocol registration.');
    }

            /**
             * Handle protocol url for windows and linux
             * This code will be different in Windows and Linux compared to MacOS.
             * This is due to both platforms emitting the second-instance event rather
             * than the open-url event and Windows requiring additional code in order to
             * open the contents of the protocol link within the same Electron instance.
             */
            if (process.platform !== "darwin") {
                const gotTheLock = app.requestSingleInstanceLock();
                if (!gotTheLock) {
                    app.quit();
                    return;
                } else {
                    app.on(
                        "second-instance",
                        (event, commandLine, workingDirectory) => {
                            // Someone tried to run a second instance, we should focus our window.
                            if (this.mainWindow) {
                                if (this.mainWindow.isMinimized())
                                    this.mainWindow.restore();
                                this.mainWindow.focus();
                            }

                            // the commandLine is array of strings in which last element is deep link url
                            notifyLaravel("events", {
                                event: "\\Native\\Laravel\\Events\\App\\OpenedFromURL",
                                payload: {
                                    url: commandLine[commandLine.length - 1],
                                },
                            });
                        },
                    );
                }
            }
        }

  private startAutoUpdater(config: any) {
    if (config?.updater?.enabled === true) {
      autoUpdater.checkForUpdatesAndNotify();
    }
  }

  private async startElectronApi() {
    // Start an Express server so that the Electron app can be controlled from PHP via API
    const electronApi = await startAPI();

    state.electronApiPort = electronApi.port;

    console.log("Electron API server started on port", electronApi.port);
  }

  private async loadPhpIni() {
    let config = {};

    try {
      const result = await retrievePhpIniSettings();

      config = JSON.parse(result.stdout);
    } catch (error) {
      console.error(error);
    }

    return config;
  }

  private async startPhpApp() {
    this.processes.push(await startPhpApp());
  }


  private stopScheduler() {
      if (this.schedulerInterval) {
          clearInterval(this.schedulerInterval);
          this.schedulerInterval = null;
      }
      killScheduler();
  }

  private startScheduler() {
    const now = new Date();
    const delay =
      (60 - now.getSeconds()) * 1000 + (1000 - now.getMilliseconds());

    setTimeout(() => {
      console.log("Running scheduler...");

      runScheduler();

      this.schedulerInterval = setInterval(() => {
        console.log("Running scheduler...");

        runScheduler();
      }, 60 * 1000);
    }, delay);
  }

  private killChildProcesses() {
    this.stopScheduler();

    this.processes
      .filter((p) => p !== undefined)
      .forEach((process) => {
        if (!process || !process.pid) return;
        if (process.killed && process.exitCode !== null) return;

        try {
          // @ts-ignore
          killSync(process.pid, 'SIGTERM', true); // Kill tree
          ps.kill(process.pid); // Sometimes does not kill the subprocess of php server

        } catch (err) {
          console.error(err);
        }

      });
  }
}

export default new NativePHP();
