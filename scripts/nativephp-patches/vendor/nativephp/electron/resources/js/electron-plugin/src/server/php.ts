import {mkdirSync, statSync, writeFileSync, existsSync} from 'fs'
import fs_extra from 'fs-extra';

const {copySync, mkdirpSync} = fs_extra;

import Store from 'electron-store'
import {promisify} from 'util'
import {join} from 'path'
import {app} from 'electron'
import {execFile, spawn, spawnSync} from 'child_process'
import {createServer} from 'net'
import state from "./state.js";
import getPort, {portNumbers} from 'get-port';
import {ProcessResult} from "./ProcessResult.js";

let _resolvedPaths: any = null;

function getResolvedPaths() {
    if (_resolvedPaths) return _resolvedPaths;

    const userData = app.getPath('userData');
    const storagePath = join(userData, 'storage');
    const databasePath = join(userData, 'database');

    _resolvedPaths = {
        storagePath,
        databasePath,
        databaseFile: join(databasePath, 'database.sqlite'),
        bootstrapCache: join(userData, 'bootstrap', 'cache'),
    };

    return _resolvedPaths;
}

function ensureFoldersExist() {
    const paths = getResolvedPaths();
    mkdirpSync(paths.bootstrapCache);
    mkdirpSync(join(paths.storagePath, 'logs'));
    mkdirpSync(join(paths.storagePath, 'framework', 'cache'));
    mkdirpSync(join(paths.storagePath, 'framework', 'sessions'));
    mkdirpSync(join(paths.storagePath, 'framework', 'views'));
    mkdirpSync(join(paths.storagePath, 'framework', 'testing'));
}

const argumentEnv = getArgumentEnv();
const appPath = getAppPath();

function runningSecureBuild() {
    return existsSync(join(appPath, 'build', '__nativephp_app_bundle'))
        && process.env.NODE_ENV !== 'development';
}

function shouldMigrateDatabase(store) {
    return store.get('migrated_version') !== app.getVersion()
        && process.env.NODE_ENV !== 'development';
}

function shouldOptimize(store) {
    /*
     * For some weird reason,
     * the cached config is not picked up on subsequent launches,
     * so we'll just rebuilt it every time for now
     */

    return process.env.NODE_ENV !== 'development';
    // return runningSecureBuild();
    // return runningSecureBuild() && store.get('optimized_version') !== app.getVersion();
}

function getServerHost() {
    const configured = process.env.NATIVEPHP_SERVER_HOST?.trim();

    if (configured) {
        return configured;
    }

    return process.env.NODE_ENV === 'development' ? '127.0.0.1' : '0.0.0.0';
}

function parseServerPort(value?: string) {
    if (!value) {
        return null;
    }

    const parsed = Number(value);

    if (!Number.isInteger(parsed) || parsed < 1 || parsed > 65535) {
        console.warn(`Ignoring invalid NATIVEPHP_SERVER_PORT value: ${value}`);
        return null;
    }

    return parsed;
}

function getFixedServerPort() {
    const configured = parseServerPort(process.env.NATIVEPHP_SERVER_PORT);

    if (configured) {
        return configured;
    }

    return process.env.NODE_ENV === 'development' ? null : 8000;
}

async function getPhpPort(host: string) {
    const fixedPort = getFixedServerPort();

    if (fixedPort) {
        if (await canBindToPort(fixedPort, host)) {
            return fixedPort;
        }

        throw new Error(`Port ${fixedPort} is not available on ${host}`);
    }

    // Try get-port first (fast path)
    const suggestedPort = await getPort({
        host,
        port: portNumbers(8100, 9000)
    });

    // Validate that we can actually bind to this port
    if (await canBindToPort(suggestedPort, host)) {
        return suggestedPort;
    }

    // If get-port gave us a bad port, manually search starting from suggestedPort + 1
    console.warn(`Port ${suggestedPort} is not bindable, manually searching...`);

    for (let port = suggestedPort + 1; port < 9000; port++) {
        if (await canBindToPort(port, host)) {
            return port;
        }
    }

    throw new Error('Could not find an available port in range 8100-9000');
}

function canBindToPort(port: number, host: string): Promise<boolean> {
    return new Promise((resolve) => {
        const server = createServer();

        server.listen(port, host, () => {
            server.close(() => resolve(true));
        });

        server.on('error', () => {
            resolve(false);
        });
    });
}

async function retrievePhpIniSettings() {
    const env = getDefaultEnvironmentVariables() as any;

    const phpOptions = {
        cwd: appPath,
        env
    };

    let command = ['artisan', 'native:php-ini'];

    if (runningSecureBuild()) {
        command.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }

    if (!state.php || typeof state.php !== 'string' || state.php.includes('undefined')) {
        throw new Error(`Critical Error: PHP binary path is invalid or undefined. Resolved as: "${state.php}". Please reinstall the application.`);
    }

    return await promisify(execFile)(state.php, command, phpOptions);
}

async function retrieveNativePHPConfig() {
    const env = getDefaultEnvironmentVariables() as any;

    const phpOptions = {
        cwd: appPath,
        env
    };

    let command = ['artisan', 'native:config'];

    if (runningSecureBuild()) {
        command.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }

    if (!state.php || typeof state.php !== 'string' || state.php.includes('undefined')) {
        throw new Error(`Critical Error: PHP binary path is invalid or undefined. Resolved as: "${state.php}". Please reinstall the application.`);
    }

    return await promisify(execFile)(state.php, command, phpOptions);
}

function callPhp(args, options, phpIniSettings = {}) {

    if (args[0] === 'artisan' && runningSecureBuild()) {
        args.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }

    let iniSettings = Object.assign(getDefaultPhpIniSettings(), phpIniSettings);

    Object.keys(iniSettings).forEach(key => {
        args.unshift('-d', `${key}=${iniSettings[key]}`);
    });

    if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
        console.log('Calling PHP', state.php, args);
    }

    if (!state.php || typeof state.php !== 'string' || state.php.includes('undefined')) {
        throw new Error(`Critical Error: PHP binary path is invalid or undefined. Resolved as: "${state.php}". Please reinstall the application.`);
    }

    return spawn(
        state.php,
        args,
        {
            cwd: options.cwd,
            env: {
                ...process.env,
                ...options.env
            },
        }
    );
}

function callPhpSync(args, options, phpIniSettings = {}) {

    if (args[0] === 'artisan' && runningSecureBuild()) {
        args.unshift(join(appPath, 'build', '__nativephp_app_bundle'));
    }

    let iniSettings = Object.assign(getDefaultPhpIniSettings(), phpIniSettings);

    Object.keys(iniSettings).forEach(key => {
        args.unshift('-d', `${key}=${iniSettings[key]}`);
    });

    if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
        console.log('Calling PHP', state.php, args);
    }

    if (!state.php || typeof state.php !== 'string' || state.php.includes('undefined')) {
        throw new Error(`Critical Error: PHP binary path is invalid or undefined. Resolved as: "${state.php}". Please reinstall the application.`);
    }

    return spawnSync(
        state.php,
        args,
        {
            cwd: options.cwd,
            env: {
                ...process.env,
                ...options.env
            }
        }
    );
}

function getArgumentEnv() {
    const envArgs = process.argv.filter(arg => arg.startsWith('--env.'));

    const env: {
        TESTING?: number,
        APP_PATH?: string
    } = {};
    envArgs.forEach(arg => {
        const [key, value] = arg.slice(6).split('=');
        env[key] = value;
    });

    return env;
}

function getAppPath() {
    let appPath = app.getAppPath().replace('app.asar', 'app.asar.unpacked')

    if (process.env.NODE_ENV === 'development' || argumentEnv.TESTING == 1) {
        appPath = process.env.APP_PATH || argumentEnv.APP_PATH;
    }
    return appPath;
}

function ensureAppFoldersAreAvailable() {
    ensureFoldersExist();
    const paths = getResolvedPaths();

    // if (!runningSecureBuild()) {
    console.log('Copying storage folder...');
    console.log('Storage path:', paths.storagePath);
        if (!existsSync(paths.storagePath) || process.env.NODE_ENV === 'development') {
            console.log("App path:", appPath);
            copySync(join(appPath, 'storage'), paths.storagePath)
        }
    // }

    mkdirSync(paths.databasePath, {recursive: true})

    // Create a database file if it doesn't exist
    try {
        statSync(paths.databaseFile)
    } catch (error) {
        writeFileSync(paths.databaseFile, '')
    }
}

function startScheduler(secret, apiPort, phpIniSettings = {}) {
    const env = getDefaultEnvironmentVariables(secret, apiPort);

    const phpOptions = {
        cwd: appPath,
        env
    };

    return callPhp(['artisan', 'schedule:run'], phpOptions, phpIniSettings);
}

function getPath(name: string) {
    try {
        // @ts-ignore
        return app.getPath(name);
    } catch (error) {
        return '';
    }
}

// Define an interface for the environment variables
interface EnvironmentVariables {
    APP_ENV: string;
    APP_DEBUG: string;
    LARAVEL_STORAGE_PATH: string;
    NATIVEPHP_RUNNING: string;
    NATIVEPHP_STORAGE_PATH: string;
    NATIVEPHP_DATABASE_PATH: string;
    NATIVEPHP_SERVER_HOST?: string;
    NATIVEPHP_SERVER_PORT?: string;
    NATIVEPHP_API_URL?: string;
    NATIVEPHP_SECRET?: string;
    NATIVEPHP_USER_HOME_PATH: string;
    NATIVEPHP_APP_DATA_PATH: string;
    NATIVEPHP_USER_DATA_PATH: string;
    NATIVEPHP_DESKTOP_PATH: string;
    NATIVEPHP_DOCUMENTS_PATH: string;
    NATIVEPHP_DOWNLOADS_PATH: string;
    NATIVEPHP_MUSIC_PATH: string;
    NATIVEPHP_PICTURES_PATH: string;
    NATIVEPHP_VIDEOS_PATH: string;
    NATIVEPHP_RECENT_PATH: string;
    // Cache variables
    APP_SERVICES_CACHE?: string;
    APP_PACKAGES_CACHE?: string;
    APP_CONFIG_CACHE?: string;
    APP_ROUTES_CACHE?: string;
    APP_EVENTS_CACHE?: string;
    VIEW_COMPILED_PATH?: string;
}

function getDefaultEnvironmentVariables(secret?: string, apiPort?: number, serverHost?: string, serverPort?: number): EnvironmentVariables {
    const paths = getResolvedPaths();

    // Base variables with string values (no null values)
    let variables: EnvironmentVariables = {
        APP_ENV: process.env.NODE_ENV === 'development' ? 'local' : 'production',
        APP_DEBUG: process.env.NODE_ENV === 'development' ? 'true' : 'false',
        LARAVEL_STORAGE_PATH: paths.storagePath,
        NATIVEPHP_RUNNING: 'true',
        NATIVEPHP_STORAGE_PATH: paths.storagePath,
        NATIVEPHP_DATABASE_PATH: paths.databaseFile,
        NATIVEPHP_SERVER_HOST: serverHost,
        NATIVEPHP_SERVER_PORT: serverPort ? String(serverPort) : undefined,
        NATIVEPHP_USER_HOME_PATH: getPath('home'),
        NATIVEPHP_APP_DATA_PATH: getPath('appData'),
        NATIVEPHP_USER_DATA_PATH: getPath('userData'),
        NATIVEPHP_DESKTOP_PATH: getPath('desktop'),
        NATIVEPHP_DOCUMENTS_PATH: getPath('documents'),
        NATIVEPHP_DOWNLOADS_PATH: getPath('downloads'),
        NATIVEPHP_MUSIC_PATH: getPath('music'),
        NATIVEPHP_PICTURES_PATH: getPath('pictures'),
        NATIVEPHP_VIDEOS_PATH: getPath('videos'),
        NATIVEPHP_RECENT_PATH: getPath('recent'),
    };

    // Only if the server has already started
    if (secret && apiPort) {
        variables.NATIVEPHP_API_URL = `http://localhost:${apiPort}/api/`;
        variables.NATIVEPHP_SECRET = secret;
    }

    // Only add cache paths if in production mode
    if (runningSecureBuild()) {
        variables.APP_SERVICES_CACHE = join(paths.bootstrapCache, 'services.php'); // Should be present and writable
        variables.APP_PACKAGES_CACHE = join(paths.bootstrapCache, 'packages.php'); // Should be present and writable
        variables.APP_CONFIG_CACHE = join(paths.bootstrapCache, 'config.php');
        variables.APP_ROUTES_CACHE = join(paths.bootstrapCache, 'routes-v7.php');
        variables.APP_EVENTS_CACHE = join(paths.bootstrapCache, 'events.php');
        // variables.VIEW_COMPILED_PATH; // TODO: keep those in the phar file if we can.
    }

    return variables;
}

function getDefaultPhpIniSettings() {
    return {
        'memory_limit': '512M',
        'curl.cainfo': state.caCert,
        'openssl.cafile': state.caCert
    }
}

function serveApp(secret, apiPort, phpIniSettings): Promise<ProcessResult> {
    return new Promise(async (resolve, reject) => {
        const appPath = getAppPath();

        console.log('Starting PHP server...', `${state.php} artisan serve`, appPath, phpIniSettings)

        ensureAppFoldersAreAvailable();

        console.log('Making sure app folders are available')

        const serverHost = getServerHost();
        const phpPort = await getPhpPort(serverHost);
        const env = getDefaultEnvironmentVariables(secret, apiPort, serverHost, phpPort);

        const phpOptions = {
            cwd: appPath,
            env
        };

        const store = new Store({
            name: 'nativephp', // So it doesn't conflict with settings of the app
        });

        // Cache the project
        if (shouldOptimize(store)) {
            console.log('Caching view and routes...');

            let result = callPhpSync(['artisan', 'optimize'], phpOptions, phpIniSettings);

            if (result.status !== 0) {
                console.error('Failed to cache view and routes:', result.stderr.toString());
            } else {
                store.set('optimized_version', app.getVersion())
            }
        }

        // Migrate the database
        if (shouldMigrateDatabase(store)) {
            console.log('Migrating database...');

            if(parseInt(process.env.SHELL_VERBOSITY) > 0) {
                console.log('Database path:', databaseFile);
            }

            let result = callPhpSync(['artisan', 'migrate', '--force'], phpOptions, phpIniSettings);

            if (result.status !== 0) {
                console.error('Failed to migrate database:', result.stderr.toString());
            } else {
                store.set('migrated_version', app.getVersion())
            }
        }

        if (process.env.NODE_ENV === 'development') {
            console.log('Skipping Database migration while in development.')
            console.log('You may migrate manually by running: php artisan native:migrate')
        }

        let serverPath: string;
        let cwd: string;

        if (runningSecureBuild()) {
            serverPath = join(appPath, 'build', '__nativephp_app_bundle');
        } else {
            console.log('* * * Running from source * * *');
            serverPath = join(appPath, 'vendor', 'laravel', 'framework', 'src', 'Illuminate', 'Foundation', 'resources', 'server.php');
            cwd = join(appPath, 'public');
        }

        console.log('Starting PHP server...');
        const phpServer = callPhp(['-S', `${serverHost}:${phpPort}`, serverPath], {
            cwd: cwd,
            env
        }, phpIniSettings)

        const portRegex = /Development Server \(.*:([0-9]+)\) started/gm

        // Show urls called
        phpServer.stdout.on('data', (data) => {
            // [Tue Jan 14 19:51:00 2025] 127.0.0.1:52779 [POST] URI: /_native/api/events
            if (parseInt(process.env.SHELL_VERBOSITY) > 0) {
                console.log(data.toString().trim());
            }
        })

        // Show PHP errors and indicate which port the server is running on
        phpServer.stderr.on('data', (data) => {
            const error = data.toString();
            const match = portRegex.exec(data.toString());

            if (match) {
                const port = parseInt(match[1]);
                console.log("PHP Server started on port: ", port);
                resolve({
                    port,
                    process: phpServer,
                });
            } else {
                if (error.includes('[NATIVE_EXCEPTION]:')) {
                    const paths = getResolvedPaths();
                    let logFile = join(paths.storagePath, 'logs');

                    console.log();
                    console.error('Error in PHP:');
                    console.error('  ' + error.split('[NATIVE_EXCEPTION]:')[1].trim());
                    console.log('Please check your log files:');
                    console.log('  ' + logFile);
                    console.log();
                }
            }
        });

        // Log when any error occurs (not started, not killed, couldn't send message, etc)
        phpServer.on('error', (error) => {
            reject(error)
        });

        // Log when the PHP server exits
        phpServer.on('close', (code) => {
            console.log(`PHP server exited with code ${code}`);
        });
    })
}

export {
    startScheduler,
    serveApp,
    getAppPath,
    retrieveNativePHPConfig,
    retrievePhpIniSettings,
    getDefaultEnvironmentVariables,
    getDefaultPhpIniSettings,
    runningSecureBuild
}
