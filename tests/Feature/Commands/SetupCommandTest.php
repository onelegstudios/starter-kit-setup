<?php

use Orchestra\Testbench\Concerns\WithWorkbench;

use function Orchestra\Testbench\workbench_path;

uses(WithWorkbench::class);

test('setup command executes all setup commands', function () {
    $configPath = config_path('solo.php');
    $templatePath = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );

    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:setup')
            ->expectsOutput('Running starter-kit-setup commands...')
            ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
            ->expectsOutput('Successfully disabled HTTP server in solo.php configuration.')
            ->expectsOutput('Successfully added Mailpit command to solo.php configuration.')
            ->expectsOutput('All starter-kit-setup commands completed successfully.')
            ->assertExitCode(0);

        $updatedContent = file_get_contents($configPath);
        $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
        $this->assertStringContainsString("        'Mailpit' => Command::from('mailpit')->lazy(),", $updatedContent);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('setup command fails when first command fails', function () {
    $configPath = config_path('solo.php');

    if (file_exists($configPath)) {
        unlink($configPath);
    }

    try {
        $this->artisan('starter-kit-setup:setup')
            ->expectsOutput('Running starter-kit-setup commands...')
            ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
            ->expectsOutput('Config file solo.php not found.')
            ->expectsOutput('The using-built-in-server command failed.')
            ->assertExitCode(1);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});
