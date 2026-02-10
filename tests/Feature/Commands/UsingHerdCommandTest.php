<?php

use Orchestra\Testbench\Concerns\WithWorkbench;

use function Orchestra\Testbench\workbench_path;

uses(WithWorkbench::class);

test('command comments http line when using herd', function () {
    $configPath = config_path('solo.php');
    $templatePath = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is uncommented before the test
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );

    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'yes')
            ->expectsOutput('Successfully disabled HTTP server in solo.php configuration.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
        $this->assertStringNotContainsString("        'HTTP' => 'php artisan serve',", $updatedContent);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command shows no changes needed when using herd and already commented', function () {
    $configPath = config_path('solo.php');
    $templatePath = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is commented
    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );
    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'yes')
            ->expectsOutput('Great! No changes needed.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command uncomments http line when not using herd', function () {
    $configPath = config_path('solo.php');
    $templatePath = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is commented
    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );
    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'no')
            ->expectsOutput('Successfully enabled HTTP server in solo.php configuration.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        'HTTP' => 'php artisan serve',", $updatedContent);
        $this->assertStringNotContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command shows already uncommented when not using herd and already uncommented', function () {
    $configPath = config_path('solo.php');
    $templatePath = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is uncommented
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );
    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'no')
            ->expectsOutput('The HTTP server line is already uncommented or not found.')
            ->assertExitCode(0);

        // Verify nothing changed
        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        'HTTP' => 'php artisan serve',", $updatedContent);
        $this->assertStringNotContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command fails when config file not found', function () {
    $configPath = config_path('solo.php');

    if (file_exists($configPath)) {
        unlink($configPath);
    }

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'yes')
            ->expectsOutput('Config file solo.php not found.')
            ->assertExitCode(1);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});
