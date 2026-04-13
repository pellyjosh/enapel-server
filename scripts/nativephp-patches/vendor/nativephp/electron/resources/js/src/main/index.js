import {app} from 'electron'
import NativePHP from '#plugin'
import path from 'path'
import { fileURLToPath } from 'url'
import fs from 'fs'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'
import certificate from '../../resources/cacert.pem?asset&asarUnpack'

/**
 * Robust PHP Binary Path Resolution
 */
function getPhpBinaryPath() {
    try {
        const platform = process.platform;
        const arch = process.arch;
        const binaryName = platform === 'win32' ? 'php.exe' : 'php';
        
        // Strategy 1: Relative to current file (import.meta.url)
        // This file is in out/main/index.js
        // We need to get to resources/app.asar.unpacked/resources/php
        const __filename = fileURLToPath(import.meta.url);
        const __dirname = path.dirname(__filename);
        
        const archFolder = (platform === 'darwin') ? (arch === 'arm64' ? 'arm64' : 'x86' ) : '';
        
        // In built app, we are in resources/app.asar/out/main/index.js
        // We want resources/app.asar.unpacked/resources/php/[archFolder]/php.exe
        let phpBinary = path.join(__dirname, '..', '..', 'resources', 'php', archFolder, binaryName)
            .replace("app.asar", "app.asar.unpacked");

        // Strategy 2: Fallback to app.getAppPath()
        if (!fs.existsSync(phpBinary)) {
            console.log('PHP binary not found at Strategy 1 path, trying Strategy 2');
            phpBinary = path.join(app.getAppPath(), 'resources', 'php', archFolder, binaryName)
                .replace("app.asar", "app.asar.unpacked");
        }

        console.log('Resolved PHP Binary Path:', phpBinary);
        
        // Final sanity check
        if (!phpBinary || phpBinary.includes('undefined')) {
             return undefined;
        }

        return phpBinary;
    } catch (e) {
        console.error('Error resolving PHP path:', e);
        return undefined;
    }
}

const phpBinary = getPhpBinaryPath();

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app,
    defaultIcon,
    phpBinary,
    certificate
);
