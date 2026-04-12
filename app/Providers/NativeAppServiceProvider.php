<?php

namespace App\Providers;

use Illuminate\Support\Facades\Artisan;
use Native\Laravel\Facades\Window;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     *
     * Migrations are run before the window is opened so that the embedded
     * PHP server is fully ready when Electron navigates to it.  On the first
     * install the SQLite database is empty and every request would 500 if
     * sessions / cache / queue tables do not exist.
     */
    public function boot(): void
    {
        // Ensure the writable storage directories exist (NativePHP moves them
        // to %LOCALAPPDATA%\Enapel Server on Windows).
        $storage = storage_path();
        foreach (['app', 'framework/cache/data', 'framework/sessions', 'framework/views', 'logs'] as $dir) {
            $path = $storage . DIRECTORY_SEPARATOR . $dir;
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
        }

        // Run outstanding migrations so the database is ready before the
        // window loads.  This is safe to call on every boot; it is a no-op
        // when nothing new needs to migrate.
        Artisan::call('migrate', ['--force' => true]);

        Window::open()
            ->id('main')
            ->title('Enapel Server')
            ->width(1280)
            ->height(800)
            ->backgroundColor('#f9fafb')
            ->rememberState()
            ->showDevTools(true);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}
