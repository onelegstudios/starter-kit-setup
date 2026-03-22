<?php

use Orchestra\Testbench\Concerns\WithWorkbench;

uses(WithWorkbench::class);

beforeEach(function (): void {
    starterKitResetSoloConfigPath();
});

afterEach(function (): void {
    starterKitResetSoloConfigPath();
});

test('command adds mailpit line to solo config', function () {
    $configPath = starterKitSoloConfigPath();
    starterKitWriteSoloConfig(starterKitSoloTemplateContent());

    starterKitArtisan('starter-kit-setup:add-mailpit')
        ->expectsOutput('Successfully added Mailpit command to solo.php configuration.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertStringContainsString("        'Mailpit' => Command::from('mailpit')->lazy(),", $updatedContent);
    $this->assertSame(1, substr_count($updatedContent, "        'Mailpit' => Command::from('mailpit')->lazy(),"));
});

test('command is idempotent when mailpit line already exists', function () {
    $configPath = starterKitSoloConfigPath();
    $templateContent = starterKitSoloTemplateContent();

    $contentWithMailpit = str_replace(
        '        // Lazy commands do not automatically start when Solo starts.',
        "        // Lazy commands do not automatically start when Solo starts.\n        'Mailpit' => Command::from('mailpit')->lazy(),",
        $templateContent
    );
    starterKitWriteSoloConfig($contentWithMailpit);

    starterKitArtisan('starter-kit-setup:add-mailpit')
        ->expectsOutput('Mailpit command is already present in solo.php configuration.')
        ->assertExitCode(0);

    $updatedContent = starterKitReadFile($configPath);
    $this->assertSame(1, substr_count($updatedContent, "        'Mailpit' => Command::from('mailpit')->lazy(),"));
});

test('command fails when config file not found', function () {
    starterKitArtisan('starter-kit-setup:add-mailpit')
        ->expectsOutput('Config file solo.php not found.')
        ->assertExitCode(1);
});

test('command fails when config path cannot be read as file', function () {
    $configPath = starterKitSoloConfigPath();
    mkdir($configPath);

    starterKitArtisan('starter-kit-setup:add-mailpit')
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

    starterKitArtisan('starter-kit-setup:add-mailpit')
        ->expectsOutput('Config file solo.php could not be read.')
        ->assertExitCode(1);
});

test('command fails when insertion anchor is not found', function () {
    $templateContent = starterKitSoloTemplateContent();

    $contentWithoutAnchor = str_replace(
        '        // Lazy commands do not automatically start when Solo starts.',
        '',
        $templateContent
    );
    starterKitWriteSoloConfig($contentWithoutAnchor);

    starterKitArtisan('starter-kit-setup:add-mailpit')
        ->expectsOutput('Unable to update solo.php: insertion anchor not found.')
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
    starterKitWriteSoloConfig(starterKitSoloTemplateContent());
    chmod($configPath, 0444);

    starterKitArtisan('starter-kit-setup:add-mailpit')
        ->expectsOutput('Unable to update solo.php: write failed.')
        ->assertExitCode(1);
});
