<?php

declare(strict_types=1);

/**
 * NativePHP patch applier.
 *
 * Copies every file from scripts/nativephp-patches/ into the matching path
 * under the project root (typically vendor/...).  Unlike the original version
 * this script will CREATE the destination file when it does not yet exist,
 * which is the normal situation after a clean `composer install --prefer-dist`
 * wipes vendor and re-downloads packages that may not include TypeScript
 * sources in their dist tarball.
 *
 * Only hard-errors on unreadable sources or unwriteable destinations.
 */

$root = dirname(__DIR__);
$patchRoot = __DIR__ . DIRECTORY_SEPARATOR . 'nativephp-patches';

if (!is_dir($patchRoot)) {
    fwrite(STDERR, "NativePHP patch directory not found: {$patchRoot}\n");
    exit(1);
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($patchRoot, RecursiveDirectoryIterator::SKIP_DOTS)
);

$created = 0;
$overwritten = 0;

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $sourcePath      = $fileInfo->getPathname();
    $relativePath    = substr($sourcePath, strlen($patchRoot) + 1);
    $destinationPath = $root . DIRECTORY_SEPARATOR . $relativePath;
    $isNew           = !file_exists($destinationPath);

    // Final safety check: remove Vite hot file if it exists in the target space
    $hot_file = dirname($destinationPath, 2) . '/public/hot';
    if (file_exists($hot_file)) {
        unlink($hot_file);
        fwrite(STDOUT, "  [removed]     Vite hot file from vendor base\n");
    }

    // Always ensure the parent directory exists.
    $destinationDir = dirname($destinationPath);
    if (!is_dir($destinationDir)) {
        if (!mkdir($destinationDir, 0755, true) && !is_dir($destinationDir)) {
            fwrite(STDERR, "Failed to create directory: {$destinationDir}\n");
            exit(1);
        }
    }

    $contents = file_get_contents($sourcePath);
    if ($contents === false) {
        fwrite(STDERR, "Failed to read patch source: {$sourcePath}\n");
        exit(1);
    }

    if (file_put_contents($destinationPath, $contents) === false) {
        fwrite(STDERR, "Failed to write patch to: {$destinationPath}\n");
        exit(1);
    }

    if ($isNew) {
        $created++;
        fwrite(STDOUT, "  [created]     {$relativePath}\n");
    } else {
        $overwritten++;
        fwrite(STDOUT, "  [overwritten] {$relativePath}\n");
    }
}

$total = $created + $overwritten;
fwrite(STDOUT, "NativePHP patches applied: {$total} file(s) ({$created} created, {$overwritten} overwritten).\n");
