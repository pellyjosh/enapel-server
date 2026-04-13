import {app} from 'electron'
import NativePHP from '#plugin'
import path from 'path'
import defaultIcon from '../../resources/icon.png?asset&asarUnpack'
import certificate from '../../resources/cacert.pem?asset&asarUnpack'

let phpBinary = process.platform === 'win32' ? 'php.exe' : 'php';

// Detect architecture for macOS universal builds
const archFolder = (process.platform === 'darwin') ? (process.arch === 'arm64' ? 'arm64' : 'x86' ) : '';
phpBinary = path.join(app.getAppPath(), 'resources', 'php', archFolder, phpBinary).replace("app.asar", "app.asar.unpacked");

/**
 * Turn on the lights for the NativePHP app.
 */
NativePHP.bootstrap(
    app,
    defaultIcon,
    phpBinary,
    certificate
);
