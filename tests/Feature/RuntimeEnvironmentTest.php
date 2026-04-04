<?php

namespace Tests\Feature;

use App\Support\RuntimeEnvironment;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RuntimeEnvironmentTest extends TestCase
{
    /** @var array<int, string> */
    private array $pathsToDelete = [];

    protected function tearDown(): void
    {
        foreach (array_reverse($this->pathsToDelete) as $path) {
            $this->deletePath($path);
        }

        parent::tearDown();
    }

    public function test_it_seeds_a_nativephp_runtime_env_and_generates_an_app_key(): void
    {
        $basePath = $this->makeTempDirectory('base');
        $userDataPath = $this->makeTempDirectory('userdata');

        file_put_contents($basePath.DIRECTORY_SEPARATOR.'.env', implode(PHP_EOL, [
            'APP_NAME="Enapel Server"',
            'APP_KEY=',
            'APP_ENV=production',
            'APP_URL=http://127.0.0.1:8000',
            '',
        ]));

        $environmentPath = RuntimeEnvironment::initialize($basePath, $userDataPath, true);

        $this->assertSame($userDataPath.DIRECTORY_SEPARATOR.'runtime', $environmentPath);

        $runtimeEnvPath = $environmentPath.DIRECTORY_SEPARATOR.'.env';
        $this->assertFileExists($runtimeEnvPath);

        $contents = file_get_contents($runtimeEnvPath);

        $this->assertIsString($contents);
        $this->assertStringContainsString('APP_NAME="Enapel Server"', $contents);
        $this->assertStringContainsString('APP_URL=http://127.0.0.1:8000', $contents);
        $this->assertMatchesRegularExpression('/^APP_KEY=base64:[A-Za-z0-9+\/=]+$/m', $contents);
    }

    public function test_it_preserves_an_existing_runtime_app_key(): void
    {
        $basePath = $this->makeTempDirectory('base');
        $userDataPath = $this->makeTempDirectory('userdata');
        $runtimePath = $userDataPath.DIRECTORY_SEPARATOR.'runtime';

        mkdir($runtimePath, 0755, true);

        $existingKey = 'base64:existing-key';

        file_put_contents($runtimePath.DIRECTORY_SEPARATOR.'.env', implode(PHP_EOL, [
            'APP_NAME="Enapel Server"',
            "APP_KEY={$existingKey}",
            '',
        ]));

        RuntimeEnvironment::initialize($basePath, $userDataPath, true);

        $contents = file_get_contents($runtimePath.DIRECTORY_SEPARATOR.'.env');

        $this->assertIsString($contents);
        $this->assertStringContainsString("APP_KEY={$existingKey}", $contents);
    }

    private function makeTempDirectory(string $suffix): string
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'enapel-runtime-'.bin2hex(random_bytes(6)).'-'.$suffix;

        mkdir($path, 0755, true);
        $this->pathsToDelete[] = $path;

        return $path;
    }

    private function deletePath(string $path): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (is_file($path)) {
            unlink($path);
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getPathname());
                continue;
            }

            unlink($item->getPathname());
        }

        rmdir($path);
    }
}
