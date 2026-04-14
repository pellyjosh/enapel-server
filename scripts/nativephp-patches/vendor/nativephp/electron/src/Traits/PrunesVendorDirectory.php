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
        Process::path($this->buildPath())
            ->forever()
            ->run('composer install --no-dev', function (string $type, string $output) {
                echo $output;
            });

        // Regenerate the autoloader after removing dev packages so ClassLoader
        // no longer references files that were deleted (e.g. fakerphp, myclabs/deep-copy).
        Process::path($this->buildPath())
            ->forever()
            ->run('composer dump-autoload --optimize --no-dev', function (string $type, string $output) {
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
