<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$patchRoot = __DIR__ . DIRECTORY_SEPARATOR . 'nativephp-patches';

if (!is_dir($patchRoot)) {
    fwrite(STDERR, "NativePHP patch directory not found: {$patchRoot}\n");
    exit(1);
}

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($patchRoot, RecursiveDirectoryIterator::SKIP_DOTS)
);

$missingTargets = [];
$copied = 0;

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) {
        continue;
    }

    $sourcePath = $fileInfo->getPathname();
    $relativePath = substr($sourcePath, strlen($patchRoot) + 1);
    $destinationPath = $root . DIRECTORY_SEPARATOR . $relativePath;

    if (!file_exists($destinationPath)) {
        $missingTargets[] = $relativePath;
        continue;
    }

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
        fwrite(STDERR, "Failed to write patch target: {$destinationPath}\n");
        exit(1);
    }

    $copied++;
}

if ($missingTargets) {
    fwrite(STDERR, "NativePHP patch targets missing:\n");
    foreach ($missingTargets as $missing) {
        fwrite(STDERR, "  - {$missing}\n");
    }
    exit(1);
}

fwrite(STDOUT, "Applied {$copied} NativePHP patch file(s).\n");
