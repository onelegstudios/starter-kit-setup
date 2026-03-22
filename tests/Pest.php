<?php

use Onelegstudios\StarterKitSetup\Tests\TestCase;

use function Orchestra\Testbench\workbench_path;

uses(TestCase::class)->in(__DIR__);

if (! function_exists('starterKitSoloConfigPath')) {
    function starterKitSoloConfigPath(): string
    {
        return config_path('solo.php');
    }
}

if (! function_exists('starterKitSoloTemplateContent')) {
    function starterKitSoloTemplateContent(): string
    {
        $templatePath = workbench_path('config/solo.php');
        $templateContent = file_get_contents($templatePath);

        if ($templateContent === false) {
            throw new RuntimeException('Unable to read workbench solo.php template.');
        }

        return $templateContent;
    }
}

if (! function_exists('starterKitWriteSoloConfig')) {
    function starterKitWriteSoloConfig(string $content): void
    {
        file_put_contents(starterKitSoloConfigPath(), $content);
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
