<?php

use Orchestra\Testbench\Concerns\WithWorkbench;

uses(WithWorkbench::class);

test('command comments http line when using herd', function () {
    $configPath = config_path('solo.php');
    $originalContent = file_get_contents($configPath);

    // Ensure the line is uncommented before the test
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $originalContent
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
        file_put_contents($configPath, $originalContent);
    }
});

test('command shows no changes needed when using herd and already commented', function () {
    $configPath = config_path('solo.php');
    $originalContent = file_get_contents($configPath);

    // Ensure the line is commented
    if (!str_contains($originalContent, "        // 'HTTP' => 'php artisan serve',")) {
        $content = str_replace(
            "        'HTTP' => 'php artisan serve',",
            "        // 'HTTP' => 'php artisan serve',",
            $originalContent
        );
        file_put_contents($configPath, $content);
    }

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'yes')
            ->expectsOutput('Great! No changes needed.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    } finally {
        file_put_contents($configPath, $originalContent);
    }
});

test('command uncomments http line when not using herd', function () {
    $configPath = config_path('solo.php');
    $originalContent = file_get_contents($configPath);

    // Ensure the line is commented
    if (!str_contains($originalContent, "        // 'HTTP' => 'php artisan serve',")) {
        $content = str_replace(
            "        'HTTP' => 'php artisan serve',",
            "        // 'HTTP' => 'php artisan serve',",
            $originalContent
        );
        file_put_contents($configPath, $content);
    }

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'no')
            ->expectsOutput('Successfully enabled HTTP server in solo.php configuration.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        'HTTP' => 'php artisan serve',", $updatedContent);
        $this->assertStringNotContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    } finally {
        file_put_contents($configPath, $originalContent);
    }
});

test('command shows already uncommented when not using herd and already uncommented', function () {
    $configPath = config_path('solo.php');
    $originalContent = file_get_contents($configPath);

    // Ensure the line is uncommented
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $originalContent
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
        file_put_contents($configPath, $originalContent);
    }
});

test('command fails when config file not found', function () {
    $configPath = config_path('solo.php');
    $originalContent = file_get_contents($configPath);

    // Temporarily move the config file
    rename($configPath, $configPath . '.backup');

    try {
        $this->artisan('starter-kit-setup:using-herd')
            ->expectsConfirmation('Are you using Laravel Herd?', 'yes')
            ->expectsOutput('Config file solo.php not found.')
            ->assertExitCode(1);
    } finally {
        // Restore the config file
        rename($configPath . '.backup', $configPath);
    }
});
