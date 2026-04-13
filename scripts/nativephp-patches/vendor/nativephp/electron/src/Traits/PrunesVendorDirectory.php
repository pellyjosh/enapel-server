<?php

/**
 * TODO: When more drivers/adapters are added, this should be relocated
 */

namespace Native\Electron\Traits;

use Illuminate\Support\Facades\Process;
use Symfony\Component\Filesystem\Filesystem;

trait PrunesVendorDirectory
{
    abstract protected function buildPath(string $path = ''): string;

    protected function pruneVendorDirectory()
    {
        // Clear caches so that package discovery doesn't try to load classes
        // from packages we are about to delete.
        Process::path($this->buildPath())->run('php artisan config:clear');
        Process::path($this->buildPath())->run('php artisan route:clear');

        $composerPath = $this->buildPath('composer.json');
        if (file_exists($composerPath)) {
            $composerData = json_decode(file_get_contents($composerPath), true);
            if (isset($composerData['require']['nativephp/php-bin'])) {
                unset($composerData['require']['nativephp/php-bin']);
                file_put_contents($composerPath, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            }
        }

        Process::path($this->buildPath())
            ->env(['COMPOSER_PROCESS_TIMEOUT' => 2000])
            ->forever()
            ->run('composer install --no-dev --ignore-platform-reqs', function (string $type, string $output) {
                echo $output;
            });

        // Regenerate the autoloader after removing dev packages so ClassLoader
        // no longer references files that were deleted (e.g. fakerphp, myclabs/deep-copy).
        Process::path($this->buildPath())
            ->env(['COMPOSER_PROCESS_TIMEOUT' => 2000])
            ->forever()
            ->run('composer dump-autoload --optimize --no-dev --ignore-platform-reqs', function (string $type, string $output) {
                echo $output;
            });

        $filesystem = new Filesystem;
        $filesystem->remove([
            $this->buildPath('/vendor/bin'),
            $this->buildPath('/vendor/nativephp/php-bin'),
        ]);

        // Remove custom php binary package directory
        $binaryPackageDirectory = $this->binaryPackageDirectory();
        if (! empty($binaryPackageDirectory) && $filesystem->exists($this->buildPath($binaryPackageDirectory))) {
            $filesystem->remove($this->buildPath($binaryPackageDirectory));
        }
    }
}
