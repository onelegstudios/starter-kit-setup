<?php

use Orchestra\Testbench\Concerns\WithWorkbench;

uses(WithWorkbench::class);

beforeEach(function (): void {
    starterKitResetSoloConfigPath();
});

afterEach(function (): void {
    starterKitResetSoloConfigPath();
});

test('command comments http line when not using built-in server', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    // Ensure the line is uncommented before the test
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );

    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
        ->expectsOutput('Successfully disabled HTTP server in solo.php configuration.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    $this->assertStringNotContainsString("        'HTTP' => 'php artisan serve',", $updatedContent);
});

test('command shows no changes needed when not using built-in server and already commented', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    // Ensure the line is commented
    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );
    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
        ->expectsOutput('Great! No changes needed.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
});

test('command uncomments http line when using built-in server', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    // Ensure the line is commented
    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );
    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
        ->expectsOutput('Successfully enabled HTTP server in solo.php configuration.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString("        'HTTP' => 'php artisan serve',", $updatedContent);
    $this->assertStringNotContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
});

test('command shows no changes needed when using built-in server and already uncommented', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    // Ensure the line is uncommented
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );
    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
        ->expectsOutput('Great! No changes needed.')
        ->assertExitCode(0);

    // Verify nothing changed
    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString("        'HTTP' => 'php artisan serve',", $updatedContent);
    $this->assertStringNotContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
});

test('command fails when config file not found', function () {
    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsOutput('Config file solo.php not found.')
        ->assertExitCode(1);
});

test('command fails when config path cannot be read as file', function () {
    $configPath = starterKitSoloConfigPath();
    mkdir($configPath);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsOutput('Unable to read config file solo.php.')
        ->assertExitCode(1);
});

test('command fails when config file is not readable', function () {
    if (! function_exists('posix_getuid')) {
        $this->markTestSkipped('POSIX permission checks are unavailable on this platform.');
    }

    if (posix_getuid() === 0) {
        $this->markTestSkipped('Cannot test file permissions as root.');
    }

    $configPath = starterKitSoloConfigPath();
    starterKitWriteSoloConfig(starterKitSoloTemplateContent());
    chmod($configPath, 0000);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsOutput('Config file solo.php could not be read.')
        ->assertExitCode(1);
});

test('command fails when config file cannot be written', function () {
    if (! function_exists('posix_getuid')) {
        $this->markTestSkipped('POSIX permission checks are unavailable on this platform.');
    }

    if (posix_getuid() === 0) {
        $this->markTestSkipped('Cannot test file permissions as root.');
    }

    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        "        // 'HTTP' => 'php artisan serve',",
        $templateContent
    );

    starterKitWriteSoloConfig($content);
    chmod($configPath, 0444);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
        ->expectsOutput('Unable to update solo.php: write failed.')
        ->assertExitCode(1);
});

test('command fails when http entry is missing from solo config', function () {
    $templateContent = starterKitSoloTemplateContent();

    $content = str_replace(
        "        'HTTP' => 'php artisan serve',",
        '',
        $templateContent
    );
    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        '',
        $content
    );

    $this->assertStringNotContainsString("'HTTP' => 'php artisan serve',", $content);

    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
        ->expectsOutput('HTTP command entry not found in solo.php configuration.')
        ->assertExitCode(1);
});

test('command uncomments customized http entry when using built-in server', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        '        // "HTTP" => env("SOLO_HTTP_COMMAND", "php artisan serve --host=127.0.0.1 --port=8080"),',
        $templateContent
    );

    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'yes')
        ->expectsOutput('Successfully enabled HTTP server in solo.php configuration.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString('        "HTTP" => env("SOLO_HTTP_COMMAND", "php artisan serve --host=127.0.0.1 --port=8080"),', $updatedContent);
    $this->assertStringNotContainsString('        // "HTTP" => env("SOLO_HTTP_COMMAND", "php artisan serve --host=127.0.0.1 --port=8080"),', $updatedContent);
});

test('command comments customized http entry when not using built-in server', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        '        "HTTP" => env("SOLO_HTTP_COMMAND", "php artisan serve --host=127.0.0.1 --port=8080"),',
        $templateContent
    );

    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
        ->expectsOutput('Successfully disabled HTTP server in solo.php configuration.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString('        // "HTTP" => env("SOLO_HTTP_COMMAND", "php artisan serve --host=127.0.0.1 --port=8080"),', $updatedContent);
    $this->assertStringNotContainsString('        "HTTP" => env("SOLO_HTTP_COMMAND", "php artisan serve --host=127.0.0.1 --port=8080"),', $updatedContent);
});

test('command comments http line with windows line endings when not using built-in server', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = str_replace("\n", "\r\n", starterKitSoloTemplateContent());

    $content = str_replace(
        "        // 'HTTP' => 'php artisan serve',",
        "        'HTTP' => 'php artisan serve',",
        $templateContent
    );

    starterKitWriteSoloConfig($content);

    starterKitArtisan('starter-kit-setup:using-built-in-server')
        ->expectsConfirmation('Are you using the built-in HTTP server?', 'no')
        ->expectsOutput('Successfully disabled HTTP server in solo.php configuration.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString("        // 'HTTP' => 'php artisan serve',", $updatedContent);
    $this->assertStringContainsString("\r\n", $updatedContent);
    $this->assertStringNotContainsString("\n        'HTTP' => 'php artisan serve',\n", str_replace("\r\n", "\n", $updatedContent));
});
