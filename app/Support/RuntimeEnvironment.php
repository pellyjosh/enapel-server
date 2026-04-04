<?php

namespace App\Support;

use RuntimeException;

class RuntimeEnvironment
{
    public static function initialize(string $basePath, ?string $nativeUserDataPath = null, ?bool $nativeRunning = null): ?string
    {
        $nativeRunning ??= self::isNativeRunning();
        $nativeUserDataPath = self::normalizeValue($nativeUserDataPath ?? self::readProcessValue('NATIVEPHP_USER_DATA_PATH'));

        if (! $nativeRunning || $nativeUserDataPath === null) {
            return null;
        }

        $environmentPath = $nativeUserDataPath.DIRECTORY_SEPARATOR.'runtime';

        if (! is_dir($environmentPath) && ! mkdir($environmentPath, 0755, true) && ! is_dir($environmentPath)) {
            throw new RuntimeException(sprintf('Unable to create NativePHP runtime environment directory [%s].', $environmentPath));
        }

        $environmentFile = $environmentPath.DIRECTORY_SEPARATOR.'.env';

        if (! is_file($environmentFile)) {
            self::seedEnvironmentFile($basePath, $environmentFile);
        }

        self::ensureAppKey($environmentFile);

        return $environmentPath;
    }

    public static function environmentFilePath(?string $basePath = null): string
    {
        if (function_exists('app')) {
            try {
                return app()->environmentFilePath();
            } catch (\Throwable) {
                // Fall back to the project root when the container is unavailable.
            }
        }

        $basePath ??= dirname(__DIR__, 2);

        return $basePath.DIRECTORY_SEPARATOR.'.env';
    }

    private static function seedEnvironmentFile(string $basePath, string $environmentFile): void
    {
        $candidates = [
            $basePath.DIRECTORY_SEPARATOR.'.env',
            $basePath.DIRECTORY_SEPARATOR.'.env.example',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate) && copy($candidate, $environmentFile)) {
                return;
            }
        }

        self::writeFile($environmentFile, '');
    }

    private static function ensureAppKey(string $environmentFile): void
    {
        $contents = is_file($environmentFile) ? file_get_contents($environmentFile) : '';

        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to read environment file [%s].', $environmentFile));
        }

        if (preg_match('/^APP_KEY=(.+)$/m', $contents, $matches) === 1 && trim($matches[1]) !== '') {
            return;
        }

        $appKey = 'base64:'.base64_encode(random_bytes(32));

        if (preg_match('/^APP_KEY=.*$/m', $contents) === 1) {
            $updated = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$appKey}", $contents, 1);
            $contents = $updated ?? $contents;
        } else {
            $separator = $contents === '' || str_ends_with($contents, PHP_EOL) ? '' : PHP_EOL;
            $contents .= $separator."APP_KEY={$appKey}".PHP_EOL;
        }

        self::writeFile($environmentFile, $contents);
    }

    private static function writeFile(string $path, string $contents): void
    {
        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException(sprintf('Unable to write environment file [%s].', $path));
        }
    }

    private static function isNativeRunning(): bool
    {
        return filter_var(self::readProcessValue('NATIVEPHP_RUNNING'), FILTER_VALIDATE_BOOLEAN) === true;
    }

    private static function readProcessValue(string $key): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

        return is_string($value) ? self::normalizeValue($value) : null;
    }

    private static function normalizeValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
