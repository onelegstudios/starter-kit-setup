<?php

use function Orchestra\Testbench\workbench_path;
use Orchestra\Testbench\Concerns\WithWorkbench;

uses(WithWorkbench::class);

test('command comments http line when not using built-in server', function () {
    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is uncommented before the test
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );

    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
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

test('command shows no changes needed when not using built-in server and already commented', function () {
    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is commented
    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );
    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
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

test('command uncomments http line when using built-in server', function () {
    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is commented
    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );
    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
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

test('command shows no changes needed when using built-in server and already uncommented', function () {
    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    // Ensure the line is uncommented
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );
    file_put_contents($configPath, $content);

    try {
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
            ->expectsOutput('Great! No changes needed.')
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
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsOutput('Config file solo.php not found.')
            ->assertExitCode(1);
    } finally {
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command fails when config path cannot be read as file', function () {
    $configPath = config_path('solo.php');

    if (file_exists($configPath)) {
        unlink($configPath);
    }

    mkdir($configPath);

    try {
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsOutput('Unable to read config file solo.php.')
            ->assertExitCode(1);
    } finally {
        if (is_dir($configPath)) {
            rmdir($configPath);
        }
    }
});

test('command fails when config file is not readable', function () {
    if (! function_exists('posix_getuid')) {
        $this->markTestSkipped('POSIX permission checks are unavailable on this platform.');
    }

    if (posix_getuid() === 0) {
        $this->markTestSkipped('Cannot test file permissions as root.');
    }

    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    file_put_contents($configPath, $templateContent);
    chmod($configPath, 0000);

    try {
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsOutput('Config file solo.php could not be read.')
            ->assertExitCode(1);
    } finally {
        chmod($configPath, 0644);
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});

test('command fails when config file cannot be written', function () {
    if (! function_exists('posix_getuid')) {
        $this->markTestSkipped('POSIX permission checks are unavailable on this platform.');
    }

    if (posix_getuid() === 0) {
        $this->markTestSkipped('Cannot test file permissions as root.');
    }

    $configPath      = config_path('solo.php');
    $templatePath    = workbench_path('config/solo.php');
    $templateContent = file_get_contents($templatePath);

    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );

    file_put_contents($configPath, $content);
    chmod($configPath, 0444);

    try {
        $this->artisan('starter-kit-setup:using-built-in-server')
            ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
            ->expectsOutput('Unable to update solo.php: write failed.')
            ->assertExitCode(1);
    } finally {
        chmod($configPath, 0644);
        if (file_exists($configPath)) {
            unlink($configPath);
        }
    }
});
