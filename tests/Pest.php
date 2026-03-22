<?php

use Illuminate\Testing\PendingCommand;
use Onelegstudios\StarterKitSetup\Tests\TestCase;

use function Orchestra\Testbench\workbench_path;

uses(TestCase::class)->in(__DIR__);

if (! function_exists('starterKitSoloConfigPath')) {
    function starterKitSoloConfigPath(): string
    {
        return config_path('solo.php');
    }
}

if (! function_exists('starterKitArtisan')) {
    /** @param array<string, mixed> $parameters */
    function starterKitArtisan(string $command, array $parameters = []): PendingCommand
    {
        $result = \Pest\Laravel\artisan($command, $parameters);

        if (! $result instanceof PendingCommand) {
            throw new RuntimeException('artisan() did not return a PendingCommand instance.');
        }

        return $result;
    }
}

if (! function_exists('starterKitReadFile')) {
    function starterKitReadFile(string $path): string
    {
        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException("Unable to read file: {$path}");
        }

        return $content;
    }
}

if (! function_exists('starterKitSoloTemplateContent')) {
    function starterKitSoloTemplateContent(): string
    {
        return starterKitReadFile(workbench_path('config/solo.php'));
    }
}

if (! function_exists('starterKitWriteSoloConfig')) {
    function starterKitWriteSoloConfig(string $content): void
    {
        $path = starterKitSoloConfigPath();
        $result = @file_put_contents($path, $content);

        if ($result === false) {
            throw new RuntimeException("Unable to write file: {$path}");
        }
    }
}

if (! function_exists('starterKitResetSoloConfigPath')) {
    function starterKitResetSoloConfigPath(): void
    {
        $configPath = starterKitSoloConfigPath();

        if (is_dir($configPath)) {
            rmdir($configPath);

            return;
        }

        if (file_exists($configPath)) {
            chmod($configPath, 0644);
            unlink($configPath);
        }
    }
}
